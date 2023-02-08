<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\PaymentEntry;
use Illuminate\Console\Command;
use App\Services\Backend\LogsService;
use App\Notifications\SendPaymentLink;
use App\Notifications\SendSuspendMail;
use Illuminate\Support\Facades\Notification;
use App\Services\Backend\PaymentSetupService;
use App\Notifications\SendPaymentLinkExtended;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email according to the expire date.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $extended_day = system_extended_day();
        $email_day = system_email_send_day();

        $email_collection = PaymentEntry::where('is_expired', 0)->where('is_active',10)->where('is_completed',0)->get();

        if(count($email_collection)>0)
        {
            foreach($email_collection as $entry)
            {
                $timing = ' - '.$email_day.' day';
                $extend_time = ' + '.$extended_day.' day';

                $notify = [
                'client_id' => $entry->client->id,
                'client'    => $entry->client->name,
                'email'     => $entry->client->email,
                'currency'  => $entry->currency,
                'total'     => number_format($entry->total, 2),
                'encrypt'   => PaymentSetupService::_encrypting($entry->setup->uuid, $entry->uuid),
                'entry'     => $entry->title,
                'uuid'      => $entry->uuid
                ];
                
                if(date('Y-m-d') >= (date('Y-m-d', strtotime($entry->start_date . $timing))) and date('Y-m-d') <= $entry->start_date){
                    //SEND PAYMENT LINK
                    Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
                    LogsService::_set('Payment Entry - ' . $entry->title . ' has been sent for setup E-mail '.$entry->client->email.' - ' . $entry->setup->title, 'payment-entry');

                }elseif( date('Y-m-d') >= $entry->start_date and date('Y-m-d') <= (date('Y-m-d', strtotime($entry->start_date . $extend_time)))){
                    //ENTENDED 
                    $entry->is_expired = 10;
                    $entry->update();

                    Notification::route('mail', $notify['email'])->notify(new SendPaymentLinkExtended($notify));
                    LogsService::_set('Payment Entry (EXTENDED) - ' . $entry->title . ' has been sent for setup E-mail '.$entry->client->email.' - ' . $entry->setup->title, 'payment-entry');

                }elseif(date('Y-m-d') > $entry->start_date){
                    //EXPIRED;
                    $entry->is_expired = 10;
                    $entry->update();

                    Notification::route('mail', $notify['email'])->notify(new SendSuspendMail($notify));
                    LogsService::_set('Payment Entry - ' . $entry->title . ' has expired of E-mail '.$entry->client->email.' - ' . $entry->setup->title, 'payment-entry');
                }
                $this->info('Successfully sent scheduled mail.');

                // if(now_day>=start_day-emailday and now_day<=start_day){
                //     send Email for payment link;
                // }

                // if(now>startday and now<=startday+extendDAY){
                //     send email with extend and payment link.
                // }

                // if(now>start_day){
                //     send expire mail;
                // }
                // $timing =  $timing = ' + '.$email_day.' day';
                // date('Y-m-d', strtotime($email . $timing));
                // dd(Carbon::now()->addDay(10));
                // dd('EMAIL');
            }

        }

    }
}
