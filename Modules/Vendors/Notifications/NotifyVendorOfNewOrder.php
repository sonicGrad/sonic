<?php

namespace Modules\Vendors\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Http;


class NotifyVendorOfNewOrder extends Notification
{
    use Queueable;

    protected  $order;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\Modules\Products\Entities\Orders $order){
        $this->order = $order;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }
    public function toDataBase($notifiable){
        // https://api.opencagedata.com/geocode/v1/json?q=31.54248%2C34.45228&key=6b85e2270825413a95e7ee5916383fb3&language=en&pretty=1
                    $locationAr= json_decode($this->order->location)->lat . '%2C' . json_decode($this->order->location)->long;
                    $response = Http::get('https://api.opencagedata.com/geocode/v1/json', [
                        'q' => $locationAr,
                        'key' => '6b85e2270825413a95e7ee5916383fb3',
                        'language' => 'ar',
                        'pretty' => '1'
                    ]);
                    $resultAR =  json_decode($response);
                    $locationEn= json_decode($this->order->location)->lat . '%2C' . json_decode($this->order->location)->long;
                    $response = Http::get('https://api.opencagedata.com/geocode/v1/json', [
                        'q' => $locationEn,
                        'key' => config('services.opencage')['key'],
                        'language' => 'en',
                        'pretty' => '1'
                    ]);
                    $resultEn =  json_decode($response);
                    return [
                        'en' => [
                            'title' => __('New Order #:number', ['number' => $this->order->id]),
                            'body' => __('A new order has been created (Order #:number). For User :user_name  in Location :location' , [
                                'number' => $this->order->id,
                                'location' => $resultAR->results[0]->formatted,
                                'user_id' => $this->order->buyer_id,
                                'user_name' => $this->order->user->first_name . ' ' . $this->order->user->last_name,
                            ]),
                        ],
                        'ar' => [
                            'title' => __('طلب جديد #:number', ['number' => $this->order->id]),
                            'body' => __('طلب جديد تم إنشائه (طلب #:number).للمستخدم :user_name  في الموقع :location' , [
                                'number' => $this->order->id,
                                'location' => $resultAR->results[0]->formatted,
                                'user_id' => $this->order->buyer_id,
                                'user_name' => $this->order->user->first_name . ' ' . $this->order->user->last_name,
                            ]),
                        ],
                    ];
    }
    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!');
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
