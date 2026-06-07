import api from './axiosInstance';

export const adminApi = {
    // ── Users ───────────────────────────────────────────────────────────────
    getUsers:    ()             => api.get('/users'),
    getUser:     (id)           => api.get(`/users/${id}`),
    createUser:  (data)         => api.post('/users', data),
    updateUser:  (id, data)     => api.put(`/users/${id}`, data),
    deleteUser:  (id)           => api.delete(`/users/${id}`),
    assignRole:  (id, role)     => api.post(`/users/${id}/role`, { role }),

    // ── Roles ───────────────────────────────────────────────────────────────
    getRoles:           ()              => api.get('/roles'),
    getRole:            (id)            => api.get(`/roles/${id}`),
    createRole:         (data)          => api.post('/roles', data),
    updateRole:         (id, data)      => api.put(`/roles/${id}`, data),
    deleteRole:         (id)            => api.delete(`/roles/${id}`),
    syncRolePermissions: (id, perms)    => api.post(`/roles/${id}/permissions`, { permissions: perms }),
    getRolePermissions: (id)            => api.get(`/roles/${id}/permissions`),

    // ── Permissions ─────────────────────────────────────────────────────────
    getAllPermissions: () => api.get('/permissions'),
};
