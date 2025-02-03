<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for a reservation.
 */
class ReservationRequest extends FormRequest
{
    /**
     * Define the validation rules for the reservation request.
     *
     * @return array
     *   The validation rules.
     */
    public function rules()
    {
        return [
            'tickets' => 'required|integer|min:1',
        ];
    }
}
