<?php

namespace App\Http\Requests\Admin\BusinessCard;

use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessCardRequest extends FormRequest
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
            'last_name_kanji' => 'required|max:255',
            'first_name_kanji' => 'required|max:255',
            'last_name_kana' => 'max:255',
            'first_name_kana' => 'max:255',
            'employee_number' => 'max:255',
            'email' => 'nullable|email|max:254',
            'image' => 'nullable|file|image'
        ];
    }
}
