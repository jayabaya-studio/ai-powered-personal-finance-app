<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserCardRequest;
use App\Models\UserCard;
use App\Services\UserCardService;
use Illuminate\Http\Request;

class UserCardController extends Controller
{
    protected $service;

    public function __construct(UserCardService $service)
    {
        $this->service = $service;
        // Jika Anda membuat Policy, aktifkan baris ini
        // $this->authorizeResource(UserCard::class, 'card');
    }

    /**
     * Display a listing of the user's cards.
     */
    public function index()
    {
        $cards = $this->service->getAllForUser();
        return view('user.cards.index', compact('cards'));
    }

    /**
     * Store a newly created card in storage.
     */
    public function store(StoreUserCardRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('cards.index')->with('success', 'Card added successfully.');
    }

    /**
     * Update the specified card in storage.
     */
    public function update(StoreUserCardRequest $request, UserCard $card)
    {
        // Pastikan pengguna yang login adalah pemilik kartu
        $this->authorize('update', $card);
        
        $this->service->update($card->id, $request->validated());
        return redirect()->route('cards.index')->with('success', 'Card updated successfully.');
    }

    /**
     * Remove the specified card from storage.
     */
    public function destroy(UserCard $card)
    {
        // Pastikan pengguna yang login adalah pemilik kartu
        $this->authorize('delete', $card);

        $this->service->delete($card->id);
        return redirect()->route('cards.index')->with('success', 'Card deleted successfully.');
    }
}
