const message = {
    namespaced: true,
    state: {
        messages: [],
        status: {},
        updatedTime: null
    },
    getters: {
        count: state => {
            return state.messages.length
        },

        isEmpty: getters => {
            return !(getters.count > 0)
        }
    },
    mutations: {
        SET: (state, messages) => {
            state.messages = messages
        },

        PUSH: (state, message) => {
            state.messages.push(message)
        },

        REFRESH_STATUS: (state, status) => {
            state.status = status
        }
    },
    actions: {
        set ({ commit }, messages) {
            commit('SET', messages)
        },

        push ({ commit }, message) {

            commit('PUSH', message)
        },

        refreshStatus ({ commit }, payload) {
            let status = {}

            Object.keys(payload).forEach((key) => {
                status[key] = payload[key].options

                status[key].forEach((item, index) => {
                    status[key][index].count = 0

                    payload[key].result.forEach((item2) => {
                        if ( item2.status === item.key ) {
                            status[key][index].count = item2.count
                        }
                    })
                })

            })

            commit('REFRESH_STATUS', status)
        }
    }
}

export default message
