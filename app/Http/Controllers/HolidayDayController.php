<?php

namespace App\Http\Controllers;

use App\HolidayDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\User;
use Mail;
use Illuminate\Support\Facades\Log;

class HolidayDayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->compruebaSeguridad('mostrar-vacaciones') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        $holidaydays = HolidayDay::select(DB::raw("DATE_FORMAT(date, '%d/%m/%Y') as formatted_date"))
            ->where('user_id', \Auth::user()->id)->pluck('formatted_date');
        return view('eunomia.holiday_days.form_edit_holiday_days', compact('holidaydays'));
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
     * @param  \App\HolidayDay  $holidayDay
     * @return \Illuminate\Http\Response
     */
    public function show(HolidayDay $holidayDay)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HolidayDay  $holidayDay
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        if (\Auth::user()->compruebaSeguridad('mostrar-vacaciones') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        $holidaydays = HolidayDay::select(DB::raw("DATE_FORMAT(date, '%d/%m/%Y') as formatted_date"))
            ->where('user_id', \Auth::user()->id)->pluck('formatted_date');
        return view('eunomia.holiday_days.form_edit_holiday_days', compact('holidaydays'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HolidayDay  $holidayDay
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HolidayDay $holidayDay)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HolidayDay  $holidayDay
     * @return \Illuminate\Http\Response
     */
    public function destroy(HolidayDay $holidayDay)
    {
        //
    }

    public function insertaDiasNoDisponibles(Request $request)
    {
        if (\Auth::user()->compruebaSeguridad('crear-vacaciones') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj', '..no tiene permisos para acceder a esta sección');
        
        Log::info('Guardando vacaciones para usuario', ['user_id' => $request->user_id, 'fechas_count' => count($request->dates ?? [])]);
        
        $user_id = $request->user_id;
        HolidayDay::where('user_id', $user_id)->delete();
        $saved_count = 0;
        
        if (isset($request->dates)) {
            $array_fechas = [];
            foreach ($request->dates as $date) {
                $holiday_days = new HolidayDay;

                $holiday_days->user_id = $user_id;
                try {
                    $carbon_date = Carbon::createFromFormat('d/m/Y', trim($date));
                } catch (\Throwable $e) {
                    try {
                        $carbon_date = Carbon::parse(trim($date));
                    } catch (\Throwable $e2) {
                        // si todo falla, salta esa fecha
                        continue;
                    }
                }
                $holiday_days->date = $carbon_date->format('Y-m-d');
                
                // GUARDAR EL REGISTRO - esto faltaba
                $holiday_days->save();
                $saved_count++;
                
                // Agregar a array para email
                $array_fechas[] = $carbon_date->format('d/m/Y');
            }

            Log::info('Vacaciones guardadas exitosamente', ['saved_count' => $saved_count]);

            //Enviamos email a todos los componentes del departamento
            $usuario_actual = User::findOrFail($user_id);
            //$usuarios = User::where('role_id',$usuario_actual->role_id)->get();
            $usuarios = User::where('id', 23)->get();

            // COMENTADO TEMPORALMENTE - El envío de email está causando errores
            /*
            foreach ($usuarios as $user) {
                if (Mail::send('eunomia.includes.emails.email_vacaciones', [
                        'usuario' => $usuario_actual->name,
                        'fechas' => $array_fechas], function ($msj) use ($user) {
                        $msj->subject('Modificación fechas vacaciones');
                        $msj->to($user->email);
                        $msj->bcc('sistemas@mglab.es');
                    }) == true) {

                }
            }
            */
        }

        // Retornar respuesta JSON para AJAX
        return response()->json(['success' => true, 'message' => 'Días no disponibles guardados correctamente', 'saved_count' => $saved_count]);
    }
}
