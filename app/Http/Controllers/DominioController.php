<?php

namespace App\Http\Controllers;

use App\AgenteDominio;
use App\Customer;
use Illuminate\Http\Request;
use App\Dominio;

class DominioController extends Controller
{
    protected $modulo = 'dominios';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-dominios') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $dominios = Dominio::all();

        return view('eunomia.' .$this->modulo . '.listado_dominios', compact('dominios'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-dominio') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $customers = Customer::orderBy('nombre_cliente')->get()->pluck('nombre_cliente','id');

        $agentes_dominios = AgenteDominio::orderBy('nombre')->get()->pluck('nombre','id');

        return view('eunomia.' .$this->modulo . '.form_ins_dominios', compact('customers','agentes_dominios'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('crear-dominio') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $this->validate($request, [
            'customer_id' => 'required',
            'dominio' => 'required',
            'fecha_contratacion' => 'required',
            'fecha_renovacion' => 'required',
            'precio_anual' => 'required'
        ]);

        $dominio = new Dominio;

        $dominio->customer_id = $request->customer_id;
        $dominio->dominio = $request->dominio;
        $dominio->fecha_contratacion = $request->fecha_contratacion;
        $dominio->fecha_renovacion = $request->fecha_renovacion;
        $dominio->agente_dominio_id = $request->agente_dominio_id;
        $dominio->precio_anual = $request->precio_anual;
        if (isset($request->hosting))
            $dominio->hosting = $request->hosting;
        $dominio->precio_hosting = $request->precio_hosting;

        $dominio->save();

        return redirect('eunomia/'.$this->modulo);
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
        if(\Auth::user()->compruebaSeguridad('editar-dominio') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $customers = Customer::orderBy('nombre_cliente')->get()->pluck('nombre_cliente','id');

        $agentes_dominios = AgenteDominio::orderBy('nombre')->get()->pluck('nombre','id');

        $dominio = Dominio::findOrFail($id);

        return view('eunomia.' .$this->modulo . '.form_edit_dominios', compact('customers','agentes_dominios','dominio'));
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
        if(\Auth::user()->compruebaSeguridad('editar-dominio') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $this->validate($request, [
            'customer_id' => 'required',
            'dominio' => 'required',
            'fecha_contratacion' => 'required',
            'fecha_renovacion' => 'required',
            'precio_anual' => 'required'
        ]);

        $dominio = Dominio::findOrFail($id);

        $dominio->customer_id = $request->customer_id;
        $dominio->dominio = $request->dominio;
        $dominio->fecha_contratacion = $request->fecha_contratacion;
        $dominio->fecha_renovacion = $request->fecha_renovacion;
        $dominio->agente_dominio_id = $request->agente_dominio_id;
        $dominio->precio_anual = $request->precio_anual;
        $dominio->hosting = 0;
        if (isset($request->hosting))
            $dominio->hosting = $request->hosting;
        $dominio->precio_hosting = $request->precio_hosting;

        $dominio->save();

        return redirect('eunomia/'.$this->modulo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-dominio') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $dominio = Dominio::findOrFail($id);

        $dominio->delete();

        return redirect('eunomia/'.$this->modulo);
    }
}
