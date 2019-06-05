const app = {
    namespaced: true,
    state: {
        myApps: []
    },
    getters: {
        hasMyApps: (state) => {
            return state.myApps.length > 0
        }
    },
    mutations: {
        SET: (state, apps) => {
            state.myApps = apps
        },
        CLEAR: (state) => {
            state.myApps = []
        },
        PUSH: (state, app) => {
            state.myApps.push(app)
        },
        REMOVE: (state, index) => {
            state.myApps.splice(index, 1)
        }
    },
    actions: {
        set ({ commit }, apps) {
            commit('SET', apps)
        },
        clear ({ commit }) {
            commit('CLEAR')
        },
        push ({ commit }, app) {
            commit('PUSH', app)
        },
        remove ({ commit }, index) {
            commit('REMOVE', index)
        }
    }
}

export default app
