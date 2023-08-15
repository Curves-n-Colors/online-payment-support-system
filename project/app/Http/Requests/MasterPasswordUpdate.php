<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterPasswordUpdate extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'master_current_password' => 'required',
            'master_password' => 'required|min:6|same:master_password_confirmation',
        ];
    }
}
