const auth = {
    namespaced: true,
    state: {
        user: null
    },
    mutations: {
        SET: (state, user) => {
            state.user = user
        }
    },
    actions: {
        set ({ commit }, user) {
            commit('SET', user)
        }
    }
}

export default auth
