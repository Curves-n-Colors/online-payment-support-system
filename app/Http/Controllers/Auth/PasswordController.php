<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Http\Requests\PasswordUpdate;
use App\Http\Requests\MasterPasswordUpdate;

class PasswordController extends Controller
{
    public function change()
    {
        return view('auth.passwords.change');
    }

    public function changing(PasswordUpdate $request)
    {
        $user = User::find(auth()->user()->id); 
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your credentials do not match.']);
        }

        $user->password = Hash::make($request->password);
        if ($user->update()) {
            return back()->with('success', 'Your password has been updated.');
        }
        return back()->with('error', 'Sorry, could not updated your password at this time. Please try again later.');
        
    }

    public function master_changing(MasterPasswordUpdate $request)
    {
        $user = User::find(auth()->user()->id); 
        if (!Hash::check($request->master_current_password, $user->master_password)) {
            return back()->withErrors(['master_current_password' => 'Your credentials do not match.']);
        }
        
        $user->master_password = Hash::make($request->master_password);
        if ($user->update()) {
            return back()->with('success', 'Your master password has been updated.');
        }
        return back()->with('error', 'Sorry, could not updated your master password at this time. Please try again later.');
        
    }
}
