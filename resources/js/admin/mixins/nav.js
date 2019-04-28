import { mapState, mapGetters, mapActions } from 'vuex'
import NavMessageNotify from './../components/Nav/MessageNotify'
import NavAccount from './../components/Nav/Account'
import messageMixin from './message'

const navMixin = {
    computed: {
        ...mapState([ 'routesGlobal', 'locale' ]),
        ...mapState({
            tagsStructure: state => state.message.state.tagsStructure,
            lastPendings: state => state.message.lastPendings,
            timeLastRequestMessage: state => state.message.timeLastRequest
        }),
        ...mapGetters({
            hasPendingMessages: 'message/hasPendings',
            countPendings: 'message/countPendings',
        }),
        lastPendingsToNotify () {
            let lastPendingsToNotify = []

            this.lastPendings.forEach((pending) => {
                let prepend = {
                    icon: '',
                    color: '',
                    class: ''
                }

                this.tagsStructure.forEach((item) => {
                    if ( item.key === pending.tag ) {
                        prepend.icon = item.icon
                        prepend.color = item.color
                        prepend.class = item.class
                    }
                })

                lastPendingsToNotify.push({
                    title: pending.subject,
                    content: pending.text,
                    url: `${this.routesGlobal.messages.index}/${pending.id}`,
                    time: pending.created_at,
                    prepend
                })
            })

            return lastPendingsToNotify
        },
        titleNavMessages () {
            return this.$t('Pending messages', this.locale)
        },
        textUrlNavMessages () {
            return this.$t('Check all messages', this.locale)
        }
    },
    components: { NavMessageNotify, NavAccount },
    mixins: [ messageMixin ],
    data () {
        return {
            secondsTimeoutLastRequestMessage: 60,
            countItemsInNotify: 3
        }
    },
    methods: {
        ...mapActions({
            refreshNotifyMessages: 'message/refresh'
        }),
        // Getters
        getRemainingTimeLastRequestMessage () {
            return this.secondsTimeoutLastRequestMessage - Vue.moment().diff(this.timeLastRequestMessage, 'seconds')
        },
        // Actions
        async refreshData () {
            const data = {
                state: await this.getMessageStateRequest({}),
                messages: await this.getLastPendingMessagesRequest()
            }

            this.refreshNotifyMessages(data)

            setTimeout(() => {
                this.refreshData()
            }, this.secondsTimeoutLastRequestMessage * 1000)
        },
        // AJAX Request
        async getLastPendingMessagesRequest () {
            const response = await this.axios.post(this.routesGlobal.messages.getLastPendingMessages, {
                count: this.countItemsInNotify
            })
            return response.data
        }
    },
    mounted () {
        this.$emit('eventToRoot')
        setTimeout(() => {
            this.refreshData()
        }, this.getRemainingTimeLastRequestMessage() * 1000)
    }
}

export default navMixin
