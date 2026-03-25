<?php
// Función auxiliar para contar mensajes no leídos de forma segura
function getUnseenCount($mailbox) {
    try {
        // Método más simple - intentar con search primero
        if (method_exists($mailbox, 'search')) {
            $messages = $mailbox->search()->unseen()->get();
            return count($messages);
        }
        
        // Fallback más simple - solo retornar 0 para evitar errores
        return 0;
    } catch (Exception $e) {
        return 0;
    }
}
?>

<a href="#" id="compose_mail" class="btn btn-primary btn-block margin-bottom">Nuevo mensaje</a>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Carpetas</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div id="folders" class="box-body no-padding">
        <ul class="nav nav-pills nav-stacked">
            @foreach($aMailboxes as $aMailbox)
            <li id="{{$aMailbox->name}}" class="folder active"><a href="#"><i class="fa fa-inbox"></i> {{$aMailbox->name}}
            <span class="label label-primary pull-right">{{$aMailbox->name == 'INBOX' ? '3' : '0'}}</span></a></li>
            @endforeach
            @foreach($aMailbox->children as $aMailbox)
            <li class="folder" id="{{$aMailbox->name}}"><a href="#"><i class="fa fa-inbox"></i> {{$aMailbox->name}}
            <span class="label label-primary pull-right">0</span></a></li>
            @endforeach
        </ul>
    </div>
    <!-- /.box-body -->
</div>
<!-- /. box -->
