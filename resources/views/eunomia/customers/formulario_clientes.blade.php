<!-- form start -->
<form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" id="formulario_clientes">
    @csrf

<div class="form-group row">
    <label for="codigo_cliente" class="col-sm-3 col-form-label">Código Cliente</label>
    <div class="col-sm-9">
        <input type="text" name="codigo_cliente" id="codigo_cliente" class="form-control" placeholder="XXX" value="{{ old('codigo_cliente') }}">
        <small class="form-text text-muted">Deben ser tres letras en mayúscula.</small>
    </div>
</div>

<div class="form-group row">
    <label for="nombre_cliente" class="col-sm-3 col-form-label">Nombre Cliente</label>
    <div class="col-sm-9">
        <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control" placeholder="Nombre del cliente" value="{{ old('nombre_cliente') }}">
    </div>
</div>

<div class="form-group row">
    <label for="email_cliente" class="col-sm-3 col-form-label">E-Mail</label>
    <div class="col-sm-9">
        <input type="email" name="email_cliente" id="email_cliente" class="form-control" placeholder="Email del cliente" value="{{ old('email_cliente') }}">
    </div>
</div>

<div class="form-group row">
    <label for="telefono_cliente" class="col-sm-3 col-form-label">Teléfono</label>
    <div class="col-sm-9">
        <input type="text" name="telefono_cliente" id="telefono_cliente" class="form-control" placeholder="Teléfono de contacto" value="{{ old('telefono_cliente') }}">
    </div>
</div>

<div class="form-group row">
    <label for="contacto_cliente" class="col-sm-3 col-form-label">Persona de contacto</label>
    <div class="col-sm-9">
        <input type="text" name="contacto_cliente" id="contacto_cliente" class="form-control" placeholder="Nombre de la persona de contacto" value="{{ old('contacto_cliente') }}">
    </div>
</div>

<input type="hidden" name="action" value="{{ $action }}">
<input type="hidden" name="role_id" value="{{ Auth::user()->role_id }}">

<div class="card-footer">
    <button type="submit" class="btn-modificar">Guardar</button>
    <a href="{{ route('customers.index') }}" class="btn-modificar"> Cancelar</a>
</div>

</form>
