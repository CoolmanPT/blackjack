<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class resetlink extends Mailable
{
    use Queueable, SerializesModels;

    private $token;
    private $sentBy;
    private $sendTo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $from, $to)
    {
        $this->token = $token;
        $this->sentBy = $from;
        $this->sendTo = $to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = 'Blackjack Staff';

        $subject = 'Recuperação de Password';


        return $this->view('emails.resetlink')
            ->with([
                'token' => $this->token,
                'receiver' => $this->sendTo,
            ])->from($this->sentBy, $name)->subject($subject);

    }
}
