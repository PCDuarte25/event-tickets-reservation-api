<?php

namespace App\Dto;

use App\Models\Event;

/**
 * Dto for reservation create request.
 */
class ReservationCreateDto
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
     * Builder.
     *
     * @param int $tickets
     *   The quantity of reserved tickets.
     * @param \App\Models\Event $event
     *   The event model.
     */
    public function __construct(int $tickets, Event $event)
    {
        $this->tickets = $tickets;
        $this->event = $event;
    }
}
