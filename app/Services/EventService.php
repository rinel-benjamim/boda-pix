<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class EventService
{
    public function create(array $data, User $user): Event
    {
        if (isset($data['cover_image'])) {
            $data['cover_image'] = Storage::disk('s3')->put('events/covers', $data['cover_image']);
        }

        $data['created_by'] = $user->id;
        
        $event = Event::create($data);
        $event->participants()->attach($user->id, ['role' => 'admin']);

        return $event;
    }

    public function update(Event $event, array $data): Event
    {
        if (isset($data['cover_image'])) {
            if ($event->cover_image) {
                Storage::disk('s3')->delete($event->cover_image);
            }
            $data['cover_image'] = Storage::disk('s3')->put('events/covers', $data['cover_image']);
        }

        $event->update($data);
        return $event->fresh();
    }

    public function delete(Event $event): bool
    {
        if ($event->cover_image) {
            Storage::disk('s3')->delete($event->cover_image);
        }

        foreach ($event->media as $media) {
            Storage::disk('s3')->delete($media->file_path);
            if ($media->thumbnail_path) {
                Storage::disk('s3')->delete($media->thumbnail_path);
            }
        }

        return $event->delete();
    }

    public function joinByCode(string $code, User $user): ?Event
    {
        $event = Event::where('access_code', $code)->first();
        
        if (!$event || $event->isMember($user)) {
            return null;
        }

        $event->participants()->attach($user->id, ['role' => 'participant']);
        return $event;
    }
}
