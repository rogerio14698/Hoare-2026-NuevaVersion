<div class="col-md-9">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Compose New Message</h3>
        </div>
        <form action="{{ route('enviaEmail') }}" method="POST" enctype="multipart/form-data" id="formulario_envio_email">
            @csrf
        @csrf
        <!-- /.box-header -->
        <div class="box-body">
            <div class="form-group">
                <input type="email" class="form-control" name="to" placeholder="Para:" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="cc" placeholder="Cc:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="bcc" placeholder="Cco:">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="subject" placeholder="Asunto:" required>
            </div>
            <div class="form-group">
                    <textarea id="compose-textarea" name="message" class="form-control" style="height: 300px" required>
                    </textarea>
            </div>
            <i class="fa fa-paperclip"></i> Adjuntos
            <div class="form-group dropzone" id="fileupload">
                <!--<div class="btn btn-default btn-file">
                    <i class="fa fa-paperclip"></i> Adjuntos
                    <input type="file" class="form-control-file" name="attachments[]" id="file" multiple="">
                </div>-->
                <div class="fallback">
                    <input name="file" type="files" multiple accept="image/jpeg, image/png, image/jpg" />
                </div>
                <p class="help-block">Max. {{ini_get('upload_max_filesize')}}B</p>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <div class="pull-right">
                <button type="button" class="btn btn-default"><i class="fa fa-pencil"></i> Borrador</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Enviar</button>
            </div>
            <button id="discard" type="reset" class="btn btn-default"><i class="fa fa-times"></i> Cancelar</button>
        </div>
        </form>
        <!-- /.box-footer -->
    </div>
    <!-- /. box -->
</div>
<!-- /.col -->

<script type="text/javascript">
    Dropzone.options.fileupload = {
        accept: function (file, done) {
            if (file.type != "application/vnd.ms-excel" && file.type != "image/jpeg, image/png, image/jpg") {
                done("Error! Files of this type are not accepted");
            } else {
                done();
            }
        }
    }

    Dropzone.options.fileupload = {
        acceptedFiles: "image/jpeg, image/png, image/jpg",
        url: '{{route('subeAdjuntos')}}',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        sending: function(file, xhr, formData) {
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        }
    }

    if (typeof Dropzone != 'undefined') {
        Dropzone.autoDiscover = false;
    }

    ;
    (function ($, window, undefined) {
        "use strict";

        $(document).ready(function () {
            // Dropzone Example
            if (typeof Dropzone != 'undefined') {
                if ($("#fileupload").length) {
                    var dz = new Dropzone("#fileupload"),
                        dze_info = $("#dze_info"),
                        status = {
                            uploaded: 0,
                            errors: 0
                        };
                    var $f = $('<tr><td class="name"></td><td class="size"></td><td class="type"></td><td class="status"></td></tr>');
                    dz.on("success", function (file, responseText) {

                        var _$f = $f.clone();

                        _$f.addClass('success');

                        _$f.find('.name').html(file.name);
                        if (file.size < 1024) {
                            _$f.find('.size').html(parseInt(file.size) + ' KB');
                        } else {
                            _$f.find('.size').html(parseInt(file.size / 1024, 10) + ' KB');
                        }
                        _$f.find('.type').html(file.type);
                        _$f.find('.status').html('Uploaded <i class="entypo-check"></i>');

                        dze_info.find('tbody').append(_$f);

                        status.uploaded++;

                        dze_info.find('tfoot td').html('<span class="label label-success">' + status.uploaded + ' uploaded</span> <span class="label label-danger">' + status.errors + ' not uploaded</span>');

                        toastr.success('Your File Uploaded Successfully!!', 'Success Alert', {
                            timeOut: 50000000
                        });

                    })
                        .on('error', function (file) {
                            var _$f = $f.clone();

                            dze_info.removeClass('hidden');

                            _$f.addClass('danger');

                            _$f.find('.name').html(file.name);
                            _$f.find('.size').html(parseInt(file.size / 1024, 10) + ' KB');
                            _$f.find('.type').html(file.type);
                            _$f.find('.status').html('Uploaded <i class="entypo-cancel"></i>');

                            dze_info.find('tbody').append(_$f);

                            status.errors++;

                            dze_info.find('tfoot td').html('<span class="label label-success">' + status.uploaded + ' uploaded</span> <span class="label label-danger">' + status.errors + ' not uploaded</span>');

                            toastr.error('Your File Uploaded Not Successfully!!', 'Error Alert', {
                                timeOut: 5000
                            });
                        });
                }
            }
        });
    })(jQuery, window);

    $('input[name="mailto"]').map(function () {
        tagify = new Tagify(this,{
            'maxTags': 15
        });
    });

    $('input[name="mailcc"]').map(function () {
        tagify = new Tagify(this,{
            'maxTags': 15
        });
    });

    $('input[name="mailbcc"]').map(function () {
        tagify = new Tagify(this,{
            'maxTags': 15
        });
    });
</script>