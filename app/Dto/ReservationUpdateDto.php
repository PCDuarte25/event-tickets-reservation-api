<?php

namespace App\Dto;

use App\Models\Event;
use App\Models\Reservation;

/**
 * Dto for reservation update request.
 */
class ReservationUpdateDto
{
    /**
     * The quantity of reserved tickets.
     *
     * @var int
     */
    public int $tickets;


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
     * @param int $tickets
     *   The quantity of reserved tickets.
     * @param \App\Models\Event $event
     *   The event model.
     * @param \App\Models\Reservation $reservation
     *   The reservation model.
     */
    public function __construct(int $tickets, Event $event, Reservation $reservation)
    {
        $this->tickets = $tickets;
        $this->event = $event;
        $this->reservation = $reservation;
    }
}
