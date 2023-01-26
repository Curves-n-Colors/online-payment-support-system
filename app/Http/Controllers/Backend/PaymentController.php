<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentDetail;
use App\Models\Client;
use App\User;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{
    public function index()
    {
        $data = PaymentDetail::orderBy('created_at', 'DESC')->get();
        return view('backend.payment.index', compact('data'));
    }

    public function create()
    {
        $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
        return view('backend.payment.create', compact('clients'));
    }

    public function store(PaymentRequest $request)
    {
        if (PaymentDetail::_storing($request)) {
            return redirect()->route('payment.index')->with('success', 'The payment has been created.');
        }
        return back()->withInput()->with('error', 'Sorry, could not create payment at this time. Please try again later.');
    }

    public function edit($uuid)
    {
        if ($data = PaymentDetail::where('uuid', $uuid)->first()) {
            $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
            return view('backend.payment.edit', compact('data', 'clients'));
        }
        return back()->with('warning', 'The payment you want to edit does not exist.');
    }

    public function update(PaymentRequest $request, $uuid)
    {
        if ($request->has('submit') && $request->submit == "CREATE NEW PAYMENT" && PaymentDetail::_storing($request)) {
            return redirect()->route('payment.index')->with('success', 'The payment has been created.');
        } else if ($request->has('submit') && $request->submit == "UPDATE PAYMENT" && PaymentDetail::_updating($request, $uuid)) {
            return redirect()->route('payment.index')->with('success', 'The payment has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update payment at this time. Please try again later.');
    }

    public function change_status($uuid)
    {
        if (PaymentDetail::_change_status($uuid)) {
            return back()->with('success', 'The payment link status has been changed.');
        }
        return back()->with('error', 'Sorry, could not change status of the payment at this time. Please try again later.');
    }

    public function send(Request $request, $uuid)
    {
        if ($request->has('master_password') && User::_check_master($request->master_password)) {
            if (PaymentDetail::_sending($uuid)) {
                return response()->json(['status' => true, 'link' => '', 'msg' => 'The Payment Link has been sent.']);
            }
            return response()->json(['status' => false, 'msg' => 'The payment link you want to send does not exist.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }

    public function copy(Request $request, $uuid)
    {
        if ($request->has('master_password') && User::_check_master($request->master_password)) {
            if ($link = PaymentDetail::_copying($uuid)) {
                return response()->json(['status' => true, 'link' => $link, 'msg' => 'The Payment Link has been copied.']);
            }
            return response()->json(['status' => false, 'msg' => 'The payment link you want to copy does not exist.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }

    public function refund(Request $request, $uuid)
    {
        if ($request->has('master_password') && User::_check_master($request->master_password)) {
            if (PaymentDetail::_refunding($request->all(), $uuid)) {
                return response()->json(['status' => true, 'msg' => 'The Payment transaction has been refunded.']);
            }
            return response()->json(['status' => false, 'msg' => 'Sorry, Could not refund the payment transaction at this time. Please try again later.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }

    public function transaction(Request $request, $uuid)
    {
        if ($transaction = PaymentDetail::_transaction($uuid)) {
            return response()->json(['status' => true, 'transaction' => $transaction]);
        }
        return response()->json(['status' => false, 'msg' => 'The payment link you want to check the transaction of, does not exist.']);
    }
}
