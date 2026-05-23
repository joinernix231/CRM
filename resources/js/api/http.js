import axios from 'axios';
import { attachValidationToError } from '../utils/httpError';
import {
    clearAuthStorage,
    isAuthApiRequest,
    redirectToLogin,
} from '../utils/session';

export function appBaseUrl() {
    const apiUrl = import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api';

    return String(apiUrl).replace(/\/api\/?$/, '');
}

const http = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

http.interceptors.response.use(
    (response) => response,
    (error) => {
        attachValidationToError(error);

        const status = error.response?.status;
        const requestUrl = error.config?.url ?? '';

        if (status === 401 && !isAuthApiRequest(requestUrl)) {
            clearAuthStorage();
            delete http.defaults.headers.common.Authorization;
            redirectToLogin();
        }

        return Promise.reject(error);
    }
);

export function setAuthToken(token) {
    if (token) {
        http.defaults.headers.common.Authorization = `Bearer ${token}`;
    } else {
        delete http.defaults.headers.common.Authorization;
    }
}

export default http;
