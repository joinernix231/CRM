import { WEB_ROUTES } from '../config/routes';

export const STORAGE_KEY = 'mini_crm_auth';

export function clearAuthStorage() {
    localStorage.removeItem(STORAGE_KEY);
}

export function isLoginPage() {
    const path = window.location.pathname;

    return path === WEB_ROUTES.login || path === '/app';
}

export function redirectToLogin() {
    if (!isLoginPage()) {
        window.location.replace(WEB_ROUTES.login);
    }
}

export function isAuthApiRequest(url = '') {
    const path = String(url);

    return path.includes('/login') || path.includes('/register');
}
