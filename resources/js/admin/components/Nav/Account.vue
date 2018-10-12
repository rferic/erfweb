<template>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-muted " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ERF
        </a>
        <div class="dropdown-menu dropdown-menu-right animated zoomIn">
            <ul class="dropdown-user">
                <li><a href="#"><i class="ti-user"></i> Profile</a></li>
                <li><a href="#"><i class="ti-wallet"></i> Balance</a></li>
                <li><a href="#"><i class="ti-email"></i> Inbox</a></li>
                <li><a href="#"><i class="ti-settings"></i> Setting</a></li>
                <li>
                    <a
                        :href="routesGlobal.logout"
                        @click.prevent="logout">
                        <i class="fa fa-power-off"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </li>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'NavAccount',
        computed: {
            ...mapState([ 'routesGlobal' ])
        }
    }
</script>
