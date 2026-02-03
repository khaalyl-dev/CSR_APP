# Plateforme CSR – Documentation des fichiers

Ce document décrit **chaque fichier créé ou modifié** pour la plateforme de gestion des activités de responsabilité sociale (CSR), avec les changements effectués.

---

## Sommaire

1. [Configuration et bootstrap](#1-configuration-et-bootstrap)
2. [Modèles (Models)](#2-modèles-models)
3. [Contrôleurs API](#3-contrôleurs-api)
4. [Contrôleurs Web](#4-contrôleurs-web)
5. [Middleware](#5-middleware)
6. [Routes](#6-routes)
7. [Migrations](#7-migrations)
8. [Seeders](#8-seeders)
9. [Vues Blade](#9-vues-blade)

---

## 1. Configuration et bootstrap

### `config/auth.php` (modifié)

- **Changement :** ajout du guard **API JWT** en plus du guard web (session).
- **Détail :**
  - Nouveau guard `api` avec `driver => 'jwt'` et `provider => 'users'`.
  - Permet l’authentification par token JWT pour les routes `/api/*` tout en gardant la session pour les routes web.

### `config/jwt.php` (créé – publié par le package)

- **Origine :** publié via `php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider" --tag=config`.
- **Rôle :** configuration du package **php-open-source-saver/jwt-auth** (secret, TTL, blacklist, etc.).
- **À faire :** générer la clé avec `php artisan jwt:secret` (ajoute `JWT_SECRET` dans `.env`).

### `bootstrap/app.php` (modifié)

- **Changement :** enregistrement du middleware **EnsureRole** sous l’alias `role`.
- **Détail :**
  - `$middleware->alias(['role' => \App\Http\Middleware\EnsureRole::class])`.
  - Permet d’utiliser `->middleware('role:plant')` ou `->middleware('role:corporate')` dans les routes.

---

## 2. Modèles (Models)

### `app/Models/User.php` (modifié)

- **Changements principaux :**
  - **Clé primaire :** `protected $primaryKey = 'user_id'` (au lieu de `id`).
  - **Authentification JWT :** implémentation de `PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject` avec `getJWTIdentifier()` et `getJWTCustomClaims()`.
  - **Sanctum retiré :** suppression du trait `HasApiTokens` (remplacé par JWT).
  - **Fillable :** `username`, `email`, `password`, `role`, `site_id` (plus `name`).
  - **Relation :** `site()` → `BelongsTo(Site::class, 'site_id', 'site_id')`.
- **Usage :** utilisateur authentifié (web session ou JWT), avec rôle `plant` ou `corporate` et lien optionnel vers un site.

### `app/Models/Site.php` (créé puis modifié)

- **Rôle :** représente un site / usine.
- **Clé primaire :** `site_id`.
- **Fillable :** `site_name`, `location`, `manager`.
- **Relations :**
  - `users()` → HasMany User
  - `annualPlans()` → HasMany AnnualPlan
  - `realizedActivities()` → HasMany RealizedActivity
  - `changeRequests()` → HasMany ChangeRequest

### `app/Models/AnnualPlan.php` (créé)

- **Rôle :** plan annuel CSR d’un site (Environnement, Social, Gouvernance).
- **Clé primaire :** `plan_id`.
- **Fillable :** `site_id`, `year`, `category`, `activity_type`, `description`, `status`.
- **Relations :** `site()` BelongsTo Site, `realizedActivities()` HasMany RealizedActivity.
- **Cast :** `year` en integer.

### `app/Models/RealizedActivity.php` (créé)

- **Rôle :** activité réalisée (liée ou non à un plan).
- **Clé primaire :** `activity_id`.
- **Fillable :** `plan_id`, `site_id`, `activity_name`, `category`, `activity_type`, `cost`, `status`, `performed_at`.
- **Relations :** `plan()` BelongsTo AnnualPlan, `site()` BelongsTo Site.
- **Casts :** `cost` decimal, `performed_at` date.

### `app/Models/ChangeRequest.php` (créé)

- **Rôle :** demande de modification pour une année clôturée (workflow corporate).
- **Clé primaire :** `request_id`.
- **Fillable :** `site_id`, `year`, `description`, `status`.
- **Relation :** `site()` BelongsTo Site.
- **Cast :** `year` en integer.

---

## 3. Contrôleurs API

### `app/Http/Controllers/API/AuthController.php` (modifié)

- **Rôle :** authentification JWT pour l’API (login, logout, refresh).
- **Changements :**
  - **Login :** utilisation de `auth('api')->attempt()` au lieu de Sanctum ; retour de `token`, `user`, `token_type`, `expires_in`.
  - **Logout :** `auth('api')->logout()` (blacklist du token).
  - **Refresh :** nouvelle route `POST /api/refresh` qui renvoie un nouveau token.
  - **Helper :** méthode privée `guard(): JWTGuard` pour le typage IDE (éviter les erreurs “undefined method”).
- **Réponses :** JSON avec messages et codes HTTP adaptés (401, 422, 200).

---

## 4. Contrôleurs Web

### `app/Http/Controllers/Web/LoginController.php` (créé)

- **Rôle :** connexion / déconnexion **web** (session Laravel).
- **Méthodes :**
  - `showLoginForm()` : affiche la page login ; si déjà connecté, redirige selon le rôle (site ou corporate).
  - `login(Request $request)` : validation email + mot de passe, `Auth::guard('web')->attempt()`, régénération de session, redirection vers `/site` (plant) ou `/corporate` (corporate).
  - `logout(Request $request)` : déconnexion, invalidation session, redirection vers `route('login')`.
- **Redirection :** plant → `site.dashboard`, corporate → `corporate.dashboard`.
- **Message d’erreur :** “Identifiants incorrects.” en cas d’échec.

### `app/Http/Controllers/Web/SiteController.php` (créé)

- **Rôle :** interface réservée aux utilisateurs **plant** (gestion du site).
- **Méthode :** `dashboard(Request $request)` → vue `site.dashboard` avec `user` et `siteId` (pour affichage et usage futur).

### `app/Http/Controllers/Web/CorporateController.php` (créé)

- **Rôle :** interface réservée aux utilisateurs **corporate** (vue globale).
- **Méthode :** `dashboard(Request $request)` → vue `corporate.dashboard` avec `user`.

---

## 5. Middleware

### `app/Http/Middleware/EnsureRole.php` (créé)

- **Rôle :** restreindre l’accès à une route selon le **rôle** de l’utilisateur (plant ou corporate).
- **Paramètre :** `$role` (string) passé depuis la route, ex. `role:plant` ou `role:corporate`.
- **Comportement :**
  - Non authentifié → redirection vers `login`.
  - Rôle différent de celui attendu → redirection vers le tableau de bord correspondant au rôle de l’utilisateur (évite qu’un plant accède à corporate et inversement).
  - Rôle OK → `$next($request)`.
- **Enregistrement :** alias `role` dans `bootstrap/app.php`.

---

## 6. Routes

### `routes/api.php` (modifié)

- **Publique :** `POST /api/login` (email, password) → retourne JWT + user.
- **Protégées (middleware `auth:api`) :**
  - `POST /api/logout` → invalide le token.
  - `POST /api/refresh` → nouveau token.
  - `GET /api/user` → utilisateur courant.
- **Changement par rapport à Sanctum :** `auth:sanctum` remplacé par `auth:api` (guard JWT).

### `routes/web.php` (modifié puis refait)

- **GET `/`** : si connecté → redirection vers `/site` ou `/corporate` selon le rôle ; sinon → redirection vers `/login`.
- **Groupe `guest` :**
  - `GET /login` → formulaire de connexion (`LoginController@showLoginForm`).
  - `POST /login` → traitement du login (`LoginController@login`).
- **Groupe `auth` :**
  - `POST /logout` → déconnexion (`LoginController@logout`).
  - `GET /site` → tableau de bord site, middleware `role:plant` (`SiteController@dashboard`).
  - `GET /corporate` → tableau de bord corporate, middleware `role:corporate` (`CorporateController@dashboard`).
- **GET `/test-auth`** : page de test login/logout (Blade + formulaire) conservée pour debug.

---

## 7. Migrations

### `database/migrations/2026_02_03_220000_create_sites_table.php` (créé)

- **Table :** `sites`.
- **Colonnes initiales :** `id`, `name`, `timestamps`.
- **Évolution :** cette table est ensuite modifiée par la migration `2026_02_03_230000` (voir plus bas).

### `database/migrations/2026_02_03_220001_modify_users_table_to_new_schema.php` (créé)

- **Changements sur `users` :**
  - Ajout : `username` (string, unique, nullable puis utilisé), `role` (enum plant/corporate), `site_id` (FK vers `sites`, nullable).
  - Suppression : `name`.
  - Renommage : `id` → `user_id` (reste clé primaire).
- **Ordre :** à exécuter après la création de la table `sites`.

### `database/migrations/2026_02_03_230000_modify_sites_table_schema.php` (créé)

- **Changements sur `sites` :**
  - Renommage : `id` → `site_id`, `name` → `site_name`.
  - Ajout : `location` (string nullable), `manager` (string nullable).
- **FK :** la colonne `users.site_id` est temporairement détachée puis rattachée à `sites.site_id`.

### `database/migrations/2026_02_03_230001_create_annual_plans_table.php` (créé)

- **Table :** `annual_plans`.
- **Colonnes :** `plan_id` (PK), `site_id` (FK → sites), `year`, `category` (enum Environnement/Social/Gouvernance), `activity_type`, `description` (text), `status` (enum draft/validated), `timestamps`.
- **Contrainte :** `site_id` référence `sites.site_id` avec `cascadeOnDelete`.

### `database/migrations/2026_02_03_230002_create_realized_activities_table.php` (créé)

- **Table :** `realized_activities`.
- **Colonnes :** `activity_id` (PK), `plan_id` (FK nullable → annual_plans), `site_id` (FK → sites), `activity_name`, `category`, `activity_type`, `cost` (decimal), `status` (enum pending/confirmed), `performed_at` (date nullable), `timestamps`.
- **Contraintes :** `plan_id` nullOnDelete, `site_id` cascadeOnDelete.

### `database/migrations/2026_02_03_230003_create_change_requests_table.php` (créé)

- **Table :** `change_requests`.
- **Colonnes :** `request_id` (PK), `site_id` (FK → sites), `year`, `description` (text), `status` (enum pending/approved/rejected), `timestamps`.
- **Contrainte :** `site_id` cascadeOnDelete.

---

## 8. Seeders

### `database/seeders/UserSeeder.php` (créé)

- **Rôle :** créer des **sites** et des **utilisateurs de test** pour le développement.
- **Sites :** “Site Lyon”, “Site Paris” (avec `location` et `manager`).
- **Utilisateurs (mot de passe commun : `password`) :**
  - `corporate@test.com` → rôle corporate, `site_id` null.
  - `plant1@test.com` → rôle plant, site Lyon.
  - `plant2@test.com` → rôle plant, site Paris.
- **Méthode :** `firstOrCreate` / `updateOrCreate` pour éviter les doublons lors de ré-exécution du seeder.

### `database/seeders/DatabaseSeeder.php` (modifié)

- **Changement :** plus de création directe d’un user “Test User” avec l’ancien champ `name`.
- **Ajout :** `$this->call([UserSeeder::class])` pour lancer uniquement le seeder des utilisateurs et sites de test.

---

## 9. Vues Blade

### `resources/views/layouts/app.blade.php` (créé)

- **Rôle :** layout commun à toutes les pages (login, site, corporate).
- **Contenu :**
  - Head : titre, **Tailwind via CDN** (`cdn.tailwindcss.com`) pour éviter l’erreur Vite manifest en dev.
  - Si `@auth` : barre de navigation (logo “Plateforme CSR”, lien Mon site / Vue globale selon le rôle, username/email, bouton Déconnexion en POST vers `logout`).
  - Zone principale : affichage des messages `session('status')` et des erreurs de validation `$errors`.
  - `@yield('content')` pour le contenu de chaque page.
- **Note :** on peut remplacer le CDN par `@vite(['resources/css/app.css', 'resources/js/app.js'])` après `npm run build` si besoin.

### `resources/views/auth/login.blade.php` (créé)

- **Rôle :** page de **connexion** (formulaire email + mot de passe).
- **Détails :** formulaire POST vers `route('login')`, champs email et password, case “Se souvenir de moi”, bouton “Se connecter”, utilisation des classes Tailwind du layout.
- **Erreurs :** affichées via le layout (`$errors`).

### `resources/views/site/dashboard.blade.php` (créé)

- **Rôle :** **interface site** (utilisateurs plant).
- **Contenu :** titre “Interface site”, message de bienvenue avec username/email, affichage du `site_id`, texte placeholder pour la gestion des activités annuelles et réalisées.
- **Variables :** `$user`, `$siteId` (passées par `SiteController@dashboard`).

### `resources/views/corporate/dashboard.blade.php` (créé)

- **Rôle :** **interface corporate** (vue globale).
- **Contenu :** titre “Vue corporate”, message de bienvenue, texte placeholder pour la consolidation et l’analyse des données CSR.
- **Variables :** `$user` (passée par `CorporateController@dashboard`).

### `resources/views/test-auth.blade.php` (existant, non modifié pour la doc)

- Page de test login/logout (formulaire + appels API JWT) ; conservée pour debug ou tests API.

---

## Récapitulatif des flux

- **Connexion web :** `/login` → formulaire → POST → session Laravel → redirection `/site` ou `/corporate` selon le rôle.
- **Accès direct :** `/` redirige vers login (si non connecté) ou vers le bon dashboard (si connecté).
- **Protection :** routes `/site` et `/corporate` protégées par `auth` + `role:plant` ou `role:corporate`.
- **API :** JWT pour `/api/login`, `/api/logout`, `/api/refresh`, `/api/user` (guard `api`).

Pour toute question sur un fichier précis, se référer à la section correspondante ci-dessus.
