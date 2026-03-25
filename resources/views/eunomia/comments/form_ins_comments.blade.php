<form id="form_ins_comment">
    @csrf
    @method('POST')
    <div class="form-group">
        <label for="comentario">Nuevo Comentario</label>
    <textarea name="comentario" id="comentario" class="form-control" rows="3"></textarea>
    </div>
    <input type="hidden" name="userc_id" id="userc_id" value="{{ Auth::user()->id }}">
    <input type="hidden" name="projectc_id" id="projectc_id" value="{{ isset($project) && is_object($project) ? $project->id : (isset($task) && is_object($task) && isset($task->project) ? $task->project->id : '') }}">
    <input type="hidden" name="taskc_id" id="taskc_id" value="{{ isset($task) && is_object($task) ? $task->id : '' }}">
    <input type="hidden" name="comment_id" id="comment_id">
    <div class="mb-3">
        <button type="button" id="boton_comentarios" class="btn-nuevo-comentario">Insertar Comentario</button>
    </div>
    <!--Aqui la idea es que cada vez que inserto un comentario se envie un mail a cada responsable del proyecto
        Entonces vamos hacer: validar y guardar el comentario -> esto funciona
        Exlcuir el autor de la lista para no enviar el email a si mismo -> no funciona aun;
        Crear un mailable con una vista simple que reciba el comentario, el proyecto y el usuario que lo ha creado -> aun no funciona
        Enviar el email a cada responsable del proyecto -> aun no funciona
    -->
</form>

<!-- Modal de confirmación Bootstrap 4 para eliminar comentario -->
<div class="modal fade" id="modalConfirmDeleteComment" tabindex="-1" role="dialog" aria-labelledby="modalConfirmDeleteCommentLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalConfirmDeleteCommentLabel">Confirmar eliminación</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">¿Está seguro que desea eliminar el comentario?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnConfirmDeleteComment">Eliminar</button>
      </div>
    </div>
  </div>
</div>

@push('js')
<script>
    $(function(){

        // === INSERTAR / EDITAR COMENTARIO (delegado) ===
        $(document).off('click.commentInsert', '#boton_comentarios');
        $(document).on('click.commentInsert', '#boton_comentarios', function(e){
            e.preventDefault();
            var $form = $('#form_ins_comment');
            var $btn = $(this);
            var url = "{{ route('insert_Comment') }}";

            $btn.prop('disabled', true);

            var comentario = (window.tinyMCE && tinyMCE.get('comentario')) ? tinyMCE.get('comentario').getContent() : $('#comentario').val();

            var data = $form.serializeArray();
            var found = false;
            $.each(data, function(i, v){ if (v.name === 'comentario') { v.value = comentario; found = true; } });
            if (!found) data.push({ name: 'comentario', value: comentario });

            $.ajax({
                url: url,
                method: 'POST',
                data: $.param(data),
                success: function(response){
                    console.log('[Comentarios] Inserción OK. Respuesta del servidor recibida (' + (typeof response === 'string' ? response.length + ' chars' : 'object') + ')');
                    // Actualizar la lista de comentarios
                    $('#comentarios').html(response);
                    // Limpiar editor
                    if (window.tinyMCE && tinyMCE.get('comentario')) tinyMCE.get('comentario').setContent('');
                    else $('#comentario').val('');
                    $('#comment_id').val('');
                    $btn.text('Insertar Comentario');
                },
                error: function(xhr){
                    console.error('[Comentarios] Error al insertar:', xhr.status, xhr.responseText || xhr.statusText);
                    alert('Error al insertar comentario. Revisa la consola.');
                },
                complete: function(){
                    $btn.prop('disabled', false);
                }
            });
        });

        // === ELIMINAR COMENTARIO (delegado) ===
        var deleteCommentId = null;
        $(document).off('click.commentDelete', '.eliminar_comentario');
        $(document).on('click.commentDelete', '.eliminar_comentario', function(e){
            e.preventDefault();
            deleteCommentId = this.id;
            $('#modalConfirmDeleteComment').modal('show');
        });

        $(document).off('click.commentConfirmDelete', '#btnConfirmDeleteComment');
        $(document).on('click.commentConfirmDelete', '#btnConfirmDeleteComment', function(){
            if (!deleteCommentId) return;
            var _token = $('input[name="_token"]').val();
            $('#modalConfirmDeleteComment').modal('hide');
            $.ajax({
                url: "{{ route('delete_Comment') }}",
                type: 'POST',
                data: {
                    comment_id: deleteCommentId,
                    tipo_comentario: 'task',
                    _token: _token,
                    taskc_id: $('#taskc_id').val(),
                    projectc_id: $('#projectc_id').val()
                },
                success: function(response){
                    document.getElementById('comentarios').innerHTML = response;
                },
                error: function(jqXHR){
                    console.error('Error al eliminar comentario:', jqXHR.responseText);
                }
            });
            deleteCommentId = null;
        });

        // === EDITAR COMENTARIO (delegado) ===
        $(document).off('click.commentEdit', '.editar_comentario');
        $(document).on('click.commentEdit', '.editar_comentario', function(e){
            e.preventDefault();
            var id = this.id;
            var html = $('#texto_comentario_' + id).html();
            if (window.tinyMCE && tinyMCE.get('comentario')) {
                tinyMCE.get('comentario').setContent(html);
            } else {
                $('#comentario').val(html);
            }
            $('#comment_id').val(id);
            $('#boton_comentarios').text('Editar Comentario');
        });

    });
</script>
@endpush