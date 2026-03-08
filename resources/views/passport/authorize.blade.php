@extends('layouts.app')

@section('title', 'Autorisation')

@section('content')
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Demande d'autorisation</h2>

            <p class="text-gray-600 mb-4">
                <strong>{{ $client->name }}</strong> demande l'autorisation d'accéder à votre compte.
            </p>

            @if (count($scopes) > 0)
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Cette application pourra :</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                        @foreach ($scopes as $scope)
                            <li>{{ $scope->description }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center gap-3 mt-6">
                <form method="POST" action="{{ route('passport.authorizations.approve') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Autoriser
                    </button>
                </form>

                <form method="POST" action="{{ route('passport.authorizations.deny') }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                        Refuser
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
