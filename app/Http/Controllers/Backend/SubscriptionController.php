<?php

namespace App\Http\Controllers\Backend;

use App\Models\Client;
use App\Models\PaymentSetup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Backend\UserService;
use App\Http\Requests\SubscriptionRequest;
use App\Services\Backend\SubscriptionService;
use App\Http\Requests\SubscriptionEditRequest;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscription = SubscriptionService::_get();
        $data = $subscription->get();
        $clients = Client::select('id', 'name')->where('is_active', 10)->get();
        return view('backend.payment.subscription.index', compact('data', 'clients'));
    }

    public function create()
    {
        $subscription = PaymentSetup::select('title', 'id')->get();
        $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
        return view('backend.payment.subscription.create', compact('clients','subscription'));
    }

    public function store(SubscriptionRequest $request)
    {
        if (SubscriptionService::_storing($request)) {
            if(isset($request->send_email)){
                return redirect()->route('payment.setup.index')->with('success', 'The Subscription has been created and email has been sent');
            }
            return redirect()->route('payment.setup.index')->with('success', 'The Subscription has been created.');
        }
        return back()->withInput()->with('error', 'Sorry, could not create subscription at this time. Please try again later.');
    }

    public function edit($uuid)
    {
        if($subscription = SubscriptionService::_find($uuid))
        {
            $subscription_plan = PaymentSetup::select('title', 'id')->get();
            $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
            return view('backend.payment.subscription.edit', compact('subscription', 'subscription_plan', 'clients'));
        }
        return back()->withInput()->with('error', 'Sorry, could not create subscription at this time. Please try again later.');
    }


    public function send(Request $request,$uuid)
    {
        if ($request->has('master_password') && UserService::_check_master($request->master_password)) {
            if (SubscriptionService::_sending($uuid)) {
                return response()->json(['status' => true, 'link' => '', 'msg' => 'The payment link email(s) has been sent.']);
            }
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }
}
