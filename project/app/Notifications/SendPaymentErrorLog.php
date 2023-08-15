<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\EmailNotification;

class SendPaymentErrorLog extends Notification
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
        // self::toDatabaseCustom($this->notify);
        return (new MailMessage)
                    ->subject('Payment Error Log - ' . env('CMS_VERSION'))
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->greeting('Hello Admin,')
                    ->line('Client - ' . $this->notify['client'])
                    ->line('Payment Title - ' . $this->notify['title'])
                    ->line('Payment Action - ' . $this->notify['action'])
                    ->line('Error Log: ' . json_encode($this->notify['error_log']));
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

    public static function toDatabaseCustom($notify)
    {
        return EmailNotification::_storing((object) [
            'uuid'      => $notify['uuid'],
            'client_id' => $notify['client_id'],
            'email'     => $notify['email'],
            'body'      => [
                'subject'  => 'Payment Error Log - ' . env('CMS_VERSION'),
                'greeting' => 'Hello Admin,',
                'message'  => 'Client - ' . $notify['client'] . 
                            ', Payment Title - ' . $notify['title'] . 
                            ', Payment Action - ' . $notify['action'] . 
                            ', Error Log: ' . json_encode($notify['error_log'])
            ]
        ]);
    }
}
