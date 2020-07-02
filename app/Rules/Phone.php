<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Phone implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 全角数字を半角へ変換
        $text = mb_convert_kana($value, 'asK', 'UTF-8');

        // ハイフンを半角へ変換
        $text = $this->formatHyphen($text);

        return preg_match("/^[\d]{2,4}-?[\d]{2,4}-?[\d]{3,4}$/", $text);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $message = __('validation.phone');

        if ($message == 'validation.phone') {
            $message = '電話番号を確認してください。';
        }

        return $message;
    }

    public function formatHyphen($value)
    {
        $table = [
            'ー' => '-',
            '−' => '-',
        ];

        // ハイフンを半角へ変換
        $search = array_keys($table);
        $replace = array_values($table);

        return str_replace($search, $replace, $value);
    }
}
