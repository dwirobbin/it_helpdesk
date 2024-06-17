<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'footer',
        'company_name',
        'company_telp',
        'company_address',
        'logo',
    ];

    /**
     * Interact with the logo attribute.
     *
     * return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function logo(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !is_null($value)
                ? asset('storage/image/' . $value)
                : null,
        );
    }
}
