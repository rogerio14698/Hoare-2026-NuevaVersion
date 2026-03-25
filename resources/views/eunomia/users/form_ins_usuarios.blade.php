@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Datos del nuevo usuario</h1>
        <a href="{{ route('users.index') }}" class="btn-modificar"> Volver</a>
    </div>
@stop

@section('content')

            <!-- general form elements -->
            <div class="card card-primary card-outline ">
                <!-- /.card-header -->
                <!-- form start -->
                <form class="bgUsuario" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">

                        <div class="row labelFormBlanco">
                            <label for="name" class="col-sm-3 col-form-label ">Nombre</label>

                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" placeholder="Nombre completo"
                                    id="name" value="{{ old('name') }}">

                            </div>


                        </div>
                        <hr>
                        <div class="row labelFormBlanco">
                            <label for="dni" class="col-sm-3 col-form-label">DNI</label>
                            <div class="col-sm-9">
                                <input type="text" name="dni" class="form-control" placeholder="DNI" id="dni"
                                    value="{{ old('dni') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="row labelFormBlanco">
                            <label for="email" class="col-sm-3 col-form-label">E-Mail</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control" placeholder="tumail@mglab.es"
                                    id="email" value="{{ old('email') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="row labelFormBlanco">
                            <label for="password" class="col-sm-3 col-form-label">Password</label>
                            <div class="col-sm-9">
                                <input type="password" placeholder="Contraseña" name="password" class="form-control" id="password">
                            </div>
                        </div>
                        <hr>
                        <div class="row labelFormBlanco">
                            <label for="role_id" class="col-sm-3 col-form-label">Departamento</label>
                            <div class="col-sm-9">
                                <select name="role_id" class="form-control" id="role_id">
                                    <option value="">Selecciona tu departamento</option>
                                    @foreach ($roles as $id => $role)
                                        <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                            {{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row labelFormBlanco">
                            <label for="roles" class="col-sm-3 col-form-label">Roles</label>
                            <div class="col-sm-9">
                                <select name="roles[]" class="form-control select2" id="roles" multiple
                                    data-placeholder="Selecciona uno o varios roles">
                                    @foreach ($rols as $id => $rol)
                                        <option value="{{ $id }}"
                                            {{ collect(old('roles'))->contains($id) ? 'selected' : '' }}>
                                            {{ $rol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row labelFormBlanco">
                            <label for="customer_id" class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9">
                                <select name="customer_id" class="form-control" id="customer_id">
                                    <option value="">Selecciona un cliente</option>
                                    @foreach ($customers as $id => $customer)
                                        <option value="{{ $id }}"
                                            {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $customer }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row labelFormBlanco">
                            <label for="avatar" class="col-sm-3 col-form-label">Avatar</label>
                            <div class="col-sm-9">
                                <input type="file" name="avatar" class="form-control" id="avatar">
                                <small class="form-text text-muted">Queremos ponerte cara!</small>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn-guardar"> Guardar</button>
                        <a href="{{ route('users.index') }}" class="btn-cancelar">
                            Cancelar</a>
                    </div>

                </form>

            </div>
            <!-- /.card -->



@endsection

@section('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/select2.min.css') }}">

@stop

@section('js')
    <!-- Select2 -->
    <script src="{{ asset('vendor/adminlte/plugins/select2/select2.full.min.js') }}"></script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
    </script>
@stop
