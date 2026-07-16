import api from '../../composables/useApi.js'

export default {
  namespaced: true,

  state: () => ({
    unreadCount: 0,
    notifications: [],
    polling: null,
  }),

  mutations: {
    SET_COUNT(state, count) {
      state.unreadCount = count
    },
    SET_NOTIFICATIONS(state, list) {
      state.notifications = list
    },
    MARK_READ(state, id) {
      const n = state.notifications.find(n => n.id === id)
      if (n) n.is_read = true
      state.unreadCount = Math.max(0, state.unreadCount - 1)
    },
    MARK_ALL_READ(state) {
      state.notifications.forEach(n => (n.is_read = true))
      state.unreadCount = 0
    },
    REMOVE(state, id) {
      const n = state.notifications.find(n => n.id === id)
      if (n && !n.is_read) state.unreadCount = Math.max(0, state.unreadCount - 1)
      state.notifications = state.notifications.filter(n => n.id !== id)
    },
    STOP_POLLING(state) {
      if (state.polling) clearInterval(state.polling)
      state.polling = null
    },
    SET_POLLING(state, id) {
      state.polling = id
    },
  },

  actions: {
    async fetchCount({ commit }) {
      try {
        const { data } = await api.get('/notifications/unread-count')
        commit('SET_COUNT', data.data?.count ?? 0)
      } catch {}
    },

    async fetchRecent({ commit }) {
      try {
        const { data } = await api.get('/notifications?per_page=10')
        commit('SET_NOTIFICATIONS', data.data ?? [])
      } catch {}
    },

    async markRead({ commit }, id) {
      try {
        await api.put(`/notifications/${id}/read`)
        commit('MARK_READ', id)
      } catch {}
    },

    async markAllRead({ commit }) {
      try {
        await api.put('/notifications/read-all')
        commit('MARK_ALL_READ')
      } catch {}
    },

    async remove({ commit }, id) {
      try {
        await api.delete(`/notifications/${id}`)
        commit('REMOVE', id)
      } catch {}
    },

    startPolling({ dispatch, commit, state }) {
      if (state.polling) return
      dispatch('fetchCount')
      const id = setInterval(() => dispatch('fetchCount'), 60000) // every 60s
      commit('SET_POLLING', id)
    },

    stopPolling({ commit }) {
      commit('STOP_POLLING')
    },
  },

  getters: {
    unreadCount: s => s.unreadCount,
    recent: s => s.notifications.slice(0, 8),
  },
}
