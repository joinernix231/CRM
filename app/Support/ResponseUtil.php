<?php

namespace App\Support;

class ResponseUtil
{
    public static function makeResponse(string $message, mixed $data): array
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];
    }

    public static function makeError(string $message, array $errors = []): array
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== []) {
            $response['data'] = $errors;
        }

        return $response;
    }
}
