<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Http\Requests\UserStore;
use App\Http\Requests\UserUpdate;
use App\Http\Requests\UserProfileUpdate;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('super.admin')->except('profile', 'profile_update');
    }

    public function index()
    {
        $data = User::orderBy('name', 'ASC')->get();
        return view('backend.user.index', compact('data'));
    }

    public function create()
    {
        return view('backend.user.create');
    }

    public function store(UserStore $request)
    {
        if (User::_storing($request)) {
            return redirect()->route('user.index')->with('success', 'The user has been created.');
        }
        return back()->withInput()->with('error', 'Sorry, could not create user at this time. Please try again later.');
    }

    public function edit($uuid)
    {
        if ($data = User::where('uuid', $uuid)->first()) { 
            return view('backend.user.edit', compact('data'));
        }
        return back()->with('warning', 'The user you want to edit does not exist.');
    }

    public function update(UserUpdate $request, $uuid)
    {
        if (User::_updating($request, $uuid)) {
            return redirect()->route('user.index')->with('success', 'The user has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update user at this time. Please try again later.');
    }
    
    public function change_status($uuid)
    {
    	if (User::_change_status($uuid)) {
    		return back()->with('success', 'The user status has been changed.');
    	}
        return back()->with('error', 'Sorry, could not change status of the user at this time. Please try again later.');
    }

    public function profile()
    {
        $data = Auth::user();
        return view('backend.user.profile', compact('data'));
    }

    public function profile_update(UserProfileUpdate $request)
    {
        if (User::_profiling($request)) {
            return back()->with('success', 'Your profile has been updated.');
        }
        return back()->withInput()->with('error', 'Sorry, could not update your profile at this time. Please try again later.');
    }
}