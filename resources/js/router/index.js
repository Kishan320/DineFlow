import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    component: () => import('@/layouts/AppLayout.vue'),
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('@/pages/DashboardPage.vue'),
      },
      {
        path: 'pos-system',
        name: 'pos',
        component: () => import('@/pages/pos-system/POSPage.vue'),
      },
      {
        path: 'sales-management',
        name: 'sales',
        component: () => import('@/pages/sales-management/SalesPage.vue'),
      },
      {
        path: 'settings/items',
        name: 'settings-items',
        component: () => import('@/pages/settings/items/ItemsPage.vue'),
      },
      {
        path: 'settings/items/create',
        name: 'settings-items-create',
        component: () => import('@/pages/settings/items/ItemFormPage.vue'),
      },
      {
        path: 'settings/items/:id/edit',
        name: 'settings-items-edit',
        component: () => import('@/pages/settings/items/ItemFormPage.vue'),
      },
      {
        path: 'settings/categories',
        name: 'settings-categories',
        component: () => import('@/pages/settings/categories/CategoriesPage.vue'),
      },
      {
        path: 'settings/customers',
        name: 'settings-customers',
        component: () => import('@/pages/settings/customers/CustomersPage.vue'),
      },
      {
        path: 'settings/tables',
        name: 'settings-tables',
        component: () => import('@/pages/settings/tables/TablesPage.vue'),
      },
      {
        path: 'settings/waiters',
        name: 'settings-waiters',
        component: () => import('@/pages/settings/waiters/WaitersPage.vue'),
      },
      {
        path: 'settings/taxes',
        name: 'settings-taxes',
        component: () => import('@/pages/settings/taxes/TaxesPage.vue'),
      },
      {
        path: 'settings/restaurant',
        name: 'settings-restaurant',
        component: () => import('@/pages/settings/restaurant/RestaurantSettingsPage.vue'),
      },
      {
        path: 'reports/cashier',
        name: 'reports-cashier',
        component: () => import('@/pages/reports/CashierReportPage.vue'),
      },
      {
        path: 'reports/daily',
        name: 'reports-daily',
        component: () => import('@/pages/reports/DailySalesPage.vue'),
      },
      {
        path: 'reports/detailed',
        name: 'reports-detailed',
        component: () => import('@/pages/reports/DetailedSalesPage.vue'),
      },
      {
        path: 'reports/print',
        name: 'reports-print',
        component: () => import('@/pages/reports/SalesPrintPage.vue'),
      },
      {
        path: 'reports/item-wise',
        name: 'reports-itemwise',
        component: () => import('@/pages/reports/ItemWiseSalesPage.vue'),
      },
      {
        path: 'reports/consolidated-item-wise',
        name: 'reports-consolidated',
        component: () => import('@/pages/reports/ConsolidatedItemWisePage.vue'),
      },
    ],
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

export default router;
