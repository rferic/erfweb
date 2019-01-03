const adminMenu = {
    namespaced: true,
    state: {
        menu: Array
    },
    getters: {
        menuIsLoaded: state => {
            return state.menu.length > 0
        }
    },
    mutations: {
        REFRESH: (state, menu) => {
            state.menu = menu
        }
    },
    actions: {
        refresh ({ commit }, menu) {
            commit('REFRESH', menu)
        }
    }
}

export default adminMenu
