<?php

namespace App\Services\Backend;

use Exception;
use Illuminate\Support\Str;
use App\Models\PaymentEntry;
use App\Models\PaymentSetup;
use App\Models\PaymentHasClient;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentSetupCategory;
use App\Services\Backend\LogsService;
use App\Notifications\SendPaymentLink;
use App\Notifications\SendPaymentNotify;
use Illuminate\Support\Facades\Notification;
use App\Services\Backend\PaymentEntryService;
use App\Services\Backend\PaymentDetailService;
use Illuminate\Contracts\Encryption\DecryptException;

class PaymentSetupService{

    public static function _find($uuid)
    {
        return PaymentSetup::where('uuid', $uuid)->firstOrFail();
    }

    public static function _get($group_id = null)
    {
        return PaymentSetup::with('clients', 'categories')->orderBy('created_at', 'ASC');
    }

    public static function _find_with_trashed($uuid)
    {
        return PaymentSetup::withTrashed()->where('uuid', $uuid)->firstOrFail();
    }

    public static function _storing($req)
    {
        DB::beginTransaction();
        try{
            $model = new PaymentSetup();
            $model->title           = $req->title;
            $model->uuid            = Str::uuid()->toString();
            $model->total           = (float) $req->total;
            $model->currency        = $req->currency;
            $model->remarks         = $req->remarks;
            $model->contents        = $req->has('contents') ? json_encode($req->contents) : '';
            $model->is_active       = 10;
            $model->is_advance      = $req->has('is_advance') ? 10 : 0;
            $model->user_id         = auth()->user()->id;
            $model->payment_options = $req->has('payment_options') ? json_encode($req->payment_options) : '';
            $model->reference_date  = date('Y-m-d', strtotime($req->reference_date));
            $model->expire_date     = $req->expire_date ? date('Y-m-d', strtotime($req->expire_date)) : null;
            $model->no_of_payments  = $req->no_of_payments;
            $model->extended_days   = $req->extended_days;
            $model->recurring_type  = $req->recurring_type;
            $model->save();


            foreach($req->contents as $c){

                $category = new PaymentSetupCategory();
                $category->payment_setup_id = $model->id;
                $category->category_id  = $c['id'];
                $category->total   = str_replace(",", "", $c['amount']);
                $category->save();
            }

        }catch (Exception $e)
        {
            DB::rollBack();
            return $e;
        }
        LogsService::_set('Payment Setup - ' . $model->id . ' has been created', 'payment-setup');
        DB::commit();
        return $model->uuid;

    }

    public static function _storensend($req){

        $model = self::_storing($req);
        if($model){
            if(self::_sending(['new'],$model)){
              return true;  
            }
        }
        return false;

    }

    public static function _updating($req, $uuid)
    {
        $model = self::_find($uuid);

        if (!$model) return false;

        DB::beginTransaction();
        try{
            
            $model->title      = $req->title;
            $model->total      = (float) $req->total;
            $model->currency   = $req->currency;
            $model->remarks    = $req->remarks;
            $model->contents   = $req->has('contents') ? json_encode($req->contents) : '';
            $model->payment_options = $req->has('payment_options') ? json_encode($req->payment_options) : '';
            $model->recurring_type  = $req->recurring_type;
            $model->extended_days   = $req->extended_days;
            $model->update();

            $model->categories()->delete();

            foreach ($req->contents as $c) {
                $category = new PaymentSetupCategory();
                $category->payment_setup_id = $model->id;
                $category->category_id  = $c['id'];
                $category->total    = str_replace(",", "", $c['amount']);
                $category->save();
            }

        }
        catch (Exception $e)
        {
            DB::rollBack();
            dd($e);
            return $e;
        }
        DB::commit();
        LogsService::_set('Payment Setup - ' . $model->id . ' has been updated', 'payment-setup');
        return true;
    }

    public static function _change_status($uuid)
    {
        $model = self::_find($uuid);
        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);

