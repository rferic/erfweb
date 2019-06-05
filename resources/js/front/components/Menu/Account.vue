<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        :nudge-width="200"
        offset-x
    >
        <template v-slot:activator="{ on }">
            <v-btn flat icon dark color="white" v-on="on">
                <v-icon>mdi-account-circle</v-icon>
            </v-btn>
        </template>

        <v-card>
            <v-list v-if="isLogged">
                <v-list-tile avatar>
                    <v-list-tile-avatar>
                        <img :src="avatar" :alt="auth.name">
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title>{{ auth.name }}</v-list-tile-title>
                        <v-list-tile-sub-title>{{ auth.email }}</v-list-tile-sub-title>
                        <v-list-tile-sub-title>{{ `${$vuetify.t('Language')} ${$vuetify.t(auth.language.name)}` }}</v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
            <v-list v-else>
                <v-list-tile avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ $vuetify.t('Who are you?') }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

            <v-divider />

            <v-card-actions>
                <v-spacer v-if="!loader"></v-spacer>
                <v-btn v-if="isLogged && !loader" color="yellow darken-3" flat tag="a" :href="routesGlobal.adminDashboard" target="_blank">
                    <v-icon light small class="pr-1">mdi-shield-account</v-icon>
                    {{ $vuetify.t('Admin panel') }}
                </v-btn>
                <v-btn v-if="isLogged && !loader" color="blue" flat tag="a" :href="routesGlobal.account">
                    <v-icon light small class="pr-1">mdi-account-edit</v-icon>
                    {{ $vuetify.t('Profile') }}
                </v-btn>
                <v-btn v-if="isLogged && !loader" color="red" flat @click="logout">
                    <v-icon light small class="pr-1">mdi-account-off</v-icon>
                    {{ $vuetify.t('Logout') }}
                </v-btn>
                <v-btn v-if="!isLogged && !loader" flat tag="a" @click="openLogin">
                    <v-icon light small class="pr-1">mdi-account-arrow-right</v-icon>
                    {{ $vuetify.t('Login') }}
                </v-btn>
                <v-btn v-if="!isLogged && !loader" flat tag="a" @click="openRegister">
                    <v-icon light small class="pr-1">mdi-account-plus</v-icon>
                    {{ $vuetify.t('Register') }}
                </v-btn>
                <v-progress-linear v-if="loader" :indeterminate="true"/>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
    import { mapState, mapGetters, mapActions } from 'vuex'

    export default {
        name: 'AccountMenu',
        props: {
            authJson: {
                type: String,
                required: false,
                default: null
            }
        },
        data () {
            return {
                menu: false,
                loader: false
            }
        },
        computed: {
            ...mapState([ 'csrfToken', 'routesGlobal' ]),
            ...mapState({
                auth: state => state.auth.user
            }),
            ...mapGetters({
                isLogged: 'auth/isLogged'
            }),
            avatar () {
                return this.isLogged ? `${window.location.origin}/${this.auth.avatar}` : null
            }
        },
        methods: {
            ...mapActions({
                setAuth : 'auth/set',
                openAuthModal: 'auth/open',
                closeAuthModal: 'auth/close',
                changeTab: 'auth/changeTab'
            }),
            async logout () {
                this.loader = true
                await this.axios.post(this.routesGlobal.logout, { _token: this.csrfToken })
                this.menu = false
                this.setAuth(null)
                this.closeAuthModal()
                this.loader = false
            },
            openLogin () {
                this.changeTab('login')
                this.openAuthModal()
            },
            openRegister () {
                this.openAuthModal()
                this.changeTab('register')
            }
        },
        created () {
            this.setAuth(JSON.parse(this.authJson))
        }
    }
</script>
