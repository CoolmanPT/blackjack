<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class changestate extends Mailable
{
    use Queueable, SerializesModels;

    private $mensagem;

    private $sentBy;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mensagem, $from)
    {
        $this->mensagem = $mensagem;
        $this->sentBy = $from;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = 'Administração';

        $subject = 'Alteração no estado da sua conta';

        return $this->view('emails.changestate')
            ->with([
                'mensagem' => $this->mensagem,
            ])->from($this->sentBy, $name)->subject($subject);

    }
}
