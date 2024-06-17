<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Respond extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'text',
        'image',
    ];

    /**
     * Get the ticket that owns the Respond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the Respond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Interact with the image attribute.
     *
     * return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function image(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !is_null($value)
                ? asset('storage/image/responds/' . $value)
                : null,
        );
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->whereHas('ticket', fn ($query) => $query->where('slug', $value))
            ->select(['id', 'ticket_id', 'text', 'image'])
            ->firstOrFail();
    }
}
