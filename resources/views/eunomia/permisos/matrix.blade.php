@extends('adminlte::page')

@section('content_header')
    <h1>
        User Matrix
        <small>Permissions that are on each user</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('eunomia.home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Permisos</li>
    </ol>
@stop

@section('content')

    <div class="visible-xs alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        This page might not be formatted properly for this screen due to the complexity of User Based Access Control permissioning.
    </div>

    <form action="{{ route('permisos.updatePermissionMatrix') }}" method="POST" class="form-horizontal">
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

                    <span class="pull-right"><span class="sr-only">Users</span>
                    <i class="fa fa-users" title="Roles"></i>
                    <i class="fa fa-arrow-right"></i>
                    </span>
                </th>
                @foreach ($users as $user)
                    <th> {{ $user->name }} <a href="{{ route( 'users.edit',$user->id) }}">
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
                            {{ $permission->modulo->nombre }}
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
                    @for ($i=0; $i < $users->count(); $i++ )
                        <td data-container="body" data-trigger="focus" data-toggle="popover" data-placement="left" data-content="Role: {{$users[$i]->name}}, Permission: {{$permission->slug}}">
                            <input type="checkbox" name="perm_user[]" value="{{ $users[$i]->id.':'.$permission->id }}" {{ in_array(($users[$i]->id.':'.$permission->id), $pivot) ? 'checked' : '' }}>
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
                <button type="submit" class="btn btn-primary form-control">Save User Permission Changes</button>
            </div>
        </div>
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </form>

@endsection