<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\EmailNotification;

class SendPaymentLink extends Notification
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
        self::toDatabaseCustom($this->notify);
        return (new MailMessage)
            ->subject('Payment Due')
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->greeting('Hello ' . $this->notify['client'] . ',')
            ->line('Your payment due is ' . $this->notify['currency'] . ' ' . $this->notify['total'])
            ->line('Please click on the pay now button below to make your payment')
            ->action('Pay Now', route('pay.index', [$this->notify['encrypt']]))
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
        return [];
    }

    // custom fix
    public static function toDatabaseCustom($notify)
    {
        return EmailNotification::_storing((object) [
            'uuid'      => $notify['uuid'],
            'client_id' => $notify['client_id'],
            'email'     => $notify['email'],
            'body'      => [
                'subject'  => 'Payment Due',
                'greeting' => 'Hello ' . $notify['client'],
                'message'  => 'Your payment due is ' . $notify['currency'] . ' ' . $notify['total'] . ' for the ' . $notify['entry'],
                'link'     => route('pay.index', [$notify['encrypt']])
            ]
        ]);
    }
}
