<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_name',
        'app_footer',
        'app_logo',
        'company_name',
        'company_telp',
        'company_address',
        'company_logo',
    ];

    /**
     * Interact with the appLogo attribute.
     *
     * return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function appLogo(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !is_null($value)
                ? asset('storage/image/' . $value)
                : null,
        );
    }

    /**
     * Interact with the companyLogo attribute.
     *
     * return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function companyLogo(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !is_null($value)
                ? asset('storage/image/' . $value)
                : null,
        );
    }
}
