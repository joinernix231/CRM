import { defineStore } from 'pinia';
import { loginRequest, logoutRequest } from '../api/auth.api';
import { setAuthToken } from '../api/http';
import { WEB_ROUTES } from '../config/routes';
import { extractAuthPayload } from '../utils/authPayload';
import { getHttpErrorMessage, parseApiErrorBody, parseHttpError } from '../utils/httpError';
import { clearAuthStorage, STORAGE_KEY } from '../utils/session';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: null,
        user: null,
        loading: false,
        error: null,
        fieldErrors: {},
    }),

    getters: {
        isAuthenticated: (state) => Boolean(state.token),
        userName: (state) => state.user?.name ?? '',
    },

    actions: {
        hydrate() {
            const raw = localStorage.getItem(STORAGE_KEY);

            if (!raw) {
                return;
            }

            try {
                const { token, user } = JSON.parse(raw);
                this.token = token;
                this.user = user;
                setAuthToken(token);
            } catch {
                clearAuthStorage();
            }
        },

        persist() {
            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify({ token: this.token, user: this.user })
            );
        },

        async login(credentials) {
            this.loading = true;
            this.error = null;
            this.fieldErrors = {};

            try {
                const data = await loginRequest(credentials);

                if (!data.success) {
                    const parsed = parseApiErrorBody(data, 'Credenciales inválidas');
                    this.error = parsed.message;
                    this.fieldErrors = { ...parsed.fieldErrors };
                    throw new Error(parsed.message);
                }

                const payload = extractAuthPayload(data);

                this.token = payload.access_token;
                this.user = payload.user;
                setAuthToken(this.token);
                this.persist();
                window.location.replace(WEB_ROUTES.dashboard);
            } catch (err) {
                const parsed = parseHttpError(err, 'No se pudo iniciar sesión');
                this.error = parsed.message;
                this.fieldErrors = { ...parsed.fieldErrors };
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await logoutRequest();
                }
            } catch {
                // Clear local session even if the token is already invalid.
            }

            this.token = null;
            this.user = null;
            this.error = null;
            this.fieldErrors = {};
            setAuthToken(null);
            clearAuthStorage();
            window.location.href = WEB_ROUTES.login;
        },
    },
});
