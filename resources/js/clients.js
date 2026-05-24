import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { WEB_ROUTES } from './config/routes';
import Clients from './pages/Clients.vue';
import { useAuthStore } from './stores/auth';

const pinia = createPinia();
const auth = useAuthStore(pinia);

auth.hydrate();

if (!auth.isAuthenticated) {
    window.location.href = WEB_ROUTES.login;
} else {
    const app = createApp(Clients);
    app.use(pinia);
    app.mount('#app');
}
