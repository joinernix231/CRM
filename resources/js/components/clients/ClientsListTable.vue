<script setup>
import { WEB_ROUTES } from '../../config/routes';

defineProps({
    title: {
        type: String,
        default: 'Clientes',
    },
    clients: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: null,
    },
    statusOptions: {
        type: Array,
        required: true,
    },
    showDelete: {
        type: Boolean,
        default: false,
    },
    showFilters: {
        type: Boolean,
        default: true,
    },
});

const search = defineModel('search', { type: String, default: '' });
const status = defineModel('status', { type: String, default: '' });

const emit = defineEmits(['search-input', 'status-change', 'delete', 'open']);

function statusLabel(value) {
    const map = {
        active: 'Activo',
        inactive: 'Inactivo',
        prospect: 'Prospecto',
    };

    return map[value] ?? value;
}

function openClient(client) {
    emit('open', client);
    window.location.href = WEB_ROUTES.clientShow(client.id);
}
</script>

<template>
    <section class="dash-panel">
        <header class="dash-panel__head">
            <h2>{{ title }}</h2>
            <div v-if="showFilters" class="dash-toolbar">
                <label class="dash-search">
                    <span class="visually-hidden">Buscar clientes</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" stroke-linecap="round" />
                    </svg>
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Buscar por nombre, empresa o email..."
                        @input="emit('search-input')"
                    >
                </label>
                <select
                    v-model="status"
                    class="dash-select"
                    aria-label="Filtrar por estado"
                    @change="emit('status-change')"
                >
                    <option
                        v-for="opt in statusOptions"
                        :key="opt.value"
                        :value="opt.value"
                    >
                        {{ opt.label }}
                    </option>
                </select>
            </div>
        </header>

        <p v-if="error" class="dash-error" role="alert">{{ error }}</p>
        <p v-else-if="loading" class="dash-loading">Cargando clientes...</p>

        <div v-else class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Empresa</th>
                        <th>Estado</th>
                        <th>Contactos</th>
                        <th v-if="showDelete">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="clients.length === 0">
                        <td :colspan="showDelete ? 5 : 4" class="dash-table__empty">
                            No hay clientes para mostrar.
                        </td>
                    </tr>
                    <tr
                        v-for="client in clients"
                        :key="client.id"
                        class="dash-table__row--clickable"
                        @click="openClient(client)"
                    >
                        <td data-label="Nombre">
                            <strong>{{ client.name }}</strong>
                        </td>
                        <td data-label="Empresa">{{ client.company }}</td>
                        <td data-label="Estado">
                            <span
                                class="dash-badge"
                                :class="`dash-badge--${client.status}`"
                            >
                                {{ statusLabel(client.status) }}
                            </span>
                        </td>
                        <td data-label="Contactos">{{ client.contacts_count ?? 0 }}</td>
                        <td v-if="showDelete" data-label="Acciones" @click.stop>
                            <button
                                type="button"
                                class="dash-delete-btn"
                                title="Eliminar cliente"
                                @click="emit('delete', client)"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6" />
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<style scoped>
.dash-panel {
    background: var(--crm-surface);
    border-radius: var(--crm-radius);
    box-shadow: var(--crm-shadow);
    overflow: hidden;
}

.dash-panel__head {
    padding: 1.25rem 1.35rem;
    border-bottom: 1px solid var(--crm-border);
}

.dash-panel__head h2 {
    margin: 0 0 1rem;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--crm-navy);
}

.dash-toolbar {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.dash-search {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
    padding: 0 0.85rem;
    background: var(--crm-bg);
    border-radius: var(--crm-radius-sm);
    border: 1px solid transparent;
    transition: border-color var(--crm-transition), box-shadow var(--crm-transition);
}

.dash-search:focus-within {
    border-color: var(--crm-blue-light);
    box-shadow: 0 0 0 3px rgb(59 130 246 / 15%);
}

.dash-search svg {
    width: 1.1rem;
    height: 1.1rem;
    color: var(--crm-text-subtle);
    flex-shrink: 0;
}

.dash-search input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 0.7rem 0;
    font-size: 0.9375rem;
    font-family: inherit;
    outline: none;
    color: var(--crm-text);
}

.dash-select {
    padding: 0.7rem 0.85rem;
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius-sm);
    font-size: 0.9375rem;
    font-family: inherit;
    color: var(--crm-text);
    background: var(--crm-surface);
    cursor: pointer;
    transition: border-color var(--crm-transition), box-shadow var(--crm-transition);
}

.dash-select:focus {
    outline: none;
    border-color: var(--crm-blue-light);
    box-shadow: 0 0 0 3px rgb(59 130 246 / 15%);
}

.dash-error {
    margin: 0;
    padding: 1rem 1.35rem;
    color: var(--crm-danger);
    font-size: 0.875rem;
}

.dash-loading {
    margin: 0;
    padding: 2rem 1.35rem;
    text-align: center;
    color: var(--crm-text-muted);
}

.dash-table-wrap {
    overflow-x: auto;
}

.dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9375rem;
}

.dash-table th {
    text-align: left;
    padding: 0.85rem 1.35rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--crm-text-muted);
    background: #f8fafc;
    border-bottom: 1px solid var(--crm-border);
}

.dash-table td {
    padding: 1rem 1.35rem;
    border-bottom: 1px solid var(--crm-border-subtle);
    color: var(--crm-text);
    vertical-align: middle;
}

.dash-table__row--clickable {
    cursor: pointer;
    transition: background var(--crm-transition);
}

.dash-table__row--clickable:hover {
    background: rgb(96 165 250 / 8%);
}

.dash-table__empty {
    text-align: center;
    color: var(--crm-text-subtle);
    padding: 2rem !important;
    cursor: default;
}

.dash-badge {
    display: inline-block;
    padding: 0.25rem 0.65rem;
    border-radius: var(--crm-radius-pill);
    font-size: 0.75rem;
    font-weight: 600;
}

.dash-badge--active {
    background: var(--crm-success-bg);
    color: var(--crm-success);
}

.dash-badge--inactive {
    background: var(--crm-danger-bg);
    color: var(--crm-danger);
}

.dash-badge--prospect {
    background: var(--crm-warning-bg);
    color: var(--crm-warning);
}

.dash-delete-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.15rem;
    height: 2.15rem;
    padding: 0;
    border: none;
    border-radius: var(--crm-radius-sm);
    background: var(--crm-gradient-danger);
    color: var(--crm-surface);
    cursor: pointer;
    box-shadow: 0 2px 8px rgb(220 38 38 / 25%);
    transition: transform var(--crm-transition), box-shadow var(--crm-transition);
}

.dash-delete-btn svg {
    width: 1rem;
    height: 1rem;
}

.dash-delete-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgb(220 38 38 / 35%);
}

@media (min-width: 640px) {
    .dash-toolbar {
        flex-direction: row;
        align-items: center;
    }

    .dash-select {
        min-width: 180px;
    }
}

@media (max-width: 767px) {
    .dash-table thead {
        display: none;
    }

    .dash-table tr {
        display: block;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--crm-border-subtle);
    }

    .dash-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 0.5rem 0;
        border: none;
    }

    .dash-table td::before {
        content: attr(data-label);
        font-weight: 600;
        font-size: 0.75rem;
        color: var(--crm-text-muted);
        text-transform: uppercase;
    }

    .dash-table__empty {
        display: block;
    }

    .dash-table__empty::before {
        display: none;
    }
}
</style>
