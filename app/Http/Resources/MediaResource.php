<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'type' => $this->type,
            'size' => $this->size,
            'url' => Storage::disk('s3')->temporaryUrl($this->file_path, now()->addHours(24)),
            'thumbnail_url' => $this->thumbnail_path ? Storage::disk('s3')->temporaryUrl($this->thumbnail_path, now()->addHours(24)) : null,
            'uploaded_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
