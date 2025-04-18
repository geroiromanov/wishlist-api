<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler
{
    public function handle(\Exception $exception): JsonResponse
    {
        Log::error($exception->getMessage());

        $statusCode = in_array($exception->getCode(), array_keys(Response::$statusTexts))
            ? $exception->getCode()
            : Response::HTTP_BAD_REQUEST;

        return response()->json([
            'success' => false,
            'message' => "Error. {$exception->getMessage()}",
        ], $statusCode);
    }
}
