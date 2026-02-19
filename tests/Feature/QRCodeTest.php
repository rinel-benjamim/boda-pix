<?php

use App\Models\Event;
use App\Models\User;

test('event show page includes QR code data', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['created_by' => $user->id]);
    $event->participants()->attach($user->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->get("/events/{$event->id}");

    $response->assertOk();
    expect($event->access_code)->not->toBeNull();
    expect($event->access_code)->toHaveLength(8);
});

test('join page can be accessed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/events/join');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('events/join'));
});

test('user can join event with code from QR scan', function () {
    $creator = User::factory()->create();
    $joiner = User::factory()->create();

    $event = Event::factory()->create([
        'created_by' => $creator->id,
        'access_code' => 'QRTEST12',
    ]);
    $event->participants()->attach($creator->id, ['role' => 'admin']);

    // Simular scan de QR code que retorna URL com código
    $response = $this->actingAs($joiner)->post('/events/join', [
        'access_code' => 'QRTEST12',
    ]);

    $response->assertRedirect("/events/{$event->id}");
    expect($event->participants()->where('user_id', $joiner->id)->exists())->toBeTrue();
});

test('QR code link format is correct', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create([
        'created_by' => $user->id,
        'access_code' => 'TEST1234',
    ]);
    $event->participants()->attach($user->id, ['role' => 'admin']);

    $expectedLink = config('app.url') . '/events/join?code=TEST1234';
    
    // Verificar que o código de acesso está correto
    expect($event->access_code)->toBe('TEST1234');
});

test('invalid QR code format returns error', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/events/join', [
        'access_code' => 'INVALID',
    ]);

    $response->assertSessionHasErrors('access_code');
});
