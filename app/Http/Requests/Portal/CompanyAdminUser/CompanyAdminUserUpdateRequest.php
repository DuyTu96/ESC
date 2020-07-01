<?php

namespace App\Http\Requests\Portal\CompanyAdminUser;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Phone;

class CompanyAdminUserUpdateRequest extends FormRequest
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
            "name" => 'min:1|max:255',
            "name_en" => "max:255",
            "url" => "max:255",
            "phone" => ['nullable', new Phone(), 'max:45'],
            "prefecture_id" => "max:11",
            "city" => "max:255",
            "subsequent_address" => "max:255"
        ];
    }
}
