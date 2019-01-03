<template>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-muted " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ acronym }}
        </a>
        <div class="dropdown-menu dropdown-menu-right animated zoomIn">
            <ul class="dropdown-user">
                <li>
                    <a :href="routesGlobal.profile.index">
                        <i class="ti-user"></i> {{ $t('Profile', locale) }}
                    </a>
                </li>
                <li>
                    <form
                        id="logout-form"
                        ref="logoutForm"
                        :action="routesGlobal.logout"
                        method="POST"
                        style="display: none;">
                        <input
                            type="hidden"
                            name="_token"
                            :value="csrfToken" />
                    </form>
                    <a
                        href="#"
                        @click="logout">
                        <i class="fa fa-power-off"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </li>
</template>

<script>
    import { mapState, mapActions } from 'vuex'

    export default {
        name: 'NavAccount',
        computed: {
            ...mapState([ 'routesGlobal', 'locale', 'csrfToken' ]),
            ...mapState({
                auth: state => state.auth.user
            }),
            acronym () {
                if ( this.auth !== null ) {
                    const acronym = this.auth.name.match(/\b\w/g) || []
                    return ((acronym.shift() || '') + (acronym.pop() || '')).toUpperCase()
                }

                return ''
            }
        },
        methods: {
            ...mapActions({
                setAuth : 'auth/set'
            }),
            async getDataProfileRequest () {
                const response = await axios.post(this.routesGlobal.profile.getData, {})
                return response.data
            },
            logout () {
                this.$refs.logoutForm.submit()
            }
        },
        async mounted () {
            this.setAuth(await this.getDataProfileRequest())
        }
    }
</script>
