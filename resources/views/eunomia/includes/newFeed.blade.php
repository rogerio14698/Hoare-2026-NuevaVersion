@php
    // Asegurar que $comments existe y es iterable
    $comments = $comments ?? collect();
    if (!is_iterable($comments)) {
        $comments = collect();
    }
@endphp

<section class="newsfeed" aria-labelledby="newsfeed-title">
    <header>
        <h3 id="newsfeed-title" >NewsFeed</h3>
    </header>

    <div class="newsfeed__list">
        @forelse($comments as $comment)
            @php
                $authorName = optional(optional($comment->comment_user)->user)->name ?? optional(optional($comment->comment_user)->name) ?? 'Usuario';
                try {
                    $fecha = \Carbon\Carbon::parse($comment->date)->format('d/m/Y H:i');
                } catch (\Exception $e) {
                    $fecha = is_string($comment->date) ? $comment->date : '';
                }
                $contenido = trim(strip_tags($comment->comment ?? ''));

                // Determinar si el comentario pertence a una tarea o proyecto y crear enlace
                $actionText = '';
                $targetUrl = null;
                $targetName = null;
                if (is_object($comment->comment_task) && is_object($comment->comment_task->task)) {
                    $task = $comment->comment_task->task;
                    $actionText = 'comentó en la tarea';
                    $targetName = ($task->project->codigo_proyecto ?? 'TASK') . '_' . ($task->titulo_tarea ?? '');
                    $targetUrl = route('tasks.edit', [$task->id]);
                } elseif (is_object($comment->comment_project) && is_object($comment->comment_project->project)) {
                    $project = $comment->comment_project->project;
                    $actionText = 'comentó en el proyecto';
                    $targetName = $project->titulo_proyecto ?? 'Proyecto';
                    $targetUrl = route('projects.edit', [$project->id]);
                }
            @endphp

            <article class="newsfeed__item">
                <div class="newsfeed__meta">
                    <time class="newsfeed__time" datetime="{{ $comment->date }}">{{ strtoupper($fecha) }}</time>
                </div>

                <div class="newsfeed__summary">
                    <p class="newsfeed__action">
                        <strong class="newsfeed__author">{{ $authorName }}</strong>
                        <span class="newsfeed__verb"> {{ $actionText }} </span>
                        @if($targetUrl)
                            <a href="{{ $targetUrl }}" class="newsfeed__link">{{ $targetName }}</a>
                        @else
                            <span class="newsfeed__target">{{ $targetName ?? '' }}</span>
                        @endif
                    </p>
                </div>

                <div class="newsfeed__body" role="article">
                    <p class="newsfeed__content">{{ $contenido }}</p>
                </div>
            </article>

        @empty
            <div class="newsfeed__empty">
                <p>No hay actividad reciente.</p>
            </div>
        @endforelse
    </div>
</section>
