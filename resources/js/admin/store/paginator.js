const paginator = {
    namespaced: true,
    state: {
        perPage: 10
    },
    mutations: {
        SET_PER_PAGE: (state, perPage) => {
            state.perPage = perPage
        }
    },
    actions: {
        setPerPage ({ commit }, perPage) {
            commit('SET_PER_PAGE', perPage)
        }
    }
}

export default paginator
