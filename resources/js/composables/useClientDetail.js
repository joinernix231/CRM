import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useClientDetailStore } from '../stores/clientDetail';
import { useClientsStore } from '../stores/clients';

export function useClientDetail() {
    const store = useClientDetailStore();
    const clientsStore = useClientsStore();

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
    } = storeToRefs(store);

    onMounted(() => {
        store.initFromPath(window.location.pathname);
    });

    return {
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
        pageTitle: store.pageTitle,
        statusOptions: clientsStore.statusOptions,
        saveClient: store.saveClient,
        removeClient: store.removeClient,
        openNewContactForm: store.openNewContactForm,
        openEditContactForm: store.openEditContactForm,
        cancelContactForm: store.cancelContactForm,
        saveContact: store.saveContact,
        removeContact: store.removeContact,
    };
}
