<template>
    <side-bar
        :background-color="sidebarBackground"
        short-title="Argon"
        title="Argon"
    >
        <template slot="links">
            <sidebar-item
                v-for="item in menu"
                :key="item.key"
                :link="item"
                :sidebar-background="sidebarBackground"
            />
        </template>
    </side-bar>
</template>

<script>
    import { mapState, mapGetters, mapActions } from 'vuex'

    export default {
        name: 'SidebarIndex',
        data () {
            return {
                sidebarBackground: 'vue', //vue|blue|orange|green|red|primary
            }
        },
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
                const { data } = await this.axios.post(this.routesGlobal.adminMenu, {})
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
