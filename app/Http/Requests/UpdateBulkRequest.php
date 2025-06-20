<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulkRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "ids" => "array|required",
            "ids.*" => "integer|exists:motorcycles,id",
            'nensiki' => [
                'nullable',
                'integer',
                'min:1800',
                'max:' . date('Y'),
            ],
            'soukou' => 'nullable|integer|min:0|max:9999999',
            'color' => 'nullable|string|max:64',
        ];
    }
}
