@extends('adminlte::page')

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
?>

@section('content_header')
    <!-- Content Wrapper. Contains page content -->
    <div class="box">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Mailbox
                <small>{{$oNewMessages->count()>0?$oNewMessages->count().' mensajes nuevos':''}}</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Mailbox</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-2" id="contenido_izda">
                </div>
                <!-- /.col -->
                <div class="col-md-10" id="contenido_dcha">
                </div>
                <!-- /.col -->
            </div>
            <div id="error"></div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <span id="respuesta"></span>
@endsection

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.css")}}">


    <link rel="stylesheet" href="{{asset('css/bootstrap3-wysihtml5.min.css')}}">

    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset("vendor/adminlte/plugins/iCheck/flat/blue.css")}}">

    <!-- Bootstrap Dialog -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />

    <!-- Tagify -->
    <link rel="stylesheet" href="{{asset('js/tagify-master/dist/tagify.css')}}">

    <!-- Mailbox -->
    <link rel="stylesheet" href="{{asset('css/mailbox.css')}}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/basic.css" rel="stylesheet" type="text/css" />
    <style>
        /*Lo mostramos 'hidden' por default*/
        .modal {
            display:    none;
            position:   fixed;
            z-index:    1000;
            top:        0;
            left:       0;
            height:     100%;
            width:      100%;
            background: rgba( 255, 255, 255, .8 );
            url('/images/ajax-loader.gif') 50% 50% no-repeat;
        }

        /* Cuando el body tiene la clase 'loading' ocultamos la barra de navegacion */
        body.loading {
            overflow: hidden;
        }

        /* Siempre que el body tenga la clase 'loading' mostramos el modal del loading */
        body.loading .modal {
            display: block;
        }
        .dropzone {
            border:2px dashed #999999;
            border-radius: 10px;
        }
        .dropzone .dz-default.dz-message {
            height: 171px;
            background-size: 132px 132px;
            margin-top: -101.5px;
            background-position-x:center;

        }
        .dropzone .dz-default.dz-message span {
            display: block;
            margin-top: 145px;
            font-size: 20px;
            text-align: center;
        }
    </style>
@endsection

