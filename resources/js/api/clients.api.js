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

export async function downloadClientPdf(id, filename = null) {
    const response = await http.get(`/clients/${id}/pdf`, {
        responseType: 'blob',
        headers: {
            Accept: 'application/pdf',
        },
    });

    const disposition = response.headers['content-disposition'] ?? '';
    const match = /filename="?([^";\n]+)"?/i.exec(disposition);
    const name = filename ?? (match?.[1] ?? `cliente-${id}.pdf`);

    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
    const link = document.createElement('a');
    link.href = url;
    link.download = name;
    link.click();
    window.URL.revokeObjectURL(url);
}
