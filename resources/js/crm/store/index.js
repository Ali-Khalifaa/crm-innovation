import { createStore } from 'vuex'
import auth from './modules/auth.js'
import ui from './modules/ui.js'
import notifications from './modules/notifications.js'

export default createStore({
    modules: { auth, ui, notifications },
})
