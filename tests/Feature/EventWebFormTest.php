<?php

use App\Models\User;
use App\Models\Event;

test('web form: user can create event via web route', function () {
    $user = User::first() ?? User::factory()->create();
    
    $response = $this->actingAs($user)->post('/events', [
        'name' => 'Web Form Event',
        'description' => 'Created via web form',
        'event_date' => '2026-12-31',
        'is_private' => true,
    ]);
    
    $response->assertRedirect();
    
    $event = Event::where('name', 'Web Form Event')->first();
    expect($event)->not->toBeNull();
    expect($event->created_by)->toBe($user->id);
});

test('web form: validation errors are returned', function () {
    $user = User::first() ?? User::factory()->create();
    
    $response = $this->actingAs($user)->post('/events', [
        'description' => 'Missing required fields',
    ]);
    
    $response->assertSessionHasErrors(['name', 'event_date']);
});

test('web form: unauthenticated user is redirected', function () {
    $response = $this->post('/events', [
        'name' => 'Test Event',
        'event_date' => '2026-12-31',
    ]);
    
    $response->assertRedirect('/login');
});
