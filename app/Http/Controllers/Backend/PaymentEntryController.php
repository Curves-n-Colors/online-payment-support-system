<?php

namespace App\Http\Controllers\Backend;

use App\Models\Client;

use App\Models\PaymentEntry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\UserService;
use App\Services\Backend\PaymentEntryService;
use Carbon\Carbon;

class PaymentEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = PaymentEntry::orderBy('created_at', 'DESC');

        if ($request->has('client') && $client = Client::select('id')->where('uuid', filter_var($request->client, FILTER_SANITIZE_STRING))->first()) {
            $entries->where('client_id', $client->id);
        }

        if ($request->has('from') and !is_null($request->from)) {
            $entries->whereDate('start_date', '>=', filter_var($request->from, FILTER_SANITIZE_STRING));
        }

        if ($request->has('to') and !is_null($request->from)) {
            $entries->whereDate('end_date', '<=', filter_var($request->to, FILTER_SANITIZE_STRING));
        }

        
        if($request->has('pending')){
            $entries->where('start_date', '<',Carbon::now())->where('is_expired', 0);
        }
        
        if($request->has('upcoming')){
            $entries->where('start_date', '>',Carbon::now())->where('is_expired', 0);
        }
        
        $data = $entries->get();
        
        // dd($pending_data, $upcoming_data);

        $clients = Client::select('uuid', 'name')->get();
        return view('backend.payment.entry.index', compact('data', 'clients'));
    }

    public function change_status($uuid)
    {
        if (PaymentEntryService::_change_status($uuid)) {
            return back()->with('success', 'The payment setup status has been changed.');
        }
        return back()->with('error', 'Sorry, could not change status of the payment setup at this time. Please try again later.');
    }

    public function send(Request $request, $uuid)
    {
        if ($request->has('master_password') && UserService::_check_master($request->master_password)) {
            if (PaymentEntryService::_sending($uuid)) {
                return response()->json(['status' => true, 'link' => '', 'msg' => 'The payment link email has been sent.']);
            }
            return response()->json(['status' => false, 'msg' => 'The payment link you want to send does not exist.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }

    public function copy(Request $request, $uuid)
    {
        if ($request->has('master_password') && UserService::_check_master($request->master_password)) {
            if ($link = PaymentEntryService::_copying($uuid)) {
                return response()->json(['status' => true, 'link' => $link, 'msg' => 'The payment link has been copied.']);
            }
            return response()->json(['status' => false, 'msg' => 'The payment link you want to copy does not exist.']);
        }
        return response()->json(['status' => false, 'msg' => 'Invalid Master Password']);
    }

    public function approve($uuid)
    {
        $entry = PaymentEntryService::_find($uuid);
        $diff = 0;
        if(isset($entry->start_date) and isset($entry->subscription->expire_date)){
            $start_date = strtotime($entry->start_date);
            $end_date = strtotime($entry->subscription->expire_date);
            $year1 = date('Y', $start_date);
            $year2 = date('Y', $end_date);
                
            $month1 = date('m', $start_date);
            $month2 = date('m', $end_date);
                
            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
        }
        return view('backend.payment.entry.approve', compact('entry', 'diff'));
    }
    
    public function approve_submit(Request $request, $uuid)
    {
        if(PaymentEntryService::_approve($request,$uuid)){
            return redirect()->route('payment.entry.index')->with('success', 'The payment entry is approved.');
        }
        return back()->withInput()->with('error', 'Sorry, could not approve the payment entry.');
    }
}
