<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $userName;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(string $code, string $userName)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    /**
     * Obtiene el sobre del mensaje.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de Seguridad para Restablecimiento de Contraseña',
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        // Usa la vista que crearás en el siguiente paso
        return new Content(
            view: 'mail.password-reset',
        );
    }

    /**
     * Obtiene los datos adjuntos para el mensaje.
     */
    public function attachments(): array
    {
        return [];
    }
}
