<template>
    <li
        v-if=""
        class="nav-item dropdown background-avatar"
        :style="`background-image: url(${avatar})`"
    >
        <a class="nav-link dropdown-toggle text-white text-center" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
    import profileMixin from '../../mixins/profile'

    export default {
        name: 'NavAccount',
        mixins: [ profileMixin ],
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
            },
            avatar () {
                return this.auth !== null ? this.auth.avatar : ''
            }
        },
        methods: {
            ...mapActions({
                setAuth : 'auth/set'
            }),
            logout () {
                this.$refs.logoutForm.submit()
            }
        },
        async mounted () {
            this.setAuth(await this.getDataProfileRequest())
        }
    }
</script>

<style scoped>
    .background-avatar {
        background-size: cover !important;
        background-position: center !important;
        background-color: transparent !important;
        background-repeat: no-repeat !important;
        border-radius: 100%;
        width: 50px;
        height: 50px;
    }

    .background-avatar:hover {
        opacity: .8;
    }

    .background-avatar > a.nav-link {
        color: white;
        text-shadow: 0px 0px 8px #1f2f3d;
    }
</style>
