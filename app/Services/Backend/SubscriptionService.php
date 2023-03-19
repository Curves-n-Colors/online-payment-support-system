<?php

namespace App\Services\Backend;

use Exception;
use Illuminate\Support\Str;
use App\Models\PaymentHasClient;
use Illuminate\Support\Facades\DB;
use App\Notifications\SendPaymentLink;
use Illuminate\Support\Facades\Notification;
use App\Services\Backend\PaymentSetupService;

class SubscriptionService
{
    public static function _find($uuid)
    {
        return PaymentHasClient::with('details', 'client', 'entry')->where('uuid', $uuid)->firstOrFail();
    }

    public static function _get($group_id = null)
    {
        return PaymentHasClient::with('details', 'client')->orderBy('created_at', 'DESC');
    }

    public static function _storing($req)
    {
        DB::beginTransaction();
        try{
            if(count($req->client)>0){
                foreach($req->client as $client){
                    $model                   = new PaymentHasClient();
                    $model->payment_setup_id = $req->subscription_id;
                    $model->client_id        = $client;
                    $model->uuid             = Str::uuid()->toString();
                    $model->reference_date   = $req->reference_date ? date('Y-m-d', strtotime($req->reference_date)) : null;
                    $model->expire_date      = $req->expire_date ? date('Y-m-d', strtotime($req->expire_date)) : null;
                    $model->is_active        = $req->has('is_active') ? 10 : 0;
                    $model->save();

                    if(isset($req->send_email)){
                        self::_sending($model->uuid);
                    }
                }
            }


        }catch (Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
   }

   public static function _get_payment_setup($payment)
   {
    
    if (isset($payment->details) and isset($payment->client)) {
            $model = $payment->details;
            
            $entries = null;
            $rctype = config('app.addons.recurring_type.' . $model->recurring_type);

            if ($rctype == 'ONETIME') {
                $old_payment_date = null;
                $new_payment_date = $payment->reference_date;
            } else {
                if ($rctype == 'YEARLY') {
                    $timing = ' + 1 year';
                } else if ($rctype == 'MONTHLY') {
                    $timing = ' + 1 month';
                } else if ($rctype == 'QUARTERLY') {
                    $timing = ' + 4 month';
                } else if ($rctype == 'WEEKLY') {
                    $timing = ' + 7 day';
                }
                 else {
                    $timing = '';
                }

                $new1 = null;
                $new2 = null;
                $entries = $model->entries->toArray() ?? null;
                $details = $model->details->toArray() ?? null;

                if ($entries) {
                    $new1 = $entries[0]['payment_date'];
                    $entries = array_map(function (array $item) {
                        return ['title' => $item['title'], 'uuid' => $item['uuid']];
                    },  $entries);
                }

                if ($details) {
                    $new2 = $details[0]['payment_date'];
                }

                if ($new1 && $new2) {
                    if ($new1 > $new2) {
                        $ref_date = $new1;
                    } else {
                        $ref_date = $new2;
                    }
                } else if ($new1) {
                    $ref_date = $new1;
                } else if ($new2) {
                    $ref_date = $new2;
                } else {
                    $ref_date = $payment->reference_date;
                }

                $new_payment_date = date('Y-m-d', strtotime($ref_date . $timing));
                $old_payment_date = date('Y-m-d', strtotime($ref_date));
            }
            return [
                'entries'   => $entries,
                'new_entry' => ['old_payment_date' => $old_payment_date, 'new_payment_date' => $new_payment_date],
                'model'  => $model,
                'rctype' => $rctype
            ];
        }
        return false;
   }

   public static function _sending($uuid)
   {
       $subscription  = self::_find($uuid);
       $entries = [isset($subscription->entry)?$subscription->entry->uuid:'new'];
        if ($data = self::_get_payment_setup($subscription)) {
            $client = $subscription->client; //CLIENT DETAILS
            $model = $data['model'];

            $notify = [
                'currency'  => $model->currency,
               
                'encrypt'   => '',
                'entry'     => '',
                'uuid'      => ''
            ];
            
            if (count($data['entries'])>0) {
                $selected_entries = array_filter(array_map(function (array $item) use ($entries) {
                    if (in_array($item['uuid'], $entries)) {
                        return $item;
                    }
                },  $data['entries']));
                
               
                foreach ($selected_entries as $ent) {
                    $notify['uuid']  = $ent['uuid'];
                    $notify['client_id']  = $client->id;
                    $notify['total']     = number_format($ent['total'], 2);
                    $notify['client']  = $client->name;
                    $notify['entry'] = $ent['title'];
                    $notify['email'] = $client->email;
                    $notify['encrypt'] = self::_encrypting($model->uuid, $ent['uuid']);

                    Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
                    LogsService::_set('Payment Entry - ' . $ent['title'] . ' has been sent for setup - ' . $model->title, 'payment-entry');
                }
            }
                if (in_array('new', $entries)) {
                    $new   = $data['new_entry'];
                    $title = $data['rctype'] . ' PAYMENT (' . ($new['old_payment_date'] ? ($new['old_payment_date'] . ' to ') : '') . $new['new_payment_date'] . ')';
                    
                    // dd($new);
                    // foreach($model->clients as $client){
                    if ($entry = PaymentEntryService::_storing($model, $title, $client, $new,  $subscription->id)) {
                        $notify['uuid']  = $entry->uuid;
                        $notify['client_id']  = $client->id;
                        $notify['total']     = number_format($entry->total, 2);
                        $notify['client']  = $client->name;
                        $notify['entry'] = $entry->title;
                        $notify['email'] = $client->email;
                        $notify['encrypt'] = self::_encrypting($model->uuid, $entry->uuid);
    
                        Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
                        LogsService::_set('Payment Entry - ' . $title . ' has been sent for Setup - ' . $model->title, 'payment-entry');
                    }else{
                        LogsService::_set('Payment Entry - ' . $title . ' could not created for Setup - ' . $model->title, 'payment-entry');
                    }
                    
                // }
            }
            return true;
        }
        return false;
   }

    public static function _encrypting($setup_uuid, $entry_uuid)
    {
        return encrypt(config('app.addons.public_key') . '__' . $setup_uuid . '__' . $entry_uuid);
    }

}
