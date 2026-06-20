import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const routes = [
  // ── Public landing page ───────────────────────────────────────────────────
  {
    path: '/',
    component: () => import('@/layouts/LandingLayout.vue'),
    children: [
      {
        path: '',
        name: 'landing',
        component: () => import('@/pages/landing/LandingPage.vue'),
      },
    ],
  },

  // ── Guest-only routes ──────────────────────────────────────────────────────
  {
    path: '/login',
    name: 'login',
    component: () => import('@/pages/auth/LoginPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/pages/auth/RegisterPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('@/pages/auth/ForgotPasswordPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/reset-password/:token',
    name: 'reset-password',
    component: () => import('@/pages/auth/ResetPasswordPage.vue'),
    meta: { guest: true },
  },

  // ── Protected routes ───────────────────────────────────────────────────────
  {
    path: '/',
    component: () => import('@/layouts/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/pages/DashboardPage.vue'),
        meta: { permission: 'dashboard.view' },
      },
      {
        path: 'pos-system',
        name: 'pos',
        component: () => import('@/pages/pos-system/POSPage.vue'),
        meta: { permission: 'pos.access' },
      },
      {
        path: 'sales-management',
        name: 'sales',
        component: () => import('@/pages/sales-management/SalesPage.vue'),
        meta: { permission: 'sales.view' },
      },

      // ── Settings ────────────────────────────────────────────────────────────
      {
        path: 'settings/items',
        name: 'settings-items',
        component: () => import('@/pages/settings/items/ItemsPage.vue'),
        meta: { permission: 'items.view' },
      },
      {
        path: 'settings/items/create',
        name: 'settings-items-create',
        component: () => import('@/pages/settings/items/ItemFormPage.vue'),
        meta: { permission: 'items.create' },
      },
      {
        path: 'settings/items/:id/edit',
        name: 'settings-items-edit',
        component: () => import('@/pages/settings/items/ItemFormPage.vue'),
        meta: { permission: 'items.edit' },
      },
      {
        path: 'settings/categories',
        name: 'settings-categories',
        component: () => import('@/pages/settings/categories/CategoriesPage.vue'),
        meta: { permission: 'categories.view' },
      },
      {
        path: 'settings/customers',
        name: 'settings-customers',
        component: () => import('@/pages/settings/customers/CustomersPage.vue'),
        meta: { permission: 'customers.view' },
      },
      {
        path: 'settings/tables',
        name: 'settings-tables',
        component: () => import('@/pages/settings/tables/TablesPage.vue'),
        meta: { permission: 'tables.view' },
      },
      {
        path: 'settings/waiters',
        name: 'settings-waiters',
        component: () => import('@/pages/settings/waiters/WaitersPage.vue'),
        meta: { permission: 'waiters.view' },
      },
      {
        path: 'settings/taxes',
        name: 'settings-taxes',
        component: () => import('@/pages/settings/taxes/TaxesPage.vue'),
        meta: { permission: 'taxes.view' },
      },
      {
        path: 'settings/restaurant',
        name: 'settings-restaurant',
        component: () => import('@/pages/settings/restaurant/RestaurantSettingsPage.vue'),
        meta: { permission: 'settings.view' },
      },

      // ── Reports ─────────────────────────────────────────────────────────────
      {
        path: 'reports/cashier',
        name: 'reports-cashier',
        component: () => import('@/pages/reports/CashierReportPage.vue'),
        meta: { permission: 'reports.cashier' },
      },
      {
        path: 'reports/daily',
        name: 'reports-daily',
        component: () => import('@/pages/reports/DailySalesPage.vue'),
        meta: { permission: 'reports.daily_sales' },
      },
      {
        path: 'reports/detailed',
        name: 'reports-detailed',
        component: () => import('@/pages/reports/DetailedSalesPage.vue'),
        meta: { permission: 'reports.detailed_sales' },
      },
      {
        path: 'reports/print',
        name: 'reports-print',
        component: () => import('@/pages/reports/SalesPrintPage.vue'),
        meta: { permission: 'reports.detailed_sales' },
      },
      {
        path: 'reports/item-wise',
        name: 'reports-itemwise',
        component: () => import('@/pages/reports/ItemWiseSalesPage.vue'),
        meta: { permission: 'reports.item_wise' },
      },
      {
        path: 'reports/consolidated-item-wise',
        name: 'reports-consolidated',
        component: () => import('@/pages/reports/ConsolidatedItemWisePage.vue'),
        meta: { permission: 'reports.item_wise' },
      },

      // ── Administration ──────────────────────────────────────────────────────
      {
        path: 'admin/users',
        name: 'admin-users',
        component: () => import('@/pages/admin/UsersPage.vue'),
        meta: { permission: 'users.view' },
      },
      {
        path: 'admin/roles',
        name: 'admin-roles',
        component: () => import('@/pages/admin/RolesPage.vue'),
        meta: { permission: 'roles.view' },
      },
    ],
  },

  {
    path: '/403',
    name: 'forbidden',
    component: () => import('@/pages/ForbiddenPage.vue'),
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/pages/NotFoundPage.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const auth = useAuthStore();

  // Not authenticated → redirect to login
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' };
  }

  // Already authenticated → redirect away from guest pages
  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'dashboard' };
  }

  // Permission check — only when authenticated
  if (auth.isAuthenticated && to.meta.permission) {
    if (!auth.hasPermission(to.meta.permission)) {
      return { name: 'forbidden' };
    }
  }
});

export default router;
