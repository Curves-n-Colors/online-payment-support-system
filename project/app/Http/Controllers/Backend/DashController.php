<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Client;
use App\Models\PaymentSetup;
use App\Models\PaymentEntry;
use App\Models\PaymentDetail;
use Illuminate\Support\Facades\Mail;

class DashController extends Controller
{
    public function index()
    {
        $completed = config('app.addons.status_payment.COMPLETED');

        $count_client    = Client::where('is_active', 10)->count();
        $count_setup     = PaymentSetup::withTrashed()->where('is_active', 10)->count();
        $count_entry     = PaymentEntry::where('is_active', 10)->count();
        $count_detail    = PaymentDetail::where('payment_status', $completed)->count();
        $count_link_sent = PaymentEntry::count() + PaymentDetail::count();
        $conversion_rate = $count_link_sent ? round((($count_detail * 100) / $count_link_sent), 2) : 0;

        $count_today_paid        = PaymentDetail::where('payment_status', $completed)->whereDate('created_at', now())->count();
        $count_last7days_paid    = PaymentDetail::where('payment_status', $completed)->whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subDays(7))->count();
        $count_last1month_paid   = PaymentDetail::where('payment_status', $completed)->whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subMonths(1))->count();
        $count_last3months_paid  = PaymentDetail::where('payment_status', $completed)->whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subMonths(3))->count();
        $count_last6months_paid  = PaymentDetail::where('payment_status', $completed)->whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subMonths(6))->count();
        $count_last12months_paid = PaymentDetail::where('payment_status', $completed)->whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subYears(1))->count();

        $count_today_sent        = PaymentEntry::whereDate('created_at', now())->count();
        $count_last7days_sent    = PaymentEntry::whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subDays(7))->count();
        $count_last1month_sent   = PaymentEntry::whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subMonths(1))->count();
        $count_last3months_sent  = PaymentEntry::whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subMonths(3))->count();
        $count_last6months_sent  = PaymentEntry::whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subMonths(6))->count();
        $count_last12months_sent = PaymentEntry::whereDate('created_at', '<=', now())->whereDate('created_at', '>', now()->subYears(1))->count();

        return view('backend.dash.index', compact(
            'count_today_paid',
            'count_last7days_paid',
            'count_last1month_paid',
            'count_last3months_paid',
            'count_last6months_paid',
            'count_last12months_paid',

            'count_today_sent',
            'count_last7days_sent',
            'count_last1month_sent',
            'count_last3months_sent',
            'count_last6months_sent',
            'count_last12months_sent',

            'count_client',
            'count_setup',
            'count_entry',
            'count_detail',
            'count_link_sent',
            'conversion_rate'
        ));
    }

    public function count_to_send($start, $end)
    {
        $today = now();
        $payment_date = date('Y-m-d', strtotime('2019-10-10'));

        if ($today < $payment_date) {
            dd('stop here');
        }

        $md1 = date('md', strtotime($today));
        $md2 = date('md', strtotime($payment_date));

        if ($md1 == $md2) {
            // do your stuff here
        } else if ($md1 == '0228' && $md2 == '0229') {
            // do your stuff here
        }
    }
}
