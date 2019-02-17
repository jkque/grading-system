<?php

namespace App\Mail;

use App\QueuedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\Factory as Queue;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

class ReadyForEnrollment extends Mailable
{
    use Queueable, SerializesModels;
    public $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($queuedEmail)
    {
        $this->mail = QueuedEmail::create($queuedEmail);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('support@grading-sytem.co')->markdown('emails.ready_for_enrollment');
    }

    /**
     * Send the mail
     */ 
    public function send(MailerContract $mailer)
    {
        $this->mail->update(['status' => true]);

        // event(new EmailSent($this->mail));

        parent::send($mailer);
    }

    /**
     * Queue the email
     */
    public function queue(Queue $queue)
    {
        // event(new EmailQueued($this->mail));

        return parent::queue($queue);
    }
}
