<?php

namespace App\Services\Backend;

use App\Models\PayNibl;


use Exception;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\PaymentDetail;
use App\Models\PaymentDetailsCategory;
use App\Services\Backend\LogsService;
use App\Notifications\SendPaymentStatus;
use Illuminate\Support\Facades\Notification;



class PaymentDetailService{
    public static function _find($uuid)
    {
        return PaymentDetail::where('uuid', $uuid)->firstOrFail();
    }

    public static function _get($group_id = null)
    {
        return PaymentDetail::orderBy('created_at', 'DESC');
    }
    

    public static function _storing($data, $detail)
    {
        DB::beginTransaction();
        try {
            $model = new PaymentDetail();
            $model->payment_setup_id = $data->payment_setup_id;
            $model->title            = $data->title;
            $model->client_id        = $data->client_id;
            $model->email            = $data->email ?? NULL;
            $model->uuid             = $data->uuid;
            $model->min_uuid         = $data->min_uuid;
            $model->sub_total        = $data->sub_total;
            $model->vat              = $data->vat;
            $model->total            = $data->total;
            $model->currency         = $data->currency;
            $model->contents         = $data->contents;
            $model->payment_date     = date('Y-m-d', strtotime($data->payment_date));
            $model->payment_status   = $detail['status'];
            $model->payment_type     = $detail['type'];
            $model->is_advance       = isset($detail['advance_month']) ?? 0;
            $model->advance_months   = isset($detail['advance_month']) ? $detail['advance_month'] : 0;

            if ($model->save()) {
                $model->ref_code = config('app.addons.ref_code_prefix') . '-' . $model->id;
                $model->update();

                if (count($data->categories) > 0) {
                    foreach ($data->categories as $c) {
                        $category = new PaymentDetailsCategory();
                        $category->payment_detail_id = $model->id;
                        $category->category_id  = $c->category_id;
                        $category->total   = $c->total;
                        $category->save();
                    }
                }
                Notification::route('mail', $model->email)->notify(new SendPaymentStatus($model));
                LogsService::_set('Payment Detail - ' . $model->title . ' has been created for Setup - ' . $data->setup->title, 'payment-detail');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }


    public static function _refunding($req, $uuid)
    {
        if ($detail = self::_find($uuid)) {
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
