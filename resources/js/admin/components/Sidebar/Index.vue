<template>
    <div class="left-sidebar">
        <!-- Sidebar scroll-->
        <div class="scroll-sidebar">
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav">
                <ul id="sidebarnavcustom" v-if="menuIsLoaded">
                    <li class="nav-devider"></li>
                    <sidebar-item
                        v-for="(item, index) in menu"
                        :key="index"
                        :item="item"
                    />
                    <!-- Reload menu button -->
                    <li class="item-refresh text-center">
                        <i class="fa fa-refresh btn" @click="forceRefresh" />
                    </li>
                </ul>
            </nav>
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </div>
</template>

<script>
    import { mapState, mapGetters, mapActions } from 'vuex'
    import SidebarItem from './Item'

    export default {
        name: 'SidebarIndex',
        components: { SidebarItem },
        computed: {
            ...mapState([ 'routesGlobal'  ]),
            ...mapState({
                menu: state => state.adminMenu.menu
            }),
            ...mapGetters({
                menuIsLoaded: 'adminMenu/menuIsLoaded'
            }),
        },
        methods: {
            ...mapActions({
                refreshAdminMenu : 'adminMenu/refresh'
            }),
            async forceRefresh () {
                await this.getAdminMenu()
            },
            async getAdminMenu () {
                const { data } = await axios.post(this.routesGlobal.adminMenu, {})
                this.refreshAdminMenu(data.result)
            }
        },
        async created () {
            if ( !this.menuIsLoaded ) {
                await this.getAdminMenu()
            }
        }
    }
</script>

<style scoped>
    .sidebar-nav #sidebarnavcustom li.item-refresh {
        position: absolute;
        bottom: 0;
        width: 100%;
    }
</style>
