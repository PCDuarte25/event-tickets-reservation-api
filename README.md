# Event Ticket Reservation Service RESTful API

A Laravel-based RESTful API for managing event ticket reservations, allowing users to view events, create, update, and cancel reservations while ensuring concurrency safety and data consistency.

## Features

- View a list of events with details (name, date, available tickets).
- Create, update, and cancel ticket reservations.
- Real-time ticket availability checks to prevent overbooking.
- Concurrency-safe reservation management using database transactions.
- RESTful endpoints with JSON responses.
- Error handling with meaningful HTTP status codes and messages.
- Dockerized environment for easy setup.

## Technologies

- **PHP 8.2** with **Laravel 11**
- **PostgreSQL** for persistent storage
- **Docker** and **Docker Compose** for containerization

## Installation

### Prerequisites

- Docker and Docker Compose installed
- (Optional) PostgreSQL client if testing locally
- The `.env` is already ready don't need to be changed

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/event-tickets-reservation-api.git
   cd event-tickets-reservation-api
   ```

2. **Start the dockers containers**
    ```bash
    export UID=$(id -u)
    export GID=$(id -g)
    docker-compose up -d
    ```

3. **Run database migrations and seed events**
    - ps: If you have postgres intalled locally you can run the seeder and migrations without enter the docker shell,

    ```bash
    docker exec -it event-tickets-reservation-api_app_1 bash
    php artisan migrate
    php artisan db:seed --class=EventsTableSeeder
    exit
    ```

## API endpoints

The API will be available at `http://localhost:8000`

### Events

- List all events
    * `GET /api/events`
- list a specific event by a given ID
    * `GET /api/events/{event_id}`

### Reservations

- Create a reservation for an event
    * `POST /api/events/{event_id}/reservations`
    * **body**: `{ "tickets": 2 }`

- Update ticket count in a reservation
    * `PUT /api/events/{event_id}/reservations/{reservation_id}`
    * **body**: `{ "tickets": 2 }`

- Cancel a reservation
    * `DELETE /api/events/{event_id}/reservations/{reservation_id}`

### Responses:
- `200` Success
- `400` Invalid request
- `404` Resource not found
- `422` Insufficient tickets

## Testing bonus:
Run the concurrency test to verify reservation integrity

```bash
docker exec -it event-tickets-reservation-api_app_1 bash
php artisan test --filter=ReservationConcurrencyTest
```
