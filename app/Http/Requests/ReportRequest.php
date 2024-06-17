<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Enums\TicketStatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    protected static $ATTRIBUTE_MESSAGES =
    [
        'required'  => ':attribute harus diisi.',
        'string'  => ':attribute harus berupa string.',
        'date_format' => ':attribute tidak valid dengan format :format.',
        'in' => ':attribute yang dipilih tidak valid.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Akhir',
        'type' => 'Tipe',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match ($this->method()) {
            'POST' => $this->user()->can('createReport', Ticket::class),
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
            'start_date' => [
                'required',
                'date_format:d/m/Y',
            ],
            'end_date' => [
                'required',
                'date_format:d/m/Y',
            ],
            'type' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(TicketStatusEnum::values()),
            ],
        ];
    }
}
