<?php

use App\Models\Event;
use App\Models\User;

test('user can access join event page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/events/join');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('events/join'));
});

test('user can join event via join page', function () {
    $creator = User::factory()->create();
    $joiner = User::factory()->create();

    $event = Event::factory()->create([
        'created_by' => $creator->id,
        'access_code' => 'TEST1234',
    ]);
    $event->participants()->attach($creator->id, ['role' => 'admin']);

    $response = $this->actingAs($joiner)
        ->from('/events/join')
        ->post('/events/join', [
            'access_code' => 'TEST1234',
        ]);

    $response->assertRedirect("/events/{$event->id}");
    $this->assertTrue($event->fresh()->participants()->where('user_id', $joiner->id)->exists());
});

test('join page shows error for invalid code', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/events/join', [
        'access_code' => 'INVALID1',
    ]);

    $response->assertSessionHasErrors('access_code');
});

test('event show page has invite link', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['created_by' => $user->id]);
    $event->participants()->attach($user->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->get("/events/{$event->id}");

    $response->assertOk();
    expect($event->access_code)->not->toBeNull();
});
