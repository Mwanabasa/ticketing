<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body'       => ['required', 'string', 'max:10000'],
            'attachment' => ['nullable', 'mimes:jpg,jpeg,png,gif,pdf', 'max:2048'],
        ];
    }
}
