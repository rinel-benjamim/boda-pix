<?php

use App\Models\Event;
use App\Models\User;

test('events page shows created and joined events separately', function () {
    $creator = User::factory()->create();
    $joiner = User::factory()->create();

    // Criar evento pelo creator
    $createdEvent = Event::factory()->create([
        'created_by' => $creator->id,
        'name' => 'Created Event',
    ]);
    $createdEvent->participants()->attach($creator->id, ['role' => 'admin']);

    // Joiner entra no evento
    $createdEvent->participants()->attach($joiner->id, ['role' => 'participant']);

    // Criar evento pelo joiner
    $joinedEvent = Event::factory()->create([
        'created_by' => $joiner->id,
        'name' => 'Joined Event',
    ]);
    $joinedEvent->participants()->attach($joiner->id, ['role' => 'admin']);

    // Creator entra no evento do joiner
    $joinedEvent->participants()->attach($creator->id, ['role' => 'participant']);

    // Verificar página do creator
    $response = $this->actingAs($creator)->get('/events');
    $response->assertOk();
    
    $events = $response->viewData('page')['props']['events'];
    expect($events)->toHaveCount(2);
    
    // Verificar que o creator vê ambos os eventos
    $eventNames = collect($events)->pluck('name')->toArray();
    expect($eventNames)->toContain('Created Event');
    expect($eventNames)->toContain('Joined Event');
});

test('user can search events', function () {
    $user = User::factory()->create();

    $event1 = Event::factory()->create([
        'created_by' => $user->id,
        'name' => 'Casamento João',
    ]);
    $event1->participants()->attach($user->id, ['role' => 'admin']);

    $event2 = Event::factory()->create([
        'created_by' => $user->id,
        'name' => 'Aniversário Maria',
    ]);
    $event2->participants()->attach($user->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->get('/events');
    $response->assertOk();
    
    $events = $response->viewData('page')['props']['events'];
    expect($events)->toHaveCount(2);
});

test('events page includes user id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/events');
    $response->assertOk();
    
    $userData = $response->viewData('page')['props']['user'];
    expect($userData['id'])->toBe($user->id);
});
