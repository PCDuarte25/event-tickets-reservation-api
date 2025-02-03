<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a reservation for an event.
 *
 * @property int $id
 *   The identifier for the reservation.
 * @property int $event_id
 *   The ID of the associated event.
 * @property int $tickets
 *   The number of tickets reserved.
 * @property \App\Models\Event $event
 *   The related event model.
 */
class Reservation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'tickets',
    ];

    /**
     * Get the event associated with the reservation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *   The relationship with the Event model.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
