@extends('layouts.app')

@section('title', 'Connexion – Plateforme CSR')

@section('content')
<div class="mx-auto max-w-sm rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <h1 class="mb-6 text-xl font-semibold text-slate-800">Connexion</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-800 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div class="mb-6">
            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Mot de passe</label>
            <input type="password" name="password" id="password" required
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-slate-800 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                <span class="text-sm text-slate-600">Se souvenir de moi</span>
            </label>
        </div>
        <button type="submit" class="w-full rounded-md bg-slate-800 px-4 py-2 font-medium text-white hover:bg-slate-700">
            Se connecter
        </button>
    </form>
</div>
@endsection
