<script setup>
import AppLayout from '../layouts/AppLayout.vue';
import StatCards from '../components/dashboard/StatCards.vue';
import { useClients } from '../composables/useClients';

const {
    summary,
} = useClients({ dashboard: true });
</script>

<template>
    <AppLayout page-title="Dashboard" active-nav="dashboard">
        <StatCards :summary="summary" />
    </AppLayout>
</template>
