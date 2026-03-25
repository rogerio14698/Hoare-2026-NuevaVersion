<?php

namespace App\Http\Controllers;

use App\Configuracion;
use Illuminate\Http\Request;
use App\Idioma;
use Illuminate\Support\Facades\DB;
use App\TextosIdioma;


class ConfiguracionController extends Controller
{
    protected $tipo_contenido = 8; // 1 - Contenido, 2 - Imágenes Slide, 3 - Noticias, 4 - Portada, 5 - Galerías, 6 - Menú, 7 - Multimedia, 8 - Configuracion

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->compruebaSeguridad('editar-configuracion') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $configuracion = Configuracion::first();
        return view('eunomia.configuracion.form_edit_configuracion',compact('configuracion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Configuracion  $configuracion
     * @return \Illuminate\Http\Response
     */
    public function show(Configuracion $configuracion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Configuracion  $configuracion
     * @return \Illuminate\Http\Response
     */
    public function edit(Configuracion $configuracion)
    {
        if(\Auth::user()->compruebaSeguridad('editar-configuracion') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $configuracion = Configuracion::first();
        return view('eunomia.configuracion.form_edit_configuracion',compact('configuracion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Configuracion  $configuracion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-configuracion') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $idiomas = Idioma::where('activado','1')->orderByDesc('principal')->get();

        foreach ($idiomas as $idioma) {
            if ($idioma->principal == 1)
                $this->validate($request, [
                    'metatitulo' => 'required'
                ]);
        }

        $configuracion = Configuracion::findOrFail($id);

        $configuracion->nombre_empresa = $request->nombre_empresa;
        $configuracion->direccion_empresa = $request->direccion_empresa;
        $configuracion->nif_cif = $request->nif_cif;
        $configuracion->telefono_empresa = $request->telefono_empresa;
        $configuracion->movil_empresa = $request->movil_empresa;
        $configuracion->email = $request->email;
        $configuracion->g_analytics = $request->g_analytics;
        $configuracion->url = $request->url;
        $configuracion->facebook = $request->facebook;
        $configuracion->twitter = $request->twitter;
        $configuracion->instagram = $request->instagram;
        $configuracion->google_plus = $request->google_plus;
        $configuracion->youtube = $request->youtube;

        if ($configuracion->save()) {

            //dd($request->visible);
            for($i=0;$i<count($request->idioma_id);$i++) {
                $textosIdioma = TextosIdioma::where('contenido_id',$id)
                    ->where('tipo_contenido_id',$this->tipo_contenido)
                    ->where('idioma_id',$request->idioma_id[$i])->first();
                if (count($textosIdioma) == 0) {
                    $textosIdioma = new TextosIdioma();
                }
                if ($request->metatitulo[$i] != '') {
                    $textosIdioma->idioma_id = $request->idioma_id[$i];
                    $textosIdioma->contenido_id = $id;
                    $textosIdioma->tipo_contenido_id = $this->tipo_contenido;
                    $textosIdioma->metadescripcion = $request->metadescripcion[$i];
                    $textosIdioma->metatitulo = $request->metatitulo[$i];

                    $textosIdioma->save();
                }
            }

        }

        return redirect('eunomia/configuracion');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Configuracion  $configuracion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Configuracion $configuracion)
    {
        //
    }
}
