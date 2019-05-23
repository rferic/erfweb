<template>
    <v-layout row wrap>
        <v-flex v-for="skill in skills" :key="skill.title" xs6 sm4 md4 lg3>
            <v-hover>
                <v-card class="mb-4 ml-2 mr-2" slot-scope="{ hover }" :class="`elevation-${hover ? 12 : 2}`">
                    <a :href="skill.link" target="_blank">
                        <v-layout>
                            <v-flex xs12 sm12 md6 lg6>
                                <v-card-title primary-title>
                                    <div>
                                        <div class="subheading">{{ skill.title }}</div>
                                    </div>
                                </v-card-title>
                            </v-flex>
                            <v-flex xs12 sm12 md6 lg6 class="pa-2">
                                <v-img :src="skill.logo" height="50px" contain/>
                            </v-flex>
                        </v-layout>
                        <v-card-actions class="pa-3">
                            <v-icon color="yellow" small>{{ getRatingClass(skill, 1) }}</v-icon>
                            <v-icon color="yellow" small>{{ getRatingClass(skill, 2) }}</v-icon>
                            <v-icon color="yellow" small>{{ getRatingClass(skill, 3) }}</v-icon>
                            <v-icon color="yellow" small>{{ getRatingClass(skill, 4) }}</v-icon>
                            <v-icon color="yellow" small>{{ getRatingClass(skill, 5) }}</v-icon>
                        </v-card-actions>
                    </a>
                </v-card>
            </v-hover>
        </v-flex>
    </v-layout>
</template>

<script>
    export default {
        name: 'SkillsGrid',
        data () {
            return {
                reviews: 413,
                value: 4.5,
                skills: [
                    {
                        title: 'PHP',
                        link: 'https://www.php.net/',
                        logo: '/images/tools/php.png',
                        rating: 4
                    },
                    {
                        title: 'Laravel',
                        link: 'https://laravel.com/',
                        logo: '/images/tools/laravel.png',
                        rating: 4
                    },
                    {
                        title: 'Javascript',
                        link: 'https://developer.mozilla.org/es/docs/Web/JavaScript',
                        logo: '/images/tools/js.png',
                        rating: 4
                    },
                    {
                        title: 'Vue.js',
                        link: 'https://vuejs.org/',
                        logo: '/images/tools/vue.png',
                        rating: 4
                    },
                    {
                        title: 'CSS',
                        link: 'https://developer.mozilla.org/en-US/docs/Web/CSS',
                        logo: '/images/tools/css.png',
                        rating: 3
                    },
                    {
                        title: 'Sass',
                        link: 'https://sass-lang.com/',
                        logo: '/images/tools/sass.svg',
                        rating: 3
                    },
                    {
                        title: 'GIT',
                        link: 'https://git-scm.com/',
                        logo: '/images/tools/git.png',
                        rating: 3
                    }
                ]
            }
        },
        methods: {
            getRatingClass (item, value) {
                return ( item.rating >= value ) ? 'mdi-star' : 'mdi-star-outline'
            }
        }
    }
</script>

<style scoped>
    a {
        color: inherit;
        text-decoration: none;
    }
</style>
