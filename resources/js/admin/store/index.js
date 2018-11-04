import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from './auth'
import message from './message'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

const store =  new Vuex.Store({
    modules: {
        auth,
        message
    },
    state: {
        locale: locale,
        csrfToken: typeof csrfToken !== typeof undefined ? csrfToken : null,
        routes: typeof routes !== typeof undefined ? routes : {},
        routesGlobal: typeof routesGlobal !== typeof undefined ? routesGlobal : {}
    },
    getters: {},
    mutations: {},
    actions: {},
    plugins: [
        createPersistedState({
            key: 'erfweb',
            paths: [
                'auth',
                'message.state',
                'message.lastPendings',
                'message.timeLastRequest'
            ]
        })
    ],
    strict: true
})

export default store
