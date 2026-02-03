<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – Plateforme CSR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">
        {{-- Left: form + logo --}}
        <div class="flex flex-1 flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-16">
            <div class="mx-auto w-full max-w-sm">
                {{-- Logo above login --}}
                <div class="mb-10 flex justify-center">
                    <img src="{{ asset('COFICAB-LOGO.png') }}" alt="COFICAB" class="h-50 w-50 object-contain">
                </div>

                <h1 class="mb-8 text-2xl font-semibold text-slate-800">Connexion</h1>

                @if($errors->any())
                    <ul class="mb-4 list-inside list-disc rounded-md bg-red-50 p-3 text-sm text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800 placeholder-slate-400 focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/20">
                    </div>
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Mot de passe</label>
                        <input type="password" name="password" id="password" required
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-slate-800 placeholder-slate-400 focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/20">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember"
                               class="h-4 w-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                        <label for="remember" class="ml-2 text-sm text-slate-600">Se souvenir de moi</label>
                    </div>
                    <button type="submit"
                            class="w-full rounded-lg bg-slate-800 px-4 py-3 font-medium text-white transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                        Se connecter
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: image --}}
        <div class="hidden lg:block lg:w-1/2">
            <div class="relative h-full min-h-[50vh] lg:min-h-screen">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80"
                     alt=""
                     class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-slate-900/30"></div>
                <div class="absolute inset-0 flex items-end p-10">
                    <p class="max-w-sm text-lg font-medium text-white drop-shadow-md">
                        Gestion des activités de responsabilité sociale au niveau de vos sites.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
