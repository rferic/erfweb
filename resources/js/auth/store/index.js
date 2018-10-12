import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

const store =  new Vuex.Store({
    state: {
        locale: locale,
        csrfToken: typeof csrfToken !== typeof undefined ? csrfToken : null,
        routes: typeof routes !== typeof undefined ? routes : {},
        routesGlobal: typeof routesGlobal !== typeof undefined ? routesGlobal : {}
    },
    getters: {},
    mutations: {},
    actions: {},
    strict: debug
})

export default store
