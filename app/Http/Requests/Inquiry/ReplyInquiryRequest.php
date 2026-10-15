<?php

namespace App\Http\Requests\Inquiry;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReplyInquiryRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reply_message' => ['required', 'string', 'min:5', 'max:5000'],
        ];
    }
}
