<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NiblRequest extends FormRequest
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
            'mer_ref_id' => 'required',
            'TXN_UUID'  => 'required',
            'mer_var_1' => 'required',
            'mer_var_2' => 'required'
        ];
    }
}
