<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\EmailNotification;

class SendPaymentNotify extends Notification
{
    use Queueable;

    public $notify;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
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
        $expire_date = $this->notify['advance_type'] == 'WEEK' ? now()->addDays(7) : now()->addMonths(1);
        self::toDatabaseCustom($this->notify, $expire_date);
        return (new MailMessage)
                    ->subject('Payment Due Alert')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->greeting('Hello ' . $this->notify['setup']->client->name . ',')
                    ->line('Your '.$this->notify['setup']->title.' service is going to expire on ' . $expire_date->format('D, jS M Y'))
                    ->line('You will receive the payment link on ' . $expire_date->format('Y-m-d'))
                    ->line('We value you as our customer and look forward to continue serving you in the future.')
                    ->line('Thank you for your business!');
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

    public static function toDatabaseCustom($notify, $expire_date)
    {
        return EmailNotification::_storing((object) [
            'uuid'      => $notify['setup']->uuid,
            'client_id' => $notify['setup']->client->id,
            'email'     => $notify['setup']->email,
            'body'      => [
                'subject'  => 'Payment Due Alert',
                'greeting' => 'Hello ' . $notify['setup']->client->name . ',',
                'message'  => 'Your '.$notify['setup']->title.' service is going to expire on ' . $expire_date->format('D, jS M Y') . '. ' .
                            'You will receive the payment link on ' . $expire_date->format('Y-m-d') . '. ' .
                            'We value you as our customer and look forward to continue serving you in the future. ' .
                            'Thank you for your business!'
            ]
        ]);
    }
}
