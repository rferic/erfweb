<template>
    <v-footer dark height="auto" color="grey darken-4" class="pl-4 pr-4">
        <v-card flat tile class="grey darken-4 white--text text-xs-center flex">
            <v-card-text class="pb-0">
                <v-btn flat icon color="white" class="mr-0" tag="a" href="https://github.com/rferic/" target="_blank">
                    <v-icon>mdi-github-circle</v-icon>
                </v-btn>
                <v-btn flat icon color="white" class="ml-0" tag="a" href="https://linkedin.com/in/eric-rojas-fuertes-2665502b/" target="_blank">
                    <v-icon>mdi-linkedin-box</v-icon>
                </v-btn>
            </v-card-text>
            <v-card-text class="white--text pt-0">
                <small class="white--text mr-4">{{ $vuetify.t('This website has been created by ') }}<strong>EricRF</strong></small>
                <span>&copy; {{ yearText }} <strong>ERF</strong>Web</span>
            </v-card-text>
        </v-card>
    </v-footer>
</template>

<script>
    export default {
        name: 'IndexFooter',
        data () {
            return {
                years: {
                    init: 2019,
                    current: parseInt(Vue.moment().locale('es').format('YYYY'))
                }
            }
        },
        computed: {
            yearText () {
                return this.years.init < this.years.current
                    ? `${this.years.init} - ${this.years.current}`
                    : `${this.years.init}`
            }
        }
    }
</script>
