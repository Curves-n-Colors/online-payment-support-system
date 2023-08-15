<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentNibl extends Model
{
    protected $table = 'payment_nibl';

    protected $fillable = [
        'refund_amount', 'refund_status'
    ];

    public static function _storing($entry_uuid, $transaction)
    {
        $model = new self();
        $model->uuid              = $entry_uuid;
        $model->bank_ref_id       = $transaction['bank_ref_id'];
        $model->ipg_txn_id        = $transaction['ipg_txn_id'];
        $model->mer_ref_id        = $transaction['mer_txn_id'];
        $model->server_time       = date('Y-m-d H:i:s', strtotime($transaction['server_time']));
        $model->masked_acc_number = $transaction['acc_no'];
        $model->card_holder_name  = $transaction['name'];
        $model->fail_reason       = $transaction['reason'];
        $model->auth_code         = $transaction['auth_code'];
        $model->status            = $transaction['txn_status'] == 'ACCEPTED'
            ? config('app.addons.status_payment.COMPLETED')
            : config('app.addons.status_payment.CANCELLED');

        if ($model->save()) {
            return $model;
        }
        return false;
    }
}
