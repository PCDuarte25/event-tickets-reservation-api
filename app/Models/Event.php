<?php

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

/**
 * The event model.
 *
 * @property int $id
 *   The identifier for the event.
 * @property string $name
 *   The name of the event.
 * @property string $description
 *   A brief description of the event.
 * @property string $date
 *   The date of the event.
 * @property int $available_tickets
 *   The number of tickets available for the event.
 */
class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'date', 'available_tickets'];

    /**
     * Ensure there are enough tickets on the event available for a reservation.
     *
     * @param int $requestedTickets
     *   The number of tickets requested.
     *
     * @throws \App\Exceptions\NotEnoughTicketsException
     *   Thrown when there are not enough tickets available.
     */
    public function ensureTicketsAvailability(int $requestedTickets)
    {
        if ($this->available_tickets < $requestedTickets) {
            throw new NotEnoughTicketsException('There are no more tickets available for this event.');
        }
    }
}
