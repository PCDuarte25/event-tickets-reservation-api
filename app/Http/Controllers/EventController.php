<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controller to the event.
 */
class EventController extends Controller
{
    /**
     * Return a collection of events.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *   The collection info of events.
     */
    public function index(): AnonymousResourceCollection
    {
        return EventResource::collection(Event::all());
    }

    /**
     * Return a single event.
     *
     * @param \App\Models\Event $event
     *   The event model.
     *
     * @return \App\Http\Resources\EventResource
     *   The single info of an event.
     */
    public function show(Event $event): EventResource
    {
        return new EventResource($event);
    }
}
