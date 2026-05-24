<script setup>
import { computed } from 'vue';
import { fieldLabel } from '../../utils/httpError';

const props = defineProps({
    message: {
        type: String,
        default: null,
    },
    fieldErrors: {
        type: Object,
        default: () => ({}),
    },
});

const items = computed(() =>
    Object.entries(props.fieldErrors)
        .filter(([, text]) => Boolean(text))
        .map(([field, text]) => ({
            field,
            label: fieldLabel(field),
            text,
        }))
);
</script>

<template>
    <div v-if="message || items.length" class="validation-alert" role="alert">
        <p v-if="message" class="validation-alert__message">{{ message }}</p>
        <ul v-if="items.length" class="validation-alert__list">
            <li v-for="item in items" :key="item.field">
                <strong>{{ item.label }}:</strong> {{ item.text }}
            </li>
        </ul>
    </div>
</template>

<style scoped>
.validation-alert {
    margin: 0 0 1rem;
    padding: 0.85rem 1rem;
    border-radius: var(--crm-radius-sm);
    background: var(--crm-danger-bg);
    color: var(--crm-danger);
    font-size: 0.875rem;
}

.validation-alert__message {
    margin: 0 0 0.5rem;
    font-weight: 600;
}

.validation-alert__message:last-child {
    margin-bottom: 0;
}

.validation-alert__list {
    margin: 0;
    padding-left: 1.15rem;
}

.validation-alert__list li {
    margin: 0.25rem 0;
}
</style>
