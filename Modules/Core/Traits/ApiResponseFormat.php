<?php

namespace Modules\Core\Traits;


use Illuminate\Support\Str;

trait ApiResponseFormat
{
    /**
     * Returns a valid response format
     * 
     * @param Object $payload
     * @param String $message
     * @param String $success
     * @return Array
     */
    private function responseFormat($payload = null, $message = null, $status = "success")
    {
        $format = [
            "status" => $status,
            "payload" => $payload,
            "message" => $message
        ];
        if (!$payload) unset($format["payload"]);

        return $format;
    }

    /**
     * Retursn a success response with payload
     * 
     * @param Object $payload
     * @param String $message
     * @param Int $code
     * 
     * @return JsonResponse
     */
    public function successResponse($payload, $message = null, $code = 200)
    {
        return response()->json($this->responseFormat($payload, $message), $code);
    }

    /**
     * Retursn a error response
     * 
     * @param String $message
     * @param Int $code
     * 
     * @return JsonResponse
     */
    public function errorResponse($message, $code = 500)
    {
        return response()->json($this->responseFormat(null, $message, "error"), $code);
    }

    /**
     * Retursn a success response with message only
     * 
     * @param String $message
     * @param Int $code
     * 
     * @return JsonResponse
     */
    public function successResponseWithMessage($message, $code = 200)
    {
        return response()->json($this->responseFormat(null, $message), $code);
    }

    /**
     * Retursn a success response with message only
     * 
     * @param \Exception $exception
     * 
     * @return JsonResponse
     */
    public function errorResponseForMissingModel($exception)
    {
        $model = Str::kebab( class_basename($exception->getModel()));
        $message = $model ? str_replace('-', ' ', $model) . " not found" : $exception->getMessage();

        return response()->json($this->responseFormat(null, $message, "error"), 404);
    }

}
