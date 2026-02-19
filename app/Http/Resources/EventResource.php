<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'cover_image' => $this->cover_image ? Storage::disk('s3')->url($this->cover_image) : null,
            'event_date' => $this->event_date->format('Y-m-d'),
            'access_code' => $this->access_code,
            'is_private' => $this->is_private,
            'is_admin' => $this->isAdmin($request->user()),
            'created_by' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ],
            'participants_count' => $this->participants_count ?? $this->participants()->count(),
            'media_count' => $this->media_count ?? $this->media()->count(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
