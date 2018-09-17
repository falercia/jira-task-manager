<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable {

   use Queueable,
       SerializesModels;

   private $nonCompliance;
   private $viewName;
   public $subject;

   public function __construct($nonCompliance, $viewName, $subject = 'Mail') {
      $this->nonCompliance = $nonCompliance;
      $this->viewName = $viewName;
      $this->subject = $subject;
   }

   public function build() {
      return $this->from((string)env('MAIL_FROM_ADDRESS'))
                      ->subject($this->subject)
                      ->view($this->viewName)
                      ->text($this->viewName)
                      ->with(['nonCompliance' => $this->nonCompliance,
      ]);
   }

}
