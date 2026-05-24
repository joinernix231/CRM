import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { WEB_ROUTES } from './config/routes';
import ClientDetail from './pages/ClientDetail.vue';
import { useAuthStore } from './stores/auth';

const pinia = createPinia();
const auth = useAuthStore(pinia);

auth.hydrate();

if (!auth.isAuthenticated) {
    window.location.href = WEB_ROUTES.login;
} else {
    const app = createApp(ClientDetail);
    app.use(pinia);
    app.mount('#app');
}
