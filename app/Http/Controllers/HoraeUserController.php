<?php

namespace App\Http\Controllers;

use App\Userdata;
use Illuminate\Http\Request;
use Hash;

use App\User;
use App\Departamento;
use Image;
use Storage;
use File;
use URL;
use Redirect;
use App\Rol;
use App\Customer;
use App\RolesUsuario;
use Validator;


class HoraeUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-usuarios') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $users = User::with(['departamento', 'roles_usuario.roles'])->where('baja', 0)->get();
        return view('eunomia.users.list_usuarios', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-usuario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $roles = Departamento::pluck( 'role_name', 'id');
        $rols = Rol::get()->pluck('name','id');
        $customers = Customer::orderBy('nombre_cliente')->get()->pluck('nombre_cliente','id');

        return view('eunomia.users.form_ins_usuarios', compact('roles','rols','customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('crear-usuario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $user = new user;

        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->fit(160, 160, function ($constraint) {
                $constraint->upsize();
            })->save('images/avatar/'.$filename );
            $user->avatar = $filename;
        }else {
            $user->avatar = 'default.svg';  // imagen por defecto
        }

        $user->name = $request->name;
        $user->dni = $request->dni;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $password = Hash::make($request->password);
        $user->password = $password;
        $user->customer_id = $request->customer_id;

        if ($user->save()){
            $lastId = $user->id;
            //Roles
            $roles = $request->roles;
            if (isset($roles)) {
                foreach ($roles as $rol) {
                    $rolesUsuario = new RolesUsuario();
                    $rolesUsuario->role_id = $rol;
                    $rolesUsuario->user_id = $lastId;

                    $rolesUsuario->save();
                }
            }
        }

        return redirect('eunomia/users');
  }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-usuario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $user = User::findOrFail($id);
        $roles = Departamento::pluck( 'role_name', 'id');
        $allrols = Rol::when(\Auth::user()->isRole('administrador'), function($query){
            $query->where('slug','<>','super-administrador');
        })->get()->pluck('name','id');
        $rols = RolesUsuario::where('user_id',$id)->pluck('role_id')->toArray();
        $customers = Customer::orderBy('nombre_cliente')->get()->pluck('nombre_cliente','id');
        

        return view('eunomia.users.form_edit_usuarios', compact('roles','allrols','rols','customers'))->withUser($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-usuario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $user = User::findOrFail($id);

        $avataractual = $user->avatar;

        if($request->hasFile('avatar')){
            File::delete('images/avatar/'.$avataractual);
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->fit(160, 160, function ($constraint) {
                $constraint->upsize();
            })->save('images/avatar/'.$filename );
            $user->avatar=$filename;
        }

        $user->name=$request->name;
        $user->dni=$request->dni;
        $user->email=$request->email;
        $user->role_id = $request->role_id;
        $user->customer_id = $request->customer_id;
        if($user->save()){
            // Eliminamos los roles del usuario para volver a insertar los nuevos
            if(\Auth::user()->compruebaSeguridad('editar-usuario') == true) {
                RolesUsuario::where('user_id', $id)->delete();
                $roles = $request->roles;
                if (isset($roles)) {
                    foreach ($roles as $rol) {
                        $rolesUsuario = new RolesUsuario();
                        $rolesUsuario->role_id = $rol;
                        $rolesUsuario->user_id = $id;

                        $rolesUsuario->save();
                    }
                }
                //Rellenamos o creamos registro con los datos auxiliares
                $userdata = Userdata::where('user_id', $user->id)->first();
                if (!is_object($userdata)){
                    $userdata = new Userdata;

                    $userdata->user_id = $user->id;
                }

                $userdata->mail_host = $request->mail_host;
                $userdata->mail_port = $request->mail_port;
                $userdata->mail_encryption = $request->mail_encryption;
                $userdata->mail_username = $request->mail_username;
                if ($request->mail_password != '')
                    $userdata->mail_password = $request->mail_password;
                $userdata->mail_message_limit = $request->mail_message_limit;

                $userdata->save();
            } else {
                return redirect('eunomia/usuarios');
            }
        }

        return redirect('eunomia/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-usuario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        
        $user = User::findOrFail($id);
        
        // IMPORTANTE: NO eliminamos físicamente para mantener los fichajes
        // Solo marcamos como baja lógica para preservar la trazabilidad legal
        $user->baja = 1;
        $user->save();

        // Nota: NO eliminamos el avatar para mantener la trazabilidad completa
        // File::delete('images/avatar/'.$avataractual);
        // $user->delete(); // ELIMINADO - Era eliminación física peligrosa

        return redirect('eunomia/users')->with('success', 'Usuario dado de baja correctamente. Los registros se mantienen por requisitos legales.');
    }

    public function password(){
        return View('eunomia.users.change_password');
    }

    public function updatePassword(Request $request){
        if(\Auth::user()->compruebaSeguridad('editar-usuario') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $rules = [
            'mypassword' => 'required',
            'password' => 'required|confirmed|min:6|max:18',
        ];

        $messages = [
            'mypassword.required' => 'La contraseña actual es requerida',
            'password.required' => 'La nueva contraseña es requerida',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.max' => 'La contraseña no puede tener más de 18 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()){
            return redirect('eunomia/users/password')->withErrors($validator);
        }
        else{
            if (Hash::check($request->mypassword, \Auth::user()->password)){
                $user = new User;
                $user->where('email', '=', \Auth::user()->email)
                    ->update(['password' => bcrypt($request->password)]);
                return redirect('eunomia')->with('status', 'Contraseña cambiada con éxito');
            }
            else
            {
                return redirect('eunomia/users/password')->with('message', 'Credenciales incorrectas');
            }
        }
    }

    /**
     * Reactivar un usuario dado de baja
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reactivar($id)
    {
        if(\Auth::user()->compruebaSeguridad('crear-usuario') == false)
            return response()->json(['success' => false, 'error' => 'No tiene permisos para reactivar usuarios'], 403);
        
        $user = User::findOrFail($id);
        
        // Verificar que el usuario esté dado de baja
        if ($user->baja != 1) {
            return response()->json(['success' => false, 'error' => 'El usuario no está dado de baja'], 400);
        }
        
        // Reactivar usuario
        $user->baja = 0;
        $user->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Usuario reactivado correctamente',
            'user' => [
                'dni' => $user->dni,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    /**
     * Devuelve los usuarios disponibles para menciones en TinyMCE.
     * Incluye usuarios de cualquier departamento.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mentionUsers()
    {
        $users = User::where('baja', 0)
            ->orderBy('name')
            ->get(['name'])
            ->map(function ($user) {
                return ['name' => $user->name];
            })
            ->values();

        return response()->json($users);
    }

}
