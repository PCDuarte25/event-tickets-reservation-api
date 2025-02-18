<?php

use App\Exceptions\NotEnoughTicketsException;
use App\Exceptions\ReservationNotInEventException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotEnoughTicketsException $e, $request) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->renderable(function (ReservationNotInEventException $e, $request) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        });

        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        });

        $exceptions->renderable(function (QueryException $e, $request) {
            $errorMessage = "An error occurred while processing your request, SQL error: {$e->getMessage()}";
            return response()->json(['error' => $errorMessage], 400);
        });
    })->create();
