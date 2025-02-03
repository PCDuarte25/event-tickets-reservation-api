<?php

namespace App\Services;

use App\Dto\ReservationCancelDto;
use App\Dto\ReservationCreateDto;
use App\Dto\ReservationUpdateDto;
use App\Exceptions\ReservationNotInEventException;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing event reservations.
 *
 * Implements event reservation creation, modification, and cancellation.
 */
class EventService implements EventServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createReservation(ReservationCreateDto $reservationCreateDto): Reservation
    {
        return DB::transaction(function() use ($reservationCreateDto) {
            $event = $this->getLockedEvent($reservationCreateDto->event);

            $event->ensureTicketsAvailability($reservationCreateDto->tickets);

            $reservation = Reservation::create([
                'event_id' => $event->id,
                'tickets' => $reservationCreateDto->tickets,
            ]);

            $event->decrement('available_tickets', $reservationCreateDto->tickets);

            return $reservation;
        }, 3);
    }

    /**
     * {@inheritdoc}
     */
    public function changeReservationTickets(ReservationUpdateDto $reservationUpdateDto): Reservation
    {
        return DB::transaction(function() use ($reservationUpdateDto) {
            $event = $this->getLockedEvent($reservationUpdateDto->event);

            $reservation = $reservationUpdateDto->reservation->fresh();

            $this->ensureEventContainsReservation($event, $reservation);

            $ticketsDiff = $reservationUpdateDto->tickets - $reservation->tickets;

            if ($ticketsDiff > 0) {
                $event->ensureTicketsAvailability($ticketsDiff);
            }

            $event->decrement('available_tickets', $ticketsDiff);

            $reservation->tickets = $reservationUpdateDto->tickets;
            $reservation->save();

            return $reservation;
        }, 3);
    }

    /**
     * {@inheritdoc}
     */
    public function cancelReservation(ReservationCancelDto $reservationCancelDto): bool
    {
        return DB::transaction(function() use ($reservationCancelDto) {
            $event = $this->getLockedEvent($reservationCancelDto->event);

            $reservation = $reservationCancelDto->reservation->fresh();

            $this->ensureEventContainsReservation($event, $reservation);

            $event->increment('available_tickets', $reservation->tickets);

            return $reservation->delete();
        }, 3);
    }

    /**
     * Ensure that the given reservation belongs to the specified event.
     *
     * @param \App\Models\Event $event
     *   The event to check against.
     * @param \App\Models\Reservation $reservation
     *   The reservation to validate.
     *
     * @throws \App\Exceptions\ReservationNotInEventException
     *   Thrown when the reservation does not belong to the specified event.
     */
    protected function ensureEventContainsReservation(Event $event, Reservation $reservation)
    {
        if (!$reservation->event->is($event)) {
            throw new ReservationNotInEventException("This reservation doesn't belong to event: {$event->name}");
        }
    }

    /**
     * Retrieves an event with a lock for update to prevent concurrent modifications.
     *
     * @param \App\Models\Event $event
     *   The event model used to fetch the locked version from the database.
     *
     * @return \App\Models\Event
     *   The locked event instance fresh from the database.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *   Thrown if the event is not found in the database.
     */
    protected function getLockedEvent(Event $event): Event
    {
        return Event::where('id', $event->id)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
