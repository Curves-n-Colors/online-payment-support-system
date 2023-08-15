<?php

namespace App\Services\Backend;
use App\User;
use Illuminate\Support\Str;
use App\Models\PaymentEntry;
use App\Models\PaymentSetup;
use App\Models\PaymentEntryCategory;
use App\Notifications\SendExtendMail;
use App\Notifications\SendPaymentLink;
use App\Notifications\SendSuspendMail;
use App\Notifications\SendReactivationMail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendReactiveMailToAdmin;

class PaymentEntryService
{
    public static function _find($uuid)
    {
        return PaymentEntry::where('uuid', $uuid)->firstOrFail();
    }

    public static function _find_min_uuid($min_uuid)
    {
        return PaymentEntry::where('min_uuid', $min_uuid)->firstOrFail();
    }

    public static function _get($group_id = null)
    {
        return PaymentEntry::orderBy('created_at', 'DESC');
    }

    public static function _change_status($uuid)
    {
        $model = self::_find($uuid);
        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);

        if ($model->update()) {
            LogsService::_set('Payment Entry - ' . $model->title . ' has been ' . ($model->is_active == 10 ? 'activated' : 'deactivated') . ' for Setup - ' . $model->setup->title, 'payment-entry');
            return true;
        }
        return false;
    }

    public static function _storing($data, $title, $client, $date, $subscription_id)
    {
        $vat_rate = vat_rate();
        $vat =($vat_rate/100) * $data->total;
        $uuid = Str::uuid()->toString();
        $model = new PaymentEntry();
        $model->payment_setup_id = $data->id;
        $model->title            = $title;
        $model->client_id        = $client->id;
        $model->email            = $client->email;
        $model->uuid             = $uuid;
        $model->min_uuid         = last(explode('-', $uuid));
        $model->sub_total        = $data->total;
        $model->vat              = $vat;
        $model->total            = $vat + $data->total;
        $model->currency         = $data->currency;
        $model->contents         = $data->contents;
        $model->is_active        = 10;
        $model->subscription_id  = $subscription_id;
        $model->user_id          = auth()->check() ? auth()->user()->id : 0;
        $model->payment_options  = $data->payment_options;
        $model->payment_date     = NULL;//date('Y-m-d', strtotime($date));
        $model->start_date       = date('Y-m-d', strtotime($date['old_payment_date']));
        $model->end_date         = date('Y-m-d', strtotime($date['new_payment_date']));
        
        if ($model->save()) {
            if (count($model->setup->categories) > 0) {
                foreach ($model->setup->categories as $c) {
                    $category = new PaymentEntryCategory();
                    $category->payment_entry_id = $model->id;
                    $category->category_id  = $c->category_id;
                    $category->total   = $c->total;
                    $category->save();
                }
            }

            LogsService::_set('Payment Entry - ' . $model->title . ' has been created for Setup - ' . $data->title, 'payment-entry');
            return $model;
        }
        return false;
    }

    public static function _sending($uuid)
    {
        if ($model = self::_find($uuid)) {
            $notify = [
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->client->email,
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => PaymentSetupService::_encrypting($model->setup->uuid, $model->uuid),
                'entry'     => $model->title,
                'uuid'      => $model->uuid
            ];

            Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
            LogsService::_set('Payment Entry - ' . $model->title . ' has been sent for setup - ' . $model->setup->title, 'payment-entry');
            return true;
        }
        return false;
    }

    public static function _suspend_status_mail($uuid)
    {
        if ($model = self::_find($uuid)) {
            $notify = [
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->email,
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => PaymentSetupService::_encrypting($model->setup->uuid, $model->uuid),
                'entry'     => $model->title,
                'uuid'      => $model->uuid
            ];

            //Suspend Payment Entry
            if( $model->is_active == 10)
            {
                Notification::route('mail', $notify['email'])->notify(new SendSuspendMail($notify));
                LogsService::_set('Payment Entry - ' . $model->title . ' has been suspended - ' . $model->setup->title.' - For Client - ' .  $model->client->name , 'payment-entry');
                $model->is_active = 0;
                $model->save();
                return [
                    'status' =>true,
                    'msg'    =>"Service suspended & Mail sent to client"
                ];

            }else{
                // Reactivate Client
                Notification::route('mail', $notify['email'])->notify(new SendReactivationMail($notify));
                LogsService::_set('Payment Entry - ' . $model->title . ' has been reactivated - ' . $model->setup->title.' - For Client - ' .  $model->client->name , 'payment-entry');

                $model->is_active  = 10;
                $model->is_expired = 0;
                $model->save();
                return [
                    'status' =>true,
                    'msg'    =>"Payment entry has  reactivated"
                ];
            }


        };

        return [
            'status' =>false,
            'msg'    =>" "
        ];
    }

    public static function _reactivate_mail_to_client($request,$uuid)
    {
        if ($model = self::_find($uuid)) {

                //Create a new entry after reactivation
                $model->deactivate_remark = $request->deactivate_remark;
                $model->is_payment_deactivate = 10;
                $model->save();

                //create new entry after reactivation
                 $start_date =  $request->reactivate_date;
                 $end_date   = null;
                 $rctype = config('app.addons.recurring_type.' . $model->setup->recurring_type);
                 if ($rctype == 'ONETIME') {
                     $end_date   = null;

                } else {
                    if ($rctype == 'YEARLY') {
                        $timing = ' + 1 year';
                    } else if ($rctype == 'MONTHLY') {
                        $timing = ' + 1 month';
                    } else {
                        $timing = '';
                    }
                    $end_date = date('Y-m-d', strtotime($start_date . $timing));
                }

                $new_entry = new PaymentEntry();
                $new_entry->payment_setup_id = $model->payment_setup_id;
                $new_entry->title            = $rctype. ' PAYMENT (' . $start_date . ' to ' . $end_date . ')';
                $new_entry->user_id          = $model->user_id;
                $new_entry->client_id        = $model->client_id;
                $new_entry->email            = $model->email;
                $new_entry->uuid             = Str::uuid()->toString();
                $new_entry->is_active        = 10;
                $new_entry->contents         = $model->contents;
                $new_entry->total            = $model->total;
                $new_entry->currency         = $model->currency;
                $new_entry->payment_options = $model->payment_options;
                $new_entry->payment_date     = null;
                $new_entry->start_date       = $request->reactivate_date;
                $new_entry->end_date         = $end_date;
                $new_entry->save();

                $notify = [
                    'client_id' => $new_entry->client->id,
                    'client'    => $new_entry->client->name,
                    'email'     => $new_entry->email,
                    'currency'  => $new_entry->currency,
                    'total'     => number_format($new_entry->total, 2),
                    'encrypt'   => PaymentSetupService::_encrypting($new_entry->setup->uuid, $new_entry->uuid),
                    'entry'     => $new_entry->title,
                    'uuid'      => $new_entry->uuid
                ];

                Notification::route('mail', $notify['email'])->notify(new SendReactivationMail($notify));
                LogsService::_set('Payment Entry - ' . $model->title . ' reactivate mail send for  - ' . $model->setup->title.' - For Client - ' .  $model->client->name , 'payment-entry');
                $model->save();
                return [
                    'status' =>true,
                ];
        };

        return [
            'status' =>false,
            'msg'    =>" "
        ];
    }


    public static function _copying($uuid)
    {
        if ($model = self::_find($uuid)) {
            LogsService::_set('Payment Entry - ' . $model->title . ' has been copied for setup - ' . $model->setup->title, 'payment-entry');
            return route('pay.index', [PaymentSetupService::_encrypting($model->setup->uuid, $model->uuid)]);
        }
        return false;
    }

    public static function _deleting($uuid)
    {
        if ($model = self::_find($uuid)) {
            $model_title = $model->title;
            $setup_title = $model->setup->title;
            if ($model->delete()) {
                LogsService::_set('Payment Entry - ' . $model_title . ' has been deleted for Setup - ' . $setup_title, 'payment-entry');
                return true;
            }
        }
        return false;
    }

    public static function _update_new_entry($uuid)
    {
        if ($model = self::_find($uuid)) {
            $time = self::_entry_timing($model->setup->recurring_type);

            $payment_setup_model = $model->subscription;

            $new_start_date = $model->end_date;
            $new_end_date = date('Y-m-d', strtotime($new_start_date . $time));

            if($new_end_date > $payment_setup_model->expire_date){
                $model->is_expired   = 10;
                $model->is_active    = 0;
                $model->is_completed = 10;
            }else{
                $uuid = Str::uuid()->toString();
                $old_title  = $model->title;
                $index = strpos($old_title,"(");
                $sub_text = substr($old_title,0,$index);

                $new_title = $sub_text. ' ( '.$new_start_date.' to '.$new_end_date.' )';

                $model->start_date = $new_start_date;
                $model->end_date = $new_end_date;
                $model->uuid     = $uuid;
                $model->min_uuid = last(explode('-',$uuid));
                $model->title   = $new_title;
                $model->total   = $model->sub_total + $model->vat;
            }
            $model->update();

            return true;
           //dd($model); Payment Entry
           //dd($model->setup); PAYMENT SETUP DETAILS
        }
        return false;
    }

    public static function _entry_timing($reccuring_type)
    {
        $rctype = config('app.addons.recurring_type.' . $reccuring_type);
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
            $timing = ' + 1 day';
        }
        return $timing;
    }

    public static function _reactivate_request_mail($entry)
    {
        if ($model = self::_find($entry['uuid'])){
            $model->is_reactivate_request = 10;
            $model->save();

            $notify = [
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->client->email,
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => PaymentSetupService::_encrypting($model->setup->uuid, $model->uuid),
                'entry'     => $model->title,
                'uuid'      => $model->uuid
            ];

            $admin_mail = User::where(['is_super'=>10,'is_active'=>10])->first()->email;
            Notification::route('mail', $admin_mail)->notify(new SendReactiveMailToAdmin($notify));
            LogsService::_set('Service Reactivation Request for - ' . $model->setup->title . ' has been received from client - ' . $model->client->name, 'payment-entry-reactive');
            return true;
        }

        return false;
    }

    public static function _extend_mail($uuid)
    {

        if ($model = self::_find($uuid)){
            $model->is_extended = 10;
            $model->save();

            $extended_days = ' + '.$model->setup->extended_days.' day';
            $end_date = $model->end_date;
            $new_payment_date = date('Y-m-d', strtotime($end_date . $extended_days));

            $notify = [
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->client->email,
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => PaymentSetupService::_encrypting($model->setup->uuid, $model->uuid),
                'entry'     => $model->title,
                'uuid'      => $model->uuid,
                'extended_date' => $new_payment_date
            ];

            Notification::route('mail', $notify['email'])->notify(new SendExtendMail($notify));
            LogsService::_set('Payment date  extended for - ' . $model->setup->title . ' for client - ' . $model->client->name, 'payment-entry-reactive');
            return true;
        }

        return false;
    }

    public static function _approve($req, $uuid)
    {
        if($model = self::_find($uuid)){
            $detail = [
                'type'   => strtoupper($req->payment_type),
                'status' => 10
            ];

            if($req->has('is_advance')){
                $detail['advance_month'] = $req->selected_month;
                
                $end_date = date('Y-m-d', strtotime($model->start_date . ' + '.$detail['advance_month'].' month'));

                $amount = $model->total*$detail['advance_month'];

                $title = $model->title;
                $index = strpos($title,"(");
                $type= substr($title,0,$index);

                $title = $type.'('.$model->start_date.' TO '.$end_date.')';
                $model->title       = $title;
                $model->end_date    = $end_date;
                $model->total       = $amount;
                $model->update();
               
            }

            PaymentDetailService::_storing($model, $detail);
            PaymentEntryService::_update_new_entry($model->uuid);

            return true;
        }
        return false;
    }

}
