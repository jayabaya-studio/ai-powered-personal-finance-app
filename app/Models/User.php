<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasFactory, Notifiable, Authenticatable, Authorizable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'current_family_id',
        'profile_photo_path',
        'timezone',
        'payday',
        'income_source',
        'location',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function cards(): HasMany
    {
        return $this->hasMany(UserCard::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function familySpaces(): BelongsToMany
    {
        return $this->belongsToMany(Family::class, 'family_space_user', 'user_id', 'family_space_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function currentFamilySpace()
    {
        return $this->belongsTo(Family::class, 'current_family_id');
    }

    public function ownsFamilySpace(Family $family): bool
    {
        return $this->id === $family->owner_user_id;
    }

    public function isMemberOfFamilySpace(Family $family): bool
    {
        return $this->familySpaces->contains($family);
    }
}
