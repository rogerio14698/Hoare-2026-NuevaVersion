@extends('adminlte::page')


@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Editar Cliente</h1>
    <a href="{{ route('customers.index') }}" class="btn-modificar">Volver</a>
  </div>
@stop

@section('content')


  <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
  <div class="row bgColorCliente">
    <div class="col-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title mb-0">Datos del cliente</h3>
        </div>
        <div class="card-body">
          <div class="form-group row">
            <label for="codigo_cliente" class="col-sm-3 col-form-label">Código Cliente</label>
            <div class="col-sm-9">
              <input type="text" name="codigo_cliente" id="codigo_cliente" class="form-control" placeholder="XXX" value="{{ old('codigo_cliente', $customer->codigo_cliente) }}">
              <small class="form-text text-muted">Ojo: deben ser tres letras en mayúscula.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="nombre_cliente" class="col-sm-3 col-form-label">Nombre Cliente</label>
            <div class="col-sm-9">
              <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control" placeholder="Nombre cliente" value="{{ old('nombre_cliente', $customer->nombre_cliente) }}">
            </div>
          </div>
          <div class="form-group row">
            <label for="email_cliente" class="col-sm-3 col-form-label">E-Mail</label>
            <div class="col-sm-9">
              <input type="email" name="email_cliente" id="email_cliente" class="form-control" placeholder="Email del cliente" value="{{ old('email_cliente', $customer->email_cliente) }}">
            </div>
          </div>
          <div class="form-group row">
            <label for="telefono_cliente" class="col-sm-3 col-form-label">Teléfono</label>
            <div class="col-sm-9">
              <input type="text" name="telefono_cliente" id="telefono_cliente" class="form-control" value="{{ old('telefono_cliente', $customer->telefono_cliente) }}">
            </div>
          </div>
          <div class="form-group row">
            <label for="contacto_cliente" class="col-sm-3 col-form-label">Persona de contacto</label>
            <div class="col-sm-9">
              <input type="text" name="contacto_cliente" id="contacto_cliente" class="form-control" placeholder="Persona de contacto" value="{{ old('contacto_cliente', $customer->contacto_cliente) }}">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
          <button type="submit" class="btn-modificar">Guardar cambios
          </button>
          <a href="{{ route('customers.index') }}" class="btn-modificar">Cancelar</a>
        </div>
      </div>
    </div>
  </div>
  </form>

@endsection

@section('css')

@stop

@section('js')

@stop
