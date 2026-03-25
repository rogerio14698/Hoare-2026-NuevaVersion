<!-- Comentarios timeline -->
<div id="comentarios">
    <div class="row">
        <div class="col-md-12">
            <!-- The time line -->
            <div class="timeline">
                @foreach($comments as $comment)
                    <!-- timeline item -->
                <div class="time-label">
                    <span class="bg-primary">
                        {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$comment->date)->format('d/m/Y')}}
                    </span>
                </div>
                <div>
                    @if(isset($comment->users[0]))
                        <i class="fas fa-user bg-info"></i>
                    @else
                        <i class="fas fa-user bg-secondary"></i>
                    @endif

                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$comment->date)->format('H:i:s')}}</span>

                        <h3 class="timeline-header">
                            @if(isset($comment->users[0]))
                                <a href="#">{{$comment->users[0]->name}}</a>
                                <small class="text-muted">({{$comment->users[0]->ncomentarios()}} comentarios)</small>
                            @else
                                <span class="text-muted">(Sin responsable)</span>
                            @endif
                            <span class="float-right">
                                <i id="{{$comment->id}}" class="fas fa-edit editar_comentario text-primary" style="cursor: pointer;" title="Editar"></i>
                                <i id="{{$comment->id}}" class="fas fa-trash-alt eliminar_comentario text-danger ml-2" style="cursor: pointer;" title="Eliminar"></i>
                            </span>
                        </h3>

                        <div id="texto_comentario_{{$comment->id}}" class="timeline-body">
                            {!! $comment->comment !!}
                        </div>
                    </div>
                </div>
                <!-- END timeline item -->
                @endforeach
                <div>
                    <i class="fas fa-clock bg-gray"></i>
                </div>
            </div>
        </div>
    </div>
</div>