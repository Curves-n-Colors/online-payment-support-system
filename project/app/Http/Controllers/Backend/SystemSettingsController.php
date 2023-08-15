<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemSettingRequest;
use App\Services\Backend\SystemSettingService;

class SystemSettingsController extends Controller
{
    public $setting;
    public function __construct()
    {
        $this->middleware('super.admin');
        $this->setting = SystemSetting::get();
    }

    public function index()
    {
        $send_email_time = [];
        $days_between_mail = [];
        $email_day = [];
        $days_between_extended_mail=[];
        $setting= $this->setting;
        if(count($setting)>0){
           foreach($setting as $set){
            array_push($email_day, $email_day[$set->recurring_type]=$set->email_day);
            array_push($days_between_mail, $days_between_mail[$set->recurring_type]=$set->days_between_mail);
            array_push($send_email_time, $send_email_time[$set->recurring_type]=$set->send_email_time);
            array_push($days_between_extended_mail, $days_between_extended_mail[$set->recurring_type]=$set->days_between_extended_mail);
           }
        }
       return view('backend.settings.form', compact('send_email_time', 'days_between_mail','email_day', 'days_between_extended_mail'));
    }

    public function store(Request $request)
    {
       
        $setting= $this->setting;
        if(count($setting)>0){
            foreach($setting as $set){
                $set->delete();
            }
        }
        
        if (SystemSettingService::_storing($request)) {
            return redirect()->route('system.settings')->with('success', 'The settings is saved.');
        }
        return back()->withInput()->with('error', 'Sorry, could save the settings. Please try again later.');
    }

}
