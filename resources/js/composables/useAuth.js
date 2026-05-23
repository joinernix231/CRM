import { storeToRefs } from 'pinia';
import { useAuthStore } from '../stores/auth';

export function useAuth() {
    const store = useAuthStore();

    const { user, loading, error, isAuthenticated, userName } = storeToRefs(store);

    return {
        user,
        loading,
        error,
        isAuthenticated,
        userName,
        login: store.login,
        logout: store.logout,
    };
}
