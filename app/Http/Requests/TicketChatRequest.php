<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TicketChatRequest extends FormRequest
{
    protected static $ATTRIBUTE_MESSAGES =
    [
        'required'  => ':attribute harus diisi.',
        'string'    => ':attribute harus berupa string.',
        'exists'    => ':attribute yang dipilih tidak valid.',
    ];
    protected static $ATTRIBUTE_NAMES = [
        'ticket_number' => 'No. Tiket',
        'user' => 'User',
        'text' => 'Pesan',
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
        return [
            'ticket_number' => ['required', 'string'],
            'user' => ['required', 'string'],
            'text' => ['required', 'string'],
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

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!Ticket::where('ticket_number', '=', $this->input('ticket_number'))->exists()) {
                    $validator->errors()->add('text', 'No. Tiket tidak valid.');
                }

                if (!User::where('slug', '=', $this->input('user'))->exists()) {
                    $validator->errors()->add('text', 'User tidak valid.');
                }
            }
        ];
    }
}
