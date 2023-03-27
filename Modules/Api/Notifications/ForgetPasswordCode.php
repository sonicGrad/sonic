<?php

namespace Modules\Api\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ForgetPasswordCode extends Notification{
    use Queueable;
    protected $user;
    protected $code;
    public function __construct($user, $code){
        $this->user = $user;
        $this->code = $code;
    }

  
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable){
        return (new MailMessage)
                ->subject('Verification Code')
                ->from('sonic_support@localhost', 'Sonic Support')
                ->line('Hi ' . $this->user->full_name . ' Your Verification code is ' . $this->code)
                ->line('Thank you for using our application!');
    }


    public function toArray($notifiable){
        return [
        ];
    }
}
