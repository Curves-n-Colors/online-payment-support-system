<?php

namespace App\Http\Controllers\Backend;
use App\Models\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $data = Setting::select('slug', 'value')->get()->toArray();
        $data = array_column($data, 'value', 'slug');
        return view('backend.setting.index', compact('data'));
    }

    public function store(Request $request)
    {
        foreach($request->data as $data=>$value){
            $setting = Setting::where('slug', $data)->first();
            $setting->update(['value' => $value]);
        }
        return redirect()->route('settings.index')->with('success', 'The Settings was updated.');

    }
}
