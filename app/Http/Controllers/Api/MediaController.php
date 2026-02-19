<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Event;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(private MediaService $mediaService) {}

    public function index(Request $request, Event $event)
    {
        $this->authorize('view', $event);
        
        $media = $event->media()
            ->with('user')
            ->latest()
            ->paginate(20);

        return MediaResource::collection($media);
    }

    public function store(UploadMediaRequest $request, Event $event)
    {
        $this->authorize('view', $event);
        
        $uploadedMedia = [];

        foreach ($request->file('files') as $file) {
            $uploadedMedia[] = $this->mediaService->upload($event, $file, $request->user());
        }

        if ($request->wantsJson()) {
            return MediaResource::collection($uploadedMedia);
        }
        
        return back();
    }

    public function destroy(Media $media)
    {
        $this->authorize('view', $media->event);
        $this->mediaService->delete($media);
        
        return response()->json(['message' => 'Media deleted successfully']);
    }
}
