<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Str;

use App\Notifications\SendPaymentLink;
use App\Notifications\SendPaymentNotify;
use App\Notifications\SendPaymentStatus;

use App\Models\PaymentEntry;
use App\Models\Logs;

class PaymentSetup extends Model
{
    use SoftDeletes;

    protected $table        = 'payment_setups';
    protected $dates        = ['deleted_at'];
    protected $hidden       = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function entries()
    {
        return $this->hasMany('App\Models\PaymentEntry', 'payment_setup_id')->orderBy('id', 'DESC');
    }

    public function details()
    {
        return $this->hasMany('App\Models\PaymentDetail', 'payment_setup_id')->orderBy('id', 'DESC');
    }

    public static function _storing($req)
    {
        $model = new self();
        $model->title      = $req->title;
        $model->client_id  = $req->client;
        $model->email      = $req->email;
        $model->uuid       = Str::uuid()->toString();
        $model->total      = (float) $req->total;
        $model->currency   = $req->currency;
        $model->remarks    = $req->remarks;
        $model->contents   = $req->has('contents') ? json_encode($req->contents) : '';
        $model->is_active  = $req->has('is_active') ? 10 : 0;
        $model->is_advance = $req->has('is_advance') ? 10 : 0;
        $model->user_id    = auth()->user()->id;
        $model->payment_options = $req->has('payment_options') ? json_encode($req->payment_options) : '';
        $model->reference_date  = date('Y-m-d', strtotime($req->reference_date));
        $model->recurring_type  = $req->recurring_type;

        if ($model->save()) {
            Logs::_set('Payment Setup - ' . $model->title . ' has been created', 'payment-setup');
            return $model;
        }
        return false;
    }

    public static function _storensend($req)
    {
        $model = new self();
        $model->title      = $req->title;
        $model->client_id  = $req->client;
        $model->email      = $req->email;
        $model->uuid       = Str::uuid()->toString();
        $model->total      = (float) $req->total;
        $model->currency   = $req->currency;
        $model->remarks    = $req->remarks;
        $model->contents   = $req->has('contents') ? json_encode($req->contents) : '';
        $model->is_active  = $req->has('is_active') ? 10 : 0;
        $model->is_advance = $req->has('is_advance') ? 10 : 0;
        $model->user_id    = auth()->user()->id;
        $model->payment_options = $req->has('payment_options') ? json_encode($req->payment_options) : '';
        $model->reference_date  = date('Y-m-d');
        $model->recurring_type  = 1; //$req->recurring_type;

        if ($model->save()) {

            $notify = [
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->email,
                'currency'  => $model->currency,
                'total'     => number_format($model->total, 2),
                'encrypt'   => '',
                'entry'     => '',
                'uuid'      => ''
            ];

            $title = 'ONETIME PAYMENT (' . $model->reference_date . ')';

            if ($entry = PaymentEntry::_storing($model, $title, $model->reference_date)) {
                $notify['uuid']  = $entry->uuid;
                $notify['entry'] = $entry->title;
                $notify['encrypt'] = self::_encrypting($model->uuid, $entry->uuid);

                Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
                Logs::_set('Payment Entry - ' . $title . ' has been sent for Setup - ' . $model->title, 'payment-entry');
                $model->delete();
            } else {
                Logs::_set('Payment Entry - ' . $title . ' could not created for Setup - ' . $model->title, 'payment-entry');
            }
            Logs::_set('Payment Setup - ' . $model->title . ' has been created', 'payment-setup');
            return true;
        }
        return false;
    }

    public static function _updating($req, $uuid)
    {
        $model = self::where('uuid', $uuid)->first();
        if (!$model) return false;

        $model->title      = $req->title;
        $model->client_id  = $req->client;
        $model->email      = $req->email;
        $model->uuid       = Str::uuid()->toString();
        $model->total      = (float) $req->total;
        $model->currency   = $req->currency;
        $model->remarks    = $req->remarks;
        $model->contents   = $req->has('contents') ? json_encode($req->contents) : '';
        // $model->is_active    = $req->has('is_active') ? 10 : 0;
        $model->is_advance      = 10; // $req->has('is_advance') ? 10 : 0;
        $model->payment_options = $req->has('payment_options') ? json_encode($req->payment_options) : '';
        $model->reference_date  = date('Y-m-d', strtotime($req->reference_date));
        $model->recurring_type  = $req->recurring_type;

        if ($model->update()) {
            Logs::_set('Payment Setup - ' . $model->title . ' has been updated', 'payment-setup');
            return $model;
        }
        return false;
    }

    public static function _change_status($uuid)
    {
        $model = self::where('uuid', $uuid)->first();
        if (!$model) return -1;

        $model->is_active = ($model->is_active == 10 ? 0 : 10);

        if ($model->update()) {
            Logs::_set('Payment Setup - ' . $model->title . ' has been ' . ($model->is_active == 10 ? 'activated' : 'deactivated'), 'payment-setup');
            return true;
        }
        return false;
    }

    public static function _entries($uuid)
    {
        if ($model = PaymentSetup::where('uuid', $uuid)->first()) {
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
                } else {
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
                'client_id' => $model->client->id,
                'client'    => $model->client->name,
                'email'     => $model->email,
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
                    Logs::_set('Payment Entry - ' . $ent['title'] . ' has been sent for setup - ' . $model->title, 'payment-entry');
                }
            }

            if (in_array('new', $entries)) {
                $new   = $data['new_entry'];
                $date  = $new['new_payment_date'];
                $title = $data['rctype'] . ' PAYMENT (' . ($new['old_payment_date'] ? ($new['old_payment_date'] . ' to ') : '') . $new['new_payment_date'] . ')';

                if ($entry = PaymentEntry::_storing($model, $title, $date)) {
                    $notify['uuid']  = $entry->uuid;
                    $notify['entry'] = $entry->title;
                    $notify['encrypt'] = self::_encrypting($model->uuid, $entry->uuid);

                    Notification::route('mail', $notify['email'])->notify(new SendPaymentLink($notify));
                    Logs::_set('Payment Entry - ' . $title . ' has been sent for Setup - ' . $model->title, 'payment-entry');
                    $model->delete();
                } else {
                    Logs::_set('Payment Entry - ' . $title . ' could not created for Setup - ' . $model->title, 'payment-entry');
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

        $setup = self::withTrashed()->where('uuid', $params[1])->first();

        if (!$setup) {
            return ['status' => 'link-unauthorised'];
        }

        $detail = null;
        $entry = PaymentEntry::where('uuid', $params[2])->first();

        if (!$entry) {
            $detail = PaymentDetail::where('uuid', $params[2])->first();
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
        Logs::_set('Payment Notify has been sent for Setup - ' . $setup->title, 'payment-notify');
    }
}
