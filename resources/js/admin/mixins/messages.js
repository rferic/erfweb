const messagesMixin = {
    data () {
        return {
            messages: [],
            totalMessages: 0,
            urlNextPage: null
        }
    },
    computed: {
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
        async getMessages ({ stack, page, perPage, url }) {
            const data = await this.getMessagesRequest({ page, perPage, url })

            if ( stack ) {
                data.data.forEach(message => {
                    this.messages.push(message)
                })
            } else {
                this.messages = data.data
            }

            this.totalMessages = data.total
            this.urlNextPage = data.next_page_url
            return data
        },
        // API Request
        async getMessagesRequest ({ page, perPage, url }) {
            if ( typeof perPage === typeof undefined ) {
                perPage = null
            }

            if ( typeof url === typeof undefined ) {
                url = routes.getMessages
            }

            const { data } = await axios.post(url, {
                perPage,
                filters: this.filters
            })
            return data
        },
        async getAuthorRequest ( message ) {
            const { data } = await axios.post(`${routes.getMessages}/${message.id}/get-author`, {})
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

export default messagesMixin
