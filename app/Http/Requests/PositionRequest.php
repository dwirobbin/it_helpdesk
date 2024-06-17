<?php

namespace App\Http\Requests;

use App\Models\Position;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
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
        'title' => 'Title',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match ($this->method()) {
            'POST' => $this->user()->can('create', Position::class),
            'PUT', 'PATCH' => $this->user()->can('update', $this->route('position')),
        };
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
        $position = $this->route('position');

        $titleUniqueRule = match ($this->method()) {
            'POST' => Rule::unique('positions', 'title'),
            'PUT' => Rule::unique('positions', 'title')->ignore($position, 'slug'),
        };

        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:50',
                $titleUniqueRule,
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
            'title' => ucfirst($this->input('title')),
        ]);
    }
}
