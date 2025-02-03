<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_race_condition()
    {
        $event = Event::factory()->create(['available_tickets' => 5]);

        $successfulResponse = $this->postJson("/api/events/{$event->id}/reservations", ['tickets' => 3]);
        $unsuccessfulResponse = $this->postJson("/api/events/{$event->id}/reservations", ['tickets' => 3]);

        $this->assertCount(1, Reservation::where('event_id', $event->id)->get());

        $this->assertEquals(2, $event->fresh()->available_tickets);

        $this->assertEquals(201, $successfulResponse->status());
        $this->assertEquals(422, $unsuccessfulResponse->status());

        $successfulResponse ->assertJsonStructure([
            'id', 'event_id', 'tickets', 'created_at'
        ]);

        $unsuccessfulResponse->assertJson([
            'error' => 'There are no more tickets available for this event.'
        ]);
    }
}
