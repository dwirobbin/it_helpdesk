<?php

namespace App\Http\Requests;

use App\Models\Respond;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RespondRequest extends FormRequest
{
    protected static $ATTRIBUTE_MESSAGES =
    [
        'min'       => [
            'string' => ':attribute minimal harus :min karakter.',
        ],
        'max'       => [
            'string' => ':attribute maksimal harus :max karakter.',
            'file'      => 'size :attribute maksimal harus :max kilobytes.',
        ],
        'required'  => ':attribute harus diisi.',
        'string'    => ':attribute harus berupa string.',
        'image'     => ':attribute harus berupa file gambar.',
        'mimes'     => ':attribute harus berformat :values.',
        'exists'    => ':attribute yang dipilih tidak valid.',
    ];
    protected static $ATTRIBUTE_NAMES = [
        'ticket_id' => 'ID Tiket',
        'text' => 'Tanggapan',
        'image' => 'Gambar',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match ($this->method()) {
            'POST' => $this->user()->can('create', Respond::class),
            'PUT', 'PATCH' => $this->user()->can('update', $this->route('respond')),
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
        return [
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'text' => ['required', 'string', 'min:3'],
            'image' => [
                'nullable',
                Rule::when($this->hasFile('image'), ['image', 'mimes:png,jpg,jpeg,gif,webp', 'max:2048']),
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
            'text' => ucfirst($this->input('text')),
        ]);
    }
}
