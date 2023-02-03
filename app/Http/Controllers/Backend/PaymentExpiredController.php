<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PaymentEntry;
use Illuminate\Http\Request;
use App\Models\Client;


class PaymentExpiredController extends Controller
{
    public function payment_expired(Request $request)
    {
        $entries = PaymentEntry::where(['is_expired'=>10])->orderBy('created_at', 'DESC');

        if (
            $request->has('client') &&
            $client = Client::select('id')->where('uuid', filter_var($request->client, FILTER_SANITIZE_STRING))->first()
        ) {
            $entries->where('client_id', $client->id);
        }
        if ($request->has('from')) {
            $entries->whereDate('payment_date', '>=', filter_var($request->from, FILTER_SANITIZE_STRING));
        }
        if ($request->has('to')) {
            $entries->whereDate('payment_date', '<=', filter_var($request->to, FILTER_SANITIZE_STRING));
        }

        $data = $entries->get();
        $clients = Client::select('uuid', 'name')->get();
        return view('backend.payment.expired.index', compact('data','clients'));

    }
}
