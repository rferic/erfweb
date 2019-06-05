<template>
    <div>
        <parallax image="/images/pictures/home.jpeg">
            <template slot="parallax-header">
                <h1 class="display-2 font-weight-thin mb-3">
                    <<strong>ERF</strong>Web />
                </h1>
                <h4 class="subheading text-xs-center">
                    {{ $vuetify.t('Welcome! This a personal website where to show my ideas')}}
                </h4>
            </template>
            <template slot="parallax-container">
                <h3 class="white--text mt-5 headline text-xs-center">
                    {{ $vuetify.t('Tools used:') }}
                </h3>
                <div class="mt-3">
                    <a href="https://laravel.com/" target="_blank" class="mr-4">
                        <img src="/images/tools/laravel.png" alt="Laravel" width="50">
                    </a>
                    <a href="https://vuejs.org/" target="_blank">
                        <img src="/images/tools/vue.png" alt="Vue.js" width="50">
                    </a>
                </div>
                <v-btn class="grey darken-4 mt-5" dark large :href="routesGlobal.whoIAm">
                    {{ $vuetify.t('Who I am?') }}
                    <v-icon class="ml-2">mdi-face-profile</v-icon>
                </v-btn>
            </template>
        </parallax>

        <section class="white">
            <v-content class="pt-3 pb-3">
                <app-grid title="Discover my apps" :apps="apps" class-columns-sizes="xs6 sm6 md3 lg3" />
            </v-content>
        </section>
        <section>
            <v-content class="pt-3 pb-3">
                <v-layout row wrap>
                    <v-flex xs10 sm10 md6 lg6 offset-xs1 offset-sm1 offset-md3 offset-lg3>
                        <h2 class="title font-weight-thin text-xs-center mt-5 mb-4">
                            <strong class="mt-2">{{ $vuetify.t('You want to tell me something?') }}</strong>
                        </h2>
                        <short-contact />
                    </v-flex>
                </v-layout>
            </v-content>
        </section>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import Parallax from './../components/Parallax'
    import AppGrid from './../components/App/Grid'
    import ShortContact from './../components/Contact/Short'
    import appMixin from './../mixins/app'

    export default {
        name: 'HomeLayout',
        components: { Parallax, AppGrid, ShortContact },
        mixins: [ appMixin ],
        data () {
            return {
                apps: [],
                format: 'collection',
                randomOrder: true,
                status: ['success'],
                types: [],
                maxItems: 8
            }
        },
        computed: {
            ...mapState(['routesGlobal']),
            hasApps () {
                return this.apps.length > 0
            }
        },
        methods: {
            async getApps () {
                this.apps = await this.getAppsRequest({
                    format: 'collection',
                    randomOrder: true,
                    maxItems: this.maxItems,
                    status: this.status,
                    types: this.types
                })
            }
        },
        created () {
            this.getApps()
        }
    }
</script>
