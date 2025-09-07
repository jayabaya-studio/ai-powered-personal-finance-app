<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $table = 'family_spaces';

    protected $fillable = [
        'owner_user_id',
        'name',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'family_space_user', 'family_space_id', 'user_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'family_space_id')->where('is_joint', true);
    }

        public function jointAccounts(): HasMany
        {
            return $this->hasMany(Account::class, 'family_space_id')->where('is_joint', true);
        }
}
