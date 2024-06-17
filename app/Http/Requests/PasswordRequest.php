<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ATTRIBUTE_MESSAGES =
    [
        'min'       => [
            'string'    => ':attribute minimal harus :min karakter.',
        ],
        'max'       => [
            'string'    => ':attribute maksimal harus :max karakter.',
        ],
        'required'  => ':attribute harus diisi.',
        'same'      => ':attribute dan :other harus cocok.',
        'string'    => ':attribute harus berupa string.',
        'current_password' => ':attribute salah.'
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'current_password'      => 'Kata Sandi Saat Ini',
        'password'              => 'Kata Sandi Baru',
        'password_confirmation' => 'Konfirmasi Kata Sandi Baru',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return self::$NEEDS_AUTHORIZATION;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password', 'exclude'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => [
                'sometimes',
                Rule::when(!is_null($this->input('password')), 'required', 'nullable'),
                'string',
                'min:8',
                'same:password',
                'exclude',
            ],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!is_null($this->input('password')) && Hash::check($this->input('password'), request()->user()->password)) {
                    $validator->errors()->add('password', 'Password baru tidak boleh sama dengan sebelumnya.');
                }
            }
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validatedData = parent::validated($key, $default);

        if (!is_null($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            $validatedData['password'] = request()->user()->password;
        }

        return $validatedData;
    }
}