        if ($model->update()) {
            LogsService::_set('Payment Setup - ' . $model->title . ' has been ' . ($model->is_active == 10 ? 'activated' : 'deactivated'), 'payment-setup');
            return true;
        }
        return false;
    }

        public static function _entries($uuid)
    {
        if ($model = self::_find($uuid)) {
            $entries = null;
            $rctype = config('app.addons.recurring_type.' . $model->recurring_type);

            if ($rctype == 'ONETIME') {
                $old_payment_date = null;
                $new_payment_date = $model->reference_date;
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
                    $ref_date = $model->reference_date;
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

    public static function _sending($entries, $uuid)
    {
        if ($data = self::_entries($uuid)) {

            $model = $data['model'];
            $notify = [
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => '',
                'entry'     => '',
                'uuid'      => ''
            ];

            if ($data['entries']) {
                $selected_entries = array_filter(array_map(function (array $item) use ($entries) {
                    if (in_array($item['uuid'], $entries)) {
                        return $item;
                    }
                },  $data['entries']));

                foreach ($selected_entries as $ent) {
                    $notify['uuid']  = $ent['uuid'];
                    $notify['entry'] = $ent['title'];
                    $notify['encrypt'] = self::_encrypting($model->uuid, $ent['uuid']);

                    Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
                    LogsService::_set('Payment Entry - ' . $ent['title'] . ' has been sent for setup - ' . $model->title, 'payment-entry');
                }
            }

             if (in_array('new', $entries)) {
                $new   = $data['new_entry'];
                $title = $data['rctype'] . ' PAYMENT (' . ($new['old_payment_date'] ? ($new['old_payment_date'] . ' to ') : '') . $new['new_payment_date'] . ')';
                foreach($model->clients as $client){
                    if ($entry = PaymentEntryService::_storing($model, $title, $client, $new)) {
                        $notify['uuid']  = $entry->uuid;
                        $notify['client_id']  = $client->client_id;
                        $notify['client']  = $client->client->name;
                        $notify['entry'] = $entry->title;
                        $notify['email'] = $client->client->email;
                        $notify['encrypt'] = self::_encrypting($model->uuid, $entry->uuid);
    
                        Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
                        LogsService::_set('Payment Entry - ' . $title . ' has been sent for Setup - ' . $model->title, 'payment-entry');
                    }else{
                        LogsService::_set('Payment Entry - ' . $title . ' could not created for Setup - ' . $model->title, 'payment-entry');
                    }
                    
                }
            }
            return true;
        }
        return false;
    }

    public static function _encrypting($setup_uuid, $entry_uuid)
    {
        return encrypt(config('app.addons.public_key') . '__' . $setup_uuid . '__' . $entry_uuid);
    }

    public static function _decrypting($encrypt)
    {
        try {
            return explode('__', decrypt($encrypt));
        } catch (DecryptException $e) {
            abort(401);
        }
    }

    public static function _validating($encrypt)
    {
        $params = self::_decrypting($encrypt);

        if (count($params) != 3) {
            return ['status' => 'link-invalid'];
        }

        $check_public_key   = $params[0];
        $current_public_key = config('app.addons.public_key');
        if ($check_public_key != $current_public_key) {
            return ['status' => 'link-invalid'];
        }

        $setup = self::_find_with_trashed($params[1]);

        if (!$setup) {
            return ['status' => 'link-unauthorised'];
        }

        $detail = null;
        $entry = PaymentEntry::where('uuid', $params[2])->where('is_expired',0)->first();
        if (!$entry) {
            $detail = PaymentDetailService::_find($params[2]);
            if (!$detail) {
                return ['status' => 'link-unauthorised'];
            }
        }

        // if ($setup->is_active != 10) {
        //     return [ 'status' => 'link-inactive', 'entry' => $entry, 'detail' => $detail ];
        // }

        if ($entry && $entry->is_active != 10) {
            return ['status' => 'link-inactive', 'entry' => $entry, 'detail' => $detail];
        }

        if ($detail && $detail->payment_status == config('app.addons.status_payment.COMPLETED')) {
            return ['status' => 200, 'entry' => $entry, 'detail' => $detail];
        }
        // dd($entry, $detail);
        return ['status' => 200, 'entry' => $entry, 'detail' => $detail];
    }

    public static function _alerting($advance_type, $setup)
    {
        $notify = [
            'advance_type' => $advance_type,
            'setup' => $setup
        ];
        Notification::route('mail', $setup->email)->notify(new SendPaymentNotify($notify));
        LogsService::_set('Payment Notify has been sent for Setup - ' . $setup->title, 'payment-notify');
    }
}
