<?php

namespace App\Http\Requests\Admin\Stripe;

use Illuminate\Foundation\Http\FormRequest;

class AddCreditCardRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'exp_date' => $this->exp_year . '/' . $this->exp_month,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => 'required|ccn',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'exp_date' => 'ccd',
            'cvc' => 'required|cvc',
            'name' => 'required',
        ];
    }
}
