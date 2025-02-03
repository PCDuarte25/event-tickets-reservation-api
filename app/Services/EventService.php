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
            // Lock the event record for writing to prevent concurrent updates
            $event = $this->getLockedEvent($reservationCreateDto->event);

            // Check if the event have enough tickets.
            $event->ensureTicketsAvailability($reservationCreateDto->tickets);

            // Create the reservation.
            $reservation = Reservation::create([
                'event_id' => $event->id,
                'tickets' => $reservationCreateDto->tickets,
            ]);

            // Update the amount of tickets.
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
            // Lock the event record for writing to prevent concurrent updates.
            $event = $this->getLockedEvent($reservationUpdateDto->event);

            // Reload reservation to get the latest state.
            $reservation = $reservationUpdateDto->reservation->fresh();

            // Verify the reservation belongs to the specified event.
            $this->ensureEventContainsReservation($event, $reservation);

            // Calculate difference between requested tickets and current reservation.
            // Example:
            // - Current reservation: 10 tickets | Requested: 12 tickets => Difference: +2 (addition)
            // - Current reservation: 10 tickets | Requested: 8 tickets  => Difference: -2 (removal).
            $ticketsDiff = $reservationUpdateDto->tickets - $reservation->tickets;

            // If adding tickets, verify event availability.
            if ($ticketsDiff > 0) {
                $event->ensureTicketsAvailability($ticketsDiff);
            }

            // Update event's available tickets:
            // - For additions: subtract difference from available (10 available - 2 = 8)
            // - For removals: add absolute value of difference (10 available - (-2) = 12).
            $event->decrement('available_tickets', $ticketsDiff);

            // Update reservation with new ticket count and persist.
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
            // Lock the event record for writing to prevent concurrent updates.
            $event = $this->getLockedEvent($reservationCancelDto->event);

            // Reload reservation to get the latest state.
            $reservation = $reservationCancelDto->reservation->fresh();

            // Verify the reservation belongs to the specified event.
            $this->ensureEventContainsReservation($event, $reservation);

            // Update the amount of tickets.
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
