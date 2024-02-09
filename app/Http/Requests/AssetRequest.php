<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $rules = [
            'name' => ['required', 'min:3', 'max:25'],
            'price' => ['required', 'numeric', 'min:0'],
        ];

        if ($this->isMethod('post')) {
            $rules['name'] = array_merge($rules['name'], ['unique:assets,name']);
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = array_merge($rules['name'], [
                Rule::unique('assets', 'name')->ignore($this->route('asset')->id),
            ]);
        }

        return $rules;
    }
}
