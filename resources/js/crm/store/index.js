import { createStore } from 'vuex'
import auth from './modules/auth.js'
import ui from './modules/ui.js'

export default createStore({
    modules: { auth, ui },
})
