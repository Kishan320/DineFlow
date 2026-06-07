import { computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';

/**
 * usePermission — Global permission composable for DineFlow
 *
 * Usage in any Vue component:
 *
 *   const { can, canAny, hasRole } = usePermission();
 *
 *   // Template: v-if="can('items.create')"
 *   // Template: v-if="canAny('items.edit', 'items.delete')"
 *   // Template: v-if="hasRole('Admin')"
 */
export function usePermission() {
    const auth = useAuthStore();

    /** Returns true if the user has the exact permission. */
    const can = (permission) => auth.hasPermission(permission);

    /** Returns true if the user has ANY of the listed permissions. */
    const canAny = (...permissions) => auth.hasAnyPermission(...permissions);

    /** Returns true if the user has ALL of the listed permissions. */
    const canAll = (...permissions) => permissions.every(p => auth.hasPermission(p));

    /** Returns true if the user has the given role. */
    const hasRole = (role) => auth.hasRole(role);

    /** Returns true if the user is Super Admin or Admin. */
    const isAdmin = computed(() =>
        auth.hasRole('Super Admin') || auth.hasRole('Admin')
    );

    /** Returns true if the user is Super Admin only. */
    const isSuperAdmin = computed(() => auth.hasRole('Super Admin'));

    return {
        can,
        canAny,
        canAll,
        hasRole,
        isAdmin,
        isSuperAdmin,
    };
}
