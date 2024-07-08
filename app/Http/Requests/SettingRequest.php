<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    protected static $ATTRIBUTE_MESSAGES =
    [
        'min'       => [
            'string' => ':attribute minimal harus :min karakter.',
        ],
        'max'       => [
            'string' => ':attribute maksimal harus :max karakter.',
            'file' => 'size :attribute maksimal harus :max kilobytes.',
        ],
        'required'  => ':attribute harus diisi.',
        'string'    => ':attribute harus berupa string.',
        'image'     => ':attribute harus berupa file gambar.',
        'mimes'     => ':attribute harus berformat :values.',
    ];
    protected static $ATTRIBUTE_NAMES = [
        'app_name' => 'Nama Aplikasi',
        'app_footer' => 'Title Footer',
        'app_logo' => 'Logo Aplikasi',
        'company_name' => 'Nama Instansi',
        'company_telp' => 'No. Telp. Instansi',
        'company_address' => 'Alamat Instansi',
        'company_logo' => 'Logo Instansi',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && $this->user()->role_id === RoleEnum::SUPER_ADMIN->value;
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
        if ($this->routeIs('settings.app-information.update')) {
            $rules = [
                'app_name' => ['required', 'string', 'min:3', 'max:50'],
                'app_footer' => ['required', 'string', 'min:3', 'max:100'],
                'app_logo' => [
                    'nullable', Rule::when(
                        $this->hasFile('app_logo'),
                        ['image', 'mimes:png,jpg,jpeg,gif,webp', 'max:2048']
                    )
                ],
            ];
        } else if ($this->routeIs('settings.company-information.update')) {
            $rules = [
                'company_name' => ['required', 'string', 'min:3', 'max:50'],
                'company_telp' => [
                    'nullable',
                    Rule::when(!is_null($this->input('company_telp')), ['string', 'min:3', 'max:20'])
                ],
                'company_address' => [
                    'nullable',
                    Rule::when(!is_null($this->input('company_address')), ['string', 'min:5'])
                ],
                'company_logo' => [
                    'nullable', Rule::when(
                        $this->hasFile('company_logo'),
                        ['image', 'mimes:png,jpg,jpeg,gif,webp', 'max:2048']
                    )
                ],
            ];
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->routeIs('settings.app-information.update')) {
            $dataManipulation = [
                'app_name' => ucfirst($this->input('app_name')),
                'app_footer' => ucfirst($this->input('app_footer')),
            ];
        } else if ($this->routeIs('settings.company-information.update')) {
            $dataManipulation = [
                'company_name' => ucfirst($this->input('company_name')),
                'company_address' => !is_null($this->input('company_address'))
                    ? ucfirst($this->input('company_address'))
                    : null,
            ];
        }

        $this->merge($dataManipulation);
    }
}
