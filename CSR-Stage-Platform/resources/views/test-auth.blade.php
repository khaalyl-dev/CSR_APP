<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login / Logout Test</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; max-width: 420px; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.25rem; margin-bottom: 1.5rem; color: #1a1a1a; }
        .card { background: #f8f9fa; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; }
        label { display: block; font-weight: 500; margin-bottom: 0.35rem; color: #333; }
        input[type="email"], input[type="password"] { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 1rem; font-size: 1rem; }
        button { padding: 0.5rem 1rem; border-radius: 6px; font-size: 1rem; cursor: pointer; border: none; }
        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-primary:hover { background: #0b5ed7; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #bb2d3b; }
        .message { padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; }
        .message.error { background: #f8d7da; color: #842029; }
        .message.success { background: #d1e7dd; color: #0f5132; }
        .message.info { background: #cff4fc; color: #055160; }
        #user-info { margin-top: 0.5rem; }
        #user-info p { margin: 0.25rem 0; color: #333; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <h1>Login / Logout Test</h1>
    <div id="message" class="message hidden" role="alert"></div>

    <div id="login-section" class="card">
        <form id="login-form">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="user@example.com" autocomplete="email">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="••••••••" autocomplete="current-password">
            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>

    <div id="logged-in-section" class="card hidden">
        <p><strong>Logged in</strong></p>
        <div id="user-info"></div>
        <button type="button" id="logout-btn" class="btn-danger">Logout</button>
    </div>

    <script>
        const API_BASE = '{{ url("/api") }}';
        const TOKEN_KEY = 'auth_token';

        const messageEl = document.getElementById('message');
        const loginSection = document.getElementById('login-section');
        const loggedInSection = document.getElementById('logged-in-section');
        const loginForm = document.getElementById('login-form');
        const userInfoEl = document.getElementById('user-info');
        const logoutBtn = document.getElementById('logout-btn');

        function showMessage(text, type = 'info') {
            messageEl.textContent = text;
            messageEl.className = 'message ' + type;
            messageEl.classList.remove('hidden');
        }

        function hideMessage() {
            messageEl.classList.add('hidden');
        }

        function getToken() {
            return localStorage.getItem(TOKEN_KEY);
        }

        function setToken(token) {
            if (token) localStorage.setItem(TOKEN_KEY, token);
            else localStorage.removeItem(TOKEN_KEY);
        }

        function showLogin() {
            loggedInSection.classList.add('hidden');
            loginSection.classList.remove('hidden');
        }

        function showLoggedIn(user) {
            loginSection.classList.add('hidden');
            loggedInSection.classList.remove('hidden');
            userInfoEl.innerHTML = '<p>ID: ' + (user.user_id || '') + '</p><p>Username: ' + (user.username || '') + '</p><p>Email: ' + (user.email || '') + '</p><p>Role: ' + (user.role || '') + '</p>';
        }

        async function login(email, password) {
            const res = await fetch(API_BASE + '/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'Login failed');
            }
            return data;
        }

        async function logout() {
            const token = getToken();
            if (!token) {
                showLogin();
                return;
            }
            const res = await fetch(API_BASE + '/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });
            const data = await res.json().catch(() => ({}));
            setToken(null);
            showLogin();
            showMessage(data.message || 'Logged out.', 'success');
        }

        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            hideMessage();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            try {
                const data = await login(email, password);
                setToken(data.token);
                showLoggedIn(data.user || {});
                showMessage('Login successful.', 'success');
            } catch (err) {
                showMessage(err.message || 'Invalid credentials', 'error');
            }
        });

        logoutBtn.addEventListener('click', function () {
            hideMessage();
            logout();
        });

        (function init() {
            const token = getToken();
            if (token) {
                fetch(API_BASE + '/user', {
                    headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
                })
                    .then(res => res.ok ? res.json() : Promise.reject())
                    .then(user => showLoggedIn(user))
                    .catch(() => { setToken(null); showLogin(); });
            } else {
                showLogin();
            }
        })();
    </script>
</body>
</html>
