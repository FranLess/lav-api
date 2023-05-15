<?php

namespace App\Http\Requests;

use App\Rules\ArrayOfNumbers;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    protected $redirect = false;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'body' => ['required', 'string'],
            'user_ids' => ['required', 'array', new ArrayOfNumbers]
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A title is required',
            'title.string' => 'HEYYAA :attribute gotta be string',
            'body.required' => 'A message is required',
        ];
    }
}
