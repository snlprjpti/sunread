<?php

namespace Modules\CheckOutMethods\Traits;

use Illuminate\Http\JsonResponse;

trait HasHandlePayementException
{
    public function handleException(object $exception): JsonResponse
    {
        return $this->errorResponse($this->getExceptionMessage($exception), $this->getExceptionStatus($exception));
    }
}
