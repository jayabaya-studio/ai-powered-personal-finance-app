<?php

namespace App\Http\Controllers;

// Tambahkan use statement untuk AuthorizesRequests
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Auth\Authenticatable;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    // protected function registerPolicyGate()
    // {
    //     Gate::before(function (Authenticatable $user, string $ability, array $args = []) {
    //     });
    // }
}

