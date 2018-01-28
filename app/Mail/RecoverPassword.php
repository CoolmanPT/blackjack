<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecoverPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $token;
    private $user;


    public function __construct($token, $to)
    {
        $this->token = $token;
        $this->user = $to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = 'Blackjack Staff';
        $subject = 'Nova Password';

        return $this->view('email.recover')
            ->with([
                'token' => $this->token,
                'user' => $this->user,
            ])->subject($subject);


    }
}
