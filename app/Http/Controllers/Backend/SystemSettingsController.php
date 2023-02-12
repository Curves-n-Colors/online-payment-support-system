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
        $this->setting = SystemSetting::first();
    }

    public function index()
    {
        $extend_day = 0;
        $email_day = 0;
        $email_send_time = NULL;
        $setting= $this->setting;
        if(!is_null($setting)){
            $extend_day       = $setting->extend_day;
            $email_day        = $setting->email_day;
            $email_send_time  = $setting->email_send_time;
        }
       return view('backend.settings.form', compact('extend_day', 'email_day','email_send_time'));
    }

    public function store(SystemSettingRequest $request)
    {
        $setting= $this->setting;
        if(!is_null($setting)){
            $setting->delete();
        }
        if (SystemSettingService::_storing($request)) {
            return redirect()->route('system.settings')->with('success', 'The settings is saved.');
        }
        return back()->withInput()->with('error', 'Sorry, could save the settings. Please try again later.');
    }

}
