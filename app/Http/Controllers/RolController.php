<?php

namespace App\Http\Controllers;

use App\Rol;
use App\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-roles') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $roles = Rol::all();
        return view('eunomia.roles.listado_roles', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-rol') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        return view('eunomia.roles.form_ins_roles');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('crear-rol') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required'
        ]);

        $rol = new Rol();

        $rol->name = $request->name;
        $rol->slug = $request->slug;
        $rol->description = $request->description;

        $rol->save();

        return redirect('eunomia/roles');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show(Rol $rol)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-rol') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $rol = Rol::findOrFail($id);
        return view('eunomia.roles.form_edit_roles',compact('rol'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-rol') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required'
        ]);

        $rol = Rol::findOrFail($id);

        $rol->name = $request->name;
        $rol->slug = $request->slug;
        $rol->description = $request->description;

        $rol->save();

        return redirect('eunomia/roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-rol') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $rol = Rol::findOrFail($id);
        $rol->delete();

        return redirect('eunomia/roles');
    }

    /**
     * A full matrix of roles and permissions.
     * @return Response
     */
    public function showRoleMatrix()
    {
        if(\Auth::user()->compruebaSeguridad('asignar-permisos-roles') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $roles = Rol::all();
        $permissions = Permission::orderBy('model')->get();
        $prs = DB::table('permission_role')->select('role_id as r_id','permission_id as p_id')->get();

        $pivot = [];
        foreach($prs as $p) {
            $pivot[] = $p->r_id.":".$p->p_id;
        }

        return view('eunomia.roles.matrix', compact('roles','permissions','pivot') );
    }

    /**
     * Sync roles and permissions.
     * @return Response
     */
    public function updateRoleMatrix(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('asignar-permisos-roles') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $bits = $request->get('perm_role', []);

        // Agrupar por role_id para actualizar cada rol de forma independiente
        $dataByRole = [];
        foreach($bits as $v) {
            $p = explode(":", $v);
            $roleId = (int) $p[0];
            $permissionId = (int) $p[1];
            $dataByRole[$roleId][] = ['role_id' => $roleId, 'permission_id' => $permissionId];
        }

        // Obtener todos los roles existentes para saber cuáles actualizar
        $allRoleIds = Rol::pluck('id')->toArray();

        DB::transaction(function () use ($dataByRole, $allRoleIds) {
            foreach ($allRoleIds as $roleId) {
                // Borrar solo los permisos de este rol
                DB::table('permission_role')->where('role_id', $roleId)->delete();

                // Insertar los nuevos permisos de este rol (si tiene alguno marcado)
                if (isset($dataByRole[$roleId]) && count($dataByRole[$roleId]) > 0) {
                    DB::table('permission_role')->insert($dataByRole[$roleId]);
                }
            }
        });

        $level = "success";
        $message = "<i class='fa fa-check-square-o fa-1x'></i> Success! Role permissions updated.";

        return redirect('eunomia/roles/matrix')
            ->with( ['flash' => ['message' => $message, 'level' =>  $level] ] );
    }
}
