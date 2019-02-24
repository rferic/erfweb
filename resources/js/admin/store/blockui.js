const blockui = {
    namespaced: true,
    state: {
        isVisible: false,
        message: 'Espera un momento...'
    },
    mutations: {
        TOGGLE_IS_VISIBLE: (state, isVisible) => {
            state.isVisible = isVisible
        }
    },
    actions: {
        toggleIsVisible ({ commit }, isVisible) {
            commit('TOGGLE_IS_VISIBLE', isVisible)
        }
    }
}

export default blockui
