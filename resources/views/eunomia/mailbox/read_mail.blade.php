<?php
// Función auxiliar para obtener lista de direcciones de email
function getMessageListAddress($message, $addressList) {
    if (method_exists($message, 'getListAddress')) {
        try {
            return $message->getListAddress($addressList);
        } catch (Exception $e) {
            // Fallback: formatear la lista manualmente
            if (is_array($addressList) && count($addressList) > 0) {
                $addresses = [];
                foreach ($addressList as $address) {
                    if (isset($address->full)) {
                        $addresses[] = $address->full;
                    } elseif (isset($address->mail)) {
                        $addresses[] = $address->mail;
                    }
                }
                return implode(', ', $addresses);
            }
            return '';
        }
    }
    
    // Fallback manual
    if (is_array($addressList) && count($addressList) > 0) {
        $addresses = [];
        foreach ($addressList as $address) {
            if (isset($address->full)) {
                $addresses[] = $address->full;
            } elseif (isset($address->mail)) {
                $addresses[] = $address->mail;
            }
        }
        return implode(', ', $addresses);
    }
    return '';
}

// Función auxiliar para obtener direcciones From seguras
function getMessageFromSafe($message) {
    try {
        $from = $message->getFrom();
        if (is_array($from) && count($from) > 0) {
            return isset($from[0]->full) ? $from[0]->full : (isset($from[0]->mail) ? $from[0]->mail : '');
        }
        return '';
    } catch (Exception $e) {
        return '';
    }
}

