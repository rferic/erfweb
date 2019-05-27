<template>
    <v-layout v-if="hasApps" row wrap>
        <v-flex v-for="app in apps" :key="app.id" :class="classColumnsSizes" class="pa-3">
            <v-hover>
                <v-card>
                    <v-img :src="getFirstAppImage(app).src">
                        <v-container
                            fill-height
                            fluid
                            pa-2
                        >
                            <v-layout fill-height>
                                <v-flex xs12 align-end flexbox>
                                    <span class="headline white--text" v-text="getAppLocale(app).title"></span>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-img>
                    <v-card-actions>
                        <v-chip label :color="getTypeOptions(app.type).color" text-color="white">
                            <v-icon left>{{ getTypeOptions(app.type).icon }}</v-icon>
                            {{ app.type }}
                        </v-chip>
                        <v-spacer></v-spacer>
                        <v-chip label color="grey darken-4" text-color="white">
                            {{ `${$vuetify.t('version')} ${app.version}` }}
                        </v-chip>
                    </v-card-actions>
                </v-card>
            </v-hover>
        </v-flex>
    </v-layout>
</template>

<script>
    import appMixin from './../../mixins/app'

    export default {
        name: 'AppGrid',
        props: {
            maxItems: {
                type: Number,
                required: true
            },
            classColumnsSizes: {
                type: String,
                required: false,
                default: 3
            }
        },
        mixins: [ appMixin ],
        data () {
            return {
                apps: [],
                format: 'collection',
                randomOrder: true,
                status: ['success'],
                types: [],
                localeItem: this.getLocaleItem()
            }
        },
        computed: {
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
            },
            getTypeOptions ( type ) {
                if ( type === 'public' ) {
                    return {
                        icon: 'mdi-eye',
                        color: 'success'
                    }
                } else if ( type === 'protected' ) {
                    return {
                        icon: 'mdi-eye-check',
                        color: 'warning'
                    }
                } else if ( type === 'private' ) {
                    return {
                        icon: 'mdi-eye-off',
                        color: 'error'
                    }
                }
            }
        },
        mounted () {
            this.getApps()
        }
    }
</script>
