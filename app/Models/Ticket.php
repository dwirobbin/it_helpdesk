<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Traits\Sluggable;
use App\Enums\TicketStatusEnum;
use App\Traits\GenerateSerialNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory, Sluggable, GenerateSerialNumber;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_number',
        'title',
        'slug',
        'description',
        'image',
        'user_id',
        'status',
    ];

    protected $sluggable = [
        'source' => 'title',
        'slug_column' => 'slug',
    ];

    protected $serialNumber = [
        'prefix' => 'T',
        'serial_column' => 'ticket_number',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => TicketStatusEnum::class,
    ];

    /**
     * Interact with the image attribute.
     *
     * return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function image(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !is_null($value)
                ? asset('storage/image/complaints/' . $value)
                : null,
        );
    }

    /**
     * Get the user that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the respond associated with the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function respond(): HasOne
    {
        return $this->hasOne(Respond::class);
    }

    /**
     * Get all of the comments for the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketChats(): HasMany
    {
        return $this->hasMany(TicketChat::class);
    }
}
