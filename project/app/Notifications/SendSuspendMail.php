<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\Backend\EmailNotificationService;


class SendSuspendMail extends Notification
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
            ->subject('Service Suspended')
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->greeting('Hello ' . $this->notify['client'] . ',')
            ->line('Your service has beend suspended for '. $this->notify['entry'])
            ->line('Please click on the below buton for Reactivation Request')
            ->action('Service Reactivation Request', route('payment.reactivate', [$this->notify['encrypt']]))
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

    public static function toDatabaseCustom($notify)
    {
        return EmailNotificationService::_storing((object) [
            'uuid'      => $notify['uuid'],
            'client_id' => $notify['client_id'],
            'email'     => $notify['email'],
            'body'      => [
                'subject'  => 'Service Suspended',
                'greeting' => 'Hello ' . $notify['client'],
                'message'  => 'Your service has beend suspended for '. $notify['entry'],
                'link'     => route('payment.reactivate', [$notify['encrypt']])
            ]
        ]);
    }
}
