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
    public static function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return response('', 403);
        }

        if ($exception instanceof AuthorizationException) {
            return response('', 403);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response('', 404);
        }

        if ($exception instanceof ValidationException) {
            return response('', 422);
        }

        if ($exception instanceof HttpException) {
            return response('', $exception->getStatusCode());
        }

        return response('', 400);
    }
}
