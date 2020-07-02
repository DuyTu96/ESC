<?php

namespace App\Http\Requests\Portal\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'qr_i_token' => 'min:1|max:36',
            'qr_g_token' => 'min:1|max:44'
        ];
    }
}
