@extends('adminlte::page')


@section('content_header')
  <div class="alinearHeader">
    <h1 class="fuenteTitulo">Listado de clientes</h1>
    <div class="separacionBotones">
      @if( \Auth::user()->compruebaSeguridad('crear-cliente') == true)
        <a href="{{ route('customers.create') }}" class="btn-guardar"> Añadir</a>
      @endif
      <a href="{{ url('eunomia/customers') }}{{ request()->has('trashed') ? '' : '?trashed=1' }}" 
         class="btn-eliminar">
        {{ request()->has('trashed') ? 'Ver activos' : 'Ver eliminados' }}
      </a>
    </div>
  </div>
@stop

@section('content')
  <div class="row">
    <div class="col-12 ">

      <div class="card card-primary card-outline ">
        <div class="card-body bg-grisOscuro">
          <div class="table-responsive">
            <table id="list" class="table table-bordered table-striped w-100">

            <thead>
            <tr class="fuente-negro">
              <th>Codigo</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Contacto</th>
              <th>Proyectos</th>
              <th>Acciones</th>
            </tr>
            </thead>

            <tfoot>
            <tr class="fuente-negro">
              <th>Codigo</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Contacto</th>
              <th>Proyectos</th>
              <th>Acciones</th>
            </tr>
            </tfoot>
            <tbody>

@foreach ($customers as $customer)

  <tr>
    <td class="fuente-blancoBold">{{$customer->codigo_cliente}}</td>
    <td class="fuenteNombresGrisClaro">{{$customer->nombre_cliente}}</td>
    <td class="fuenteNombresGris">{{$customer->email_cliente}}</td>
    <td class="fuente-negro">{{$customer->telefono_cliente}}</td>
    <td class="fuenteNombresBlanco">{{$customer->contacto_cliente}}</td>
  <td class="text-center">{!! $customer->projects()>0?'<img class="proyectos" id="customer_' . $customer->id . '" alt="' . $customer->projects() . ' proyecto(s)" title="' . $customer->projects() . ' proyectos(s)" src="' . asset('/images/projects.png') . '" width="20">':'' !!}</td>
    <td class="text-nowrap">
      @if(request()->has('trashed'))
        @if( \Auth::user()->compruebaSeguridad('editar-cliente') == true)
          <form action="{{ route('customers.restore', $customer->id) }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-undo"></i> Restaurar</button>
          </form>
        @endif
      @else
        @if( \Auth::user()->compruebaSeguridad('editar-cliente') == true)
          <a href="{{ route('customers.edit', $customer) }}" class="btn-accion"> Editar</a>
        @endif
        @if( \Auth::user()->compruebaSeguridad('eliminar-cliente') == true)
          <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline" class="form_eliminar">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-eliminar"> Eliminar</button>
          </form>
        @endif
      @endif
    </td>
  </tr>

@endforeach
        </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de confirmación de eliminación -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white" id="deleteModalLabel">
            <i class="fas fa-exclamation-triangle"></i> Confirmar eliminación
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="mb-0">¿Está seguro que desea eliminar este cliente?</p>
          <small class="text-muted">Esta acción se puede revertir desde "Ver eliminados".</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
          </button>
          <button type="button" class="btn btn-danger" id="confirmDelete">
            <i class="fas fa-trash"></i> Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('css')
    <style>
      /* Espaciado entre botones en celdas de acciones */
      td .btn + .btn,
      td .btn + form,
      td form + .btn,
      td form + form {
        margin-left: 5px;
      }
    </style>
  <!-- DataTables -->



@stop

@section('js')

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(function () {
      $('#list').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "stateSave": true,
        "responsive": true,
      });
    });
  </script>

  <script language="JavaScript">
      var deleteForm = null;
      
      // Usar un selector más específico para evitar conflicto con el botón del modal
      $('table .btn-danger').click(function(e){
          e.preventDefault();
          deleteForm = $(this).parent();
          $('#deleteModal').modal('show');
      });

      $('#confirmDelete').click(function(){
          if(deleteForm) {
              $('#deleteModal').modal('hide');
              deleteForm.submit();
          }
      });
  </script>

  <script language="JavaScript">
    @foreach($customers as $customer)
    @if($customer->projects() > 0)
    $('#customer_{{$customer->id}}').click(function(){
      var modal = $('<div class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-primary"><h5 class="modal-title">Proyectos cliente "{{$customer->nombre_cliente}}"</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button></div></div></div></div>');
      modal.find('.modal-body').load('/eunomia/customers/muestraProyectosCliente/{{$customer->id}}');
      modal.modal('show');
      modal.on('hidden.bs.modal', function () {
        modal.remove();
      });
    });
    @endif
    @endforeach
  </script>

@stop
