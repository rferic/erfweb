<template>
    <div class="d-inline-flex">
        <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-width="200"
            offset-x
        >
            <template v-slot:activator="{ on }">
                <v-btn flat dark color="white" v-on="on">
                    {{ currentLocale.code }}
                </v-btn>
            </template>

            <v-card>
                <v-list>
                    <v-list-tile avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ $vuetify.t('Your current language is') }} <strong>{{ $vuetify.t(currentLocale.name) }}</strong>
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>

                <v-divider />

                <v-card-actions>
                    <v-btn v-for="translate in routesGlobal.translates" v-if="translate.locale.code !== locale" :key="translate.locale.iso" flat tag="a" :href="translate.url">
                        {{ translate.locale.name }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-menu>
    </div>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'LanguageMenu',
        data () {
            return {
                menu: false
            }
        },
        computed: {
            ...mapState([ 'locale', 'localesSupported', 'routesGlobal' ]),
            currentLocale () {
                for ( const localeSupported of this.localesSupported ) {
                    if ( localeSupported.code === this.locale ) {
                        return localeSupported
                    }
                }
            }
        }
    }
</script>
