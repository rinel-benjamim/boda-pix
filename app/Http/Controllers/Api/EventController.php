<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(Request $request)
    {
        $events = Event::whereHas('participants', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })
        ->with('creator')
        ->withCount(['participants', 'media'])
        ->latest()
        ->get();

        return EventResource::collection($events);
    }

    public function store(StoreEventRequest $request)
    {
        $event = $this->eventService->create($request->validated(), $request->user());
        return (new EventResource($event->load('creator')))->response()->setStatusCode(201);
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);
        return new EventResource($event->load(['creator', 'participants', 'media']));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $event = $this->eventService->update($event, $request->validated());
        return new EventResource($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $this->eventService->delete($event);
        return response()->json(['message' => 'Event deleted successfully']);
    }

    public function join(Request $request)
    {
        $request->validate(['access_code' => 'required|string|size:8']);
        
        $event = $this->eventService->joinByCode($request->access_code, $request->user());
        
        if (!$event) {
            return response()->json(['message' => 'Invalid code or already a member'], 404);
        }

        return new EventResource($event);
    }
}
