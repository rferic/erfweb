<template>
    <ul class="navbar-nav my-lg-0">
        <nav-message-notify
            :title="titleNavMessages"
            icon="fa-envelope" />
        <nav-account />
    </ul>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import NavMessageNotify from './MessageNotify'
    import NavAccount from './Account'

    export default {
        name: 'NavRight',
        components: {
            NavMessageNotify,
            NavAccount
        },
        computed: {
            ...mapState([ 'routesGlobal', 'locale' ]),
            ...mapState({
                statusMessages: 'message/status'
            }),
            titleNavMessages () {
                return 'asd'
            }
        },
        methods: {
            ...mapActions({
               refreshStatusMessages: 'message/refreshStatus'
            }),
            logout () {
                document.getElementById('logout-form').submit()
            },
            // Getters
            async getStatus () {
                this.refreshStatusMessages(await this.getStatusRequest())
            },
            // AJAX Request
            async getStatusRequest () {
                const response = await axios.post(this.routesGlobal.messages.getStatus)
                return response.data
            }
        },
        mounted () {
            this.getStatus()
        }
    }
</script>
