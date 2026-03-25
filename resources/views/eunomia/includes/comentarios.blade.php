@foreach($comments as $comment)
    <!-- chat item -->
    <div class="item">
        <img src="/images/avatar/{{ isset($comment->comment_user->user->avatar)?$comment->comment_user->user->avatar:'sinavatar.jpg'}}" alt="user image" class="online">

        <p class="message">
            <small class="text-muted float-right"><i class="far fa-calendar"></i> {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$comment->date)->format('d/m/Y')}} <i class="far fa-clock"></i> {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$comment->date)->format('H:i')}}</small>
            <strong>{{is_object($comment->comment_user)?$comment->comment_user->user->name:''}}</strong><br>
            {!! is_object($comment->comment_project)
                ? ('<strong>Proyecto</strong>: <a href="' . route('projects.edit', $comment->comment_project->project) . '">' . e($comment->comment_project->project->titulo_proyecto) . '</a>')
                : ('<strong>Tarea</strong>: ' . (is_object($comment->comment_task)
                    ? '<a href="' . route('tasks.edit', $comment->comment_task->task) . '">' . e($comment->comment_task->task->project->codigo_proyecto . '_' . $comment->comment_task->task->titulo_tarea) . '</a>'
                        . '<br><strong>Proyecto</strong>: <a href="' . route('projects.edit', $comment->comment_task->task->project) . '">' . e($comment->comment_task->task->project->codigo_proyecto . '_' . $comment->comment_task->task->project->titulo_proyecto) . '</a>'
                    : ''))
            !!}<br>
        </p>
        <p class="direct-chat-text">{!! strip_tags($comment->comment) !!}</p>
        <hr>
    </div>
    <!-- /.item -->
@endforeach
