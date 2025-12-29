<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'trading_name' => $this->trading_name,
            'display_name' => $this->display_name,
            'registration_number' => $this->registration_number,
            'vat_number' => $this->vat_number,
            'type' => $this->type,
            'status' => $this->status,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => [
                'line_1' => $this->address_line_1,
                'line_2' => $this->address_line_2,
                'city' => $this->city,
                'county' => $this->county,
                'postcode' => $this->postcode,
                'country' => $this->country,
                'full' => $this->full_address,
            ],
            'industry' => $this->industry,
            'employee_count' => $this->employee_count,
            'annual_revenue' => $this->annual_revenue,
            'notes' => $this->notes,
            'assigned_to' => $this->assigned_to,
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'communications' => CommunicationResource::collection($this->whenLoaded('communications')),
            'contacts_count' => $this->when($this->contacts_count !== null, $this->contacts_count),
            'communications_count' => $this->when($this->communications_count !== null, $this->communications_count),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
