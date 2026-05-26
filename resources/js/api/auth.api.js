import http from './http';

export async function loginRequest(credentials) {
    const { data } = await http.post('/login', credentials);

    return data;
}

export async function logoutRequest() {
    const { data } = await http.post('/logout');

    return data;
}
