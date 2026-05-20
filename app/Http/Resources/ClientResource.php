<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ClientResource extends BaseJsonResource
{
    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'contacts_count' => $this->whenCounted('contacts'),
            'primary_contact' => new ContactResource($this->whenLoaded('primaryContact')),
            'contacts' => $this->when(
                $this->relationLoaded('contacts'),
                fn () => ContactResource::collection($this->contacts)->resolve()
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
