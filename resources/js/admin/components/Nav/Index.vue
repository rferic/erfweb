<template>
    <base-nav class="navbar-top navbar-dark" id="navbar-main-mobile" :show-toggle-button="false" expand>
        <ul class="navbar-nav align-items-center d-none d-md-flex">
            <nav-message-notify
                :title="titleNavMessages"
                :alert="hasPendingMessages"
                :items="lastPendingsToNotify"
                :count="countPendings"
                :url="`${routesGlobal.messages.index}?status=pending`"
                :url-text="textUrlNavMessages"
                icon="ni ni-email-83"
            />
            <nav-account />
        </ul>
    </base-nav>
</template>

<script>
    import navMixin from './../../mixins/nav'

    export default {
        name: 'NavIndex',
        mixins: [ navMixin ],
    }
</script>
