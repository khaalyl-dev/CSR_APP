<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Plateforme CSR')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    @auth
    <header class="bg-white border-b border-slate-200">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center gap-6">
                <a href="{{ auth()->user()->role === 'plant' ? route('site.dashboard') : route('corporate.dashboard') }}" class="font-semibold text-slate-800">
                    Plateforme CSR
                </a>
                @if(auth()->user()->role === 'plant')
                    <a href="{{ route('site.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900">Mon site</a>
                @else
                    <a href="{{ route('corporate.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900">Vue globale</a>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-500">{{ auth()->user()->username ?? auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">Déconnexion</button>
                </form>
            </div>
        </nav>
    </header>
    @endauth

    <main class="@auth mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 @endauth">
        @if(session('status'))
            <p class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800">{{ session('status') }}</p>
        @endif
        @if($errors->any())
            <ul class="mb-4 list-inside list-disc rounded-md bg-red-50 p-3 text-sm text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        @yield('content')
    </main>
</body>
</html>
