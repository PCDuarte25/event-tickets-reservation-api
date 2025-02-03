<?php

namespace App\Services;

use App\Dto\ReservationCreateDto;
use App\Dto\ReservationUpdateDto;
use App\Dto\ReservationCancelDto;
use App\Models\Reservation;

/**
 * Interface for event reservation services.
 *
 * Defines the contract for handling event reservations, including creation,
 * modification, and cancellation.
 */
interface EventServiceInterface
{
    /**
     * Create a new reservation for an event.
     *
     * @param \App\Dto\ReservationCreateDto $reservationCreateDto
     *   The Dto containing the reservation details.
     *
     * @return \App\Models\Reservation
     *   The created reservation instance.
     */
    public function createReservation(ReservationCreateDto $reservationCreateDto): Reservation;

    /**
     * Change the number of tickets in an existing reservation.
     *
     * @param \App\Dto\ReservationUpdateDto $reservationUpdateDto
     *   The Dto containing the updated reservation details.
     *
     * @return \App\Models\Reservation
     *   The updated reservation instance.
     */
    public function changeReservationTickets(ReservationUpdateDto $reservationUpdateDto): Reservation;

    /**
     * Cancel an existing reservation.
     *
     * @param \App\Dto\ReservationCancelDto $reservationCancelDto
     *   The Dto containing the reservation cancellation details.
     *
     * @return bool
     *   True if the reservation was successfully canceled, false otherwise.
     */
    public function cancelReservation(ReservationCancelDto $reservationCancelDto): bool;
}
