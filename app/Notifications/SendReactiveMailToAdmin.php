<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendReactiveMailToAdmin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $notify;
     public function __construct($notify)
    {
        $this->notify = $notify;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Service Reactiva Request  : '.$this->notify['entry'])
                ->from(env('MAIL_FROM_ADDRESS'), $this->notify['email'])
                ->greeting('Client Name ' . $this->notify['client'] . ',')
                ->line('Payment Reactive request for Entry Name '. $this->notify['entry'])
                ->action('Reactive Service ', route('payment.expired'));

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
