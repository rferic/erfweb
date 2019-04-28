<template>
    <nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
        <div class="container-fluid">

            <!--Toggler-->
            <navbar-toggle-button @click.native="showSidebar">
                <span class="navbar-toggler-icon"></span>
            </navbar-toggle-button>
            <a class="navbar-brand" href="/">
                <b>ERF</b>Web
            </a>

            <slot name="mobile-right">
                <ul class="nav align-items-center d-md-none">
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
            </slot>
            <slot></slot>
            <div class="navbar-collapse collapse show" id="sidenav-collapse-main" v-show="$sidebar.showSidebar">

                <div class="navbar-collapse-header d-md-none">
                    <div class="row">
                        <div class="col-6 collapse-brand">
                            <a to="/">
                                <b>ERF</b>Web
                            </a>
                        </div>
                        <div class="col-6 collapse-close">
                            <navbar-toggle-button @click.native="closeSidebar"></navbar-toggle-button>
                        </div>
                    </div>
                </div>

                <ul class="navbar-nav">
                    <slot name="links">
                    </slot>
                </ul>
            </div>
        </div>
    </nav>
</template>
<script>
    import NavbarToggleButton from './../NavbarToggleButton'
    import navMixin from './../../../../js/admin/mixins/nav'

    export default {
        name: 'sidebar',
        components: {
            NavbarToggleButton
        },
        props: {
            autoClose: {
                type: Boolean,
                default: true,
                description:
                    'Whether sidebar should autoclose on mobile when clicking an item'
            }
        },
        mixins: [ navMixin ],
        provide () {
            return {
                autoClose: this.autoClose
            };
        },
        methods: {
            closeSidebar() {
                this.$sidebar.displaySidebar(false)
            },
            showSidebar() {
                this.$sidebar.displaySidebar(true)
            }
        },
        beforeDestroy() {
            if (this.$sidebar.showSidebar) {
                this.$sidebar.showSidebar = false;
            }
        }
    };
</script>
