<?php

namespace App\Http\Requests\Portal\BusinessCard;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Phone;

class BusinessCardRequest extends FormRequest
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
            'setting_code' => 'min:1|max:29',
            'last_name_kanji' => 'min:1|max:255',
            'first_name_kanji' => 'min:1|max:255',
            'last_name_kana' => 'nullable|max:255',
            'first_name_kana' => 'nullable|max:255',
            'employee_number' => 'nullable|max:255',
            'hire_date' => 'nullable|date_format:Y-m-d',
            'email' => 'nullable|email|max:255',
            'phone' => ['nullable', new Phone(), 'max:45']
        ];
    }
}
