<?php

namespace Modules\Core\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseFormat
{
    private function responseFormat(?string $message = null, string $status = "success", ?object $payload = null): array
    {
        $format = [
            "status" => $status,
            "payload" => $payload,
            "message" => json_decode($message) ?? $message
        ];
        if (!$payload) unset($format["payload"]);

        return $format;
    }

    public function successResponse(object $payload, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json($this->responseFormat($message, "success", $payload), $code);
    }

    public function errorResponse(string $message, int $code = 500): JsonResponse
    {
        return response()->json($this->responseFormat($message, "error"), $code);
    }

    public function successResponseWithMessage(string $message, int $code = 200): JsonResponse
    {
        return response()->json($this->responseFormat($message), $code);
    }
}
