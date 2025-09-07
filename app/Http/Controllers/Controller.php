<?php

namespace App\Http\Controllers;

// Tambahkan use statement untuk AuthorizesRequests
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
// Tambahkan use statement untuk Authenticatable dari kontrak Laravel
use Illuminate\Contracts\Auth\Authenticatable;

// Hapus 'abstract' dan tambahkan trait AuthorizesRequests
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // Anda bisa menambahkan Gate::before() atau Gate::after() di AuthServiceProvider
    // atau jika diperlukan di sini untuk debugging, tapi umumnya lebih baik di AuthServiceProvider.
    // Pastikan `$user` di-type hint sebagai Authenticatable
    // protected function registerPolicyGate()
    // {
    //     Gate::before(function (Authenticatable $user, string $ability, array $args = []) {
    //         // Logika otorisasi global Anda
    //     });
    // }
}

