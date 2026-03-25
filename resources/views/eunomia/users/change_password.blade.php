@extends('adminlte::page')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="mb-0">Cambiar contraseña</h1>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- general form elements -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Cambiar contraseña</h3>
                </div>
                <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (Session::has('message'))
                    <div class="alert alert-danger">
                        {{Session::get('message')}}
                    </div>
                @endif

                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="{{url('eunomia/users/updatepassword')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="mypassword">Introduce tu contraseña actual:</label>
                        <input type="password" id="mypassword" name="mypassword" class="form-control" value="{{ old('mypassword') }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Introduce tu nueva contraseña:</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirma tu nueva contraseña:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Cambiar mi contraseña</button>
                    </div>
                </form>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>

@endsection



@section('js')

@stop
