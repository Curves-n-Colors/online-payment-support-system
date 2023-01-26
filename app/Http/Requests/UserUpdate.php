<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'         => 'required|unique:users,email,'.$this->uuid.',uuid',
            'name'          => 'required',
            'password'      => 'confirmed|min:6',
            'master_password' => 'confirmed|min:4'
        ];
    }
}
