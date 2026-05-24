<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import ClientFormPanel from '../components/clients/ClientFormPanel.vue';
import ContactsPanel from '../components/clients/ContactsPanel.vue';
import ValidationAlert from '../components/ui/ValidationAlert.vue';
import { WEB_ROUTES } from '../config/routes';
import { useClientDetail } from '../composables/useClientDetail';

const {
    form,
    contacts,
    contactForm,
    showContactForm,
    editingContactId,
    loading,
    saving,
    contactsLoading,
    error,
    fieldErrors,
    contactError,
    contactFieldErrors,
    isCreate,
    pageTitle,
    statusOptions,
    saveClient,
    removeClient,
    openNewContactForm,
    openEditContactForm,
    cancelContactForm,
    saveContact,
    removeContact,
} = useClientDetail();
</script>

<template>
    <AppLayout :page-title="pageTitle" active-nav="clients">
        <a :href="WEB_ROUTES.clients" class="client-back">← Volver al listado</a>

        <p v-if="loading && !isCreate" class="client-muted">Cargando cliente…</p>

        <template v-else>
            <ValidationAlert :message="error" :field-errors="fieldErrors" />

            <ClientFormPanel
                :form="form"
                :saving="saving"
                :is-create="isCreate"
                :status-options="statusOptions"
                :field-errors="fieldErrors"
                @save="saveClient"
                @delete="removeClient"
            />

            <ContactsPanel
                v-if="!isCreate"
                :contacts="contacts"
                :contact-form="contactForm"
                :show-contact-form="showContactForm"
                :editing-contact-id="editingContactId"
                :loading="contactsLoading"
                :saving="saving"
                :error="contactError"
                :field-errors="contactFieldErrors"
                @add="openNewContactForm"
                @edit="openEditContactForm"
                @cancel="cancelContactForm"
                @save="saveContact"
                @delete="removeContact"
            />
        </template>
    </AppLayout>
</template>

<style scoped>
.client-back {
    display: inline-flex;
    margin-bottom: 1.25rem;
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--crm-blue);
    text-decoration: none;
    transition: color var(--crm-transition);
}

.client-back:hover {
    color: var(--crm-navy);
}

.client-muted {
    margin: 0 0 1rem;
    color: var(--crm-text-muted);
}
</style>
