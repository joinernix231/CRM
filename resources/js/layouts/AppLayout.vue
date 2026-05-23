<script setup>
import { computed, ref } from 'vue';
import { useAuth } from '../composables/useAuth';

const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Dashboard',
    },
    activeNav: {
        type: String,
        default: 'dashboard',
    },
});

const sidebarOpen = ref(false);
const { user, userName, logout } = useAuth();

const userEmail = computed(() => user.value?.email ?? '');
const userInitials = computed(() => {
    const name = userName.value || userEmail.value || '?';
    const parts = name.trim().split(/\s+/);

    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }

    return name.slice(0, 2).toUpperCase();
});

const navItems = [
    {
        id: 'dashboard',
        label: 'Dashboard',
        href: '/dashboard',
        icon: 'grid',
    },
    {
        id: 'clients',
        label: 'Clientes',
        href: '/clients',
        icon: 'users',
    }
];

function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}

function closeSidebar() {
    sidebarOpen.value = false;
}

async function onLogout() {
    await logout();
}
</script>

<template>
    <div class="crm-app" :class="{ 'crm-app--sidebar-open': sidebarOpen }">
        <div
            class="crm-sidebar-backdrop"
            aria-hidden="true"
            @click="closeSidebar"
        />

        <aside class="crm-sidebar">
            <div class="crm-sidebar__brand">
                <span class="crm-sidebar__logo" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h10" stroke-linecap="round" />
                    </svg>
                </span>
                <span class="crm-sidebar__title">Mini CRM</span>
            </div>

            <nav class="crm-sidebar__nav" aria-label="Principal">
                <a
                    v-for="item in navItems"
                    :key="item.id"
                    :href="item.href"
                    class="crm-sidebar__link"
                    :class="{ 'crm-sidebar__link--active': item.id === props.activeNav }"
                    @click="item.href === '#' ? $event.preventDefault() : closeSidebar()"
                >
                    <span class="crm-sidebar__link-icon" aria-hidden="true">
                        <svg v-if="item.icon === 'grid'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        <svg v-else-if="item.icon === 'users'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" />
                        </svg>
                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke-linecap="round" />
                        </svg>
                    </span>
                    {{ item.label }}
                </a>
            </nav>

            <div class="crm-sidebar__footer">
                <div class="crm-sidebar__user">
                    <span class="crm-sidebar__avatar">{{ userInitials }}</span>
                    <div class="crm-sidebar__user-text">
                        <strong>{{ userName || 'Usuario' }}</strong>
                        <span>{{ userEmail }}</span>
                    </div>
                </div>
                <button type="button" class="crm-btn crm-btn--logout" @click="onLogout">
                    Cerrar sesión
                </button>
            </div>
        </aside>

        <div class="crm-main">
            <header class="crm-header">
                <div class="crm-header__left">
                    <button
                        type="button"
                        class="crm-header__menu"
                        aria-label="Abrir menú"
                        @click="toggleSidebar"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
                        </svg>
                    </button>
                    <h1 class="crm-header__title">{{ pageTitle }}</h1>
                </div>

                <div class="crm-header__user">
                    <span class="crm-header__name">{{ userName }}</span>
                    <span class="crm-header__avatar">{{ userInitials }}</span>
                </div>
            </header>

            <main class="crm-content">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.crm-app {
    min-height: 100vh;
    background: var(--crm-bg);
    color: var(--crm-text);
}

.crm-sidebar-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgb(15 23 42 / 45%);
    z-index: 40;
}

.crm-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 50;
    width: var(--crm-sidebar-width);
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: var(--crm-navy);
    color: var(--crm-surface);
    transform: translateX(-100%);
    transition: transform var(--crm-transition);
}

.crm-app--sidebar-open .crm-sidebar {
    transform: translateX(0);
}

.crm-app--sidebar-open .crm-sidebar-backdrop {
    display: block;
}

.crm-sidebar__brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 1rem;
    border-bottom: 1px solid rgb(255 255 255 / 10%);
}

.crm-sidebar__logo {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 10px;
    background: var(--crm-gradient-primary);
    box-shadow: 0 4px 12px rgb(37 99 235 / 35%);
}

.crm-sidebar__logo svg {
    width: 1.15rem;
    height: 1.15rem;
}

.crm-sidebar__title {
    font-size: 1.125rem;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.crm-sidebar__nav {
    flex: 1;
    padding: 1rem 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.crm-sidebar__link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 0.85rem;
    border-radius: 10px;
    color: rgb(255 255 255 / 85%);
    text-decoration: none;
    font-size: 0.9375rem;
    font-weight: 500;
    transition: background var(--crm-transition), color var(--crm-transition);
}

.crm-sidebar__link:hover {
    background: var(--crm-blue);
    color: var(--crm-surface);
}

.crm-sidebar__link--active {
    background: var(--crm-blue);
    color: var(--crm-surface);
    box-shadow: inset 3px 0 0 var(--crm-accent);
}

.crm-sidebar__link-icon {
    display: flex;
    width: 1.25rem;
    height: 1.25rem;
}

.crm-sidebar__link-icon svg {
    width: 100%;
    height: 100%;
}

.crm-sidebar__footer {
    padding: 1rem;
    border-top: 1px solid rgb(255 255 255 / 10%);
}

.crm-sidebar__user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.85rem;
}

.crm-sidebar__avatar,
.crm-header__avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--crm-blue-light), var(--crm-accent));
    color: var(--crm-surface);
}

.crm-sidebar__avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
}

.crm-sidebar__user-text {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    font-size: 0.8125rem;
}

.crm-sidebar__user-text strong {
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.crm-sidebar__user-text span {
    color: rgb(255 255 255 / 65%);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.crm-btn {
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: transform var(--crm-transition), box-shadow var(--crm-transition), opacity var(--crm-transition);
}

.crm-btn--logout {
    width: 100%;
    background: rgb(255 255 255 / 12%);
    color: var(--crm-surface);
    border: 1px solid rgb(255 255 255 / 15%);
}

.crm-btn--logout:hover {
    background: rgb(255 255 255 / 18%);
}

.crm-main {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.crm-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: var(--crm-surface);
    box-shadow: var(--crm-shadow-soft);
    position: sticky;
    top: 0;
    z-index: 30;
}

.crm-header__left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 0;
}

.crm-header__menu {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    padding: 0;
    border: none;
    border-radius: 10px;
    background: var(--crm-bg);
    color: var(--crm-navy);
    cursor: pointer;
    transition: background var(--crm-transition);
}

.crm-header__menu:hover {
    background: var(--crm-border);
}

.crm-header__menu svg {
    width: 1.35rem;
    height: 1.35rem;
}

.crm-header__title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--crm-navy);
    letter-spacing: -0.02em;
}

.crm-header__user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.crm-header__name {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--crm-text-muted);
    display: none;
}

.crm-header__avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgb(37 99 235 / 25%);
}

.crm-content {
    flex: 1;
    padding: 1.25rem;
}

@media (min-width: 1024px) {
    .crm-sidebar {
        transform: translateX(0);
    }

    .crm-sidebar-backdrop {
        display: none !important;
    }

    .crm-header__menu {
        display: none;
    }

    .crm-main {
        margin-left: var(--crm-sidebar-width);
    }

    .crm-header__name {
        display: block;
    }

    .crm-content {
        padding: 1.5rem 1.75rem;
    }
}
</style>
