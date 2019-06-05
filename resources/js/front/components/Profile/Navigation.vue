<template>
    <v-navigation-drawer
        v-if="$root.isLogged"
        v-model="drawer"
        :mini-variant="mini"
        hide-overlay
        stateless
        light
    >
        <v-toolbar flat class="transparent">
            <v-list class="pa-0">
                <v-list-tile avatar>
                    <v-list-tile-avatar>
                        <img :src="`/${user.avatar}`">
                    </v-list-tile-avatar>
                </v-list-tile>
            </v-list>
        </v-toolbar>

        <v-list class="pt-0" dense>
            <v-divider></v-divider>

            <v-list-tile v-for="item in items" :key="item.view">
                <v-list-tile-action>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn flat icon :disabled="item.view === currentView" @click="$emit('changeView', item.view)">
                                <v-icon>{{ item.icon }}</v-icon>
                            </v-btn>
                        </template>
                        <span>{{ $vuetify.t(item.title) }}</span>
                    </v-tooltip>
                </v-list-tile-action>
            </v-list-tile>

            <v-list-tile v-if="$root.isAdmin">
                <v-list-tile-action>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn flat icon tag="a" :href="routesGlobal.adminDashboard" target="_blank">
                                <v-icon color="yellow darken-3">mdi-shield-account</v-icon>
                            </v-btn>
                        </template>
                        <span>{{ $vuetify.t('Admin panel') }}</span>
                    </v-tooltip>
                </v-list-tile-action>
            </v-list-tile>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../../includes/mixins/clone'

    export default {
        name: 'ProfileNavigation',
        props: {
            currentView: {
                type: String,
                required: true
            }
        },
        mixins: [ cloneMixin ],
        data () {
            return {
                drawer: true,
                items: [
                    { title: 'Profile', icon: 'mdi-face-profile', view: 'profile-form' },
                    { title: 'Messages', icon: 'mdi-forum', view: 'profile-messages' },
                    { title: 'Private apps with access', icon: 'mdi-webpack', view: 'profile-apps' }
                ],
                mini: true,
                right: null
            }
        },
        computed: {
            ...mapState(['routesGlobal']),
            ...mapState({
                user: state => state.auth.user
            })
        }
    }
</script>
