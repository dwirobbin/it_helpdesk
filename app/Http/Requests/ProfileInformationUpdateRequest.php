<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileInformationUpdateRequest extends FormRequest
{
    protected static $ATTRIBUTE_MESSAGES =
    [
        'required'  => ':attribute harus diisi.',
        'string'  => ':attribute harus berupa string.',
        'min'       => [
            'string' => ':attribute minimal harus :min karakter.',
        ],
        'max'       => [
            'string' => ':attribute maksimal harus :max karakter.',
        ],
        'email'     => ':attribute tidak valid.',
        'lowercase' => ':attribute harus berupa huruf kecil',
        'unique'    => ':attribute sudah ada.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'name' => 'Nama',
        'email' => 'Email',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return static::$ATTRIBUTE_MESSAGES;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return static::$ATTRIBUTE_NAMES;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => ucfirst($this->input('name')),
        ]);
    }
}
