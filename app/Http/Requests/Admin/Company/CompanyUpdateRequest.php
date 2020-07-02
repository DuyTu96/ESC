<?php

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
            'name' => 'min:1|max:255',
            'name_en' => 'max:255',
            'logo' => 'nullable|file|image|max:100',
            'url' => [
                'max:255',
                'regex:/^(http|https):/'
            ],
            'phone' => 'max:45',
            'postal_code' => 'min:7|max:8',
            'city' => 'max:255',
            'subsequent_address' => 'max:255'
        ];
    }
}
