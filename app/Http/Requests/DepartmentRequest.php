<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    private static Department $department;

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
        'section' => 'Divisi',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match ($this->method()) {
            'POST' => $this->user()->can('create', Department::class),
            'PUT', 'PATCH' => $this->user()->can('update', $this->route('department')),
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
        $sectionUniqueRule = match ($this->method()) {
            'POST' => Rule::unique('departments', 'section'),
            'PUT' => Rule::unique('departments', 'section')->ignore(self::$department, 'slug'),
        };

        return [
            'section' => [
                'required',
                'string',
                'min:3',
                'max:50',
                $sectionUniqueRule,
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
        if ($this->isMethod('PUT')) {
            self::$department = $this->route('department');
        }

        $this->merge([
            'section' => ucfirst($this->input('section')),
        ]);
    }
}
