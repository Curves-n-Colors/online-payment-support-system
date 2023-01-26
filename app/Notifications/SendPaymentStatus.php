<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\EmailNotification;

use PDF;

class SendPaymentStatus extends Notification
{
    use Queueable;

    public $detail;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($detail)
    {
        $this->detail = $detail;
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
        if ($this->detail->payment_status == config('app.addons.status_payment.COMPLETED')) {
            
            $logo = 'https://climbalaya.com/images/logo/logo.png';
            $pdf = PDF::setOptions(['dpi' => 150, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); // , 'defaultFont' => 'sans-serif', 'images' => true
            $pdf->loadView('pdf.invoice', ['logo' => $logo, 'data' => $this->detail]);

            return (new MailMessage)
                        ->subject('Payment Success')
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->greeting('Hello ' . $this->detail->client->name . ',')
                        ->line('Your payment of ' . $this->detail->currency . ' ' . number_format($this->detail->total, 2) . ' for the ' . $this->detail->title . ' have been completed successfully.')
                        ->line('Referene Code #' . $this->detail->ref_code)
                        ->line('Thank you for making the payment timely.')
                        ->attachData($pdf->output(), 'invoice.pdf', [
                            'mime' => 'application/pdf',
                        ]);
        }
        else if ($this->detail->payment_status == config('app.addons.status_payment.REFUNDED')) {
            return (new MailMessage)
                        ->subject('Payment Refund')
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->greeting('Hello ' . $this->detail->client->name . ',')
                        ->line('Your payment of ' . $this->detail->currency . ' ' . number_format($this->detail->total, 2) . ' for the ' . $this->detail->title . ' have been refunded.')
                        ->line('Referene Code #' . $this->detail->ref_code)
                        ->line('Thank you for doing business with us.');
        }
        else {
            return (new MailMessage)
                        ->subject('Payment Cancelled')
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->greeting('Hello ' . $this->detail->client->name . ',')
                        ->line('Your payment of ' . $this->detail->currency . ' ' . number_format($this->detail->total, 2) . ' for the ' . $this->detail->title . ' have been cancelled from the payment gateway server.')
                        ->line('Referene Code #' . $this->detail->ref_code)
                        ->line('Please forward this email to our admin teamn at ' . env('PRIMARY_MAIL'))
                        ->line('Our Admin Team will contact you back with further details')
                        ->line('Thank You');
        }
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

    public static function toDatabaseCustom($detail)
    {
        if ($detail->payment_status == config('app.addons.status_payment.COMPLETED')) {
            $body = [
                'subject'  => 'Payment Success',
                'greeting' => 'Hello ' . $detail->client->name . ',',
                'message'  => 'Your payment of ' . $detail->currency . ' ' . number_format($detail->total, 2) . ' for the ' . $detail->title . ' have been completed successfully. ' . 
                            'Referene Code #' . $detail->ref_code .  '. ' .
                            'Thank you for your business!'
            ];
        }
        else if ($detail->payment_status == config('app.addons.status_payment.REFUNDED')) {
            $body = [
                'subject'  => 'Payment Refund',
                'greeting' => 'Hello ' . $detail->client->name . ',',
                'message'  => 'Your payment of ' . $detail->currency . ' ' . number_format($detail->total, 2) . ' for the ' . $detail->title . ' have been refunded. ' . 
                            'Referene Code #' . $detail->ref_code .  '. ' .
                            'Thank you for your business!'
            ];
        }
        else {
            $body = [
                'subject'  => 'Payment Cancelled',
                'greeting' => 'Hello ' . $detail->client->name . ',',
                'message'  => 'Your payment of ' . $detail->currency . ' ' . number_format($detail->total, 2) . ' for the ' . $detail->title . ' have been cancelled from the payment gateway server. ' . 
                            'Referene Code #' . $detail->ref_code . '. ' .
                            'Please forward this email to our admin teamn at ' . env('PRIMARY_MAIL') . '. ' .
                            'Our Admin Team will contact you back with further details. '.
                            'Thank you for your business!'
            ];
        }

        return EmailNotification::_storing((object) [
            'uuid'      => $detail->uuid,
            'client_id' => $detail->client_id,
            'email'     => $detail->email,
            'body'      => $body
        ]);
    }
}
