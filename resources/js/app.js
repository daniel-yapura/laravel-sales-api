import './bootstrap';

export function getToken() {
    return sessionStorage.getItem('token');
}

export function setToken(token) {
    sessionStorage.setItem('token', token);
}

export function clearToken() {
    sessionStorage.removeItem('token');
}

export async function apiFetch(path, options = {}) {
    const token = getToken();
    const response = await fetch(`/api${path}`, {
        ...options,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
            ...(options.headers ?? {}),
        },
        body: options.body ? JSON.stringify(options.body) : undefined,
    });

    if (response.status === 401) {
        clearToken();
        window.location.href = '/login';
        throw new Error('No autorizado');
    }

    const data = await response.json().catch(() => null);

    if (!response.ok) {
        throw { status: response.status, data };
    }

    return data;
}

export function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) {
        return;
    }

    toast.textContent = message;
    toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg text-white shadow-lg ${type === 'error' ? 'bg-red-600' : 'bg-green-600'}`;
    toast.classList.remove('hidden');

    setTimeout(() => toast.classList.add('hidden'), 3000);
}

export function errorText(error) {
    return error?.data?.error || error?.data?.message || 'Ocurrió un error';
}

export function validationErrors(error) {
    return error?.data?.errors ?? {};
}

window.AppApi = {
    getToken,
    setToken,
    clearToken,
    apiFetch,
    showToast,
    errorText,
    validationErrors,
};

function updateNav() {
    const token = getToken();
    const loginLink = document.getElementById('login-link');
    const logoutBtn = document.getElementById('logout-btn');

    if (loginLink) {
        loginLink.classList.toggle('hidden', !!token);
    }

    if (logoutBtn) {
        logoutBtn.classList.toggle('hidden', !token);
        logoutBtn.onclick = async () => {
            try {
                await apiFetch('/logout', { method: 'POST' });
            } catch (e) {
                // Ignorar si el token ya no es válido.
            }
            clearToken();
            window.location.href = '/login';
        };
    }
}

function bootPage() {
    updateNav();

    if (!getToken() && !['/login', '/register'].includes(window.location.pathname)) {
        window.location.href = '/login';
        return;
    }

    if (window.location.pathname === '/login') {
        const form = document.getElementById('login-form');
        form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const data = await apiFetch('/login', {
                    method: 'POST',
                    body: { email, password },
                });
                setToken(data.token);
                window.location.href = '/products';
            } catch (error) {
                showToast(errorText(error), 'error');
            }
        });
    }

    if (window.location.pathname === '/register') {
        const form = document.getElementById('register-form');
        form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                await apiFetch('/register', {
                    method: 'POST',
                    body: { name, email, password },
                });
                showToast('Usuario registrado. Inicia sesión.');
                setTimeout(() => window.location.href = '/login', 1000);
            } catch (error) {
                showToast(errorText(error), 'error');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', bootPage);
