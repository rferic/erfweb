<template>
    <ul class="navbar-nav my-lg-0">
        <nav-message-notify
            :title="titleNavMessages"
            :alert="hasPendingMessages"
            :items="lastPendingsToNotify"
            :count="countPending"
            :url="`${routesGlobal.messages.index}?status=pending`"
            :url-text="textUrlNavMessages"
            icon="fa-envelope" />
        <nav-account />
    </ul>
</template>

<script>
    import { mapState, mapGetters, mapActions } from 'vuex'
    import NavMessageNotify from './MessageNotify'
    import NavAccount from './Account'

    export default {
        name: 'NavRight',
        components: {
            NavMessageNotify,
            NavAccount
        },
        data () {
            return {
                secondsTimeoutLastRequestMessage: 60,
                countItemsInNotify: 3
            }
        },
        computed: {
            ...mapState([ 'routesGlobal', 'locale' ]),
            ...mapState({
                tagsStructure: state => state.message.state.tagsStructure,
                countPending: state => state.message.countPendings,
                lastPendings: state => state.message.lastPendings,
                timeLastRequestMessage: state => state.message.timeLastRequest
            }),
            ...mapGetters({
                hasPendingMessages: 'message/hasPendings'
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
        methods: {
            ...mapActions({
                refreshNotifyMessages: 'message/refresh'
            }),
            logout () {
                document.getElementById('logout-form').submit()
            },
            // Getters
            getRemainingTimeLastRequestMessage () {
                return this.secondsTimeoutLastRequestMessage - Vue.moment().diff(this.timeLastRequestMessage, 'seconds')
            },
            // Actions
            async refreshData () {
                const data = {
                    state: await this.getMessageStateRequest(),
                    messages: await this.getLastPendingMessagesRequest()
                }

                this.refreshNotifyMessages(data)

                setTimeout(() => {
                    this.refreshData()
                }, this.secondsTimeoutLastRequestMessage * 1000)
            },
            // AJAX Request
            async getMessageStateRequest () {
                const response = await axios.post(this.routesGlobal.messages.getState)
                return response.data
            },
            async getLastPendingMessagesRequest () {
                const response = await axios.post(this.routesGlobal.messages.getLastPendingMessages, {
                    count: this.countItemsInNotify
                })
                return response.data
            }
        },
        mounted () {
            setTimeout(() => {
                this.refreshData()
            }, this.getRemainingTimeLastRequestMessage() * 1000)
        }
    }
</script>
