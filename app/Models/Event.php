<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'cover_image',
        'event_date',
        'created_by',
        'access_code',
        'is_private',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_private' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($event) {
            if (empty($event->access_code)) {
                $event->access_code = strtoupper(Str::random(8));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function isAdmin(User $user): bool
    {
        return $this->created_by === $user->id || 
               $this->participants()->wherePivot('user_id', $user->id)->wherePivot('role', 'admin')->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->created_by === $user->id || 
               $this->participants()->wherePivot('user_id', $user->id)->exists();
    }
}
