@extends('layouts.app')

@section('title', 'Mon site – Plateforme CSR')

@section('content')
<div>
    <h1 class="mb-2 text-2xl font-semibold text-slate-800">Interface site</h1>
    <p class="mb-6 text-slate-600">Bienvenue, {{ $user->username ?? $user->email }}. Vous gérez les activités CSR de votre site.</p>
    <div class="rounded-lg border border-slate-200 bg-white p-6">
        <p class="text-sm text-slate-600">Site ID : {{ $siteId ?? '—' }}</p>
        <p class="mt-2 text-sm text-slate-600">Cette interface permettra de planifier les activités annuelles et de saisir les activités réalisées.</p>
    </div>
</div>
@endsection
