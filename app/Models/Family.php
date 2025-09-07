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

    protected $table = 'family_spaces'; // Pastikan nama tabel benar

    protected $fillable = [
        'owner_user_id',
        'name',
        // 'slug', // Jika Anda menambahkan slug
    ];

    /**
     * Get the user who owns the family space.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * The users that belong to the family space.
     * Kita akan memuat kolom 'role' dari tabel pivot 'family_space_user'.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'family_space_user', 'family_space_id', 'user_id')
                    ->withPivot('role') // Memuat kolom 'role' dari tabel pivot
                    ->withTimestamps();
    }

    /**
     * Get the accounts associated with the family space (joint accounts).
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'family_space_id')->where('is_joint', true);
    }

            // Untuk Opsi C: Relasi ke akun bersama milik keluarga ini
        public function jointAccounts(): HasMany
        {
            return $this->hasMany(Account::class, 'family_space_id')->where('is_joint', true);
        }
}
