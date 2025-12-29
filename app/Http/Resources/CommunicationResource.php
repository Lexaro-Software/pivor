<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'type' => $this->type,
            'direction' => $this->direction,
            'subject' => $this->subject,
            'content' => $this->content,
            'client_id' => $this->client_id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'contact_id' => $this->contact_id,
            'contact' => new ContactResource($this->whenLoaded('contact')),
            'due_at' => $this->due_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'priority' => $this->priority,
            'status' => $this->status,
            'is_task' => $this->is_task,
            'is_overdue' => $this->is_overdue,
            'created_by' => $this->created_by,
            'created_by_user' => $this->when($this->relationLoaded('createdBy'), function () {
                return [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                ];
            }),
            'assigned_to' => $this->assigned_to,
            'assigned_user' => $this->when($this->relationLoaded('assignedUser'), function () {
                return $this->assignedUser ? [
                    'id' => $this->assignedUser->id,
                    'name' => $this->assignedUser->name,
                ] : null;
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
