import axios from 'axios'
import store from '../store/index.js'

const api = axios.create({
    baseURL: '/api/crm',
    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
    transformResponse: [
        (data) => {
            if (typeof data === 'string') {
                data = data.replace(/^﻿+/, '');
                try { return JSON.parse(data); } catch(e) { return data; }
            }
            return data;
        }
    ],
})

api.interceptors.request.use(config => {
    const token = store.state.auth.token
    if (token) config.headers.Authorization = `Bearer ${token}`
    return config
})

api.interceptors.response.use(
    res => res,
    async err => {
        if (err.response?.status === 401) {
            store.commit('auth/SET_TOKEN', null)
            store.commit('auth/SET_USER', null)
            store.commit('auth/SET_TENANT', null)
            window.location.href = '/crm/login'
        }
        if (err.response?.status === 403 && err.response?.data?.message?.includes('plan')) {
            store.commit('ui/SET_PLAN_LIMIT_MODAL', true)
        }
        return Promise.reject(err)
    }
)

export default api
