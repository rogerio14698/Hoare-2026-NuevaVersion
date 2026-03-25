<?php

namespace App\Http\Controllers;

use App\Modulo;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-modulos') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $modulos = Modulo::all();
        return view('eunomia.modulos.listado_modulos', compact('modulos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-modulo') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        return view('eunomia.modulos.form_ins_modulos');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('crear-modulo') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $this->validate($request, [
            'nombre' => 'required',
            'slug' => 'required'
        ]);

        $modulo = new Modulo();

        $modulo->nombre = $request->nombre;
        $modulo->descripcion = $request->descripcion;
        if (isset($request->imagen))
            $modulo->imagen = $request->imagen;
        else
            $modulo->imagen = 0;
        $modulo->slug = $request->slug;

        $modulo->save();

        return redirect('eunomia/modulos');
    }

    /**
     * Display the specified resource by slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function showBySlug($slug)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-modulos') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $modulo = \App\Modulo::where('slug', $slug)->first();
        if (!$modulo) {
            return view('eunomia.mensajes.mensaje_error')->with('msj','Módulo no encontrado');
        }
        return view('eunomia.modulos.ver_modulo', compact('modulo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-modulo') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $modulo = Modulo::findOrFail($id);
        return view('eunomia.modulos.form_edit_modulos',compact('modulo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-modulo') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $this->validate($request, [
            'nombre' => 'required',
            'slug' => 'required'
        ]);

        $modulo = Modulo::findOrFail($id);

        $modulo->nombre=$request->nombre;
        $modulo->descripcion=$request->descripcion;
        if (isset($request->imagen))
            $modulo->imagen = $request->imagen;
        else
            $modulo->imagen = 0;
        $modulo->slug = $request->slug;

        $modulo->save();

        return redirect('eunomia/modulos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-modulo') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $modulo = Modulo::findOrFail($id);
        $modulo->delete();

        return redirect('eunomia/modulos');
    }
    /**
     * Display the specified resource (por id).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modulo = \App\Modulo::find($id);
        if (!$modulo) {
            return view('eunomia.mensajes.mensaje_error')->with('msj','Módulo no encontrado');
        }
        // Redirige al detalle por slug
        return redirect()->route('modulo.showBySlug', ['slug' => $modulo->slug]);
    }
}
