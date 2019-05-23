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
                <v-btn class="grey darken-4 mt-5" dark large :href="routesGlobal.technologies">
                    {{ $vuetify.t('View all tools') }}
                </v-btn>
            </template>
        </parallax>

        <section class="mt-5">
        </section>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import Parallax from './../components/Parallax'

    export default {
        name: 'HomeLayout',
        components: { Parallax },
        computed: {
            ...mapState(['routesGlobal'])
        }
    }
</script>
