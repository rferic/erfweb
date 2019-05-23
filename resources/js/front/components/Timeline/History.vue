<template>
    <v-timeline>
        <v-timeline-item
            v-for="(item, index) in items"
            :key="index"
            color="grey darken-4"
            small
        >
            <template v-slot:opposite>
                <span class="subheading">{{ item.year }}</span>
            </template>
            <v-card class="elevation-2">
                <v-card-title class="headline">{{ $vuetify.t(item.title) }}</v-card-title>
                <v-card-text>{{ $vuetify.t(item.description) }}</v-card-text>
            </v-card>
        </v-timeline-item>
    </v-timeline>
</template>

<script>
    export default {
        name: 'HistoryTimeline',
        data () {
            return {
                items: [
                    {
                        year: '2002',
                        title: 'First contact with programming',
                        description: 'My first contact was in high school using Turbo Pascal. Yeah, with Turbo Pascal...but I started to get interested in this world.'
                    },
                    {
                        year: '2003',
                        title: 'Dreamweaver and the web',
                        description: 'A teacher decided to teach us to create websites, the problem is that it was through Dreamweaver.'
                    },
                    {
                        year: '2007',
                        title: 'Landing to the university',
                        description: 'I started to program seriously in different languages and, above all, I learned to think in code.'
                    },
                    {
                        year: '2009',
                        title: 'First job in the web world',
                        description: 'I\'ve learned HTML, CSS and Javascript to get the job. Once inside I started to relearn.'
                    },
                    {
                        year: '2009',
                        title: 'First job in the web world',
                        description: 'I\'ve learned HTML, CSS and Javascript to get the job. Once inside I started to relearn.'
                    },
                    {
                        year: '2009',
                        title: 'Hi, my name is PHP',
                        description: 'First steps in PHP with previous knowledge of Java programming'
                    },
                    {
                        year: '2012',
                        title: 'PHP and the real web',
                        description: 'First projects within different CMS and native PHP'
                    },
                    {
                        year: '2016',
                        title: 'Welcome to frameworks',
                        description: 'We start with Laravel and Vue courses'
                    },
                    {
                        year: '2017',
                        title: 'Playing with the new tools',
                        description: 'First projects within Laravel and Vue'
                    },
                    {
                        year: '2019',
                        title: 'ERFWeb',
                        description: 'Start my personal project'
                    }
                ]
            }
        }
    }
</script>
