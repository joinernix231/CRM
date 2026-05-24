import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useClientsStore } from '../stores/clients';

export function useClients({ dashboard = false } = {}) {
    const store = useClientsStore();

    const { clients, summary, loading, error, search, status } = storeToRefs(store);

    onMounted(() => {
        if (dashboard) {
            store.initDashboard();
        } else {
            store.initClientsList();
        }
    });

    return {
        clients,
        summary,
        loading,
        error,
        search,
        status,
        statusOptions: store.statusOptions,
        onSearchInput: store.onSearchInput,
        onStatusChange: store.onStatusChange,
        onDelete: store.onDelete,
    };
}
