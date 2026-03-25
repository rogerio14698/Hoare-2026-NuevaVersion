<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CommentAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $comment;
    public $meta; // array with context: project|task info

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($comment, $meta = [])
    {
        $this->comment = $comment;
        $this->meta = $meta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = ($this->meta['author_name'] ?? 'Alguien') . ' ha insertado un nuevo comentario';
        $mail = $this->subject($subject)
                    ->view('emails.comment_added')
                    ->with([ 'comment' => $this->comment, 'meta' => $this->meta ]);

        // Añadir traza del mensaje Swift (Laravel 8 usa SwiftMailer) para depuración
        $this->withSwiftMessage(function ($message) {
            try {
                Log::debug('CommentAdded: SwiftMessage envelope/headers', [
                    'from' => $message->getFrom(),
                    'to' => $message->getTo(),
                    'cc' => $message->getCc(),
                    'bcc' => $message->getBcc(),
                    'return_path' => $message->getReturnPath(),
                    'sender' => $message->getSender(),
                    'headers' => method_exists($message, 'getHeaders') ? (string) $message->getHeaders() : null,
                ]);
            } catch (\Throwable $e) {
                Log::error('CommentAdded: error logging SwiftMessage - ' . $e->getMessage());
            }
        });

        return $mail;
    }
}
