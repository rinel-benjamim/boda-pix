<?php

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

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
    
    $event = Event::first();
    expect($event->name)->toBe('Test Event');
    expect($event->access_code)->not->toBeNull();
    expect($event->created_by)->toBe($user->id);
    expect($event->participants()->count())->toBe(1);
});

test('user can list their events', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['created_by' => $user->id]);
    $event->participants()->attach($user->id, ['role' => 'admin']);
    
    $response = $this->actingAs($user)->getJson('/api/events');
    
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description', 'event_date', 'access_code']
            ]
        ]);
});

test('user cannot see events they are not part of', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $event = Event::factory()->create(['created_by' => $user1->id]);
    $event->participants()->attach($user1->id, ['role' => 'admin']);
    
    $response = $this->actingAs($user2)->getJson('/api/events');
    
    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

test('event requires name', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'description' => 'Test Description',
        'event_date' => '2026-12-31',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('event requires valid date', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event',
        'event_date' => 'invalid-date',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['event_date']);
});

test('unauthenticated user cannot create event', function () {
    $response = $this->postJson('/api/events', [
        'name' => 'Test Event',
        'event_date' => '2026-12-31',
    ]);
    
    $response->assertStatus(401);
});

test('unauthenticated user cannot list events', function () {
    $response = $this->getJson('/api/events');
    
    $response->assertStatus(401);
});

test('event index does not have N+1 query problem', function () {
    $user = User::factory()->create();
    
    for ($i = 0; $i < 3; $i++) {
        $event = Event::factory()->create(['created_by' => $user->id]);
        $event->participants()->attach($user->id, ['role' => 'admin']);
    }
    
    DB::enableQueryLog();
    
    $this->actingAs($user)->getJson('/api/events');
    
    $queries = DB::getQueryLog();
    
    expect(count($queries))->toBeLessThan(10);
});

test('created event returns with creator relationship loaded', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event',
        'event_date' => '2026-12-31',
    ]);
    
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'created_by' => ['id', 'name']
            ]
        ]);
});
