<template>
    <v-layout v-if="hasApps" row wrap>
        <v-flex class="xs12">
            <h2 v-if="hasTitle" class="title font-weight-thin text-xs-center mb-4">
                <strong class="mt-2">{{ $vuetify.t(title) }}</strong>
            </h2>
        </v-flex>
        <v-flex v-for="app in appsCalculated" :key="app.id" :class="classColumnsSizes" class="pa-3">
            <v-hover>
                <v-card slot-scope="{ hover }" :class="`elevation-${hover ? 12 : 2}`">
                    <v-img :src="app.images_localization[0].src">
                        <v-container
                            fill-height
                            fluid
                            pa-2
                        >
                            <v-layout fill-height>
                                <v-flex xs12 align-end flexbox>
                                    <h6 class="title white--text" v-text="app.locale.title"></h6>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-img>
                    <v-card-actions>
                        <v-chip v-if="showType" label :color="app.typeOptions.color" text-color="white" small>
                            <v-icon left>{{ app.typeOptions.icon }}</v-icon>
                            {{ app.type }}
                        </v-chip>
                        <v-spacer></v-spacer>
                        <v-chip v-if="showVersion" label color="white" text-color="grey darken-4" small outline>
                            {{ `v. ${app.version}` }}
                        </v-chip>
                        <v-btn v-if="showBtnAccess && isLogged && !app.myRelationship.request" icon small>
                            <v-icon>mdi-key-plus</v-icon>
                        </v-btn>
                        <v-icon v-if="app.myRelationship.request && !app.myRelationship.accepted">mdi-progress-clock</v-icon>
                        <v-btn icon small tag="a" :href="app.pageLocale.url">
                            <v-icon>mdi-share</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-hover>
        </v-flex>
    </v-layout>
</template>

<script>
    import { mapState, mapGetters } from 'vuex'
    import cloneMixin from './../../../includes/mixins/clone'
    import appMixin from './../../mixins/app'

    export default {
        name: 'AppGrid',
        props: {
            apps: {
                type: Array,
                required: false,
                default: Array
            },
            classColumnsSizes: {
                type: String,
                required: false,
                default: 3
            },
            title: {
                type: String,
                required: false,
                default: null
            },
            showType: {
                type: Boolean,
                required: false,
                default: true
            },
            showVersion: {
                type: Boolean,
                required: false,
                default: true
            },
            showBtnAccess: {
                type: Boolean,
                required: false,
                default: true
            }
        },
        mixins: [ cloneMixin, appMixin ],
        computed: {
            ...mapState({
                myApps: state => state.app.myApps
            }),
            ...mapGetters({
                isLogged: 'auth/isLogged'
            }),
            hasApps () {
                return this.apps.length > 0
            },
            hasTitle () {
                return this.title !== null
            },
            appsCalculated () {
                let appsCalculated = []
                let item

                for ( const app of this.apps ) {
                    item = this.clone(app)
                    item.typeOptions = this.getTypeOptions(app)
                    item.myRelationship = {
                        request: this.myApps.some(myApp => myApp.id === app.id),
                        accepted: this.myApps.some(myApp => myApp.id === app.id && myApp.pivot.active === 1)
                    }
                    appsCalculated.push(item)
                }

                return appsCalculated
            }
        },
        methods: {
            getTypeOptions ( app ) {
                const request = this.myApps.some(myApp => myApp.id === app.id)
                const accepted = this.myApps.some(myApp => myApp.id === app.id && myApp.pivot.active === 1)

                if (
                    app.type === 'public' ||
                    ( app.type === 'protected' && request ) ||
                    ( app.type === 'private' && request && accepted )
                ) {
                    return {
                        icon: 'mdi-lock-open',
                        color: 'success'
                    }
                } else if ( app.type === 'private' && request ) {
                    return {
                        icon: 'mdi-lock-clock',
                        color: 'warning'
                    }
                } else {
                    return {
                        icon: 'mdi-lock',
                        color: 'error'
                    }
                }
            }
        },
        created () {
            this.getMyApps()
        }
    }
</script>
