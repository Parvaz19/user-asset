<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'name' => ['required', 'min:3', 'max:25', 'string'],
            'value' => ['required'],
        ];

        if ($this->isMethod('post')) {
            $rules['name'] = array_merge($rules['name'], ['unique:settings,name']);
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = array_merge($rules['name'], [
                Rule::unique('settings', 'name')->ignore($this->route('setting')->id),
            ]);
        }

        return $rules;
    }
}
