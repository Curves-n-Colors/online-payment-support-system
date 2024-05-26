<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
        $payment_setup_id = $this->input('payment_setup_id');
        return [
            'subscription_id' => 'required',
            //'client' => 'required|unique:payment_has_clients,payment_setup_id'
            'client_id' => ['required',Rule::unique('payment_has_clients')->where(function ($query) use ($payment_setup_id) {
                return $query->where('payment_setup_id', $payment_setup_id);
            }),]
        ];
    }
}
