<?php

namespace App\Services;

use App\Jobs\GenerateThumbnailJob;
use App\Models\Event;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function upload(Event $event, UploadedFile $file, User $user): Media
    {
        $type = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
        $path = Storage::disk('s3')->put("events/{$event->id}/media", $file);

        $media = Media::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'file_path' => $path,
            'type' => $type,
            'size' => $file->getSize(),
        ]);

        GenerateThumbnailJob::dispatch($media);

        return $media;
    }

    public function delete(Media $media): bool
    {
        Storage::disk('s3')->delete($media->file_path);
        
        if ($media->thumbnail_path) {
            Storage::disk('s3')->delete($media->thumbnail_path);
        }

        return $media->delete();
    }

    public function getUrl(Media $media): string
    {
        return Storage::disk('s3')->url($media->file_path);
    }

    public function getThumbnailUrl(Media $media): ?string
    {
        return $media->thumbnail_path ? Storage::disk('s3')->url($media->thumbnail_path) : null;
    }
}
