import { defineStore } from 'pinia';
import {
    createClient,
    deleteClient,
    fetchClient,
    updateClient,
} from '../api/clients.api';
import {
    createContact,
    deleteContact,
    fetchContacts,
    updateContact,
} from '../api/contacts.api';
import { WEB_ROUTES } from '../config/routes';
import { getHttpErrorMessage, parseApiErrorBody, parseHttpError } from '../utils/httpError';

const emptyClientForm = () => ({
    name: '',
    email: '',
    phone: '',
    company: '',
    status: 'active',
});

const emptyContactForm = () => ({
    name: '',
    email: '',
    phone: '',
    position: '',
    is_primary: false,
});

export const useClientDetailStore = defineStore('clientDetail', {
    state: () => ({
        clientId: null,
        isCreate: false,
        form: emptyClientForm(),
        contacts: [],
        contactForm: emptyContactForm(),
        editingContactId: null,
        showContactForm: false,
        loading: false,
        saving: false,
        contactsLoading: false,
        error: null,
        fieldErrors: {},
        contactError: null,
        contactFieldErrors: {},
    }),

    getters: {
        pageTitle: (state) => (state.isCreate ? 'Nuevo cliente' : 'Detalle del cliente'),
    },

    actions: {
        clearClientErrors() {
            this.error = null;
            this.fieldErrors = {};
        },

        clearContactErrors() {
            this.contactError = null;
            this.contactFieldErrors = {};
        },

        initFromPath(pathname) {
            if (pathname === WEB_ROUTES.clientCreate) {
                this.isCreate = true;
                this.clientId = null;
                this.form = emptyClientForm();
                this.contacts = [];
                return;
            }

            const match = pathname.match(/^\/clients\/(\d+)$/);

            if (!match) {
                window.location.href = WEB_ROUTES.clients;
                return;
            }

            this.isCreate = false;
            this.clientId = Number(match[1]);
            this.loadClient();
            this.loadContacts();
        },

        async loadClient() {
            this.loading = true;
            this.clearClientErrors();

            try {
                const { data } = await fetchClient(this.clientId);

                if (!data.success || !data.data) {
                    throw new Error(data.message ?? 'Cliente no encontrado');
                }

                const client = data.data;
                this.form = {
                    name: client.name ?? '',
                    email: client.email ?? '',
                    phone: client.phone ?? '',
                    company: client.company ?? '',
                    status: client.status ?? 'active',
                };
            } catch (err) {
                const parsed = parseHttpError(err, 'No se pudo cargar el cliente');
                this.error = parsed.message;
                this.fieldErrors = { ...parsed.fieldErrors };
            } finally {
                this.loading = false;
            }
        },

        async loadContacts() {
            if (!this.clientId) {
                return;
            }

            this.contactsLoading = true;
            this.contactError = null;

            try {
                const { data } = await fetchContacts(this.clientId);

                if (!data.success) {
                    throw new Error(data.message ?? 'No se pudieron cargar los contactos');
                }

                this.contacts = data.data ?? [];
            } catch (err) {
                this.contactError = getHttpErrorMessage(err, 'Error al cargar contactos');
                this.contacts = [];
            } finally {
                this.contactsLoading = false;
            }
        },

        applyClientValidationFailure(body, fallback) {
            const parsed = parseApiErrorBody(body, fallback);
            this.error = parsed.message;
            this.fieldErrors = { ...parsed.fieldErrors };
        },

        applyClientValidationError(err, fallback) {
            const parsed = parseHttpError(err, fallback);
            this.error = parsed.message;
            this.fieldErrors = { ...parsed.fieldErrors };
        },

        async saveClient() {
            this.saving = true;
            this.clearClientErrors();

            try {
                const payload = { ...this.form };

                if (this.isCreate) {
                    const { data } = await createClient(payload);

                    if (!data.success) {
                        this.applyClientValidationFailure(data, 'No se pudo crear el cliente');
                        return;
                    }

                    if (!data.data?.id) {
                        this.error = data.message ?? 'No se pudo crear el cliente';
                        return;
                    }

                    window.location.href = WEB_ROUTES.clientShow(data.data.id);
                    return;
                }

                const { data } = await updateClient(this.clientId, payload);

                if (!data.success) {
                    this.applyClientValidationFailure(data, 'No se pudo actualizar el cliente');
                    return;
                }

                await this.loadClient();
            } catch (err) {
                this.applyClientValidationError(err, 'No se pudo guardar el cliente');
            } finally {
                this.saving = false;
            }
        },

        async removeClient() {
            if (this.isCreate) {
                return;
            }

            if (!window.confirm('¿Eliminar este cliente y todos sus contactos?')) {
                return;
            }

            try {
                await deleteClient(this.clientId);
                window.location.href = WEB_ROUTES.clients;
            } catch (err) {
                window.alert(getHttpErrorMessage(err, 'No se pudo eliminar el cliente'));
            }
        },

        openNewContactForm() {
            this.editingContactId = null;
            this.contactForm = emptyContactForm();
            this.showContactForm = true;
            this.clearContactErrors();
        },

        openEditContactForm(contact) {
            this.editingContactId = contact.id;
            this.contactForm = {
                name: contact.name ?? '',
                email: contact.email ?? '',
                phone: contact.phone ?? '',
                position: contact.position ?? '',
                is_primary: Boolean(contact.is_primary),
            };
            this.showContactForm = true;
            this.clearContactErrors();
        },

        cancelContactForm() {
            this.showContactForm = false;
            this.editingContactId = null;
            this.contactForm = emptyContactForm();
        },

        applyContactValidationFailure(body, fallback) {
            const parsed = parseApiErrorBody(body, fallback);
            this.contactError = parsed.message;
            this.contactFieldErrors = { ...parsed.fieldErrors };
        },

        applyContactValidationError(err, fallback) {
            const parsed = parseHttpError(err, fallback);
            this.contactError = parsed.message;
            this.contactFieldErrors = { ...parsed.fieldErrors };
        },

        async saveContact() {
            this.saving = true;
            this.clearContactErrors();

            try {
                const payload = {
                    ...this.contactForm,
                    is_primary: Boolean(this.contactForm.is_primary),
                };

                if (this.editingContactId) {
                    const { data } = await updateContact(this.clientId, this.editingContactId, payload);

                    if (!data.success) {
                        this.applyContactValidationFailure(data, 'No se pudo actualizar el contacto');
                        return;
                    }
                } else {
                    const { data } = await createContact(this.clientId, payload);

                    if (!data.success) {
                        this.applyContactValidationFailure(data, 'No se pudo crear el contacto');
                        return;
                    }
                }

                this.cancelContactForm();
                await this.loadContacts();
            } catch (err) {
                this.applyContactValidationError(err, 'No se pudo guardar el contacto');
            } finally {
                this.saving = false;
            }
        },

        async removeContact(contact) {
            if (!window.confirm(`¿Eliminar el contacto ${contact.name}?`)) {
                return;
            }

            try {
                await deleteContact(this.clientId, contact.id);
                await this.loadContacts();
            } catch (err) {
                window.alert(getHttpErrorMessage(err, 'No se pudo eliminar el contacto'));
            }
        },
    },
});
