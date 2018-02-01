<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $user;
    private $sentBy;
    public function __construct($to, $from)
    {
        $this->user = $to;
        $this->sentBy = $from;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = 'BlackJack Staff';
        $subject = 'Password changed!';

        return $this->view('email.passwordChanged')
            ->from($this->sentBy, $name)->subject($subject);
    }
}
