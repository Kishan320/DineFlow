import api from './axiosInstance';

export const authApi = {
    login:          (data) => api.post('/login', data),
    register:       (data) => api.post('/register', data),
    logout:         ()     => api.post('/logout'),
    me:             ()     => api.get('/me'),
    forgotPassword: (data) => api.post('/forgot-password', data),
    resetPassword:  (data) => api.post('/reset-password', data),
};
