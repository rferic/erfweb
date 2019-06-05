import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from './../../store/auth'
import app from './app'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'
const store =  new Vuex.Store({
    modules: {
        auth,
        app
    },
    state: {
        locale,
        localesSupported,
        csrfToken: typeof csrfToken !== typeof undefined ? csrfToken : null,
        routes: typeof routes !== typeof undefined ? routes : {},
        routesGlobal: typeof routesGlobal !== typeof undefined ? routesGlobal : {}
    },
    getters: {},
    mutations: {
        REFRESH_CSRF_TOKEN: (state, csrfToken) => {
            state.csrfToken = csrfToken
        }
    },
    actions: {
        refreshCsrfToken ({ commit }, csrfToken) {
            commit('REFRESH_CSRF_TOKEN', csrfToken)
        },
    },
    plugins: [
        createPersistedState({
            key: 'erfweb',
            paths: [
                'auth/user'
            ]
        })
    ],
    strict: true
})

export default store
