<script setup>
import FormField from '../ui/FormField.vue';

defineProps({
    form: {
        type: Object,
        required: true,
    },
    saving: {
        type: Boolean,
        default: false,
    },
    isCreate: {
        type: Boolean,
        default: false,
    },
    statusOptions: {
        type: Array,
        required: true,
    },
    fieldErrors: {
        type: Object,
        default: () => ({}),
    },
    pdfLoading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['save', 'delete', 'download-pdf']);
</script>

<template>
    <section class="client-panel">
        <header class="client-panel__head">
            <h2>{{ isCreate ? 'Datos del cliente' : 'Información del cliente' }}</h2>
            <button
                v-if="!isCreate"
                type="button"
                class="crm-btn-secondary"
                :disabled="saving || pdfLoading"
                @click="emit('download-pdf')"
            >
                {{ pdfLoading ? 'Generando PDF…' : 'Descargar PDF' }}
            </button>
        </header>

        <form class="client-form" @submit.prevent="emit('save')">
            <div class="client-form__grid">
                <FormField
                    v-model="form.name"
                    label="Nombre"
                    required
                    :error="fieldErrors.name"
                />
                <FormField
                    v-model="form.company"
                    label="Empresa"
                    required
                    :error="fieldErrors.company"
                />
                <FormField
                    v-model="form.email"
                    label="Email"
                    type="email"
                    required
                    :error="fieldErrors.email"
                />
                <FormField
                    v-model="form.phone"
                    label="Teléfono"
                    required
                    :error="fieldErrors.phone"
                />
                <label class="field" :class="{ 'field--invalid': fieldErrors.status }">
                    <span>Estado</span>
                    <select v-model="form.status" class="client-form__select" required>
                        <option
                            v-for="opt in statusOptions.filter((o) => o.value)"
                            :key="opt.value"
                            :value="opt.value"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                    <span v-if="fieldErrors.status" class="field__error">{{ fieldErrors.status }}</span>
                </label>
            </div>

            <div class="client-form__actions">
                <button type="submit" class="crm-btn-primary" :disabled="saving">
                    {{ saving ? 'Guardando…' : (isCreate ? 'Crear cliente' : 'Guardar cambios') }}
                </button>
                <button
                    v-if="!isCreate"
                    type="button"
                    class="crm-btn-danger"
                    :disabled="saving"
                    @click="emit('delete')"
                >
                    Eliminar cliente
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
    margin-bottom: 1.5rem;
}

.client-panel__head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 1.25rem 1.35rem;
    border-bottom: 1px solid var(--crm-border);
}

.client-panel__head h2 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--crm-navy);
}

.crm-btn-secondary {
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius-sm);
    padding: 0.55rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: inherit;
    color: var(--crm-navy);
    background: var(--crm-surface);
    cursor: pointer;
    transition: border-color var(--crm-transition), color var(--crm-transition);
}

.crm-btn-secondary:hover:not(:disabled) {
    border-color: var(--crm-blue);
    color: var(--crm-blue);
}

.crm-btn-secondary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.client-form {
    padding: 1.25rem 1.35rem 1.5rem;
}

.client-form__grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.25rem 1rem;
}

.client-form__select {
    padding: 0.65rem 0.75rem;
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius-sm);
    font-size: 1rem;
    font-family: inherit;
    color: var(--crm-text);
    background: var(--crm-surface);
}

.field--invalid .client-form__select {
    border-color: var(--crm-danger);
}

.client-form__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1.25rem;
}

.crm-btn-primary,
.crm-btn-danger {
    border: none;
    border-radius: var(--crm-radius-sm);
    padding: 0.65rem 1.15rem;
    font-size: 0.9375rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: transform var(--crm-transition), box-shadow var(--crm-transition), opacity var(--crm-transition);
}

.crm-btn-primary {
    background: var(--crm-gradient-primary);
    color: var(--crm-surface);
    box-shadow: 0 2px 8px rgb(37 99 235 / 30%);
}

.crm-btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgb(37 99 235 / 35%);
}

.crm-btn-danger {
    background: var(--crm-gradient-danger);
    color: var(--crm-surface);
    box-shadow: 0 2px 8px rgb(220 38 38 / 25%);
}

.crm-btn-danger:hover:not(:disabled) {
    transform: translateY(-1px);
}

.crm-btn-primary:disabled,
.crm-btn-danger:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

@media (min-width: 768px) {
    .client-form__grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
