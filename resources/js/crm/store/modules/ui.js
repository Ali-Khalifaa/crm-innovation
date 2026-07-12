export default {
    namespaced: true,
    state: () => ({
        sidebarOpen: true,
        sidebarCollapsed: localStorage.getItem('crm_sidebar_collapsed') === 'true',
        planLimitModal: false,
        darkMode: localStorage.getItem('crm_dark') === 'true',
        locale: localStorage.getItem('crm_locale') || 'en',
    }),
    mutations: {
        TOGGLE_SIDEBAR(state) { state.sidebarOpen = !state.sidebarOpen },
        SET_SIDEBAR(state, val) { state.sidebarOpen = val },
        TOGGLE_SIDEBAR_COLLAPSE(state) {
            state.sidebarCollapsed = !state.sidebarCollapsed
            localStorage.setItem('crm_sidebar_collapsed', state.sidebarCollapsed)
        },
        SET_PLAN_LIMIT_MODAL(state, val) { state.planLimitModal = val },
        TOGGLE_DARK(state) {
            state.darkMode = !state.darkMode
            localStorage.setItem('crm_dark', state.darkMode)
        },
        INIT_DARK(state) {
            // dark mode handled via data-bs-theme attribute on hk-wrapper
        },
        SET_LOCALE(state, locale) {
            state.locale = locale
            localStorage.setItem('crm_locale', locale)
            document.documentElement.lang = locale
            document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr'
        },
    },
}
