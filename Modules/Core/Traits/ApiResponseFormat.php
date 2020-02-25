<?php

namespace Modules\Core\Traits;

trait ApiResponseFormat
{
    public function successResponse($code, $payload, $message = null)
    {
        $format = [
            'status' => 'success',
            'payload' => $payload,
        ];
        $format = isset($message)? array_merge($format,['message'=> $message]):$format;
        return response()->json($format, $code);

    }

    public function errorResponse($code, $message = null)
    {
        $format = [
            'status' => 'error',
        ];
        $format = isset($message)? array_merge($format,['message'=> $message]):$format;

        return response()->json($format, $code);
    }

}