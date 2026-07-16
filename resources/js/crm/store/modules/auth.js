import api from '../../composables/useApi.js'

export default {
    namespaced: true,
    state: () => ({
        token: localStorage.getItem('crm_token') || null,
        user: JSON.parse(localStorage.getItem('crm_user') || 'null'),
        tenant: JSON.parse(localStorage.getItem('crm_tenant') || 'null'),
        subscription: null,
    }),
    getters: {
        isLoggedIn: s => !!s.token,
        user: s => s.user,
        tenant: s => s.tenant,
        plan: s => s.tenant?.plan || null,
        isOwner: s => s.user?.role === 'owner',
    },
    mutations: {
        SET_TOKEN(state, token) {
            state.token = token
            if (token) localStorage.setItem('crm_token', token)
            else localStorage.removeItem('crm_token')
        },
        SET_USER(state, user) {
            state.user = user
            if (user) localStorage.setItem('crm_user', JSON.stringify(user))
            else localStorage.removeItem('crm_user')
        },
        SET_TENANT(state, tenant) {
            state.tenant = tenant
            if (tenant) localStorage.setItem('crm_tenant', JSON.stringify(tenant))
            else localStorage.removeItem('crm_tenant')
        },
    },
    actions: {
        async login({ commit }, credentials) {
            const { data } = await api.post('/login', credentials)
            commit('SET_TOKEN', data.data.access_token)
            commit('SET_USER', data.data.user)
            commit('SET_TENANT', data.data.tenant)
            return data
        },
        async logout({ commit }) {
            try { await api.post('/logout') } catch {}
            commit('SET_TOKEN', null)
            commit('SET_USER', null)
            commit('SET_TENANT', null)
            window.location.href = '/'
        },
        async fetchMe({ commit }) {
            const { data } = await api.get('/me')
            commit('SET_USER', data.data)
            commit('SET_TENANT', data.data.tenant)
        },
        async refreshToken({ commit }) {
            const { data } = await api.post('/refresh')
            commit('SET_TOKEN', data.data.access_token)
        },
    },
}
