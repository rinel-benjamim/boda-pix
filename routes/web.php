<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/events');
    }
    return redirect('/login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/events', function () {
        $events = auth()->user()->events()->with(['creator', 'participants', 'media'])->latest()->get();
        return Inertia::render('events/index', ['events' => $events]);
    })->name('events.index');

    Route::get('/events/create', function () {
        return Inertia::render('events/create');
    })->name('events.create');

    Route::get('/events/{event}', function ($id) {
        $event = \App\Models\Event::with(['creator', 'participants'])->findOrFail($id);
        abort_unless($event->isMember(auth()->user()), 403);
        
        $media = $event->media()->with('user')->latest()->paginate(20);
        
        return Inertia::render('events/show', [
            'event' => new \App\Http\Resources\EventResource($event),
            'media' => \App\Http\Resources\MediaResource::collection($media)->response()->getData(true)
        ]);
    })->name('events.show');
});

require __DIR__.'/settings.php';
