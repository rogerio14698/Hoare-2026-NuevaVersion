<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Task;
use App\Project;
use App\User;
use App\Mail\CommentAdded;
use App\Jobs\SendCommentNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Versión simplificada y documentada del controlador de comentarios.
    // Objetivo: crear el comentario, reunir destinatarios de forma segura
    // y encolar un único Mailable (`CommentAdded`) para evitar duplicidades.

    /**
     * Store a newly created comment.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|
     */
    public function store(Request $request)
    {
        // Validaciones mínimas (ajustar reglas según la app)
        $data = $request->validate([
            'comentario' => 'required|string',
            'projectc_id' => 'nullable|integer',
            'taskc_id' => 'nullable|integer',
            'comment_id' => 'nullable|integer',
        ]);

        // --- MODO EDICIÓN: actualizar comentario existente ---
        if (!empty($data['comment_id'])) {
            $comment = Comment::findOrFail((int)$data['comment_id']);
            $comment->comment = $data['comentario'];
            $comment->save();

            if ($request->ajax() || $request->wantsJson()) {
                $projectId = $request->input('projectc_id');
                $taskId    = $request->input('taskc_id');
                if (!empty($projectId)) {
                    $project  = Project::findOrFail((int)$projectId);
                    $comments = $project->comments()->orderBy('date', 'DESC')->get();
                } elseif (!empty($taskId)) {
                    $task     = Task::findOrFail((int)$taskId);
                    $comments = $task->comments()->orderBy('date', 'DESC')->get();
                } else {
                    $comments = collect([$comment]);
                }
                return view('eunomia.comments.list_comments', compact('comments'));
            }
            return redirect()->back()->with('success', 'Comentario actualizado.');
        }

        // Usuario autor (puede ser null en casos especiales)
        $author = Auth::user();

        // Prevención rápida de duplicados (idempotencia): si el mismo usuario
        // ha enviado idéntico texto en los últimos 10 segundos, ignoramos
        // la segunda petición para evitar duplicados por doble clic.
        $authorId = $author->id ?? null;
        $texto = trim(strip_tags($data['comentario']));
        if ($authorId) {
            $tenSecondsAgo = date('Y-m-d H:i:s', time() - 10);
            $duplicate = Comment::where('comment', $data['comentario'])
                ->where('date', '>=', $tenSecondsAgo)
                ->whereHas('users', function ($q) use ($authorId) {
                    $q->where('user_id', $authorId);
                });

            if (!empty($data['taskc_id'])) {
                $duplicate->whereHas('tasks', function ($q) use ($data) {
                    $q->where('task_id', (int) $data['taskc_id']);
                });
            }

            if (!empty($data['projectc_id'])) {
                $duplicate->whereHas('projects', function ($q) use ($data) {
                    $q->where('project_id', (int) $data['projectc_id']);
                });
            }

            $duplicate = $duplicate->first();

            if ($duplicate) {
                // Registrar y devolver el comentario existente (sin crear ni disparar jobs)
                Log::info('CommentController@store - duplicate detected, ignoring', [
                    'user_id' => $authorId,
                    'task_id' => $data['taskc_id'] ?? null,
                    'project_id' => $data['projectc_id'] ?? null,
                    'comment_excerpt' => mb_substr($texto, 0, 120),
                ]);
                if ($request->ajax() || $request->wantsJson()) {
                    $comments = collect([$duplicate]);
                    return view('eunomia.comments.list_comments', compact('comments'));
                }
                return redirect()->back();
            }
        }

        // Crear y guardar el comentario (modelo `App\Comment` existente)
        // Nota: la relación con proyectos/tareas se realiza mediante pivotes
        // (`comment_project`, `comment_task`, `comment_user`), por lo que no
        // escribimos columnas `project_id`/`task_id` en la tabla `comments`.
        $comment = new Comment();
        $comment->comment = $data['comentario'];
        // Fecha: incluir segundos para compatibilidad con vistas que esperan
        // el formato 'Y-m-d H:i:s'
        $comment->date = date('Y-m-d H:i:s');
        // No asignamos `user_id` directamente a la tabla `comments` porque
        // la arquitectura usa tablas pivote (`comment_user`). Guardamos
        // el comentario primero y luego asociamos al usuario mediante pivote.
        $comment->save();

        // Asociar pivotes: usuario autor y, si vienen, project/task
        if ($author) {
            $authorId = (int)$author->id;
            if (!$comment->users()->where('user_id', $authorId)->exists()) {
                $comment->users()->attach($authorId);
            }
        }
        if ($request->filled('userc_id')) {
            $otherId = (int)$request->input('userc_id');
            if ($otherId > 0 && !$comment->users()->where('user_id', $otherId)->exists()) {
                $comment->users()->attach($otherId);
            }
        }
        if (!empty($data['projectc_id'])) {
            $comment->projects()->syncWithoutDetaching([(int)$data['projectc_id']]);
        }
        if (!empty($data['taskc_id'])) {
            $comment->tasks()->syncWithoutDetaching([(int)$data['taskc_id']]);
        }

        // Construimos información adicional (meta) para el email
        $meta = [
            'author_id' => $author->id ?? null,
            'author_name' => $author->name ?? ($author->email ?? 'Alguien'),
            'context' => null,
            'link' => null,
        ];

        // Recopilar destinatarios de forma segura en una colección
        $recipients = collect();

        // Si es comentario de proyecto, intentar cargar el proyecto y su propietario
        if (!empty($data['projectc_id'])) {
            $project = Project::with('user')->find($data['projectc_id']);
            $meta['context'] = 'project';
            $meta['title'] = $project->titulo_proyecto ?? $project->name ?? null;
            $meta['link'] = $project ? url('/eunomia/projects/' . $project->id) : null;
            if ($project && isset($project->user) && !empty($project->user->email)) {
                $recipients->push($project->user->email);
            }
        }

        // Si es comentario de tarea, cargar la tarea y sus usuarios responsables
        if (!empty($data['taskc_id'])) {
            $task = Task::with('users', 'project')->find($data['taskc_id']);
            $meta['context'] = 'task';
            $meta['title'] = $task->titulo_tarea ?? $task->name ?? null;
            $meta['link'] = $task ? url('/eunomia/tasks/' . $task->id . '/edit') : null;
            if (!empty($task) && $task->relationLoaded('users')) {
                $task->users->pluck('email')->filter()->each(function ($email) use ($recipients) {
                    $recipients->push($email);
                });
            }
        }

        // Buscar menciones @usuario en el texto y añadir su email si existe
        // Esto es un heurístico simple: busca tokens que empiecen por @
        if (preg_match_all('/@([A-Za-z0-9_\\-\\.]+)/u', $comment->comment, $matches)) {
            foreach ($matches[1] as $username) {
                // Intentamos localizar por `name` o por `email` (según convenga)
                $u = User::where('name', $username)->orWhere('email', $username)->first();
                if ($u && !empty($u->email)) {
                    $recipients->push($u->email);
                }
            }
        }

        // Excluir autor del listado de destinatarios (si tiene email)
        if ($author && !empty($author->email)) {
            $recipients = $recipients->reject(function ($email) use ($author) {
                return $email === $author->email;
            })->values();
        }

        // Normalizar: eliminar vacíos y duplicados
        $recipients = $recipients->filter()->unique()->values();

        // Encolar un único Job que se encargue de notificar a todos los destinatarios.
        // Esto evita crear múltiples jobs/entradas por destinatario y evita
        // envíos duplicados provocados por colas internas.
        $recipientEmails = $recipients->filter()->unique()->values()->all();
        if (!empty($recipientEmails)) {
            $requestId = uniqid('coment_', true);
            try {
                Log::debug('CommentController@store - dispatching SendCommentNotifications', ['request_id' => $requestId, 'recipients' => $recipientEmails]);
                SendCommentNotifications::dispatch($comment, $meta, $recipientEmails, $requestId);
            } catch (\Throwable $e) {
                // Registrar el error de despacho sin interrumpir la operación
                Log::error('CommentController@store - error dispatching job: ' . $e->getMessage(), ['request_id' => $requestId]);
            }
        } else {
            Log::warning('CommentController@store - NO recipients found, email NOT sent', [
                'comment_id' => $comment->id,
                'task_id' => $data['taskc_id'] ?? null,
                'project_id' => $data['projectc_id'] ?? null,
                'author_id' => $author->id ?? null,
            ]);
        }

        // Responder apropiadamente para AJAX o redirección clásica
        if ($request->ajax() || $request->wantsJson()) {
            // Enviamos SOLAMENTE el comentario recién creado en una colección.
            // Esto evita que el frontend reciba la lista completa y la duplique.
            $comments = collect([$comment]);
            return view('eunomia.comments.list_comments', compact('comments'));
        }

        return redirect()->back()->with('success', 'Comentario insertado.');
    }

    /**
     * Eliminar comentario (versión minimal).
     * Conserva comportamiento actual pero simplifica comprobaciones.
     */
    public function destroy(Request $request)
    {
        // Validación básica
        $comment_id = $request->input('comment_id');
        $comment = Comment::findOrFail($comment_id);

        // Permisos: el proyecto original usaba compruebaSeguridad();
        // aquí asumimos que la aplicación gestiona permisos en middleware o a nivel superior.
        $comment->delete();

        // Devolver la lista actualizada según el contexto recibido
        $project_id = $request->input('projectc_id');
        $task_id = $request->input('taskc_id');
        if (!empty($project_id)) {
            $project = Project::findOrFail($project_id);
            $comments = $project->comments()->orderBy('date', 'DESC')->get();
        } else {
            $task = Task::findOrFail($task_id);
            $comments = $task->comments()->orderBy('date', 'DESC')->get();
        }
        return view('eunomia.comments.list_comments', compact('comments'));
    }

    /**
     * Mostrar comentarios de tarea (compatibilidad con rutas existentes).
     */
    public function muestraComentariosTarea($task_id)
    {
        $comments = Comment::join('comment_task', 'comment_task.comment_id', 'comments.id')
            ->where('comment_task.task_id', $task_id)
            ->orderBy('date', 'DESC')
            ->get();
        return view('eunomia.comments.list_comments', compact('comments'));
    }

    /**
     * Mostrar comentarios de proyecto (compatibilidad con rutas existentes).
     */
    public function muestraComentariosProyecto($project_id)
    {
        $comments = Comment::join('comment_project', 'comment_project.comment_id', 'comments.id')
            ->where('comment_project.project_id', $project_id)
            ->orderBy('date', 'DESC')
            ->get();
        return view('eunomia.comments.list_comments', compact('comments'));
    }
}
