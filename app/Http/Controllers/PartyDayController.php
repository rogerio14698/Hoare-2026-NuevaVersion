<?php

namespace App\Http\Controllers;

use App\PartyDay;
use Illuminate\Http\Request;

class PartyDayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-dias-festivos') == false)
            return response(view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección'));

        $year = $request->input('year', date('Y'));
        $party_days = PartyDay::whereYear('date', $year)->orderBy('date')->paginate(10);
        $date_types = ['Nacional' => 'Nacional','Autonómica' => 'Autonómica','Local' => 'Local'];
        $years = PartyDay::selectRaw('YEAR(date) as year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $all_party_days = PartyDay::orderBy('date')->get();
        return response(view('eunomia.party_days.form_edit_party_days', compact('party_days','date_types','year','years','all_party_days')));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // No implementado
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // No implementado
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PartyDay  $partyDay
     * @return \Illuminate\Http\Response
     */
    public function show(PartyDay $partyDay)
    {
        // No implementado
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PartyDay  $partyDay
     * @return \Illuminate\Http\Response
     */
    public function edit(PartyDay $partyDay)
    {
        if(\Auth::user()->compruebaSeguridad('crear-dia-festivo') == false)
            return response(redirect()->route('party_days.index')->with('error', 'No tiene permisos para editar días festivos'));

        $date_types = ['Nacional' => 'Nacional','Autonómica' => 'Autonómica','Local' => 'Local'];
        return response(view('eunomia.party_days.edit', compact('partyDay', 'date_types')));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PartyDay  $partyDay
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, PartyDay $partyDay)
    {
        if(\Auth::user()->compruebaSeguridad('crear-dia-festivo') == false)
            return redirect()->route('party_days.index')->with('error', 'No tiene permisos para editar días festivos');

        $request->validate([
            'name' => 'required|string|max:255',
            'date_type' => 'required|string',
            'date' => 'required|date',
        ]);

        $partyDay->name = $request->name;
        $partyDay->date_type = $request->date_type;
        $partyDay->date = $request->date;
        $partyDay->save();

    return redirect()->route('party_days.index')->with('success', 'Día festivo actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PartyDay  $partyDay
     * @return \Illuminate\Http\Response
     */

    public function destroy(PartyDay $partyDay)
    {
        if(\Auth::user()->compruebaSeguridad('crear-dia-festivo') == false)
            return redirect()->route('party_days.index')->with('error', 'No tiene permisos para eliminar días festivos');

    $partyDay->delete();
    return redirect()->route('party_days.index')->with('success', 'Día festivo eliminado correctamente');
    }

    public function insertaDiasFestivos(Request $request){
        if(\Auth::user()->compruebaSeguridad('crear-dia-festivo') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        if ($request->date != ''){
            $party_day = new PartyDay;

            $party_day->name = $request->name;
            $party_day->date_type = $request->date_type;
            $party_day->date = $request->date;

            $party_day->save();
        }
        
        // Devolver respuesta JSON para AJAX en lugar de vista HTML
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Día festivo guardado correctamente',
                'party_day' => $party_day ?? null
            ]);
        }
        
        // Solo para peticiones no AJAX (fallback)
        $party_days = PartyDay::orderBy('date')->get();
        return view('eunomia.party_days.calendario',compact('party_days'));

    }
}
