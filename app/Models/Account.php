<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
    {
        use HasFactory;

        protected $fillable = [
            'user_id',
            'name',
            'type',
            'balance',
            'family_space_id',
            'is_joint',
        ];

        protected $casts = [
            'balance' => 'decimal:2',
            'is_joint' => 'boolean',
        ];

        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

        public function familySpace(): BelongsTo
        {
            return $this->belongsTo(Family::class, 'family_space_id');
        }

        public function transactions(): HasMany
        {
            return $this->hasMany(Transaction::class);
        }

        public function transferFromTransactions(): HasMany
        {
            return $this->hasMany(Transaction::class, 'transfer_to_account_id');
        }
    }
    