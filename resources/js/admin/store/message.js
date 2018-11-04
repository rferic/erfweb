import moment from 'moment'

const message = {
    namespaced: true,
    state: {
        state: [],
        timeLastRequest: null,
        lastPendings: []
    },
    getters: {
        hasPendings: (state) => {
            return state.state.status.pending > 0
        }
    },
    mutations: {
        REFRESH_STATE: (state, status) => {
            state.state = status
        },
        SET_TIME_LAST_REQUEST: (state) => {
            state.timeLastRequest = moment()
        },
        REFRESH_LAST_PENDINGS: (state, messages) => {
            state.lastPendings = messages
        }
    },
    actions: {
        refresh ({ commit }, params) {
            commit('REFRESH_STATE', params.state)
            commit('REFRESH_LAST_PENDINGS', params.messages)
            commit('SET_TIME_LAST_REQUEST')
        }
    }
}

export default message
