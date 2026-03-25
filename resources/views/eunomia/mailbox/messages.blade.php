<?php
function getSubString($string, $length=NULL){
    //Si no se especifica la longitud por defecto es 50
    if ($length == NULL)
        $length = 50;
    //Primero eliminamos las etiquetas html y luego cortamos el string
    $stringDisplay = substr(strip_tags($string), 0, $length);
    //Si el texto es mayor que la longitud se agrega puntos suspensivos
    if (strlen(strip_tags($string)) > $length)
        $stringDisplay .= ' ...';
    return $stringDisplay;
}

// Función auxiliar para verificar si un mensaje está visto
function isMessageSeen($message) {
    return method_exists($message, 'isSeen') && $message->isSeen();
}

// Función auxiliar para verificar si un mensaje está marcado
function isMessageFlagged($message) {
    if (method_exists($message, 'getFlags')) {
        try {
            $flags = $message->getFlags();
            return isset($flags['flagged']) ? $flags['flagged'] : false;
        } catch (Exception $e) {
            return false;
        }
    }
    return method_exists($message, 'isFlagged') && $message->isFlagged();
}

// Función auxiliar para obtener atributos del header
function getMessageHeaderAttribute($message, $attribute) {
    if (method_exists($message, 'getHeaderAttribute')) {
        try {
            return $message->getHeaderAttribute($attribute);
        } catch (Exception $e) {
            return '';
        }
    }
    // Método alternativo si existe
    if (method_exists($message, 'getHeader')) {
        try {
            $headers = $message->getHeader();
            return isset($headers[$attribute]) ? $headers[$attribute] : '';
        } catch (Exception $e) {
            return '';
        }
    }
    return '';
}
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 id="folder_title" class="box-title">{!! $oFolder->name !!}</h3>

        <div class="box-tools pull-right">
            <div class="has-feedback">
                <input type="text" class="form-control input-sm" placeholder="Search Mail">
                <span class="glyphicon glyphicon-search form-control-feedback"></span>
            </div>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body no-padding">
        <div class="mailbox-controls">
            <!-- Check all button -->
            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
            </button>
            <div class="btn-group">
                <button id="delete_mails" type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                <button type="button" class="btn btn-default btn-sm msg-ant"><i class="fa fa-arrow-left"></i></button>
                <button type="button" class="btn btn-default btn-sm msg-pos"><i class="fa fa-arrow-right"></i></button>
            </div>
            <!-- /.btn-group -->
            <button type="button" class="btn btn-default btn-sm refresh_button"><i class="fa fa-refresh"></i></button>
        </div>
        <div id="messages" class="table-responsive mailbox-messages">
            <span>Mensajes {{($page-1)*$message_limit+1}} a {{$page*$message_limit}} de {{$total_mensajes}}</span>
            <table id="list" class="table table-hover table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th><i class="fa fa-star-o text-yellow"></i></th>
                    <th>De</th>
                    <th>Asunto</th>
                    <th><i class="fa fa-paperclip"></i></th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody>
                @foreach($oMessages as $oMessage)
                    <tr>
                        <td><input name="checkmsg" type="checkbox" value="{{$oMessage->getUid()}}"></td>
                        <td class="mailbox-star"><a href="#"><i id="flagged_{{$oMessage->getUid()}}_{{$oFolder->name}}" class="fa {{isMessageFlagged($oMessage)?'fa-star':'fa-star-o'}} text-yellow stars"></i></a></td>
                        <td class="mailbox-name"><a class="link_email" id="name_{{$oMessage->getUid()}}" href="#">{!! !isMessageSeen($oMessage)?'<strong>':'' !!}{{count($oMessage->getFrom())>0?($oMessage->getFrom()[0]->personal!=''?$oMessage->getFrom()[0]->personal:$oMessage->getFrom()[0]->mail):''}}{!! !isMessageSeen($oMessage)?'</strong>':'' !!}</a></td>
                        <td class="mailbox-subject">{{getSubString(count(imap_mime_header_decode($oMessage->getSubject()))>0?imap_mime_header_decode($oMessage->getSubject())[0]->text:'',100)}}</td>
                        <td class="mailbox-attachment">{!! getMessageHeaderAttribute($oMessage, 'Content-Type')=='multipart/mixed;'?'<i class="fa fa-paperclip"></i>':'' !!}</td>
                        <td class="mailbox-date">{{\Carbon\Carbon::parse($oMessage->getDate())->format('d/m/Y H:i')}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <!-- /.table -->
        </div>
        <!-- /.mail-box-messages -->
    </div>
    <!-- /.box-body -->
    <div class="box-footer no-padding">
        <div class="mailbox-controls">
            <!-- Check all button -->
            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                <button type="button" class="btn btn-default btn-sm msg-ant"><i class="fa fa-arrow-left"></i></button>
                <button type="button" class="btn btn-default btn-sm msg-pos"><i class="fa fa-arrow-right"></i></button>
            </div>
            <!-- /.btn-group -->
            <button type="button" class="btn btn-default btn-sm refresh_button"><i class="fa fa-refresh"></i></button>
            <input type="hidden" name="folder_actual" value="{{ $oFolder->name }}">
        </div>
    </div>
</div>
<script type="text/javascript" language="javascript" src="{{ asset('vendor/adminlte/plugins/datatables/extensions/RowReorder/js/dataTables.rowReorder.min.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>
<script>
    $(function () {
        $('#list').DataTable({
            paging: false,
            lengthChange: true,
            responsive: true,
            pageLength: 25,
            displayLength: 25,
            info: false,
            order: [[5, "desc"]],
            /*columnDefs: [ {
                targets: 5,
                render: $.fn.dataTable.render.moment( 'DD/MM/YYYY', 'DD/MM/YYYY' )
            } ],*/
        });
    });

    $(document).ready(function(){
        $('.msg-ant').on('click', function(){
            devuelveMensajesCarpeta('{{$oFolder->name}}','{{$page>1?$page-1:$page}}')
        });
        $('.msg-pos').on('click', function(){
            devuelveMensajesCarpeta('{{$oFolder->name}}','{{$page+1}}')
        });
    });
</script>
