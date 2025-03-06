<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->name,
            'price' => $this->price,
            'user' => $this->whenLoaded('user'),
            'provider' => $this->whenLoaded('provider'),
            'service' => $this->whenLoaded('service'),
        ];
    }
}
