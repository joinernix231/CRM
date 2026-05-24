<script setup>
import FormField from '../ui/FormField.vue';
import ValidationAlert from '../ui/ValidationAlert.vue';

defineProps({
    contacts: {
        type: Array,
        required: true,
    },
    contactForm: {
        type: Object,
        required: true,
    },
    showContactForm: {
        type: Boolean,
        default: false,
    },
    editingContactId: {
        type: [Number, null],
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    saving: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: null,
    },
    fieldErrors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['add', 'edit', 'cancel', 'save', 'delete']);
</script>

<template>
    <section class="client-panel">
        <header class="client-panel__head client-panel__head--row">
            <h2>Contactos</h2>
            <button type="button" class="crm-btn-primary crm-btn-primary--sm" @click="emit('add')">
                Nuevo contacto
            </button>
        </header>

        <p v-if="loading" class="client-panel__loading">Cargando contactos…</p>

        <div v-else-if="!loading" class="contacts-list">
            <p v-if="contacts.length === 0" class="contacts-list__empty">Sin contactos registrados.</p>
            <article
                v-for="contact in contacts"
                :key="contact.id"
                class="contact-card"
            >
                <div class="contact-card__body">
                    <strong>{{ contact.name }}</strong>
                    <span v-if="contact.is_primary" class="contact-card__badge">Principal</span>
                    <p>{{ contact.position }}</p>
                    <p class="contact-card__meta">{{ contact.email }} · {{ contact.phone }}</p>
                </div>
                <div class="contact-card__actions">
                    <button type="button" class="crm-btn-ghost" @click="emit('edit', contact)">
                        Editar
                    </button>
                    <button type="button" class="crm-btn-ghost crm-btn-ghost--danger" @click="emit('delete', contact)">
                        Eliminar
                    </button>
                </div>
            </article>
        </div>

        <form
            v-if="showContactForm"
            class="contact-form"
            @submit.prevent="emit('save')"
        >
            <h3>{{ editingContactId ? 'Editar contacto' : 'Nuevo contacto' }}</h3>

            <ValidationAlert :message="error" :field-errors="fieldErrors" />

            <div class="contact-form__grid">
                <FormField
                    v-model="contactForm.name"
                    label="Nombre"
                    required
                    :error="fieldErrors.name"
                />
                <FormField
                    v-model="contactForm.position"
                    label="Cargo"
                    required
                    :error="fieldErrors.position"
                />
                <FormField
                    v-model="contactForm.email"
                    label="Email"
                    type="email"
                    required
                    :error="fieldErrors.email"
                />
                <FormField
                    v-model="contactForm.phone"
                    label="Teléfono"
                    required
                    :error="fieldErrors.phone"
                />
                <label class="field field--checkbox" :class="{ 'field--invalid': fieldErrors.is_primary }">
                    <input v-model="contactForm.is_primary" type="checkbox">
                    <span>Contacto principal</span>
                    <span v-if="fieldErrors.is_primary" class="field__error">{{ fieldErrors.is_primary }}</span>
                </label>
            </div>
            <div class="client-form__actions">
                <button type="submit" class="crm-btn-primary" :disabled="saving">
                    {{ saving ? 'Guardando…' : 'Guardar contacto' }}
                </button>
                <button type="button" class="crm-btn-ghost" @click="emit('cancel')">
                    Cancelar
                </button>
            </div>
        </form>
    </section>
</template>

<style scoped>
.client-panel {
    background: var(--crm-surface);
    border-radius: var(--crm-radius);
    box-shadow: var(--crm-shadow);
    overflow: hidden;
}

.client-panel__head {
    padding: 1.25rem 1.35rem;
    border-bottom: 1px solid var(--crm-border);
}

.client-panel__head--row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.client-panel__head h2 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--crm-navy);
}

.client-panel__loading {
    margin: 0;
    padding: 2rem 1.35rem;
    text-align: center;
    color: var(--crm-text-muted);
}

.contacts-list {
    padding: 1rem 1.35rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.contacts-list__empty {
    margin: 0;
    color: var(--crm-text-muted);
    text-align: center;
    padding: 1rem 0;
}

.contact-card {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius-sm);
    background: var(--crm-bg);
    transition: border-color var(--crm-transition);
}

.contact-card:hover {
    border-color: var(--crm-blue-light);
}

.contact-card__body {
    min-width: 0;
}

.contact-card__body strong {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--crm-navy);
}

.contact-card__badge {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: var(--crm-radius-pill);
    background: var(--crm-success-bg);
    color: var(--crm-success);
}

.contact-card__body p {
    margin: 0.35rem 0 0;
    font-size: 0.875rem;
    color: var(--crm-text);
}

.contact-card__meta {
    color: var(--crm-text-muted) !important;
}

.contact-card__actions {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    flex-shrink: 0;
}

.contact-form {
    padding: 1.25rem 1.35rem 1.5rem;
    border-top: 1px solid var(--crm-border);
    background: #f8fafc;
}

.contact-form h3 {
    margin: 0 0 1rem;
    font-size: 1rem;
    color: var(--crm-navy);
}

.contact-form__grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.25rem 1rem;
}

.field--checkbox {
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
}

.field--checkbox input {
    width: auto;
}

.client-form__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
}

.crm-btn-primary {
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
    transition: transform var(--crm-transition), box-shadow var(--crm-transition), opacity var(--crm-transition);
}

.crm-btn-primary--sm {
    padding: 0.5rem 0.9rem;
    font-size: 0.875rem;
}

.crm-btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
}

.crm-btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.crm-btn-ghost {
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius-sm);
    padding: 0.45rem 0.75rem;
    font-size: 0.8125rem;
    font-weight: 600;
    font-family: inherit;
    background: var(--crm-surface);
    color: var(--crm-navy);
    cursor: pointer;
    transition: background var(--crm-transition), border-color var(--crm-transition);
}

.crm-btn-ghost:hover {
    background: var(--crm-bg);
    border-color: var(--crm-blue-light);
}

.crm-btn-ghost--danger {
    color: var(--crm-danger);
}

@media (min-width: 768px) {
    .contact-form__grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
