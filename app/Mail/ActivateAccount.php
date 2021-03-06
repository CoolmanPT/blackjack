<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivateAccount extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $token;
    private $sentBy;
    private $user;

    public function __construct($token, $from, $to)
    {
        $this->token = $token;
        $this->sentBy = $from;
        $this->user = $to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = 'BlackJack Staff';

        $subject = 'Activate Account';

        return $this->view('email.activateAccount')
            ->with([
                'token' => $this->token,
            ])->from($this->sentBy, $name)->subject($subject);
    }
}
