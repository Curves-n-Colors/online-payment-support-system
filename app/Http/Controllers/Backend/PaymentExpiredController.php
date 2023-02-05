<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PaymentEntry;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\PaymentSetup;
use App\Services\Backend\PaymentEntryService;
use App\Services\Backend\UserService;
use PhpParser\Node\Expr\FuncCall;

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

    public function suspend_status(Request $request, $uuid)
    {
        if ($request->has('master_password') && UserService::_check_master($request->master_password)) {
            $data = PaymentEntryService::_suspend_status_mail($uuid);
            if ($data['status']) {

                return response()->json(['status' => true, 'msg' => $data['msg']]);
            }
            return response()->json(['status' => false, 'msg' => 'Something went wrong.Please try again later.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }


    public function reactivation(Request $request, $encrypt)
    {
        $check = PaymentSetup::_validating($encrypt);
        if ($check['status'] == 200) {
            $entry = $check['entry'];
            $detail = $check['detail'];

            if ($entry) {
                return view('frontend.pay.reactivation', compact('encrypt', 'entry'));
            } else if ($detail) {
                return redirect()->route('result.success', [$encrypt]);
            }
        }
        return redirect()->route('result.error', [$check['status']]);
    }

}
