<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateBusinessOrderRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'source_name' => 'required|string',
            'source_address' => 'required|string',
            'source_phone' => 'required|string',
            'source_lat' => 'required',
            'source_long' => 'required',
            'destination_name' => 'required|string',
            'destination_address' => 'required|string',
            'destination_phone' => 'required|string',
            'destination_lat' => 'required',
            'destination_long' => 'required',
        ];
    }
}
