<?php

namespace App\Http\Controllers\Backend;

use App\User;
use App\Models\Client;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Http\Controllers\Controller;
use App\Services\Backend\PaymentDetailService;

class PaymentDetailController extends Controller
{
    public function index(Request $request)
    {
        $entries = PaymentDetailService::_get();

        if ($request->has('client') &&
            $client = Client::select('id')->where('uuid', filter_var($request->client, FILTER_SANITIZE_STRING))->first()) {
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
        return view('backend.payment.detail.index', compact('data', 'clients'));
    }

    public function refund(Request $request, $uuid)
    {
        if ($request->has('master_password') && UserService::_check_master($request->master_password)) {
            if (PaymentDetail::_refunding($request->all(), $uuid)) {
                return response()->json(['status' => true, 'msg' => 'The Payment transaction has been refunded.']);
            }
            return response()->json(['status' => false, 'msg' => 'Sorry, Could not refund the payment transaction at this time. Please try again later.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }

    public function invoice_download(Request $request, $uuid)
    {
        return PaymentDetailService::_invoicing($uuid);
    }
}
