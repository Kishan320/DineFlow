import api from './axiosInstance';

export const categoryApi = {
  list:    (params, signal) => api.get('/categories', { params, signal }),
  create:  (data)   => api.post('/categories', data),
  update:  (id, data) => api.put(`/categories/${id}`, data),
  remove:  (id)     => api.delete(`/categories/${id}`),
};

export const itemApi = {
  list:    (params)   => api.get('/items', { params }),
  getOne:  (id)       => api.get(`/items/${id}`),
  create:  (formData) => api.post('/items', formData, { headers: { 'Content-Type': 'multipart/form-data' } }),
  update:  (id, formData) => api.post(`/items/${id}?_method=PUT`, formData, { headers: { 'Content-Type': 'multipart/form-data' } }),
  remove:  (id)       => api.delete(`/items/${id}`),
};

export const customerApi = {
  list:    (params, signal) => api.get('/customers', { params, signal }),
  create:  (data)     => api.post('/customers', data),
  update:  (id, data) => api.put(`/customers/${id}`, data),
  remove:  (id)       => api.delete(`/customers/${id}`),
};

export const tableApi = {
  list:    (params, signal) => api.get('/tables', { params, signal }),
  create:  (data)     => api.post('/tables', data),
  update:  (id, data) => api.put(`/tables/${id}`, data),
  remove:  (id)       => api.delete(`/tables/${id}`),
};

export const waiterApi = {
  list:    (params, signal) => api.get('/waiters', { params, signal }),
  create:  (data)     => api.post('/waiters', data),
  update:  (id, data) => api.put(`/waiters/${id}`, data),
  remove:  (id)       => api.delete(`/waiters/${id}`),
};

export const taxApi = {
  list:    (params, signal) => api.get('/taxes', { params, signal }),
  create:  (data)     => api.post('/taxes', data),
  update:  (id, data) => api.put(`/taxes/${id}`, data),
  remove:  (id)       => api.delete(`/taxes/${id}`),
};

export const restaurantSettingsApi = {
  get:    () => api.get('/restaurant-settings'),
  save:   (data) => api.post('/restaurant-settings', data),
  update: (data) => api.put('/restaurant-settings', data),
};
