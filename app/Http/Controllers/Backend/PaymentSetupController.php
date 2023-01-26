<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentSetup;
use App\Models\Client;
use App\User;

use App\Http\Requests\PaymentSetupStore;
use App\Http\Requests\PaymentSetupUpdate;
use Illuminate\Support\Facades\Validator;

class PaymentSetupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('client')) {
            $client = Client::where('uuid', filter_var($request->client, FILTER_SANITIZE_STRING))->first();
            $data = $client->payment_setups ?? null;
        } else {
            $data = PaymentSetup::orderBy('reference_date', 'ASC')->get();
        }

        $clients = Client::select('uuid', 'name')->get();
        return view('backend.payment.setup.index', compact('data', 'clients'));
    }

    public function create()
    {
        $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
        return view('backend.payment.setup.create', compact('clients'));
    }

    public function store(PaymentSetupStore $request)
    {
        if ($request->payment_option == 1) {
            if (PaymentSetup::_storensend($request)) {
                return redirect()->route('payment.entry.index')->with('success', 'The payment setup has been created.');
            }
        } else {
            if (PaymentSetup::_storing($request)) {
                return redirect()->route('payment.setup.index')->with('success', 'The payment setup has been created.');
            }
        }
        return back()->withInput()->with('error', 'Sorry, could not create payment setup at this time. Please try again later.');
    }

    public function edit($uuid)
    {
        if ($data = PaymentSetup::where('uuid', $uuid)->first()) {
            $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
            return view('backend.payment.setup.edit', compact('data', 'clients'));
        }
        return back()->with('warning', 'The payment setup you want to edit does not exist.');
    }

    public function update(PaymentSetupUpdate $request, $uuid)
    {
        if (PaymentSetup::_updating($request, $uuid)) {
            return redirect()->route('payment.setup.index')->with('success', 'The payment setup has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update payment setup at this time. Please try again later.');
    }

    public function change_status($uuid)
    {
        if (PaymentSetup::_change_status($uuid)) {
            return back()->with('success', 'The payment setup status has been changed.');
        }
        return back()->with('error', 'Sorry, could not change status of the payment setup at this time. Please try again later.');
    }

    public function entry($uuid)
    {
        if ($data = PaymentSetup::_entries($uuid)) {
            return response()->json(['status' => true, 'entries' => $data['entries'], 'new_entry' => $data['new_entry']]);
        }
        return response()->json(['status' => false]);
    }

    public function send(Request $request, $uuid)
    {
        if ($request->has('master_password') && User::_check_master($request->master_password)) {
            if ($request->has('entries')) {
                if (PaymentSetup::_sending($request->entries, $uuid)) {
                    return response()->json(['status' => true, 'link' => '', 'msg' => 'The payment link email(s) has been sent.']);
                }
                return response()->json(['status' => false, 'msg' => 'The payment setup you want to send does not exist.']);
            }
            return response()->json(['status' => false, 'msg' => 'Please select at least one to proceed.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }

    public function verifyPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'master_password' => 'required'
        ]);

        if (!$validator->fails()) {
            if ($request->has('master_password') && User::_check_master($request->master_password)) {
                return response()->json(['status' => true, 'msg' => '']);
            }
            return response()->json(['status' => false, 'msg' => 'Sorry, PIN is not correct.']);
        }
        return response()->json(['status' => false, 'msg' => 'Master PIN is required.']);
    }
}
