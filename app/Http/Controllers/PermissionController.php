<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Modulo;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-permisos') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $permissions = Permission::all();
        return view('eunomia.permisos.listado_permisos', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-permiso') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $modulos = Modulo::all()->pluck('nombre','id');
        return view('eunomia.permisos.form_ins_permisos',compact('modulos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('crear-permiso') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required'
        ]);

        $permission = new Permission();

        $permission->name = $request->name;
        $permission->slug = $request->slug;
        $permission->model = $request->model;
        $permission->permission_type = $request->permission_type;
        $permission->description = $request->description;

        $permission->save();

        return redirect('eunomia/permisos');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-permiso') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $permission = Permission::findOrFail($id);
        $modulos = Modulo::all()->pluck('nombre','id');
        return view('eunomia.permisos.form_edit_permisos',compact('permission','modulos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-permiso') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required'
        ]);

        $permission = Permission::findOrFail($id);

        $permission->name = $request->name;
        $permission->slug = $request->slug;
        $permission->model = $request->model;
        $permission->permission_type = $request->permission_type;
        $permission->description = $request->description;

        $permission->save();

        return redirect('eunomia/permisos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-permiso') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect('eunomia/permisos');
    }

    /**
     * A full matrix of roles and permissions.
     * @return Response
     */
    public function showPermissionMatrix($id)
    {
        if(\Auth::user()->compruebaSeguridad('asignar-permisos-usuarios') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $users = User::where('id',$id)->get();
        $permissions = Permission::orderBy('model')->get();
        $prs = DB::table('permission_user')->select('user_id as u_id','permission_id as p_id')->get();

        $pivot = [];
        foreach($prs as $p) {
            $pivot[] = $p->u_id.":".$p->p_id;
        }

        return view('eunomia.permisos.matrix', compact('users','permissions','pivot') );
    }

    /**
     * Sync roles and permissions.
     * @return Response
     */
    public function updatePermissionMatrix(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('asignar-permisos-usuarios') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $bits = $request->get('perm_user');
        if (is_array($bits)) {
            foreach ($bits as $v) {
                $p = explode(":", $v);
                $data[] = array('user_id' => $p[0], 'permission_id' => $p[1]);
            }
            DB::transaction(function () use ($data) {
                DB::table('permission_user')->delete();
                DB::statement('ALTER TABLE permission_user AUTO_INCREMENT = 1');
                DB::table('permission_user')->insert($data);
            });
        } else {
            DB::transaction(function () {
                DB::table('permission_user')->delete();
            });
        }


        $level = "success";
        $message = "<i class='fa fa-check-square-o fa-1x'></i> Success! User permissions updated.";

        return redirect('eunomia/permisos/matrix/'.$request->user_id)
            ->with( ['flash' => ['message' => $message, 'level' =>  $level] ] );
    }
}
