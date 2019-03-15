import { mapState } from 'vuex'

const messageMixin = {
    data () {
        return {
            messages: [],
            totalMessages: 0,
            urlNextPage: null,
            orderBy: {
                'way': 'ASC',
                'attribute': 'created_at'
            }
        }
    },
    computed: {
        ...mapState([ 'routes' ]),
        hasMessages () {
            return this.messages.length > 0
        },
        hasNextPage () {
            return this.urlNextPage !== null
        }
    },
    methods: {
        // Actions
        removeFromList ( message ) {
            this.messages.some((messageList, index) => {
                const scapeCondition = message.id === messageList.id

                if ( scapeCondition ) {
                    this.messages.splice(index, 1)
                    this.totalMessages--
                }

                return scapeCondition
            })

            this.messagesWithCheckedAttr = this.clone(this.messages)
        },
        // Getters
        async getMessages ({ stack, page, perPage, url, filters, orderBy }) {
            const data = await this.getMessagesRequest({ page, perPage, url, filters, orderBy })

            if ( stack ) {
                for ( let message of data.data ) {
                    this.messages.push(message)
                }
            } else {
                this.messages = data.data
            }

            this.totalMessages = data.total
            this.urlNextPage = data.next_page_url
            return data
        },
        getDataStatusSelected ( statusKey ) {
            let statusSelected = ''

            this.statusList.some((status) => {
                const conditionScape = statusKey === status.key

                if ( conditionScape ) {
                    statusSelected = status
                }

                return conditionScape
            })

            return statusSelected
        },
        getDataTagSelected ( tagKey ) {
            let tagSelected = ''

            this.tagsList.some((tag) => {
                const conditionScape = tagKey === tag.key

                if ( conditionScape ) {
                    tagSelected = tag
                }

                return conditionScape
            })

            return tagSelected
        },
        // API Request
        async getMessagesRequest ({ page, perPage, url, filters, orderBy }) {
            if ( typeof page === typeof page ) {
                page = null
            }

            if ( typeof perPage === typeof undefined ) {
                perPage = null
            }

            if ( typeof url === typeof undefined ) {
                url = page !== null ? `${routes.getMessages}?page=${page}` : routes.getMessages
            }

            const { data } = await axios.post(url, {
                perPage,
                filters,
                orderBy
            })
            return data
        },
        async getMessageStateRequest ({ filters }) {
            const response = await axios.post(this.routesGlobal.messages.getState, { filters })
            return response.data
        },
        async getAuthorRequest ( message ) {
            const { data } = await axios.post(`${routes.getMessages}/${message.id}/get-author`, {})
            return data
        },
        async updateMessageRequest ( message ) {
            const { data } = await axios.post(`${this.routes.getMessages}/${message.id}/update`, message)
            return data
        },
        async removeMessageRequest ( message ) {
            const { data } = await axios.delete(`${this.routes.getMessages}/${message.id}/remove`, {})
            return data
        },
        async restoreMessageRequest ( message ) {
            const { data } = await axios.post(`${this.routes.getMessages}/${message.id}/restore`, {})
            return data
        },
        async destroyMessageRequest ( message ) {
            const { data } = await axios.delete(`${this.routes.getMessages}/${message.id}/destroy`, {})
            return data
        }
    }
}

export default messageMixin
