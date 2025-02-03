<?php

namespace App\Dto;

use App\Models\Event;
use App\Models\Reservation;

/**
 * Dto for reservation cancel request.
 */
class ReservationCancelDto
{
    /**
     * The event model.
     *
     * @var \App\Models\Event
     */
    public Event $event;

    /**
     * The reservation model.
     *
     * @var \App\Models\Reservation
     */
    public Reservation $reservation;

    /**
     * Builder.
     *
     * @param \App\Models\Event $event
     *   The event model.
     * @param \App\Models\Reservation $reservation
     *   The reservation model.
     */
    public function __construct(Event $event, Reservation $reservation)
    {
        $this->event = $event;
        $this->reservation = $reservation;
    }
}
