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
        component: () => import('@/pages/PlaceholderPage.vue'),
        props: { title: 'Restaurant Settings', breadcrumb: 'Settings · Restaurant Settings' },
      },
      {
        path: 'reports/cashier',
        name: 'reports-cashier',
        component: () => import('@/pages/PlaceholderPage.vue'),
        props: { title: 'Cashier Report', breadcrumb: 'Reports · Cashier Report' },
      },
      {
        path: 'reports/daily',
        name: 'reports-daily',
        component: () => import('@/pages/PlaceholderPage.vue'),
        props: { title: 'Daily Sales', breadcrumb: 'Reports · Daily Sales' },
      },
      {
        path: 'reports/detailed',
        name: 'reports-detailed',
        component: () => import('@/pages/PlaceholderPage.vue'),
        props: { title: 'Detailed Sales', breadcrumb: 'Reports · Detailed Sales' },
      },
      {
        path: 'reports/print',
        name: 'reports-print',
        component: () => import('@/pages/PlaceholderPage.vue'),
        props: { title: 'Sales Print Report', breadcrumb: 'Reports · Sales Print Report' },
      },
      {
        path: 'reports/item-wise',
        name: 'reports-itemwise',
        component: () => import('@/pages/PlaceholderPage.vue'),
        props: { title: 'Item Wise Sales', breadcrumb: 'Reports · Item Wise Sales' },
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
