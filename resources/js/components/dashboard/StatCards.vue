<script setup>
import { computed } from 'vue';

const props = defineProps({
    summary: {
        type: Object,
        required: true,
    },
});

const statCards = computed(() => [
    {
        key: 'total',
        label: 'Total Clientes',
        value: props.summary.total,
        accent: 'blue',
        icon: 'users',
    },
    {
        key: 'active',
        label: 'Clientes Activos',
        value: props.summary.active,
        accent: 'green',
        icon: 'check',
    },
    {
        key: 'prospect',
        label: 'Prospectos',
        value: props.summary.prospect,
        accent: 'yellow',
        icon: 'star',
    },
    {
        key: 'contacts',
        label: 'Total Contactos',
        value: props.summary.contacts,
        accent: 'cyan',
        icon: 'contact',
    },
]);
</script>

<template>
    <section class="dash-stats" aria-label="Resumen">
        <article
            v-for="card in statCards"
            :key="card.key"
            class="dash-card"
            :class="`dash-card--${card.accent}`"
        >
            <div class="dash-card__body">
                <p class="dash-card__label">{{ card.label }}</p>
                <p class="dash-card__value">{{ card.value }}</p>
            </div>
            <span class="dash-card__icon" aria-hidden="true">
                <svg v-if="card.icon === 'users'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" />
                </svg>
                <svg v-else-if="card.icon === 'check'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-linecap="round" />
                    <path d="M22 4 12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <svg v-else-if="card.icon === 'star'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" stroke-linejoin="round" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke-linecap="round" />
                </svg>
            </span>
        </article>
    </section>
</template>

<style scoped>
.dash-stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.dash-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.35rem;
    background: var(--crm-surface);
    border-radius: var(--crm-radius);
    box-shadow: var(--crm-shadow);
    transition: transform var(--crm-transition), box-shadow var(--crm-transition);
}

.dash-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--crm-shadow-hover);
}

.dash-card__label {
    margin: 0 0 0.35rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--crm-text-muted);
}

.dash-card__value {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--crm-navy);
    letter-spacing: -0.03em;
}

.dash-card__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    border-radius: var(--crm-radius);
    flex-shrink: 0;
}

.dash-card__icon svg {
    width: 1.5rem;
    height: 1.5rem;
}

.dash-card--blue .dash-card__icon {
    background: rgb(37 99 235 / 12%);
    color: var(--crm-blue);
}

.dash-card--green .dash-card__icon {
    background: var(--crm-success-bg);
    color: var(--crm-success);
}

.dash-card--yellow .dash-card__icon {
    background: var(--crm-warning-bg);
    color: var(--crm-warning);
}

.dash-card--cyan .dash-card__icon {
    background: rgb(96 165 250 / 15%);
    color: var(--crm-blue-light);
}

@media (min-width: 640px) {
    .dash-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1200px) {
    .dash-stats {
        grid-template-columns: repeat(4, 1fr);
    }
}
</style>
