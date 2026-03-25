<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str as Str;
use Auth;
use JsValidator;
use App\Project;

class HoraeCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('mostrar-clientes') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        
        if($request->has('trashed')) {
            $customers = Customer::onlyTrashed()->orderBy('nombre_cliente')->get();
        } else {
            $customers = Customer::orderBy('nombre_cliente')->get();
        }
        
        return view('eunomia.customers.list_customers', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->compruebaSeguridad('crear-cliente') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $customers = Customer::orderBy('nombre_cliente')->get();
        $action=null;
        $rules = [
            'codigo_cliente' => 'required|size:3|unique:customers,codigo_cliente',
            'nombre_cliente' => 'required',
            'email_cliente' => 'required|email'
        ];

        $messages = [
            'codigo_cliente.required' => 'El código del cliente es obligatorio',
            'codigo_cliente.size' => 'El código del cliente debe tener 3 caracteres',
            'codigo_cliente.unique' => 'Ya existe un cliente con este código',
            'nombre_cliente.required' => 'El nombre del cliente es obligatorio',
            'email_cliente.required' => 'El email es obligatorio',
            'email_cliente.email' => 'Escriba un email válido'
        ];
        $validator = JsValidator::make($rules, $messages, [], '#formulario_clientes');
        return view('eunomia.customers.form_ins_customers', compact('customers','action','validator'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->compruebaSeguridad('crear-cliente') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        if ($request->action != 1){
            //Validación de los datos del formulario
            $this->validate($request, [
                'codigo_cliente' => 'required|size:3|unique:customers,codigo_cliente',
                'nombre_cliente' => 'required',
                'email_cliente' => 'required|email'
            ],[
                'codigo_cliente.required' => 'El código del cliente es obligatorio',
                'codigo_cliente.size' => 'El código del cliente debe tener 3 caracteres',
                'codigo_cliente.unique' => 'Ya existe un cliente con este código',
                'nombre_cliente.required' => 'El nombre del cliente es obligatorio',
                'email_cliente.required' => 'El email es obligatorio',
                'email_cliente.email' => 'Escriba un email válido'
            ]);
        } else {
            // Validación para AJAX (cuando action = 1)
            $this->validate($request, [
                'codigo_cliente' => 'required|size:3|unique:customers,codigo_cliente',
                'nombre_cliente' => 'required',
                'email_cliente' => 'required|email'
            ]);
        }

        $customer = new customer;

        $customer->codigo_cliente = $request->codigo_cliente;
        $customer->nombre_cliente = $request->nombre_cliente;
        $customer->email_cliente = $request->email_cliente;
        $customer->telefono_cliente = $request->telefono_cliente;
        $customer->contacto_cliente = $request->contacto_cliente;
        $customer->role_id = $request->role_id;
        $customer->slug = Str::slug($request->nombre_cliente);
        $customer->save();

        if ($request->action == 1)
            return $customer->id;
        else
            return redirect('eunomia/customers');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        if(\Auth::user()->compruebaSeguridad('editar-cliente') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        return view('eunomia.customers.form_edit_customers')->withCustomer($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        if(\Auth::user()->compruebaSeguridad('editar-cliente') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');

        $customer->codigo_cliente = $request->codigo_cliente;
        $customer->nombre_cliente = $request->nombre_cliente;
        $customer->email_cliente = $request->email_cliente;
        $customer->telefono_cliente = $request->telefono_cliente;
        $customer->contacto_cliente = $request->contacto_cliente;
        $customer->slug = Str::slug($request->nombre_cliente);

        $customer->save();

        return redirect('eunomia/customers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        if(\Auth::user()->compruebaSeguridad('eliminar-cliente') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        $customer->delete();
        return redirect('eunomia/customers');
    }

    /**
     * Restore a soft deleted customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if(\Auth::user()->compruebaSeguridad('editar-cliente') == false)
            return view('eunomia.mensajes.mensaje_error')->with('msj','..no tiene permisos para acceder a esta sección');
        
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        
        return redirect('eunomia/customers?trashed=1')->with('success', 'Cliente restaurado correctamente');
    }

    public function muestraFormularioCliente(){
        $action = 1;
        $rules = [
            'codigo_cliente' => 'required|size:3',
            'nombre_cliente' => 'required',
            'email_cliente' => 'required|email'
        ];

        $messages = [
            'codigo_cliente.required' => 'El código del cliente es obligatorio',
            'codigo_cliente.size' => 'El código del cliente debe tener 3 caracteres',
            'nombre_cliente.required' => 'El nombre del cliente es obligatorio',
            'email_cliente.required' => 'El email es obligatorio',
            'email.email' => 'Escriba un email válido'
        ];
        $validator = JsValidator::make($rules, $messages, [], '#formulario_clientes');
        return view('eunomia.customers.formulario_clientes', compact('action','validator'));
    }

    /**
     * Añade los clientes a un select.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response json
     */
    public function addCustomers(Request $request){
        if ($request->ajax()){
            $customers = Customer::orderBy('codigo_cliente')->get();
            return response()->json($customers);
        }
    }

    public function muestraProyectosCliente($project_id){
        $projects = Project::where('customer_id',$project_id)
            ->where('estado_proyecto', '<>', 4)
            ->orderBy('fechaentrega_proyecto','DESC')->get();

        return view('eunomia.projects.listado_proyectos_cliente',compact('projects'));
    }
}