// Función auxiliar para obtener fecha segura
function getMessageDateSafe($message) {
    try {
        $date = $message->getDate();
        if ($date) {
            return $date->format('d M Y H:i');
        }
        return '';
    } catch (Exception $e) {
        return '';
    }
}
?>

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Leer Mensaje</h3>
                        </div>
                        @if (!$images)
                            <div class="box-header with-border">
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                                    Para proteger su privacidad, las imágenes externas de este mensaje han sido bloqueadas. <button id="mostrarimagenes_{{$oMessage->getUid()}}" class="btn btn-xs btn-github mostrar_imagenes">Mostrar imágenes</button>
                                </div>
                            </div>
                        @endif
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <div class="mailbox-read-info">
                                <h3>{!! $oMessage->getSubject() !!}</h3>
                                <h5><strong>De:</strong> {{getMessageFromSafe($oMessage)}}
                                    <span class="mailbox-read-time pull-right">{{getMessageDateSafe($oMessage)}}</span></h5>
                                <h5><strong>Para:</strong> {{getMessageListAddress($oMessage, $oMessage->getTo())}}</h5>
                            </div>
                            <!-- /.mailbox-read-info -->
                            <div class="mailbox-controls with-border text-center">
                                <button id="back" type="button" class="btn btn-default btn-sm back" data-toggle="tooltip" data-container="body" title="Back">
                                    <i class="fa fa-arrow-left"></i></button>
                                <div class="btn-group">
                                    <button id="refresh_{{$oMessage->getUid()}}" type="button" class="btn btn-default btn-sm refresh" data-toggle="tooltip" data-container="body" title="Refresh">
                                        <i class="fa fa-refresh"></i></button>
                                    <button id="delete_mails" type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Delete">
                                        <i class="fa fa-trash-o"></i></button>
                                    <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Reply">
                                        <i class="fa fa-reply"></i></button>
                                    <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Forward">
                                        <i class="fa fa-share"></i></button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" title="Print">
                                    <i class="fa fa-print"></i></button>
                            </div>
                            <!-- /.mailbox-controls -->
                            <div id="body_mail" class="mailbox-read-message">
                                {!! $body !!}
                            </div>
                            <!-- /.mailbox-read-message -->
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <ul class="mailbox-attachments clearfix">
                                @foreach($oAttachments as $key => $oAttachment)
                                    <?php
                                        if (is_numeric($key)){
                                            switch($oAttachment->content_type){
                                                case 'application/pdf':
                                                    $icono = 'fa-file-pdf-o';
                                                    break;
                                                case 'other/plain':
                                                    $icono = 'fa-file-text-o';
                                                    break;
                                                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                                                    $icono = 'fa-file-excel-o';
                                                    break;
                                                case 'other/html':
                                                    $icono = 'fa-file-code-o';
                                                    break;
                                                case 'application/msword':
                                                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                                                    $icono = 'fa-file-word-o';
                                                    break;
                                                case 'application/octet-stream':
                                                    $icono = 'fa-file-zip-o';
                                                    break;
                                                case 'image/jpeg':
                                                    $oAttachment->save($attachmentPath);
                                                default:
                                                    $icono = 'fa-file-o';
                                                    break;
                                            }
                                    ?>
                                <li>
                                    <span class="mailbox-attachment-icon{{strpos($oAttachment->content_type,'image/')!==false?' has-img':''}}">{!! strpos($oAttachment->content_type,'image/')===false?'<i class="fa '.$icono.'"></i>':'<img src="' . Storage::disk('mail_attachments')->url($oAttachment->name) . '" alt="' . $oAttachment->name . '" width="198">' !!}</span>

                                    <div class="mailbox-attachment-info">
                                        <a href="#" title="{{$oAttachment->getName()}}" class="mailbox-attachment-name" id="{{$oFolder->name}}::{{$oMessage->getUid()}}::{{$oAttachment->getName()}}"><i class="fa fa-paperclip"></i> {!! strlen($oAttachment->getName())>20?(substr($oAttachment->getName(),0,20) . '...'):$oAttachment->getName() !!}</a>
                                        <span class="mailbox-attachment-size">
                                            {{round($oAttachment->size/1024)}} KB
                                            <a href="#" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                        </span>
                                    </div>
                                </li>
                                    <?php } ?>
                                @endforeach
                                <!--<li>
                                    <span class="mailbox-attachment-icon"><i class="fa fa-file-word-o"></i></span>

                                    <div class="mailbox-attachment-info">
                                        <a href="#" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> App Description.docx</a>
                                        <span class="mailbox-attachment-size">
                                          1,245 KB
                                          <a href="#" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <span class="mailbox-attachment-icon has-img"><img src="../../dist/img/photo1.png" alt="Attachment"></span>

                                    <div class="mailbox-attachment-info">
                                        <a href="#" class="mailbox-attachment-name"><i class="fa fa-camera"></i> photo1.png</a>
                                        <span class="mailbox-attachment-size">
                                          2.67 MB
                                          <a href="#" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <span class="mailbox-attachment-icon has-img"><img src="../../dist/img/photo2.png" alt="Attachment"></span>

                                    <div class="mailbox-attachment-info">
                                        <a href="#" class="mailbox-attachment-name"><i class="fa fa-camera"></i> photo2.png</a>
                                        <span class="mailbox-attachment-size">
                                          1.9 MB
                                          <a href="#" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
                                        </span>
                                    </div>
                                </li>-->
                            </ul>
                        </div>
                        <!-- /.box-footer -->
                        <div class="box-footer">

                            <div class="pull-right">
                                <button type="button" class="btn btn-default"><i class="fa fa-reply"></i> Reply</button>
                                <button type="button" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
                            </div>
                            <button type="button" class="btn btn-default"><i class="fa fa-trash-o"></i> Delete</button>
                            <button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!-- /. box -->

                    <script>
                        $(document).ready(function(){
                            $('.back').on('click', function(){
                                devuelveMensajesCarpeta('{{$oFolder->name}}','{{$page}}');
                                devuelveCarpetas(); // Refrescar contadores al regresar
                            });

                            $('.refresh').on('click',function(){
                                leerMensaje(this,'{{$oFolder->name}}','{{$page}}');
                            });

                            $('.mostrar_imagenes').on('click',function(){
                                leerMensaje(this,'{{$oFolder->name}}','{{$page}}',1);
                            })
                        });
                    </script>