<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return $event->isMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Event $event): bool
    {
        return $event->isAdmin($user);
    }

    public function delete(User $user, Event $event): bool
    {
        return $event->created_by === $user->id;
    }

    public function uploadMedia(User $user, Event $event): bool
    {
        return $event->isMember($user);
    }
}
