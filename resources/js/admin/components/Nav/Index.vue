<template>
    <b-navbar toggleable="md" class="top-navbar">
        <div class="navbar-header">
            <a class="navbar-brand" :href="routesGlobal.dashboard">
                <b>ERF</b>W
                <span>Admin</span>
            </a>
        </div>
        <div class="navbar-collapse">
            <ul class="navbar-nav mr-auto mt-md-0">
                <li class="nav-item">
                    <a class="nav-link nav-toggler hidden-md-up text-muted" href="javascript:void(0)">
                        <i class="mdi mdi-menu"></i>
                    </a>
                </li>
                <li class="nav-item m-l-10">
                    <a class="nav-link sidebartoggler hidden-sm-down text-muted" href="javascript:void(0)">
                        <i class="ti-menu"></i>
                    </a>
                </li>
            </ul>
            <nav-right />
        </div>
    </b-navbar>
</template>

<script>
    import { mapState } from 'vuex'
    import NavRight from './Right'

    export default {
        name: 'NavIndex',
        components: {
            NavRight
        },
        computed: {
            ...mapState([ 'routesGlobal' ])
        }
    }
</script>
