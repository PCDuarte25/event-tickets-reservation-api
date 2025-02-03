<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource class for transforming reservation data into a JSON response.
 */
class ReservationResource extends JsonResource
{
    /**
     * Transform the reservation resource into an array.
    */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'tickets' => $this->tickets,
            'created_at' => $this->created_at,
        ];
    }
}
