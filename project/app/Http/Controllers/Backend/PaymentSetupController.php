<?php

namespace App\Http\Controllers\Backend;

use App\Models\Client;
use App\Models\Category;
use App\Models\PaymentSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\UserService;
use App\Http\Requests\PaymentSetupStore;
use App\Http\Requests\PaymentSetupUpdate;
use Illuminate\Support\Facades\Validator;
use App\Services\Backend\PaymentSetupService;

class PaymentSetupController extends Controller
{
    public function index(Request $request)
    {
        $id = 1;
        $data = PaymentSetupService::_get();

        if($request->has('category') and isset($request->category)){
            $category = Category::where('id', filter_var($request->category, FILTER_SANITIZE_STRING))->first();
            $id = $category->id;
            $data = $data->whereHas('categories', function ($query) use ($id) {
                    $query->where('category_id', $id);
                });

        }

        if ($request->has('type') and isset($request->type)) {
           $type = $request->type;
           $data = $data->where('recurring_type', $type);
        }

        if ($request->has('client') and isset($request->client)) {
            $client = Client::select('id')->where('uuid', filter_var($request->client, FILTER_SANITIZE_STRING))->first();
            $id = $client->id;
            $data = $data->whereHas('clients', function ($query) use ($id) {
                    $query->where('client_id', $id);
                    });
         }

        $data = $data->get();
      
        $clients = Client::select('uuid', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        return view('backend.payment.setup.index', compact('data', 'clients', 'categories'));
    }

    public function create()
    {
        $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
        $items = Category::select('id', 'name')->where('is_active',10)->get();
        return view('backend.payment.setup.create', compact('clients', 'items'));
    }

    public function store(PaymentSetupStore $request)
    {
       
        $new_array = [];
        foreach($request->contents as $c){
           
            $detals = explode('^',$c['title']);
            array_push($new_array, [
                'id' => $detals[1],
                'title'=>$detals[0],
                'amount' => $c['amount'],
                'description' => $c['description']
            ]);
            
        }
        $request->contents = $new_array;
        // dd($request->contents);
        if ($request->payment_option == 1) {
            dd(1);
            if (PaymentSetupService::_storensend($request)) {
                return redirect()->route('payment.entry.index')->with('success', 'The payment setup has been created.');
            }
        }else{
            if (PaymentSetupService::_storing($request)) {
                return redirect()->route('payment.setup.index')->with('success', 'The payment setup has been created.');
            }
         }
        return back()->withInput()->with('error', 'Sorry, could not create payment setup at this time. Please try again later.');
    }

    public function edit($uuid)
    {
        if ($data = PaymentSetupService::_find($uuid)) {
            $clients = Client::select('id', 'name', 'email')->where('is_active', 10)->get();
            $items = Category::select('id', 'name')->where('is_active', 10)->get();
            return view('backend.payment.setup.edit', compact('data', 'clients', 'items'));
        }
        return back()->with('warning', 'The payment setup you want to edit does not exist.');
    }

    public function update(PaymentSetupUpdate $request, $uuid)
    {
        $new_array = [];
        foreach ($request->contents as $c) {
            $detals = explode('^', $c['title']);
            array_push($new_array, [
                'id' => $detals[1],
                'title' => $detals[0],
                'amount' => $c['amount'],
                'description' => $c['description']
            ]);
        }

        $request->contents = $new_array;
        $update = PaymentSetupService::_updating($request, $uuid);
        if ($update) {
            return redirect()->route('payment.setup.index')->with('success', 'The payment setup has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update payment setup at this time. Please try again later.');
    }

    public function change_status($uuid)
    {
        if (PaymentSetupService::_change_status($uuid)) {
            return back()->with('success', 'The payment setup status has been changed.');
        }
        return back()->with('error', 'Sorry, could not change status of the payment setup at this time. Please try again later.');
    }

    public function entry($uuid)
    {
        if ($data = PaymentSetupService::_entries($uuid)) {
            return response()->json(['status' => true, 'entries' => $data['entries'], 'new_entry' => $data['new_entry']]);
        }
        return response()->json(['status' => false]);
    }

    public function send(Request $request, $uuid)
    {
        if ($request->has('master_password') && UserService::_check_master($request->master_password)) {
            if ($request->has('entries')) {
                if (PaymentSetupService::_sending($request->entries, $uuid)) {
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
            if ($request->has('master_password') && UserService::_check_master($request->master_password)) {
                return response()->json(['status' => true, 'msg' => '']);
            }
            return response()->json(['status' => false, 'msg' => 'Sorry, PIN is not correct.']);
        }
        return response()->json(['status' => false, 'msg' => 'Master PIN is required.']);
    }
}
