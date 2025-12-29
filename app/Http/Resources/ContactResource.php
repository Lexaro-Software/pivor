<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'job_title' => $this->job_title,
            'department' => $this->department,
            'client_id' => $this->client_id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'is_primary_contact' => $this->is_primary_contact,
            'address' => [
                'line_1' => $this->address_line_1,
                'line_2' => $this->address_line_2,
                'city' => $this->city,
                'county' => $this->county,
                'postcode' => $this->postcode,
                'country' => $this->country,
                'full' => $this->full_address,
            ],
            'linkedin_url' => $this->linkedin_url,
            'status' => $this->status,
            'notes' => $this->notes,
            'assigned_to' => $this->assigned_to,
            'communications' => CommunicationResource::collection($this->whenLoaded('communications')),
            'communications_count' => $this->when($this->communications_count !== null, $this->communications_count),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
