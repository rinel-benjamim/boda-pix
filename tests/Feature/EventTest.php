<?php

use App\Models\User;
use App\Models\Event;

test('user can create event', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event',
        'description' => 'Test Description',
        'event_date' => '2026-12-31',
        'is_private' => true,
    ]);
    
    $response->assertStatus(201);
    expect(Event::count())->toBe(1);
});

test('user can list their events', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['created_by' => $user->id]);
    $event->participants()->attach($user->id, ['role' => 'admin']);
    
    $response = $this->actingAs($user)->getJson('/api/events');
    
    $response->assertStatus(200);
});
