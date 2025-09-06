<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
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
        $assetId = $this->route('asset')->id ?? null;

        return [
            'reference'     => ['required', 'string', 'max:255'],
            'serial_number' => [
                'required', 'string', 'max:255',
                Rule::unique('assets', 'serial_number')->ignore($assetId)
            ],
            'description'   => ['nullable', 'string'],
            'owner_id'      => ['nullable', 'exists:owners,id'],
            'owned_from'    => ['nullable', 'date'],
        ];
    }
}
