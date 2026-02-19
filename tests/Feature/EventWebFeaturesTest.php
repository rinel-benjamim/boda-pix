<?php

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Storage::fake('s3');
    Queue::fake();
});

test('web: user can upload media to event', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['created_by' => $user->id]);
    $event->participants()->attach($user->id, ['role' => 'admin']);
    
    $file = UploadedFile::fake()->image('photo.jpg');
    
    $response = $this->actingAs($user)->post("/events/{$event->id}/media", [
        'files' => [$file],
    ]);
    
    $response->assertRedirect();
    expect($event->media()->count())->toBe(1);
});

test('web: user can join event with code', function () {
    $creator = User::factory()->create();
    $joiner = User::factory()->create();
    
    $event = Event::factory()->create(['created_by' => $creator->id]);
    $event->participants()->attach($creator->id, ['role' => 'admin']);
    
    $response = $this->actingAs($joiner)->post('/events/join', [
        'access_code' => $event->access_code,
    ]);
    
    $response->assertRedirect('/events/' . $event->id);
    expect($event->participants()->where('user_id', $joiner->id)->exists())->toBeTrue();
});

test('web: join with invalid code shows error', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post('/events/join', [
        'access_code' => 'INVALID1',
    ]);
    
    $response->assertSessionHasErrors(['access_code']);
});
