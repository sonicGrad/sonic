<?php

namespace Modules\Api\Notifications;
use NotificationChannels\Twilio\TwilioChannel;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ForgetPasswordViaPhoneNumber extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $code;
    protected $user;
    public function __construct($user,$code){
        $this->code = $code;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }

   
    public function toTwilio($notifiable)
    {
        return (new \NotificationChannels\Twilio\TwilioSmsMessage())
            ->content("Verification Code, Hi  Your Verification code is " . $this->code);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
