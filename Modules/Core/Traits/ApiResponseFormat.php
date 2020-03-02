<?php

namespace Modules\Core\Traits;


trait ApiResponseFormat
{
    public function successResponse($payload, $code)
    {
        $format = [
            'status' => 'success',
            'payload' => $payload,
        ];
        return response()->json($format, $code);
    }

    public function errorResponse($message, $code)
    {
        $format = [
            'status' => 'error',
            'message' => $message,
        ];
        return response()->json($format, $code);
    }

    public function successResponseWithMessage($payload, $message, $code)
    {
        $format = [
            'status' => 'success',
            'payload' => $payload,
            'message' => $message,
        ];
        return response()->json($format, $code);
    }


}