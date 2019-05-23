import { mapState } from 'vuex'

const messageMixin = {
    computed: {
        ...mapState([ 'routesGlobal' ])
    },
    methods: {
        async sendMessageRequest ({ subject, text, tag }) {
            const status = 'pending'
            const { data } = await this.axios.post(this.routesGlobal.sendMessage, { subject, text, tag, status })
            return data
        }
    }
}

export default messageMixin
