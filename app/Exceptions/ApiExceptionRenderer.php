<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiExceptionRenderer
{
    public static function errorResponse($code, $message = null)
    {
        switch ($code) {
            case 403:
                $message = 'Forbidden';
                break;

            case 404:
                $message = 'Not Found';
                break;

            case 405:
                $message = 'Method Not Allowed';
                break;

            case 422:
                $message = 'Invalid Data';
                break;

            case 500:
                $message = 'Unexpected Error';
                break;

            default:
                $message = $message ?: 'Unknown Error';
                break;
        }

        return response()->json(['error' => $message], $code);
    }

    public static function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return self::errorResponse(403);
        }

        if ($exception instanceof AuthorizationException) {
            return self::errorResponse(403);
        }

        if ($exception instanceof ModelNotFoundException) {
            return self::errorResponse(404);
        }

        if ($exception instanceof HttpException) {
            return self::errorResponse($exception->getStatusCode());
        }

        if ($exception instanceof ValidationException) {
            // FIXME: expose validation error messages ?
            return self::errorResponse(422);
        }

        return self::errorResponse(500);
    }
}
