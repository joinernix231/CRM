import axios from 'axios';
import http, { appBaseUrl } from './http';

export async function ensureCsrfCookie() {
    await axios.get(`${appBaseUrl()}/sanctum/csrf-cookie`, {
        withCredentials: true,
        headers: { Accept: 'application/json' },
    });
}

export async function loginRequest(credentials) {
    await ensureCsrfCookie();
    const { data } = await http.post('/login', credentials);

    return data;
}

export async function logoutRequest() {
    const { data } = await http.post('/logout');

    return data;
}
