<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'client'   => 'required',
            'email'    => 'required',
            'contents' => 'required',
            'total'    => 'required',
            'currency' => 'required',
            'expired_date' => 'required',
            'expired_time' => 'required',
            'payment_options' => 'required'
        ];
    }
}
