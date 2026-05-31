import api from './axiosInstance';

export const categoryApi = {
  list:    (params) => api.get('/categories', { params }),
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
  getAll:  ()         => api.get('/customers'),
  create:  (data)     => api.post('/customers', data),
  update:  (id, data) => api.put(`/customers/${id}`, data),
  remove:  (id)       => api.delete(`/customers/${id}`),
};

export const tableApi = {
  getAll:  ()         => api.get('/tables'),
  create:  (data)     => api.post('/tables', data),
  update:  (id, data) => api.put(`/tables/${id}`, data),
  remove:  (id)       => api.delete(`/tables/${id}`),
};

export const waiterApi = {
  getAll:  ()         => api.get('/waiters'),
  create:  (data)     => api.post('/waiters', data),
  update:  (id, data) => api.put(`/waiters/${id}`, data),
  remove:  (id)       => api.delete(`/waiters/${id}`),
};

export const taxApi = {
  getAll:  ()         => api.get('/taxes'),
  create:  (data)     => api.post('/taxes', data),
  update:  (id, data) => api.put(`/taxes/${id}`, data),
  remove:  (id)       => api.delete(`/taxes/${id}`),
};
