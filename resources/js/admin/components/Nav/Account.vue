<template>
    <base-dropdown class="nav-link pr-0" tag="li">
        <div class="media align-items-center" slot="title">
            <span class="avatar avatar-sm rounded-circle">
              <img alt="Image placeholder" :src="avatar">
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
                <span class="mb-0 text-sm  font-weight-bold">{{ acronym }}</span>
            </div>
        </div>
        <form
            ref="logoutForm"
            :action="routesGlobal.logout"
            method="POST"
            style="display: none;">
            <input
                type="hidden"
                name="_token"
                :value="csrfToken" />
        </form>

        <template>
            <div class=" dropdown-header noti-title">
                <h6 class="text-overflow m-0">{{ $t('Welcome!', { locale }) }}</h6>
            </div>
            <div class="dropdown-divider"></div>
            <a :href="routesGlobal.profile.index" class="dropdown-item">
                <i class="ni ni-single-02"></i>
                <span>{{ $t('Profile', locale) }}</span>
            </a>
            <a href="#" @click="logout" class="dropdown-item">
                <i class="ni ni-user-run"></i>
                <span>{{ $t('Logout', { locale }) }}</span>
            </a>
        </template>
    </base-dropdown>
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
