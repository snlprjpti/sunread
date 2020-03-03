<?php

namespace Modules\Core\Traits;


trait ApiResponseFormat
{
    public function successResponse($payload, $code = 200)
    {
        $format = [
            'status' => 'success',
            'payload' => $payload,
        ];
        return response()->json($format, $code);
    }

    public function errorResponse($message, $code = 500)
    {
        $format = [
            'status' => 'error',
            'message' => $message,
        ];
        return response()->json($format, $code);
    }

    public function successResponseWithMessage($payload, $message, $code = 200)
    {
        $format = [
            'status' => 'success',
            'payload' => $payload,
            'message' => $message,
        ];
        return response()->json($format, $code);
    }


}