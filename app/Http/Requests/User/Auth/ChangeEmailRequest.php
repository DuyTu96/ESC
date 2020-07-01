<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class ChangeEmailRequest extends FormRequest
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
        if ($this->email) {
            $this->merge([
                'email' => base64_decode($this->email),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (!Auth::guard('user')->user()) {
            return [];
        }

        return [
            'email' => [
                'required',
                'unique:users,email',
                'regex:/^([A-Za-z0-9])+([\w\.\!\#\$\%\&\*\+\-\/\=\?\^\`\{\|\}\~])*([a-zA-Z0-9])+@([a-zA-Z0-9]+\.)+[a-zA-Z0-9]{2,8}$/',
            ],
        ];
    }
}
