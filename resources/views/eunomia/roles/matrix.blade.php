@extends('adminlte::page')

@section('content_header')
    <div class="alinearHeader">
        <h1 class="fuenteTitulo">Matriz de Roles</h1>
    </div>
@stop

@section('content')

    <div class="visible-xs alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        Esta página puede no verse correctamente en pantallas pequeñas debido a la complejidad de la gestión de permisos basada en roles.
    </div>

    <form action="{{ route('roles.updateRoleMatrix') }}" method="POST" class="form-horizontal">
        @csrf
    <div class="table" style="overflow:auto; border: 1px dashed;">
        <table class="table table-bordered table-striped table-hover" style=" margin-bottom:0">
            <thead>
            <tr class="alert-dismissable">
                <th class="text-center">
                    <span class="pull-left"><span class="sr-only">Permissions</span>
                      <i class="fa fa-arrow-down"></i>
                      <i class="fa fa-key fa-lg"></i>
                    </span>

                    <span class="pull-right"><span class="sr-only">Roles</span>
                    <i class="fa fa-users" title="Roles"></i>
                    <i class="fa fa-arrow-right"></i>
                    </span>
                </th>
                @foreach ($roles as $rol)
                    <th> {{ $rol->name }} <a href="{{ route( 'roles.edit',$rol->id) }}">
                            <button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-link"></span></button></a>
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            <?php $modulo = 0; ?>
            @foreach($permissions as $permission)
                @if($modulo != $permission->model)
                    <tr>
                        <th class="alert-info">
                            {{ $permission->modulo ? $permission->modulo->nombre : '' }}
                        </th>
                    </tr>
                    <?php $modulo = $permission->model; ?>
                @endif
                <tr>
                    <th class="alert-dismissable">
                        <a href="{{ route('permisos.edit',$permission->id) }}">
                            <button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-link"></span></button></a>
                        {{ $permission->name }}
                    </th>
                    @for ($i=0; $i < $roles->count(); $i++ )
                        <td data-container="body" data-trigger="focus" data-toggle="popover" data-placement="left" data-content="Role: {{$roles[$i]->name}}, Permission: {{$permission->slug}}">
                            <input type="checkbox" name="perm_role[]" value="{{ $roles[$i]->id.':'.$permission->id }}" {{ in_array(($roles[$i]->id.':'.$permission->id), $pivot) ? 'checked' : '' }}>
                        </td>
                    @endfor
                </tr>
            @endforeach

            <!-- table footer -->
            <tfoot>
            </tfoot>
            </tbody>
        </table>
    </div>

        <div class="form-group">
            <div class="col-sm-3">
                <button type="submit" class="btn-modificar">Guardar cambios</button>
            </div>
        </div>
    </form>

@endsection