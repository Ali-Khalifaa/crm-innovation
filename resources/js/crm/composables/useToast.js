import { reactive } from 'vue'

// Singleton — shared across all components
const state = reactive({ toasts: [] })
let nextId = 0

export function useToast() {
    function show(message, type = 'success', duration = 3500) {
        const id = ++nextId
        state.toasts.push({ id, message, type })
        setTimeout(() => {
            const idx = state.toasts.findIndex(t => t.id === id)
            if (idx !== -1) state.toasts.splice(idx, 1)
        }, duration)
    }

    return {
        toasts: state.toasts,
        success: (msg, d) => show(msg, 'success', d),
        error:   (msg, d) => show(msg, 'error',   d),
        warning: (msg, d) => show(msg, 'warning', d),
        info:    (msg, d) => show(msg, 'info',    d),
    }
}
