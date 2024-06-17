<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePictureUpdateRequest extends FormRequest
{
    protected static $NEEDS_AUTHORIZATION = true;
    protected static $ATTRIBUTE_MESSAGES =
    [
        'min'       => [
            'file'      => 'Size :attribute minimal harus :min kilobytes.',
        ],
        'max'       => [
            'file'      => 'Size :attribute maksimal harus :max kilobytes.',
        ],
        'required'  => ':attribute harus diisi.',
        'image'     => ':attribute harus berupa file gambar.',
        'mimes'     => ':attribute harus berformat :values.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'photo'                 => 'Photo',
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
            'photo' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg,gif,webp',
                'max:2048',
            ],
        ];
    }
}
