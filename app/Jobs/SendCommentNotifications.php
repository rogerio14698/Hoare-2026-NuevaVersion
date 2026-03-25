<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CommentAdded;

class SendCommentNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $comment;
    public $meta;
    public $recipients;
    public $requestId;

    /**
     * Create a new job instance.
     */
    public function __construct($comment, $meta, array $recipients, $requestId = null)
    {
        $this->comment = $comment;
        $this->meta = $meta;
        $this->recipients = $recipients;
        $this->requestId = $requestId ?? uniqid('job_', true);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::debug('SendCommentNotifications@handle - start', ['request_id' => $this->requestId, 'recipients' => $this->recipients]);
        try {
            // Enviar una sola vez el mailable a todos los destinatarios
            Mail::to($this->recipients)->send(new CommentAdded($this->comment, $this->meta));
            Log::debug('SendCommentNotifications@handle - finished', ['request_id' => $this->requestId]);
        } catch (\Throwable $e) {
            Log::error('SendCommentNotifications@handle - error sending mail: ' . $e->getMessage(), ['request_id' => $this->requestId]);
            // Re-lanzar para que el job pueda ser reintentado según la configuración
            throw $e;
        }
    }
}
