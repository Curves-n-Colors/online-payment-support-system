<?php

namespace App\Http\Controllers\Backend;

use App\Models\Client;
use App\Models\Category;
use App\Models\PaymentSetup;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Backend\UserService;
use App\Http\Requests\SubscriptionRequest;
use App\Services\Backend\SubscriptionService;
use App\Http\Requests\SubscriptionEditRequest;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscription = SubscriptionService::_get();
        if ($request->has('client') and isset($request->client)) {
            $client = Client::select('id')->where('uuid', $request->client)->first();
            $subscription = $subscription->where('client_id', $client->id);
        }

        if ($request->has('type') and isset($request->type)) {
            $type = $request->type;
            $subscription = $subscription->whereHas('details', function ($query) use ($type) {
                $query->where('recurring_type', $type);
            });
        }

        if ($request->has('category') and isset($request->category)) {
            $category = Category::where('id', filter_var($request->category, FILTER_SANITIZE_STRING))->first();
            $id = $category->id;
            $subscription = $subscription->whereHas('details', function ($query) use ($id) {
                $category_id = $id;
                $query->with('categories')->whereHas('categories', function ($query) use ($category_id){
                    $query->where('category_id', $category_id);
                });
            });
        }
        $data = $subscription->get();

        $clients = Client::select('uuid', 'name')->where('is_active', 10)->get();
        $categories = Category::select('id', 'name')->get();
        return view('backend.payment.subscription.index', compact('data', 'clients', 'categories'));
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
