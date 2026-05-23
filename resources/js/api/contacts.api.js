import http from './http';

export function fetchContacts(clientId) {
    return http.get(`/clients/${clientId}/contacts`);
}

export function createContact(clientId, payload) {
    return http.post(`/clients/${clientId}/contacts`, payload);
}

export function updateContact(clientId, contactId, payload) {
    return http.put(`/clients/${clientId}/contacts/${contactId}`, payload);
}

export function deleteContact(clientId, contactId) {
    return http.delete(`/clients/${clientId}/contacts/${contactId}`);
}
