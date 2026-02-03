@extends('layouts.app')

@section('title', 'Vue globale – Plateforme CSR')

@section('content')
<div>
    <h1 class="mb-2 text-2xl font-semibold text-slate-800">Vue corporate</h1>
    <p class="mb-6 text-slate-600">Bienvenue, {{ $user->username ?? $user->email }}. Consolidation et analyse des données CSR.</p>
    <div class="rounded-lg border border-slate-200 bg-white p-6">
        <p class="text-sm text-slate-600">Cette interface permettra de suivre et consolider les données de tous les sites et d’analyser l’impact global.</p>
    </div>
</div>
@endsection
