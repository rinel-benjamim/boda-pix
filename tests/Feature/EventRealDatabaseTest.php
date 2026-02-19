<?php

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Limpar dados de teste anteriores
    DB::table('event_user')->where('user_id', '>', 0)->delete();
    DB::table('media')->where('event_id', '>', 0)->delete();
    DB::table('events')->where('name', 'like', 'Test%')->delete();
    DB::table('users')->where('email', 'like', 'test%')->delete();
});

test('real database: user can create event', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event ' . time(),
        'description' => 'Test Description',
        'event_date' => '2026-12-31',
        'is_private' => true,
    ]);
    
    $response->assertStatus(201);
    
    $event = Event::where('created_by', $user->id)->first();
    expect($event)->not->toBeNull();
    expect($event->name)->toContain('Test Event');
    expect($event->access_code)->not->toBeNull();
    expect($event->access_code)->toHaveLength(8);
    
    // Verificar se o usuário foi adicionado como participante
    $participant = DB::table('event_user')
        ->where('event_id', $event->id)
        ->where('user_id', $user->id)
        ->first();
    
    expect($participant)->not->toBeNull();
    expect($participant->role)->toBe('admin');
});

test('real database: user can list their events', function () {
    $user = User::create([
        'name' => 'Test User List',
        'email' => 'testlist' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    // Criar evento via API
    $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event List ' . time(),
        'description' => 'Test Description',
        'event_date' => '2026-12-31',
    ]);
    
    $response = $this->actingAs($user)->getJson('/api/events');
    
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'event_date',
                    'access_code',
                    'is_admin',
                    'created_by' => ['id', 'name'],
                    'participants_count',
                    'media_count'
                ]
            ]
        ]);
    
    $data = $response->json('data.0');
    expect($data['is_admin'])->toBeTrue();
    expect($data['participants_count'])->toBe(1);
    expect($data['media_count'])->toBe(0);
});

test('real database: multiple users can create events independently', function () {
    $user1 = User::create([
        'name' => 'Test User 1',
        'email' => 'testuser1' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $user2 = User::create([
        'name' => 'Test User 2',
        'email' => 'testuser2' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    // User 1 cria evento
    $response1 = $this->actingAs($user1)->postJson('/api/events', [
        'name' => 'User 1 Event ' . time(),
        'event_date' => '2026-12-31',
    ]);
    $response1->assertStatus(201);
    
    // User 2 cria evento
    $response2 = $this->actingAs($user2)->postJson('/api/events', [
        'name' => 'User 2 Event ' . time(),
        'event_date' => '2026-12-31',
    ]);
    $response2->assertStatus(201);
    
    // User 1 só vê seu evento
    $listUser1 = $this->actingAs($user1)->getJson('/api/events');
    $listUser1->assertJsonCount(1, 'data');
    
    // User 2 só vê seu evento
    $listUser2 = $this->actingAs($user2)->getJson('/api/events');
    $listUser2->assertJsonCount(1, 'data');
});

test('real database: access code is unique', function () {
    $user = User::create([
        'name' => 'Test User Code',
        'email' => 'testcode' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $codes = [];
    
    for ($i = 0; $i < 3; $i++) {
        $response = $this->actingAs($user)->postJson('/api/events', [
            'name' => 'Test Event Code ' . $i . ' ' . time(),
            'event_date' => '2026-12-31',
        ]);
        
        $response->assertStatus(201);
        $code = $response->json('data.access_code');
        
        expect($code)->not->toBeIn($codes);
        $codes[] = $code;
    }
    
    expect(count($codes))->toBe(3);
    expect(count(array_unique($codes)))->toBe(3);
});

test('real database: event date is stored correctly', function () {
    $user = User::create([
        'name' => 'Test User Date',
        'email' => 'testdate' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event Date ' . time(),
        'event_date' => '2026-06-15',
    ]);
    
    $response->assertStatus(201);
    
    $event = Event::where('created_by', $user->id)->first();
    expect($event->event_date->format('Y-m-d'))->toBe('2026-06-15');
});


test('real database: event without required fields fails', function () {
    $user = User::create([
        'name' => 'Test User Validation',
        'email' => 'testvalidation' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'description' => 'Only description',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'event_date']);
});

test('real database: event with invalid date fails', function () {
    $user = User::create([
        'name' => 'Test User Invalid Date',
        'email' => 'testinvaliddate' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event',
        'event_date' => 'not-a-date',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['event_date']);
});

test('real database: user can join event with access code', function () {
    $creator = User::create([
        'name' => 'Event Creator',
        'email' => 'creator' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $joiner = User::create([
        'name' => 'Event Joiner',
        'email' => 'joiner' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    // Criar evento
    $createResponse = $this->actingAs($creator)->postJson('/api/events', [
        'name' => 'Test Event Join ' . time(),
        'event_date' => '2026-12-31',
    ]);
    
    $accessCode = $createResponse->json('data.access_code');
    
    // Outro usuário entra com código
    $joinResponse = $this->actingAs($joiner)->postJson('/api/events/join', [
        'access_code' => $accessCode,
    ]);
    
    $joinResponse->assertStatus(200);
    
    // Verificar na base de dados
    $participant = DB::table('event_user')
        ->where('user_id', $joiner->id)
        ->first();
    
    expect($participant)->not->toBeNull();
    expect($participant->role)->toBe('participant');
    
    // Joiner agora vê o evento
    $listResponse = $this->actingAs($joiner)->getJson('/api/events');
    $listResponse->assertJsonCount(1, 'data');
});

test('real database: user cannot join with invalid code', function () {
    $user = User::create([
        'name' => 'Test User Invalid Code',
        'email' => 'testinvalidcode' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $response = $this->actingAs($user)->postJson('/api/events/join', [
        'access_code' => 'INVALID1',
    ]);
    
    $response->assertStatus(404);
});

test('real database: user cannot join same event twice', function () {
    $creator = User::create([
        'name' => 'Event Creator Twice',
        'email' => 'creatortwice' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $joiner = User::create([
        'name' => 'Event Joiner Twice',
        'email' => 'joinertwice' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    // Criar evento
    $createResponse = $this->actingAs($creator)->postJson('/api/events', [
        'name' => 'Test Event Twice ' . time(),
        'event_date' => '2026-12-31',
    ]);
    
    $accessCode = $createResponse->json('data.access_code');
    
    // Primeira vez - sucesso
    $this->actingAs($joiner)->postJson('/api/events/join', [
        'access_code' => $accessCode,
    ])->assertStatus(200);
    
    // Segunda vez - falha
    $secondJoin = $this->actingAs($joiner)->postJson('/api/events/join', [
        'access_code' => $accessCode,
    ]);
    
    $secondJoin->assertStatus(404);
});

test('real database: event creator is automatically admin', function () {
    $user = User::create([
        'name' => 'Test Creator Admin',
        'email' => 'testcreatoradmin' . time() . '@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $response = $this->actingAs($user)->postJson('/api/events', [
        'name' => 'Test Event Admin ' . time(),
        'event_date' => '2026-12-31',
    ]);
    
    $response->assertStatus(201);
    
    $eventId = $response->json('data.id');
    
    $participant = DB::table('event_user')
        ->where('event_id', $eventId)
        ->where('user_id', $user->id)
        ->first();
    
    expect($participant->role)->toBe('admin');
    
    // Verificar via API
    $listResponse = $this->actingAs($user)->getJson('/api/events');
    expect($listResponse->json('data.0.is_admin'))->toBeTrue();
});
