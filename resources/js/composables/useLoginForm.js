import { reactive } from 'vue';
import { useAuth } from './useAuth';

export function useLoginForm(initial = {}) {
    const { login, loading, error } = useAuth();

    const form = reactive({
        email: initial.email ?? '',
        password: initial.password ?? '',
    });

    async function submit() {
        await login({
            email: form.email,
            password: form.password,
        });
    }

    return {
        form,
        loading,
        error,
        submit,
    };
}
