<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\Backend\PaymentSettingService;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        $data = PaymentSettingService::_get();

        return view('backend.payment.settings.index', compact('data'));
    }

    public function create()
    {
         return view('backend.payment.settings.create');
    }

    public function store(Request $request)
    {
        if (PaymentSettingService::_storing($request)) {
            return redirect()->route('payment.settings')->with('success', 'The payment method has been created.');
        }
        return back()->withInput()->with('error', 'Sorry, could not create payment method at this time. Please try again later.');

    }

    public function edit($uuid)
    {
       if ($data = PaymentSettingService::_find($uuid)) { 
        	return view('backend.payment.settings.edit', compact('data'));
        }
        return back()->with('warning', 'The payment method you want to edit does not exist.');
    }
    
    public function update(Request $request, $uuid)
    {
         if (PaymentSettingService::_updating($request, $uuid)) {
            return redirect()->route('payment.settings')->with('success', 'The payment method has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update payment method at this time. Please try again later.');
    }

    public function delete()
    {

    }

    public function change_status($uuid)
    {
        if (PaymentSettingService::_change_status($uuid)) {
            return back()->with('success', 'The payment method status has been changed.');
        }
        return back()->with('error', 'Sorry, could not change status of the payment method at this time. Please try again later.');
    }
}