@section('js')
    <!-- DataTables -->

    <script src="{{asset("vendor/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"> </script>
    <script src="{{asset("vendor/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"> </script>

    <!-- iCheck -->
    <script src="{{asset("vendor/adminlte/plugins/iCheck/icheck.min.js")}}"> </script>

    <!-- Tagify -->
    <script src="{{asset('js/tagify-master/dist/tagify.js')}}"></script>

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>

    <!-- Spin ajax -->
    <script src="{{asset('js/spin.js')}}"></script>
    <script language="JavaScript">
        var opts = {
            lines: 12, // The number of lines to draw
            length: 41, // The length of each line
            width: 16, // The line thickness
            radius: 47, // The radius of the inner circle
            scale: 0.45, // Scales overall size of the spinner
            corners: 1, // Corner roundness (0..1)
            color: '#0c8dbc', // CSS color or array of colors
            fadeColor: 'transparent', // CSS color or array of colors
            opacity: 0.15, // Opacity of the lines
            rotate: 27, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            speed: 0.9, // Rounds per second
            trail: 60, // Afterglow percentage
            fps: 20, // Frames per second when using setTimeout() as a fallback in IE 9
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            className: 'spinner', // The CSS class to assign to the spinner
            top: '200px', // Top position relative to parent
            left: '50%', // Left position relative to parent
            position: 'absolute' // Element positioning
        };
        var target = document.getElementById('contenido_dcha');
        var spinner = new Spinner(opts).spin(target);
        target = document.getElementById('contenido_izda');
        spinner = new Spinner(opts).spin(target);

    </script>

    <!--Bootstrap WYSIHTML5 -->
    <script src="{{asset('js/bootstrap3-wysihtml5.all.min.js')}}"></script>

    <!-- Bootstrap Dialog -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

    <script>

        var carpeta = 'INBOX';
        function devuelveMensajesCarpeta(folder,page=1) {
            carpeta = folder;
            var target = document.getElementById('contenido_dcha');
            var spinner = new Spinner(opts).spin(target);
            $.ajax({
                url: '{{route('devuelveMensajesCarpeta')}}',
                type: 'POST',
                data: {
                    folder: folder,
                    page: page,
                    _token: $("input[name='_token']").val()
                },
                error: function (jqXHR, textStatus) {
                    $('#error').html(jqXHR.responseText);
                },
                success: function (data) {
                    $('#contenido_dcha').html(data);
                    $('#folder_title').html(folder);
                    $('.stars').on('click', function(){
                        accion = 1;
                        if ($(this).hasClass('fa-star')){
                            accion = 0;
                        }
                        changeFlag($(this).attr('id'), 'flagged',$(this).attr('id').split('_')[1], $(this).attr('id').split('_')[2] , accion);
                    });
                    $('.link_email').click(function(){
                        leerMensaje(this, folder, page);
                    });
                    $('.refresh_button').click(function(){
                        devuelveCarpetas();
                        devuelveMensajesCarpeta(folder);
                    });

                    $('#delete_mails').click(function(){
                        eliminaMensajes();
                    });

                    if ( $.fn.dataTable.isDataTable( '#list' ) ) {
                        //$('#list').DataTable();
                    } else {
                        /*$('#list').DataTable({
                            "paging": true,
                            "lengthChange": true,
                            "responsive": true,
                            "pageLength": 25,
                            "displayLength": 25,
                            "order": [[5, "desc"]]
                        });*/
                    }

                    //Enable iCheck plugin for checkboxes
                    //iCheck for checkbox and radio inputs
                    $('.mailbox-messages input[type="checkbox"]').iCheck({
                        checkboxClass: 'icheckbox_flat-blue',
                        radioClass: 'iradio_flat-blue'
                    });

                    //Enable check and uncheck all functionality
                    $(".checkbox-toggle").click(function () {
                        var clicks = $(this).data('clicks');
                        if (clicks) {
                            //Uncheck all checkboxes
                            $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
                            $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
                        } else {
                            //Check all checkboxes
                            $(".mailbox-messages input[type='checkbox']").iCheck("check");
                            $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
                        }
                        $(this).data("clicks", !clicks);
                    });

                    //Handle starring for glyphicon and font awesome
                    $(".mailbox-star").click(function (e) {
                        e.preventDefault();
                        //detect type
                        var $this = $(this).find("a > i");
                        var glyph = $this.hasClass("glyphicon");
                        var fa = $this.hasClass("fa");
                        var msglist = $(this).id.split('_')[1];

                        //Switch states
                        if (glyph) {
                            $this.toggleClass("glyphicon-star");
                            $this.toggleClass("glyphicon-star-empty");
                            setFlag(msglist,'\\Flagged');
                        }

                        if (fa) {
                            $this.toggleClass("fa-star");
                            $this.toggleClass("fa-star-o");
                        }
                    });
                }
            });
        }

        function devuelveCarpetas() {
            var target = document.getElementById('contenido_izda');
            var spinner = new Spinner(opts).spin(target);
            $.ajax({
                url: '{{route('devuelveCarpetas')}}',
                type: 'POST',
                data: {_token: $("input[name='_token']").val()},
                error: function (jqXHR, textStatus) {
                    $('#error').html(jqXHR.responseText);
                },
                success: function (data) {
                    $('#contenido_izda').html(data);
                    $('.folder').click(function(){
                        var carpeta = this.id;
                        if (carpeta!='INBOX'){
                            carpeta = 'INBOX.' + carpeta;
                        }
                        console.log(carpeta);
                        devuelveMensajesCarpeta(carpeta);
                    });
                    $('#compose_mail').click(function(){
                        cargaNuevoMensaje();
                    });
                }
            });
        }

        function cargaNuevoMensaje(){
            var target = document.getElementById('contenido_dcha');
            var spinner = new Spinner(opts).spin(target);
            $.ajax({
                url: '{{route('cargaNuevoMensaje')}}',
                type: 'POST',
                data: {_token: $("input[name='_token']").val()},
                error: function (jqXHR, textStatus) {
                    $('#error').html(jqXHR.responseText);
                },
                success: function (data) {
                    $('#contenido_dcha').html(data);
                    //Add text editor
                    $("#compose-textarea").wysihtml5();
                    $('#discard').click(function(){
                        devuelveMensajesCarpeta('INBOX');
                    });
                }
            });
        }

        function leerMensaje(ele, folder, page, mostrarimagenes=0){
            var target = document.getElementById('contenido_dcha');
            var spinner = new Spinner(opts).spin(target);
            $.ajax({
                url: '{{route('leerMensaje')}}',
                type: 'POST',
                data: {
                    uid: ele.id.split('_')[1],
                    folder: folder,
                    page: page,
                    mostrarimagenes: mostrarimagenes,
                    _token: $("input[name='_token']").val()},
                error: function (jqXHR, textStatus) {
                    $('#error').html(jqXHR.responseText);
                },
                success: function (data) {
                    $('#contenido_dcha').html(data);
                    $('.mailbox-attachment-name').click(function(){
                        descargarArchivo($(this));
                    });
                }
            });
        }

        function descargarArchivo(ele){
            var folder = ele.attr('id').split('::')[0];
            var uid = ele.attr('id').split('::')[1];
            var filename = ele.attr('id').split('::')[2];
            $.ajax({
                url: '{{route('descargaArchivo')}}',
                type: 'POST',
                data: {
                    folder: folder,
                    uid: uid,
                    filename: filename,
                    _token: $("input[name='_token']").val()
                },
                error: function (jqXHR, textStatus) {
                    $('#error').html(jqXHR.responseText);
                },
                success: function (data, status, xhr) {
                    if (data != null && data != "FAIL") {
                        var b64Data = data;
                        var contentType = xhr.getResponseHeader("Content-Type"); //Obtenemos el tipo de los datos
                        var filename = xhr.getResponseHeader("Content-disposition");//Obtenemos el nombre del fichero a desgargar
                        filename = filename.substring(filename.lastIndexOf("=") + 1) || "download";

                        var sliceSize = 512;


                        var byteCharacters = window.atob(b64Data);
                        var byteArrays = [];

                        for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                            var slice = byteCharacters.slice(offset, offset + sliceSize);

                            var byteNumbers = new Array(slice.length);
                            for (var i = 0; i < slice.length; i++) {
                                byteNumbers[i] = slice.charCodeAt(i);
                            }

                            var byteArray = new Uint8Array(byteNumbers);

                            byteArrays.push(byteArray);
                        }
                        //Tras el procesado anterior creamos un objeto blob
                        var blob = new Blob(byteArrays, {
                            type : contentType
                        });

                        // IE 10+
                        if (navigator.msSaveBlob) {
                            navigator.msSaveBlob(blob, filename);
                        } else {
                            //Descargamos el fichero obtenido en la petición ajax
                            var url = URL.createObjectURL(blob);
                            var link = document.createElement('a');
                            link.href = url;
                            link.download = filename;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }

                    }
                    //console.log(data);
                    //$('#respuesta').html(data);
                    /*var reader = new FileReader();
                    reader.onload = function (event) {
                        var save = document.createElement('a');
                        save.href = event.target.result;
                        save.target = '_blank';
                        save.download = filename || 'archivo.dat';
                        var clicEvent = new MouseEvent('click', {
                            'view': window,
                            'bubbles': true,
                            'cancelable': true
                        });
                        save.dispatchEvent(clicEvent);
                        (window.URL || window.webkitURL).revokeObjectURL(save.href);
                    };
                    reader.readAsDataURL(new Blob([data.fichero],{type:data.content_type}));*/
                }
            });
        }

        function eliminaMensajes(){
            var seleccionado = false;
            $("input:checkbox:checked").each(function() {
                seleccionado = true;
            });
            if (!seleccionado){
                var dialog = BootstrapDialog.show({
                    title: 'Información Horae',
                    message: 'Debes seleccionar al menos un mensaje',
                    buttons: [{
                        label: 'Aceptar',
                        action: function(dialogItself){
                            dialogItself.close();
                        }
                    }]
                });
            } else {
                BootstrapDialog.confirm(
                    '¿Está seguro que desea eliminar el registro?', function(result) {

                        if (result) {
                            var listaMsgs = '';
                            $("input[name=checkmsg]").each(function (index) {
                                if($(this).is(':checked')){
                                    if (listaMsgs != ''){
                                        listaMsgs += ',';
                                    }
                                    listaMsgs += $(this).val();
                                }
                            });
                            $.ajax({
                                url: '{{route('eliminaMensajes')}}',
                                type: 'POST',
                                data: {uids: listaMsgs,_token: $("input[name='_token']").val()},
                                error: function (jqXHR, textStatus) {
                                    $('#error').html(jqXHR.responseText);
                                },
                                success: function (data) {
                                    $('#respuesta').html(data);
                                    devuelveCarpetas();
                                    devuelveMensajesCarpeta('INBOX');
                                }
                            });
                        }

                    });
            }
        }

        function changeFlag(id, flag, messageId, folder, set=1){
            $.ajax({
                url: '{{route('changeFlag')}}',
                type: 'POST',
                data: {
                    flag: flag,
                    messageId: messageId,
                    folder: folder,
                    set: set,
                    _token: $("input[name='_token']").val()
                },
                error: function (jqXHR, textStatus) {
                    $('#error').html(jqXHR.responseText);
                },
                success: function (data) {
                    if (set) {
                        $('#' + id).removeClass('fa-star-o');
                        $('#' + id).addClass('fa-star');
                    } else {
                        $('#' + id).removeClass('fa-star');
                        $('#' + id).addClass('fa-star-o');
                    }
                }
            });
        }

        $('document').ready(function(){
            devuelveCarpetas();
            //setInterval('devuelveCarpetas()',60000);
            devuelveMensajesCarpeta(carpeta);
            //setInterval('devuelveMensajesCarpeta(carpeta)',60000);
            $('#refresh_button').click(function(){
                target = document.getElementById('contenido_dcha');
                spinner = new Spinner(opts).spin(target);
                devuelveMensajesCarpeta($('#folder_title').html());
            });
        });
    </script>

@endsection