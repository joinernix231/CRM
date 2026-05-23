import http from './http';

export function fetchClients(params = {}) {
    return http.get('/clients', { params });
}

export function fetchClient(id) {
    return http.get(`/clients/${id}`);
}

export function createClient(payload) {
    return http.post('/clients', payload);
}

export function updateClient(id, payload) {
    return http.put(`/clients/${id}`, payload);
}

export function deleteClient(id) {
    return http.delete(`/clients/${id}`);
}
