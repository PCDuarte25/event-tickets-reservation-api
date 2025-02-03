<?php

namespace App\Models;

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
 * @property int $availabe_tickets
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
}
