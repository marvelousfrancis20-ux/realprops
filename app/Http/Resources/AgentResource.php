<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->whenLoaded('agentProfile');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp ?? optional($profile)->whatsapp,
            'avatar' => $this->avatarUrl(),
            'agency_name' => optional($profile)->agency_name,
            'years_experience' => optional($profile)->years_experience,
            'verified' => $this->isVerifiedAgent(),
            'bio' => optional($profile)->bio ?? $this->bio,
        ];
    }
}
