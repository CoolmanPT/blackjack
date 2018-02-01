<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class changedState extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */


    private $sentBy;
    private $text;
    private $user;

    public function __construct($info, $from, $to)
    {
        $this->text = $info;
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
        $name = 'Blackjack Staff';

        $subject = 'Account status changed';

        return $this->view('email.accountChanged')
            ->with([
                'msg' => $this->text,
            ])->from($this->sentBy, $name)->subject($subject);
    }
}
