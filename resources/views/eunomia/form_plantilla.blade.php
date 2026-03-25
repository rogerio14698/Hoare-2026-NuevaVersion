@extends('adminlte::page')

@section('content_header')
  <h1>
    Formulario
    <small>Usuarios</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ route('eunomia.home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Usuarios</li>
  </ol>
@stop

@section('content')
  <div class="row">
    <div class="col-xs-12">
          <!-- general form elements -->
          <div class="box box-default">

            <!-- /.box-header -->
            <!-- form start -->
            <form action="{{ url('foo/bar') }}" method="POST">
              @csrf



              <div class="box-body">


                <div class="form-group">
                  <label for="email">Email address</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                </div>


                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>

                <div class="form-group">
                  <label>Textarea</label>
                  <textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>
                </div>


                <div class="form-group">
                  <label for="upload_file">File input</label>
                  <input type="file" id="upload_file" name="upload_file">

                  <p class="help-block">Example block-level help text here.</p>
                </div>


                <div class="checkbox">
                  <label>
                    <input type="checkbox"> Check me out
                  </label>
                </div>


              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-default">Submit</button>
              </div>



            </form>

          </div>
          <!-- /.box -->
        </div>
      </div>


@endsection

@section('css')

@stop

@section('js')

@stop
