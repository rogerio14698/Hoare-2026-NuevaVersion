<!-- Comentarios -->
<!-- Bootstrap Dialog -->
document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>');

$('#form_ins_comment').click(function (){
    var _token = $("input[name='_token']").val() // Token generado en el campo de arriba para los formularios de Laravel (CSRF Protection)
    $.ajax({
        url: "/eunomia/comments/new",
        data: 'user_id=' + this.user_id.value + '&project_id=' + this.projectc_id.value + '&task_id=' + this.taskc_id.value + '&comentario=' + tinyMCE.get('comentario').getContent()+ '&_token=' + _token + '&comment_id=' + this.comment_id.value,
        type: 'POST',
        evalScripts:true,
        success: function (response) {
            document.getElementById('comentarios').innerHTML = response;
            tinyMCE.get('comentario').setContent('');
            $('#boton_comentarios').text('Insertar Comentario');
            $('#comment_id').val(null);
            $.getScript( "/js/comments.js", function( data, textStatus, jqxhr ) {
                console.log( data ); // Data returned
                console.log( textStatus ); // Success
                console.log( jqxhr.status ); // 200
                console.log( "Load was performed." );
            });
        },
        error: function (jqXHR, textStatus) {
            console.log(jqXHR.responseText);
        }
    }).done(function(){

    });
});

// Usar delegación para elementos dinámicos
$(document).on('click', '.eliminar_comentario', function (e) {
    e.preventDefault();
    var id = this.id;

    BootstrapDialog.confirm('¿Está seguro que desea eliminar el registro?', function (result) {
        if (result) {
            var _token = $("input[name='_token']").val();
            $.ajax({
                url: "/eunomia/comments/delete",
                data: {
                    comment_id: id,
                    tipo_comentario: 'task',
                    _token: _token,
                    taskc_id: $('#taskc_id').val(),
                    projectc_id: $('#projectc_id').val()
                },
                type: 'POST',
                success: function (response) {
                    document.getElementById('comentarios').innerHTML = response;
                },
                error: function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                }
            });
        }
    });
});

$(document).on('click', '.editar_comentario', function (e) {
    e.preventDefault();
    var id = this.id;
    tinyMCE.get('comentario').setContent($('#texto_comentario_' + id).html());
    $('#comment_id').val(id);
    $('#boton_comentarios').text('Editar Comentario');
});