<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Support\ResponseUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class Controller extends BaseController
{
    public function sendResponse(mixed $result, string $message, int $code = 200): JsonResponse
    {
        if ($result instanceof JsonResource) {
            $payload = $result->response()->getData(true);
            $payload['message'] = $message;

            return response()->json($payload, $code);
        }

        if (is_array($result) && array_key_exists('data', $result)) {
            $result['message'] = $message;

            return response()->json($result, $code);
        }

        return response()->json(ResponseUtil::makeResponse($message, $result), $code);
    }

    public function sendError(string $error, int $code = 400): JsonResponse
    {
        return response()->json(ResponseUtil::makeError($error), $code);
    }
}
