<?php

namespace Modules\CheckOutMethods\Traits;

use Illuminate\Http\JsonResponse;

trait HasHandlePayementException
{
    // protected $exception_statuses;

    // public function getExceptionStatus(object $exception): int
    // {
    //     return $this->exception_statuses[get_class($exception)] ?? 500;
    // }

    // public function getExceptionMessage(object $exception): string
    // {
    //     switch(get_class($exception))
    //     {
    //         case ValidationException::class:
    //             $exception_message = json_encode($exception->errors());
    //         break;

    //         case ModelNotFoundException::class:
    //             $class = $exception->getModel();
    //             $path = explode('\\', $class);
    //             $model  = array_pop($path);
    //             $model_name = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $model);
    //             $exception_message = $this->lang('not-found', [ 'name' => $model_name ]);
    //         break;

    //         case QueryException::class:
    //             $exception_message = $exception->errorInfo[1] == 1062 ? "Duplicate Entry" : $exception->getMessage();
    //         break;

    //         default:
    //             $exception_message = $exception->getMessage();
    //         break;
    //     }

    //     return $exception_message;
    // }

    // public function handleException(object $exception): JsonResponse
    // {
    //     return $this->errorResponse($this->getExceptionMessage($exception), $this->getExceptionStatus($exception));
    // }
}
