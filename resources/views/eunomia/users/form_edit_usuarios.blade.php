@extends('adminlte::page')
@section('content')
    <div class="row ">
        <div class="col-12 ">

            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="bgUsuario card card-outline  ">
                    <div class="alinearHeader">
                        <h2 class="fuenteTitulo">Editar Usuarios: datos del perfil | {{ $user->name }}</h2>
                        <a href="{{ route('users.index') }}" class="btn-modificar">Volver</a>
                    </div>
                    <div class="card-body ">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade show active" id="p1" role="tabpanel"
                                aria-labelledby="custom-tabs-one-profile-tab">

                                <div class="row">
                                    <div class="labelFormBlanco">
                                        <label for="name">Nombre</label>
                                    </div>
                                    <div class="w-100">
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Nombre" autocomplete="name" value="{{ old('name', $user->name) }}">
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="labelFormBlanco">
                                        <label for="dni">DNI</label>
                                    </div>
                                    <div class="w-100">
                                        <input type="text" name="dni" id="dni" class="form-control"
                                            placeholder="DNI" autocomplete="dni" value="{{ old('dni', $user->dni) }}">
                                    </div>
                                </div>
                                <hr>


                                <div class="row">
                                    <div class="labelFormBlanco">
                                        <label for="email">E-Mail</label>
                                    </div>
                                    <div class="w-100">
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="tumail@mglab.es" autocomplete="email"
                                            value="{{ old('email', $user->email) }}">
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="labelFormBlanco">
                                        <label for="role_id">Departamento</label>
                                    </div>
                                    <div class="w-100">
                                        <select name="role_id" id="role_id" class="form-control"
                                            autocomplete="organization">
                                            <option value="">selecciona tu departamento</option>
                                            @foreach ($roles as $id => $role)
                                                <option value="{{ $id }}"
                                                    {{ old('role_id', $user->role_id) == $id ? 'selected' : '' }}>
                                                    {{ $role }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <!--Aqui tengo que ver donde se genera este codigo
                                    para cambiar el color y el tamaño de las etiquetas de los roles -->
                                @if (Auth::user()->compruebaSeguridad('editar-usuario'))
                                    <div class="row">
                                        <div class="labelFormBlanco">
                                            <label for="roles">Roles</label>
                                        </div>
                                        <div class="w-100">
                                            <select name="roles[]" id="roles" class="form-control select2" multiple
                                                data-placeholder="selecciona uno o varios roles" autocomplete="off">
                                                @foreach ($allrols as $id => $rol)
                                                    <option value="{{ $id }}"
                                                        {{ collect(old('roles', $rols))->contains($id) ? 'selected' : '' }}>
                                                        {{ $rol }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                @endif

                                <div class="row">
                                    <div class="labelFormBlanco">
                                        <label for="customer_id">Cliente</label>
                                    </div>
                                    <div class="w-100">
                                        <select name="customer_id" id="customer_id" class="form-control"
                                            autocomplete="organization">
                                            <option value="">selecciona un cliente</option>
                                            @foreach ($customers as $id => $customer)
                                                <option value="{{ $id }}"
                                                    {{ old('customer_id', $user->customer_id) == $id ? 'selected' : '' }}>
                                                    {{ $customer }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="avatar">Avatar</label>
                                    <div class="btnSeleccionarFoto">
                                        <input type="file" name="avatar" id="avatar" class="form-control-file ">
                                    </div>
                                    <small class="form-text text-muted">esta es tu cara? ohhhhh!!!</small>
                                    <img src="{{ $user->avatar_url }}" alt="avatar" class="img-thumbnail mt-2"
                                        style="max-width: 150px;">
                                </div>
                            </div>

                            <div class="tab-pane fade" id="p2" role="tabpanel"
                                aria-labelledby="custom-tabs-one-mail-tab">
                                <h5 class="mb-3">Datos servidor email</h5>
                                <div class="form-group">
                                    <label for="mail_host">Host</label>
                                    <input type="text" name="mail_host" id="mail_host" class="form-control"
                                        placeholder="Host" autocomplete="off"
                                        value="{{ old('mail_host', is_object($user->userdata) ? $user->userdata->mail_host : null) }}">
                                </div>

                                <div class="form-group">
                                    <label for="mail_port">Puerto</label>
                                    <input type="number" name="mail_port" id="mail_port" class="form-control"
                                        placeholder="Puerto" min="1" max="65535" step="1"
                                        autocomplete="off"
                                        value="{{ old('mail_port', is_object($user->userdata) ? $user->userdata->mail_port : null) }}">
                                </div>

                                <div class="form-group">
                                    <label for="mail_encryption">Encryption</label>
                                    <select name="mail_encryption" id="mail_encryption" class="form-control">
                                        <option value="">Host</option>
                                        <option value="1"
                                            {{ old('mail_encryption', is_object($user->userdata) ? $user->userdata->mail_encryption : 0) == 1 ? 'selected' : '' }}>
                                            Si</option>
                                        <option value="0"
                                            {{ old('mail_encryption', is_object($user->userdata) ? $user->userdata->mail_encryption : 0) == 0 ? 'selected' : '' }}>
                                            No</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="mail_username">Usuario</label>
                                    <input type="text" name="mail_username" id="mail_username" class="form-control"
                                        placeholder="Usuario" autocomplete="username"
                                        value="{{ old('mail_username', is_object($user->userdata) ? $user->userdata->mail_username : null) }}">
                                </div>

                                <div class="form-group">
                                    <label for="mail_password">Contraseña</label>
                                    <input type="password" name="mail_password" id="mail_password" class="form-control"
                                        autocomplete="new-password">
                                </div>

                                <div class="form-group">
                                    <label for="mail_message_limit">Límite de mensajes</label>
                                    <input type="number" name="mail_message_limit" id="mail_message_limit"
                                        class="form-control" placeholder="Límite de mensajes" min="0"
                                        step="1" autocomplete="off"
                                        value="{{ old('mail_message_limit', is_object($user->userdata) ? $user->userdata->mail_message_limit : null) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn-modificar">Guardar cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('css')
    <!-- Select2 -->

    <style>
        /* Mejorar visibilidad de las etiquetas seleccionadas en Select2 */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            border-color: #0056b3 !important;
            color: white !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white !important;
            margin-right: 5px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffdddd !important;
        }
    </style>
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
