@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- We use the card-glass component for a consistent look --}}
            <x-card-glass class="p-4 sm:p-8">
                <div class="max-w-xl">
                    {{-- Include the form to update profile information --}}
                    @include('profile.partials.update-profile-information-form')
                </div>
            </x-card-glass>

            <x-card-glass class="p-4 sm:p-8">
                <div class="max-w-xl">
                    {{-- Include the form to update the password --}}
                    @include('profile.partials.update-password-form')
                </div>
            </x-card-glass>

            <x-card-glass class="p-4 sm:p-8">
                <div class="max-w-xl">
                    {{-- Include the form to delete the user account --}}
                    @include('profile.partials.delete-user-form')
                </div>
            </x-card-glass>
        </div>
    </div>
@endsection
