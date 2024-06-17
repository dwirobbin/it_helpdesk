<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Models\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    private static Employee $employee;

    protected static $ATTRIBUTE_MESSAGES =
    [
        'boolean'   => ':attribute harus true atau false.',
        'email'     => ':attribute tidak valid.',
        'in'        => ':attribute yang dipilih tidak valid.',
        'min'       => [
            'string'    => ':attribute minimal harus :min karakter.',
        ],
        'max'       => [
            'string'    => ':attribute maksimal harus :max karakter.',
        ],
        'size' => [
            'string'    => ':attribute panjangnya harus :size karakter.',
        ],
        'required'  => ':attribute harus diisi.',
        'same'      => ':attribute dan :other harus cocok.',
        'string'    => ':attribute harus berupa string.',
        'unique'    => ':attribute sudah ada.',
        'exists'    => ':attribute yang dipilih tidak valid.',
    ];
    protected static $ATTRIBUTE_NAMES     = [
        'nik'                   => 'NIK',
        'name'                  => 'Name',
        'position'              => 'Jabatan',
        'department'            => 'Divisi',
        'role'                  => 'Level User',
        'email'                 => 'Email',
        'password'              => 'Password',
        'password_confirmation' => 'Konfirmasi Password',
        'is_active_account'     => 'Aktifkan Akun',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return match ($this->method()) {
            'POST' => $this->user()->can('create', Employee::class),
            'PUT', 'PATCH' => $this->user()->can('update', $this->route('employee')),
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
        if ($this->isMethod('POST')) {
            $nikUniqueRule = Rule::unique('employees', 'nik');
            $emailUniqueRule = Rule::unique('users', 'email');
            $passwordRequiredRule = 'required';
        } else if ($this->isMethod('PUT')) {
            $employee = self::$employee;

            $nikUniqueRule = Rule::unique('employees', 'nik')->ignore($employee, 'id');
            $emailUniqueRule = Rule::unique('users', 'email')->ignore($employee->user, 'slug');
            $passwordRequiredRule = !is_null($this->input('current_password')) ? 'required' : 'nullable';
        }

        return [
            'nik' => ['required', 'string', 'min:8', 'max:18', $nikUniqueRule],
            'name' => ['required', 'string', 'min:3'],
            'position' => ['sometimes', 'nullable', 'array', 'size:3'],
            'position.id' => ['sometimes', 'nullable', 'numeric', 'exists:positions,id'],
            'position.title' => ['sometimes', 'nullable', 'string', 'exists:positions,title'],
            'position.slug' => ['sometimes', 'nullable', 'string', 'exists:positions,slug'],
            'department' => ['sometimes', 'nullable', 'array', 'size:3'],
            'department.id' => ['sometimes', 'nullable', 'numeric', 'exists:departments,id'],
            'department.section' => ['sometimes', 'nullable', 'string', 'exists:departments,section'],
            'department.slug' => ['sometimes', 'nullable', 'string', 'exists:departments,slug'],
            'role' => ['required', 'array', 'size:2'],
            'role.id' => ['required', 'numeric', 'exists:roles,id'],
            'role.name' => ['required', 'string', Rule::in([RoleEnum::IT_SUPPORT->label(), RoleEnum::STAFF->label()])],
            'email' => ['required', 'email', 'min:6', $emailUniqueRule],
            'current_password' => ['sometimes', 'nullable', 'string', 'exclude'],
            'password' => ['sometimes', $passwordRequiredRule, 'string', 'min:8'],
            'password_confirmation' => ['sometimes', Rule::when(!is_null($this->input('password')), 'required', 'nullable'), 'string', 'min:8', 'same:password', 'exclude'],
            'is_active_account' => ['required', 'boolean'],
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
            self::$employee = $this->route('employee')->load('user');
        }

        $this->merge([
            'name' => ucfirst($this->input('name')),
        ]);
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->isMethod('PUT')) {
                    if (!is_null($this->input('current_password')) && !Hash::check($this->input('current_password'), self::$employee->user->password)) {
                        $validator->errors()->add('current_password', 'Password saat ini salah.');
                    }

                    if (!is_null($this->input('password')) && Hash::check($this->input('password'), self::$employee->user->password)) {
                        $validator->errors()->add('password', 'Password tidak boleh sama dengan sebelumnya.');
                    }
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
            $validatedData['password'] = self::$employee->user->password;
        }

        return $validatedData;
    }
}
