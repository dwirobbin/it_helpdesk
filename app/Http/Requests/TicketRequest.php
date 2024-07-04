<?php

namespace App\Http\Requests;

use App\Enums\TicketStatusEnum;
use App\Models\Ticket;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
    ];
    protected static $ATTRIBUTE_NAMES = [
        'title' => 'Keluhan',
        'description' => 'Deskripsi',
        'image' => 'Gambar',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match ($this->method()) {
            'POST' => $this->user()->can('create', Ticket::class),
            'PUT', 'PATCH' => $this->user()->can('update', $this->route('ticket')),
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
            'title' => ['required', 'string', 'min:5'],
            'description' => ['required', 'string', 'min:5'],
            'image' => [
                'nullable',
                Rule::when($this->hasFile('image'), ['image', 'mimes:png,jpg,jpeg,gif,webp', 'max:2048'])
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

    /**
     * Get the validated data from the request.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        return array_merge(parent::validated($key, $default), [
            'user_id' => auth()->id(),
            'status' => TicketStatusEnum::WAITING->value,
        ]);
    }
}
