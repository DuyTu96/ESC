<?php

namespace App\Http\Requests\User\BusinessCard;

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
            'phone' => 'sometimes|max:45',
            'image' => 'sometimes|nullable|file|image'
        ];
    }
}
