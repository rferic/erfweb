<template>
    <div>
        <section class="mt-5">
            <h2 class="title font-weight-thin text-xs-center mb-5">
                {{ $vuetify.t('Hi! I\'m a') }} <strong>fullstack developer</strong>
                <br>
                {{ $vuetify.t('and I created this website to put into practice what I learned in the world of programming') }}
            </h2>
            <v-content class="pt-3">
                <v-layout row wrap>
                    <v-flex xs10 sm10 md6 lg6 offset-xs1 offset-sm1 offset-md3 offset-lg3>
                        <h2 class="title font-weight-thin text-xs-center mb-4">
                            <strong class="mt-2">{{ $vuetify.t('This is my history') }}:</strong>
                        </h2>
                        <history-timeline />
                    </v-flex>
                </v-layout>
                <v-layout row wrap class="white">
                    <v-flex xs10 sm10 md6 lg6 offset-xs1 offset-sm1 offset-md3 offset-lg3>
                        <v-card flat>
                            <v-card-title primary-title>
                                <h3 class="subheading font-weight-thin text-xs-center">
                                    {{ $vuetify.t('I have experience working with:')}}
                                </h3>
                            </v-card-title>

                            <skills-grid />
                        </v-card>
                    </v-flex>
                </v-layout>
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
    import SkillsGrid from './../components/Skills/Grid'
    import HistoryTimeline from './../components/Timeline/History'
    import ShortContact from './../components/Contact/Short'

    export default {
        name: 'WhoIAmLayout',
        components: { SkillsGrid, HistoryTimeline, ShortContact }
    }
</script>
