<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

use App\Notifications\SendPaymentStatus;
use App\Models\Logs;
use App\Models\PayNibl;

use PDF;

class PaymentDetail extends Model
{
    protected $table = 'payment_details';

    protected $fillable = [
        'payment_status'
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function setup()
    {
        return $this->belongsTo('App\Models\PaymentSetup', 'payment_setup_id')->withTrashed();
    }

    public function payment_nibl()
    {
        return $this->hasOne('App\Models\PaymentNibl', 'uuid', 'uuid');
    }

    public function payment_khalti()
    {
        return $this->hasOne('App\Models\PaymentKhalti', 'uuid', 'uuid');
    }

    public static function _storing($data, $detail)
    {
        $model = new self();
        $model->payment_setup_id = $data->setup->id;
        $model->title            = $data->title;
        $model->client_id        = $data->client_id;
        $model->email            = $data->email;
        $model->uuid             = $data->uuid;
        $model->total            = $data->total;
        $model->currency         = $data->currency;
        $model->contents         = $data->contents;
        $model->payment_date     = date('Y-m-d', strtotime($data->payment_date));
        $model->payment_status   = $detail['status'];
        $model->payment_type     = $detail['type'];

        if ($model->save()) {
            $model->ref_code = config('app.addons.ref_code_prefix') . '-' . $model->id;
            $model->update();

            Notification::route('mail', $model->email)->notify(new SendPaymentStatus($model));
            Logs::_set('Payment Detail - ' . $model->title . ' has been created for Setup - ' . $data->setup->title, 'payment-detail');
            return true;
        }
        return false;
    }

    public static function _refunding($req, $uuid)
    {
        if ($detail = PaymentDetail::where('uuid', $uuid)->first()) {
            if ($detail->payment_type == 'NIBL') {
                $params = (array) $detail->payment_nibl->only(['mer_ref_id', 'ipg_txn_id', 'status']);

                if ($params['status'] == config('app.addons.status_payment.COMPLETED')) {

                    $params['ref_code'] = $detail->ref_code;
                    if (isset($req['is_full'])) {
                        $params['amount'] = $detail->total;
                        $params['is_full'] = true;
                    } else {
                        $params['amount'] = (float) $req['refund_amount'];
                        $params['is_full'] = false;
                    }

                    if ($params['amount'] <= $detail->total) {
                        $pay = new PayNibl();
                        $check = $pay->refund($detail, $params);

                        if ($check['status'] == 200) {
                            $detail->update([
                                'payment_status' => config('app.addons.status_payment.REFUNDED')
                            ]);
                            $detail->payment_nibl->update([
                                'refund_amount' => $params['amount'],
                                'refund_status' => 10
                            ]);

                            Notification::route('mail', $detail->email)->notify(new SendPaymentStatus($detail));
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function _invoicing($uuid)
    {
        if ($detail = PaymentDetail::where('uuid', $uuid)->where('payment_status', config('app.addons.status_payment.COMPLETED'))->first()) {
            $logo = 'https://climbalaya.com/images/logo/logo.png';
            $pdf = PDF::setOptions(['dpi' => 150, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); // , 'defaultFont' => 'sans-serif', 'images' => true
            $pdf->loadView('pdf.invoice', ['logo' => $logo, 'data' => $detail]);
            return $pdf->download('invoice.pdf');
        }
        return false;
    }
}
