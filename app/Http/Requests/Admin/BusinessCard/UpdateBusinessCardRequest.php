<?php

namespace App\Http\Requests\Admin\BusinessCard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessCardRequest extends FormRequest
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
            'last_name_kanji' => 'sometimes|max:255',
            'first_name_kanji' => 'sometimes|max:255',
            'last_name_kana' => 'sometimes|max:255',
            'first_name_kana' => 'sometimes|max:255',
            'employee_number' => 'sometimes|max:255',
            'email' => 'sometimes|nullable|email|max:254',
            'image' => 'sometimes|nullable|file|image'
        ];
    }
}
