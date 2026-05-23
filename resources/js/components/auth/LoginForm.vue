<script setup>
import BaseButton from '../ui/BaseButton.vue';
import FormField from '../ui/FormField.vue';
import { useLoginForm } from '../../composables/useLoginForm';

const { form, loading, error, submit } = useLoginForm({
    email: 'Joiner@email.com',
    password: 'secret',
});
</script>

<template>
    <form class="auth-card" @submit.prevent="submit">
        <h1>Mini CRM</h1>
        <p class="auth-subtitle">Inicia sesión para continuar</p>

        <FormField
            v-model="form.email"
            label="Email"
            type="email"
            autocomplete="email"
            required
        />

        <FormField
            v-model="form.password"
            label="Contraseña"
            type="password"
            autocomplete="current-password"
            required
        />

        <p v-if="error" class="auth-error">{{ error }}</p>

        <BaseButton type="submit" :disabled="loading">
            {{ loading ? 'Entrando…' : 'Entrar' }}
        </BaseButton>
    </form>
</template>
