<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EmailNotification;
use App\Models\PaymentSetup;
use App\Models\PaymentEntry;
use App\Models\PaymentDetail;
use App\Models\Client;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $email = EmailNotification::orderBy('created_at', 'DESC');
        
        if ($request->has('client') && 
            $client = Client::select('id')->where('uuid', filter_var($request->client, FILTER_SANITIZE_STRING))->first()) {
            $email->where('client_id', $client->id);
        }
        if ($request->has('from')) {
            $email->whereDate('created_at', '>=', filter_var($request->from, FILTER_SANITIZE_STRING));
        }
        if ($request->has('to')) {
            $email->whereDate('created_at', '<=', filter_var($request->to, FILTER_SANITIZE_STRING));
        }
        
        $data = $email->get();
        $clients = Client::select('uuid', 'name')->get();
        return view('backend.email.index', compact('data', 'clients'));
    }

    // not is use, only for backup
    public function payment_setup($uuid)
    {
        if ($payment = PaymentSetup::where('uuid', $uuid)->first()) {
            $data = null;
            $entry_uuids  = ($payment->entries) ? array_column($payment->entries->toArray(), 'uuid') : [];
            $detail_uuids = ($payment->details) ? array_column($payment->details->toArray(), 'uuid') : [];
            $uuids = array_merge($entry_uuids, $detail_uuids); 
            
            if ($uuids) $data = EmailNotification::whereIn('uuid', $uuids)->orderBy('created_at', 'DESC')->get(); 
            return view('backend.email.payment_setup', compact('data', 'payment'));
        }
        return back()->with('warning', 'The payment setup does not exist.');
    }

    // not is use, only for backup
    // either payment-entry or payment-detail
    public function payment_entry_detail($uuid)
    {
        $payment = PaymentDetail::where('uuid', $uuid)->first();
        if (!$payment) $payment = PaymentEntry::where('uuid', $uuid)->first();

        if ($payment) {
            $data = EmailNotification::where('uuid', $uuid)->orderBy('created_at', 'DESC')->get();
            return view('backend.email.payment_entry_detail', compact('data', 'payment'));
        }
        return back()->with('warning', 'The payment email does not exist.');
    }
}