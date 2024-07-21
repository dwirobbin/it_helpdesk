<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    protected static $ATTRIBUTE_MESSAGES =
    [
        'min'       => [
            'string' => ':attribute minimal harus :min karakter.',
        ],
        'max'       => [
            'string' => ':attribute maksimal harus :max karakter.',
        ],
        'required'  => ':attribute harus diisi.',
        'string'    => ':attribute harus berupa string.',
        'unique'    => ':attribute sudah ada.',
    ];
    protected static $ATTRIBUTE_NAMES = [
        'name' => 'Nama',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
        $room = $this->route('room');

        $nameUniqueRule = match ($this->method()) {
            'POST' => Rule::unique('rooms', 'name'),
            'PUT' => Rule::unique('rooms', 'name')->ignore($room, 'slug'),
        };

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                $nameUniqueRule,
            ],
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
