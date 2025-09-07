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
        // $this->authorizeResource(UserCard::class, 'card');
    }

    public function index()
    {
        $cards = $this->service->getAllForUser();
        return view('user.cards.index', compact('cards'));
    }

    public function store(StoreUserCardRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('cards.index')->with('success', 'Card added successfully.');
    }

    public function update(StoreUserCardRequest $request, UserCard $card)
    {
        $this->authorize('update', $card);
        
        $this->service->update($card->id, $request->validated());
        return redirect()->route('cards.index')->with('success', 'Card updated successfully.');
    }

    public function destroy(UserCard $card)
    {
        $this->authorize('delete', $card);

        $this->service->delete($card->id);
        return redirect()->route('cards.index')->with('success', 'Card deleted successfully.');
    }
}
