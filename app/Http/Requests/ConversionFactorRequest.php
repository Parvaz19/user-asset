<?php

namespace App\Http\Requests;

use App\Models\ConversionFactor;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConversionFactorRequest extends FormRequest
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
        return [
            'fee' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'from_asset_id' => ['required', 'exists:assets,id', 'different:to_asset_id'],
            'to_asset_id' => ['required', 'exists:assets,id', 'different:from_asset_id',
                function ($attribute, $value, Closure $fail) {
                    $count = ConversionFactor::where('from_asset_id', $this->from_asset_id)
                        ->where('to_asset_id', $this->to_asset_id)->count();
                    if ($count > 0 && $this->isMethod('post')) {
                        $fail("Conversion factor already exists for selected assets.");
                    }
                },
            ],
        ];
    }
}
