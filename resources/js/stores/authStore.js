import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authApi } from '@/services/authApi';

export const useAuthStore = defineStore('auth', () => {
    const user        = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'));
    const token       = ref(localStorage.getItem('auth_token') || null);
    const permissions = ref(JSON.parse(localStorage.getItem('auth_permissions') || '[]'));
    const roles       = ref(JSON.parse(localStorage.getItem('auth_roles') || '[]'));
    const loading     = ref(false);

    const isAuthenticated = computed(() => !!token.value);
    const userName        = computed(() => user.value?.name || '');
    const userEmail       = computed(() => user.value?.email || '');
    const userInitials    = computed(() => {
        if (!user.value?.name) return '?';
        return user.value.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    });

    /** Primary role label shown in the UI (e.g. "Admin", "Cashier"). */
    const primaryRole = computed(() => roles.value[0] || 'User');

    /** Check if the user has a specific permission. */
    const hasPermission = computed(() => (permission) => {
        return permissions.value.includes(permission);
    });

    /** Check if the user has ANY of the given permissions. */
    const hasAnyPermission = computed(() => (...perms) => {
        return perms.some(p => permissions.value.includes(p));
    });

    /** Check if the user has a specific role. */
    const hasRole = computed(() => (role) => {
        return roles.value.includes(role);
    });

    function _persist(tokenVal, userData) {
        token.value       = tokenVal;
        user.value        = userData;
        permissions.value = userData?.permissions || [];
        roles.value       = userData?.roles || [];

        localStorage.setItem('auth_token',       tokenVal);
        localStorage.setItem('auth_user',        JSON.stringify(userData));
        localStorage.setItem('auth_permissions', JSON.stringify(userData?.permissions || []));
        localStorage.setItem('auth_roles',       JSON.stringify(userData?.roles || []));
    }

    function _clear() {
        token.value       = null;
        user.value        = null;
        permissions.value = [];
        roles.value       = [];
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        localStorage.removeItem('auth_permissions');
        localStorage.removeItem('auth_roles');
    }

    async function login(credentials) {
        loading.value = true;
        try {
            const res = await authApi.login(credentials);
            _persist(res.token, res.user);
            return res;
        } finally {
            loading.value = false;
        }
    }

    async function register(data) {
        loading.value = true;
        try {
            const res = await authApi.register(data);
            _persist(res.token, res.user);
            return res;
        } finally {
            loading.value = false;
        }
    }

    async function logout() {
        try {
            await authApi.logout();
        } catch {
            // token may already be expired — still clear locally
        } finally {
            _clear();
        }
    }

    /**
     * Re-fetches the current user including fresh roles/permissions.
     * Called on every page refresh by App.vue to stay in sync.
     */
    async function fetchMe() {
        if (!token.value) return;
        try {
            const res = await authApi.me();
            // Update in-memory and localStorage
            user.value        = res.user;
            permissions.value = res.user?.permissions || [];
            roles.value       = res.user?.roles || [];
            localStorage.setItem('auth_user',        JSON.stringify(res.user));
            localStorage.setItem('auth_permissions', JSON.stringify(permissions.value));
            localStorage.setItem('auth_roles',       JSON.stringify(roles.value));
        } catch {
            _clear();
        }
    }

    return {
        user, token, permissions, roles, loading,
        isAuthenticated, userName, userEmail, userInitials,
        primaryRole, hasPermission, hasAnyPermission, hasRole,
        login, register, logout, fetchMe,
    };
});
