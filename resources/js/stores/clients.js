import { defineStore } from 'pinia';
import {
    deleteClient as deleteClientRequest,
    fetchClients,
} from '../api/clients.api';
import { WEB_ROUTES } from '../config/routes';
import { getHttpErrorMessage } from '../utils/httpError';

const STATUS_OPTIONS = [
    { value: '', label: 'Todos los estados' },
    { value: 'active', label: 'Activo' },
    { value: 'inactive', label: 'Inactivo' },
    { value: 'prospect', label: 'Prospecto' },
];

let searchTimer = null;

function computeSummary(list, total) {
    return {
        total: total ?? list.length,
        active: list.filter((c) => c.status === 'active').length,
        prospect: list.filter((c) => c.status === 'prospect').length,
        contacts: list.reduce((sum, c) => sum + (c.contacts_count ?? 0), 0),
    };
}

export const useClientsStore = defineStore('clients', {
    state: () => ({
        clients: [],
        summary: {
            total: 0,
            active: 0,
            prospect: 0,
            contacts: 0,
        },
        loading: false,
        error: null,
        search: '',
        status: '',
        listPerPage: 10,
    }),

    getters: {
        statusOptions: () => STATUS_OPTIONS,
    },

    actions: {
        async loadSummary() {
            try {
                const { data } = await fetchClients({ per_page: 100 });

                if (data.success && Array.isArray(data.data)) {
                    this.summary = computeSummary(data.data, data.total);
                }
            } catch {
                // Keep dashboard usable if summary fails.
            }
        },

        async loadClients(perPage = 10) {
            this.loading = true;
            this.error = null;

            try {
                const params = { per_page: perPage };

                if (this.search.trim()) {
                    params.search = this.search.trim();
                }

                if (this.status) {
                    params.status = this.status;
                }

                const { data } = await fetchClients(params);

                if (!data.success) {
                    throw new Error(data.message ?? 'No se pudieron cargar los clientes');
                }

                this.clients = data.data ?? [];
            } catch (err) {
                this.error = getHttpErrorMessage(err, 'Error al cargar clientes');
                this.clients = [];
            } finally {
                this.loading = false;
            }
        },

        onSearchInput() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                this.loadClients(this.listPerPage);
            }, 300);
        },

        onStatusChange() {
            this.loadClients(this.listPerPage);
        },

        setListPerPage(perPage) {
            this.listPerPage = perPage;
        },

        async onDelete(client) {
            if (!window.confirm(`¿Eliminar a ${client.name}?`)) {
                return;
            }

            try {
                await deleteClientRequest(client.id);
                await this.loadClients(this.listPerPage);

                if (this.listPerPage <= 10) {
                    await this.loadSummary();
                }
            } catch (err) {
                window.alert(getHttpErrorMessage(err, 'No se pudo eliminar el cliente'));
            }
        },

        goToClient(clientId) {
            window.location.href = WEB_ROUTES.clientShow(clientId);
        },

        async initDashboard() {
            this.listPerPage = 10;
            await Promise.all([this.loadSummary(), this.loadClients(10)]);
        },

        async initClientsList() {
            this.listPerPage = 25;
            await this.loadClients(25);
        },
    },
});
