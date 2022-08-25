<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class SendEmailViaQueue extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $template = false;

    private $shotcodes = [];

    public $messagecontent = '';

    private $mysubject = '';

    public function __construct($template_id = false, $shortcodes = [], $message = '', $subject = '')
    {
        $this->template = EmailTemplate::find($template_id);

        $this->shortcodes = $shortcodes;
        $this->messagecontent = $message;
        $this->mysubject = $subject;

        if($this->template){
            $this->messagecontent =  $this->template->parseContent($this->shortcodes);
            $this->mysubject =  $this->template->parseSubject($this->shortcodes);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject($this->mysubject);

        return $mail->markdown('emails.common')->with([
            'messagecontent' => $this->messagecontent,
            'subject' => $this->mysubject,
        ]);
    }

}
