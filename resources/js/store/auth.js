const auth = {
    namespaced: true,
    state: {
        user: null,
        modal: false,
        tab: 'login'
    },
    getters: {
        isLogged: (state) => {
            return state.user !== null
        }
    },
    mutations: {
        SET: (state, user) => {
            state.user = user
        },
        OPEN: (state) => {
            state.modal = true
        },
        CLOSE: (state) => {
            state.modal = false
        },
        CHANGE_TAB: (state, tab) => {
            state.tab = tab
        }
    },
    actions: {
        set ({ commit }, user) {
            commit('SET', user)
        },
        open ({ commit }) {
            commit('OPEN')
        },
        close ({ commit }) {
            commit('CLOSE')
        },
        changeTab ({ commit }, tab) {
            commit('CHANGE_TAB', tab)
        }
    }
}

export default auth
