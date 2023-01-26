<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentSetupUpdate extends FormRequest
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
            'title'    => 'required|unique:payment_setups,title,'.$this->uuid.',uuid',
            'client'   => 'required',
            'email'    => 'required',
            'contents' => 'required',
            'total'    => 'required',
            'currency' => 'required',
            'payment_options' => 'required',
            'recurring_type'  => 'required',
            'reference_date'  => 'required'
        ];
    }
}
