@extends('layouts.app')

@section('title', 'Crear Sala Jitsi')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h2 class="font-semibold text-2xl text-indigo-600 dark:text-indigo-400 leading-tight mt-1">
        {{ __('Crear Sala Jitsi') }}
    </h2>

    <div class="bg-gray-800 p-6 rounded-lg shadow-md my-6">
        @if (session('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg mb-4">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ url('/jitsi/create-room') }}">
            @csrf
            <div class="mb-4">
                <label for="room_name" class="block text-lg font-medium text-white">Nombre de la Sala</label>
                <input type="text" id="room_name" name="room_name" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Crear Sala
            </button>
        </form>
    </div>
</div>
@endsection
