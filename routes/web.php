<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/events');
    }
    return redirect('/login');
})->name('home');

Route::get('/pwa-debug', function () {
    return view('pwa-debug');
})->name('pwa.debug');

Route::middleware(['auth'])->group(function () {
    Route::get('/events', function () {
        $events = auth()->user()->events()->with(['creator'])->withCount(['participants', 'media'])->latest()->get();
        
        $eventsData = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'cover_image' => $event->cover_image ? \Storage::disk('s3')->temporaryUrl($event->cover_image, now()->addHours(24)) : null,
                'event_date' => $event->event_date->format('Y-m-d'),
                'access_code' => $event->access_code,
                'is_private' => $event->is_private,
                'participants_count' => $event->participants_count,
                'media_count' => $event->media_count,
                'created_at' => $event->created_at->toISOString(),
            ];
        });
        
        return Inertia::render('events/index', ['events' => $eventsData]);
    })->name('events.index');

    Route::get('/events/create', function () {
        return Inertia::render('events/create');
    })->name('events.create');
    
    Route::get('/events/join', function () {
        return Inertia::render('events/join');
    })->name('events.join');
    
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    
    Route::post('/events/join', function (\Illuminate\Http\Request $request) {
        $request->validate(['access_code' => 'required|string|size:8']);
        
        $eventService = app(\App\Services\EventService::class);
        $event = $eventService->joinByCode($request->access_code, $request->user());
        
        if (!$event) {
            return back()->withErrors(['access_code' => 'Código inválido ou já és membro']);
        }
        
        return redirect('/events/' . $event->id)->with('success', 'Entraste no evento com sucesso!');
    })->name('events.join.submit');
    
    Route::post('/events/{event}/media', [\App\Http\Controllers\Api\MediaController::class, 'store'])
        ->where('event', '[0-9]+')
        ->name('events.media.store');

    Route::get('/events/{event}', function ($id) {
        $event = \App\Models\Event::with(['creator', 'participants'])->findOrFail($id);
        abort_unless($event->isMember(auth()->user()), 403);
        
        $media = $event->media()->with('user')->latest()->paginate(20);
        
        return Inertia::render('events/show', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'cover_image' => $event->cover_image ? \Storage::disk('s3')->temporaryUrl($event->cover_image, now()->addHours(24)) : null,
                'event_date' => $event->event_date->format('Y-m-d'),
                'access_code' => $event->access_code,
                'is_private' => $event->is_private,
                'is_admin' => $event->isAdmin(auth()->user()),
                'created_by' => [
                    'id' => $event->creator->id,
                    'name' => $event->creator->name,
                ],
                'participants_count' => $event->participants()->count(),
                'media_count' => $event->media()->count(),
                'created_at' => $event->created_at->toISOString(),
            ],
            'media' => \App\Http\Resources\MediaResource::collection($media)->response()->getData(true)
        ]);
    })->name('events.show');
});

require __DIR__.'/settings.php';
