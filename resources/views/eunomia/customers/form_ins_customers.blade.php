@extends('adminlte::page')

@section('content_header')
  <div class="alinearHeader">
    <h1>Nuevo Cliente</h1>
    <a href="{{ route('customers.index') }}" class="btn-modificar"> Volver</a>
  </div>
@stop

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">Datos del cliente</h3>
        </div>
        <div class="card-body">
          @include('eunomia.customers.formulario_clientes')
        </div>
      </div>
    </div>
  </div>

@endsection

@section('css')

@stop

@section('js')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
{!! $validator !!}

<script>
$(function() {
    // Solo interceptar el submit si es para AJAX (action=1)
    $('#formulario_clientes').submit(function(e){
        // Si action es 1, es desde modal y usa AJAX
        if ($('#formulario_clientes input[name="action"]').val() == '1') {
            e.preventDefault();
            
            if ($('#codigo_cliente').val() != '' && $('#nombre_cliente').val() != '' && $('#email_cliente').val() != '') {
                $.ajax({
                    url: '{{route('insertaClienteDesdeTarea')}}',
                    type: 'POST',
                    data: $('#formulario_clientes').serialize(),
                    error: function (jqXHR, textStatus) {
                        console.log(jqXHR.responseText);
                    },
                    success: function (data) {
                        console.log(data);
                        if (data > 0) {
                            $.get('/eunomia/add_customers', function (res, sta) {
                                console.log(res);
                                $('#customer_id').empty();
                                res.forEach(element => {
                                    $('#customer_id').append(`<option value=${element.id}>${element.codigo_cliente}_${element.nombre_cliente}</option>`);
                                });
                                $('#customer_id').val(data);
                            });
                            if (typeof dialog !== 'undefined') {
                                dialog.close();
                            }
                        }
                    }
                });
            }
            return false;
        }
        // Si no es modal (action != 1), dejar que el formulario se envíe normalmente
    });
});
</script>

@stop
