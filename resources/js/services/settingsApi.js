import api from './axiosInstance';

export const categoryApi = {
  list: (params, signal) => api.get('/categories', { params, signal }),
  listAll: () => api.get('/categories/list'),
  create: (data) => api.post('/categories', data),
  update: (id, data) => api.put(`/categories/${id}`, data),
  remove: (id) => api.delete(`/categories/${id}`),
};

export const itemApi = {
  list: (params) => api.get('/items', { params }),
  getOne: (id) => api.get(`/items/${id}`),
  create: (formData) => api.post('/items', formData, { headers: { 'Content-Type': 'multipart/form-data' } }),
  update: (id, formData) => api.post(`/items/${id}?_method=PUT`, formData, { headers: { 'Content-Type': 'multipart/form-data' } }),
  remove: (id) => api.delete(`/items/${id}`),
};

export const customerApi = {
  list: (params, signal) => api.get('/customers', { params, signal }),
  create: (data) => api.post('/customers', data),
  update: (id, data) => api.put(`/customers/${id}`, data),
  remove: (id) => api.delete(`/customers/${id}`),
};

export const tableApi = {
  list: (params, signal) => api.get('/tables', { params, signal }),
  create: (data) => api.post('/tables', data),
  update: (id, data) => api.put(`/tables/${id}`, data),
  remove: (id) => api.delete(`/tables/${id}`),
};

export const waiterApi = {
  list: (params, signal) => api.get('/waiters', { params, signal }),
  create: (data) => api.post('/waiters', data),
  update: (id, data) => api.put(`/waiters/${id}`, data),
  remove: (id) => api.delete(`/waiters/${id}`),
};

export const taxApi = {
  list: (params, signal) => api.get('/taxes', { params, signal }),
  listAll: () => api.get('/taxes/list'),
  create: (data) => api.post('/taxes', data),
  update: (id, data) => api.put(`/taxes/${id}`, data),
  remove: (id) => api.delete(`/taxes/${id}`),
};

export const restaurantSettingsApi = {
  get: () => api.get('/restaurant-settings'),
  update: (data) => api.put('/restaurant-settings', data),
};

export const salesApi = {
  list: (params) => api.get('/pos/reports/sales', { params }),
};

export const reportApi = {
  cashier: (params) => api.get('/reports/cashier', { params }),
  dailySales: (params) => api.get('/reports/daily-sales', { params }),
  detailedSales: (params) => api.get('/reports/detailed-sales', { params }),
  salesPrint: (params) => api.get('/reports/sales-print', { params }),
  itemWise: (params) => api.get('/reports/item-wise', { params }),
  consolidatedItems: (params) => api.get('/reports/consolidated-items', { params }),
};

export const posApi = {  // Products
  getProducts: (params) => api.get('/pos/products', { params }),
  getCategories: () => api.get('/pos/categories'),

  // Tables & Waiters
  getTables: () => api.get('/pos/tables'),
  getWaiters: () => api.get('/pos/waiters'),

  // Customers
  searchCustomers: (search) => api.get('/pos/customers/search', { params: { search } }),
  createCustomer: (data) => api.post('/pos/customers', data),

  // Sessions
  getActiveSession: () => api.get('/pos/session/active'),
  openSession: (data) => api.post('/pos/session/open', data),
  closeSession: (id, data) => api.post(`/pos/session/${id}/close`, data),
  destroySession: (id) => api.delete(`/pos/session/${id}`),

  // Orders
  getOngoing: () => api.get('/pos/orders/ongoing'),
  getOrders: (params) => api.get('/pos/orders', { params }),
  createOrder: (data) => api.post('/pos/orders', data),
  getOrder: (id) => api.get(`/pos/orders/${id}`),
  updateOrderStatus: (id, status) => api.patch(`/pos/orders/${id}/status`, { status }),
  deleteOrder: (id) => api.delete(`/pos/orders/${id}`),
  getBillData: (id) => api.get(`/pos/orders/${id}/bill`),

  // KOT
  getKots: (orderId) => api.get(`/pos/orders/${orderId}/kots`),
  createKot: (orderId, data) => api.post(`/pos/orders/${orderId}/kots`, data),
  updateKotStatus: (kotId, status) => api.patch(`/pos/kots/${kotId}/status`, { status }),
  getPendingKots: () => api.get('/pos/kots/pending'),

  // Reports
  dailySales: (date) => api.get('/pos/reports/daily', { params: { date } }),
  itemSales: (params) => api.get('/pos/reports/items', { params }),
  categorySales: (params) => api.get('/pos/reports/categories', { params }),
  paymentReport: (params) => api.get('/pos/reports/payments', { params }),
  waiterSales: (params) => api.get('/pos/reports/waiters', { params }),
  tableSales: (params) => api.get('/pos/reports/tables', { params }),
  taxReport: (params) => api.get('/pos/reports/tax', { params }),

  // Stripe
  createStripeSession: (amount, order_type) => api.post('/stripe/create-session', { amount, order_type }),
  verifyStripePayment: (session_id) => api.post('/stripe/payment-status', { session_id }),
};
