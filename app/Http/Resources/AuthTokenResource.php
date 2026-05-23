<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'access_token' => $this->resource['access_token'],
            'user' => (new UserResource($this->resource['user']))->resolve(),
        ];
    }

    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function with($request): array
    {
        return [
            'success' => true,
        ];
    }
}
