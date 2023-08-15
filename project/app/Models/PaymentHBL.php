<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentNotify;

class PaymentHBL extends Model
{
    protected $table = 'payment_hbl';

    public static function _create($entry, $request)
    {
        $currency_code = config('app.api.hbl.currency_code');
        
        $hbl = new self();
        $hbl->uuid               =  $entry->uuid;
        $hbl->amount             =  $request['Amount'] * 100;
        $hbl->eci                =  $request['Eci'];
        // $hbl->currency_code      =  $currency_code[$entry['currency']];
        $hbl->invoice_no         =  $request['invoiceNo'];
        $hbl->tran_ref           =  $request['tranRef'];
        $hbl->response_code      =  $request['respCode'];
        $hbl->approval_code      =  $request['approvalCode'];
        $hbl->fraud_code         =  $request['fraudCode'];
        $hbl->transaction_status =  $request['Status'];
        $hbl->transaction_at     =  date('Y-m-d H:i:s', strtotime($request['dateTime']));
        $hbl->status             =  $request['Status'] == config('app.addons.payment_options.hbl.status.success_status')
            ? config('app.addons.status_payment.COMPLETED')
            : config('app.addons.status_payment.CANCELLED');
        $hbl->save();

        return $hbl;
    }

    public static function _check_backend($req)
    {
        $notify = (object) [
            'subject' => 'HBL Payment Backend',
            'body' => json_encode($req)
        ];
        Notification::route('mail', config('app.api.admin_email'))->notify(new PaymentNotify($notify));
    }
}
