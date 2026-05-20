<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'access_token' => $this->resource['access_token'],
            'token_type' => $this->resource['token_type'],
            'user' => (new UserResource($this->resource['user']))->resolve(),
        ];
    }

    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function with($request)
    {
        return [
            'success' => true,
        ];
    }
}
