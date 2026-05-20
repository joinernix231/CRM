<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ContactResource extends BaseJsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'is_primary' => (bool) $this->is_primary,
            'user_id' => $this->when(isset($this->user_id), $this->user_id),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
