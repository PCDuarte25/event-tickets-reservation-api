<?php

namespace App\Http\Controllers;

use App\Dto\ReservationCancelDto;
use App\Dto\ReservationCreateDto;
use App\Dto\ReservationUpdateDto;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Event;
use App\Models\Reservation;
use App\Services\EventServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Controller to reservation.
 */
class ReservationController extends Controller
{
    /**
     * The service to deal with event operations.
     *
     * @var \App\Services\EventServiceInterface
     */
    protected $eventService;

    /**
     * Builder.
     *
     * @param \App\Services\EventServiceInterface $eventService
     *   The service to deal with event operations.
     */
    public function __construct(EventServiceInterface $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Store a new reservation for an event.
     *
     * @param \App\Http\Requests\ReservationRequest $request
     *   The request containing the reservation data.
     * @param \App\Models\Event $event
     *   The event for which the reservation is being made.
     *
     * @return \Illuminate\Http\JsonResponse
     *   The created reservation data.
     */
    public function store(ReservationRequest $request, Event $event): JsonResponse
    {
        $data = $request->validated();
        $requestCreateDto = new ReservationCreateDto($data['tickets'], $event);

        $reservation = $this->eventService->createReservation($requestCreateDto);

        return response()->json($reservation, Response::HTTP_CREATED);
    }

    /**
     * Update an existing reservation for an event.
     *
     * @param \App\Http\Requests\ReservationRequest $request
     *   The request containing the reservation data.
     * @param \App\Models\Event $event
     *   The event associated with the reservation.
     * @param \App\Models\Reservation $reservation
     *   The reservation being updated.
     *
     * @return \Illuminate\Http\JsonResponse
     *   The updated reservation data.
     */
    public function update(ReservationRequest $request, Event $event, Reservation $reservation)
    {
        $data = $request->validated();
        $reservationUpdateDto = new ReservationUpdateDto($data['tickets'], $event, $reservation);

        $reservation = $this->eventService->changeReservationTickets($reservationUpdateDto);

        return response()->json($reservation, Response::HTTP_CREATED);
    }

    /**
     * Cancel and delete a reservation for an event.
     *
     * @param \App\Models\Event $event
     *   The event associated with the reservation.
     * @param \App\Models\Reservation $reservation
     *   The reservation being canceled.
     *
     * @return \Illuminate\Http\JsonResponse
     *   A response indicating successful deletion.
     */
    public function destroy(Event $event, Reservation $reservation)
    {
        $reservationCancelDto = new ReservationCancelDto($event, $reservation);

        $this->eventService->cancelReservation($reservationCancelDto);

        return response()->noContent();
    }

    /**
     * Retrieves a list of all reservations associated with the specified event.
     *
     * @param \App\Models\Event $event
     *   The event associated with the reservation.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *   A collection of reservations.
     */
    public function index(Event $event): AnonymousResourceCollection
    {
        $reservations = Reservation::where('event_id', $event->id)->get();

        return ReservationResource::collection($reservations);
    }
}
