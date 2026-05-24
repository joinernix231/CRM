<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import ClientsListTable from '../components/clients/ClientsListTable.vue';
import { WEB_ROUTES } from '../config/routes';
import { useClients } from '../composables/useClients';

const {
    clients,
    loading,
    error,
    search,
    status,
    statusOptions,
    onSearchInput,
    onStatusChange,
    onDelete,
} = useClients();
</script>

<template>
    <AppLayout page-title="Clientes" active-nav="clients">
        <div class="clients-page__toolbar">
            <a :href="WEB_ROUTES.clientCreate" class="crm-btn-primary">Nuevo cliente</a>
        </div>

        <ClientsListTable
            v-model:search="search"
            v-model:status="status"
            title="Listado de clientes"
            :clients="clients"
            :loading="loading"
            :error="error"
            :status-options="statusOptions"
            :show-delete="true"
            @search-input="onSearchInput"
            @status-change="onStatusChange"
            @delete="onDelete"
        />
    </AppLayout>
</template>

<style scoped>
.clients-page__toolbar {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1rem;
}

.crm-btn-primary {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    border: none;
    border-radius: var(--crm-radius-sm);
    padding: 0.65rem 1.15rem;
    font-size: 0.9375rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    background: var(--crm-gradient-primary);
    color: var(--crm-surface);
    box-shadow: 0 2px 8px rgb(37 99 235 / 30%);
    transition: transform var(--crm-transition), box-shadow var(--crm-transition);
}

.crm-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgb(37 99 235 / 35%);
}
</style>
