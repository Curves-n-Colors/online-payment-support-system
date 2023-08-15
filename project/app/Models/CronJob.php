<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Notification;

use App\Notifications\SendCronJobLog;
use App\Models\CronJobPaymentLog;
use App\Models\CronJobNotifyLog;

class CronJob extends Model
{
    public static function _get_payment_log($rec_type)
    {
        return CronJobPaymentLog::where('recurring_type', $rec_type)->whereDate('created_at', now())->orderBy('created_at', 'DESC')->first();
    }

    public static function _save_payment_log($cron, $rec_type, $take, $count)
    {
        if ($cron) {
            $cron->start = $cron->start + $count;
            $cron->update();
        }
        else {
            $cron = new CronJobPaymentLog();
            $cron->recurring_type = $rec_type;
            $cron->start = $count;
            $cron->limit = $take;
            $cron->save();
        }
    }

    public static function _error_payment_log($rec_type, $skip, $error)
    {
        $notify = [
            'error_log' => $error,
            'action' => 'PAYMENT_LINK',
            'recurring_type' => $rec_type,
            'start' => $skip
		];
		Notification::route('mail', env('PRIMARY_MAIL'))->notify(new SendCronJobLog($notify));
    }

    public static function _get_notify_log($rec_type, $adv_type)
    {
        return CronJobNotifyLog::where('recurring_type', $rec_type)->where('advance_type', $adv_type)->whereDate('created_at', now())->orderBy('created_at', 'DESC')->first();
    }

    public static function _save_notify_log($cron, $rec_type, $adv_type, $take, $count)
    {
        if ($cron) {
            $cron->start = $cron->start + $count;
            $cron->update();
        }
        else {
            $cron = new CronJobNotifyLog();
            $cron->recurring_type = $rec_type;
            $cron->advance_type = $adv_type;
            $cron->start = $count;
            $cron->limit = $take;
            $cron->save();
        }
    }

    public static function _error_notify_log($rec_type, $adv_type, $skip, $error)
    {
        $notify = [
            'error_log' => $error,
            'action' => 'NOTIFY',
            'recurring_type' => $rec_type,
            'advance_type' => $adv_type,
            'start' => $skip
		];
		Notification::route('mail', env('PRIMARY_MAIL'))->notify(new SendCronJobLog($notify));
    }

    public static function _flush_log()
    {
        CronJobPaymentLog::truncate();
        CronJobNotifyLog::truncate();
    }

    public static function _validating($encrypt, $check)
    {
        $value = self::_decrypting($encrypt);
        return ($value == $check);
    }

    public static function _decrypting($encrypt)
    {
        try {
            return decrypt($encrypt);
        } 
        catch (DecryptException $e) {
            abort(401);
        }
    }
}
