import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authApi } from '@/services/authApi';

export const useAuthStore = defineStore('auth', () => {
    const user    = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'));
    const token   = ref(localStorage.getItem('auth_token') || null);
    const loading = ref(false);

    const isAuthenticated = computed(() => !!token.value);
    const userName        = computed(() => user.value?.name || '');
    const userEmail       = computed(() => user.value?.email || '');
    const userInitials    = computed(() => {
        if (!user.value?.name) return '?';
        return user.value.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    });

    function _persist(tokenVal, userVal) {
        token.value = tokenVal;
        user.value  = userVal;
        localStorage.setItem('auth_token', tokenVal);
        localStorage.setItem('auth_user', JSON.stringify(userVal));
    }

    function _clear() {
        token.value = null;
        user.value  = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
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

    async function fetchMe() {
        if (!token.value) return;
        try {
            const res = await authApi.me();
            user.value = res.user;
            localStorage.setItem('auth_user', JSON.stringify(res.user));
        } catch {
            _clear();
        }
    }

    return {
        user, token, loading,
        isAuthenticated, userName, userEmail, userInitials,
        login, register, logout, fetchMe,
    };
});
