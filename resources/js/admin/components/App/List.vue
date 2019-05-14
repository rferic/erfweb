<template>
    <transition name="bounceRight">
        <v-wait for="loader">
            <notifications
                group="notify"
                position="top right"
            />
            <template slot="waiting">
                <div>
                    {{ $t('Loading...', locale) }}
                </div>
            </template>
            <div class="dropdown-divider" />
            <b-alert
                :show="!hasApps && !isBusy"
                variant="warning"
            >
                {{ $t('Apps not found', { locale: this.locale }) }}
            </b-alert>
            <sweet-modal ref="confirmDestroy">
                <h2>{{ $t('Confirm destroy app', { locale }) }}</h2>
                <p>{{ $t('Are you sure to destroy the app? You will not be able to restore it.', { locale }) }}</p>
                <b-button
                    size="sm"
                    class="mt-4"
                    variant="danger"
                    @click="destroy"
                >
                    {{ $t('Confirm destroy app', { locale }) }}
                </b-button>
            </sweet-modal>
            <sweet-modal ref="confirmDestroySelected">
                <h2>{{ $t('Confirm destroy selected apps', { locale }) }}</h2>
                <p>{{ $t('Are you sure to destroy the selected apps? You will not be able to restore it.', { locale }) }}</p>
                <b-button
                    size="sm"
                    class="mt-4"
                    variant="danger"
                    @click="destroySelected"
                >
                    {{ $t('Confirm destroy selected apps', { locale }) }}
                </b-button>
            </sweet-modal>
            <div v-if="hasApps || isBusy">
                <div class="mb-2">
                    <div class="pull-left">
                        <b-form-checkbox
                            v-model="checkAll"
                            @input="onToggleCheckAll"
                        />
                        <b-button
                            size="sm"
                            variant="danger"
                            @click="$refs.confirmDestroySelected.open()"
                            :disabled="!hasAppsSelected"
                        >
                            <i class="fa fa-trash" /> {{ $t('Delete selected apps', { locale }) }}
                        </b-button>
                    </div>
                    <div class="text-right pull-right">
                        <b-button
                            size="sm"
                            variant="success"
                            @click.prevent="$emit('onGoToCreateApp')"
                        >
                            <i class="fa fa-plus" /> {{ $t('Create new', { locale }) }}
                        </b-button>
                        <b-form-select
                            class="w-auto"
                            size="sm"
                            v-model="selectPerPage"
                            :options="optionsPerPage"
                            @input="onChangePerPage"
                        />
                    </div>
                    <div class="clearfix" />
                </div>
                <b-table
                    id="apps"
                    ref="table"
                    responsive
                    small
                    hover
                    striped
                    :fields="columns"
                    :items="appsWithCheckedAttrAndDefaultData"
                    :busy="isBusy"
                >
                    <div
                        slot="table-busy"
                        class="text-center text-primary my-2"
                    >
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>{{ $t('Loading', { locale }) }}...</strong>
                    </div>
                    <template
                        slot="check"
                        slot-scope="data"
                    >
                        <b-form-checkbox v-model="data.item.checked" />
                    </template>
                    <template
                        slot="languages"
                        slot-scope="data"
                    >
                        <b-badge
                            v-for="language of data.item.languages"
                            :key="language.iso"
                            :variant="language.has ? 'success' : 'secondary'"
                            class="mr-2"
                        >
                            {{ language.iso }}
                        </b-badge>
                    </template>
                    <template
                        slot="type"
                        slot-scope="data"
                    >
                        <b-badge
                            v-if="data.item.type === 'public'"
                            sm
                            variant="success"
                        >
                            {{ data.item.type }}
                        </b-badge>
                        <b-badge
                            v-if="data.item.type === 'protected'"
                            sm
                            variant="warning"
                        >
                            {{ data.item.type }}
                        </b-badge>
                        <b-badge
                            v-if="data.item.type === 'private'"
                            sm
                            variant="danger"
                        >
                            {{ data.item.type }}
                        </b-badge>
                    </template>
                    <template
                        slot="status"
                        slot-scope="data"
                    >
                        <b-badge
                            v-if="data.item.status === 'success'"
                            sm
                            variant="success"
                        >
                            {{ data.item.status }}
                        </b-badge>
                        <b-badge
                            v-if="data.item.status === 'working'"
                            sm
                            variant="warning"
                        >
                            {{ data.item.status }}
                        </b-badge>
                        <b-badge
                            v-if="data.item.status === 'error'"
                            sm
                            variant="danger"
                        >
                            {{ data.item.status }}
                        </b-badge>
                    </template>
                    <template
                        slot="actions"
                        slot-scope="data"
                    >
                        <a
                            href="#"
                            class="mr-2"
                            @click.prevent="$emit('onGoToApp', data.item)"
                        >
                            <i class="fa fa-pencil text-primary" />
                        </a>
                        <a
                            href="#"
                            @click.prevent="onDestroy(data.item)"
                        >
                            <i class="fa fa-trash text-danger" />
                        </a>
                    </template>
                </b-table>
            </div>
            <div
                v-if="hasApps"
                class="text-right"
            >
                <em>{{ apps.length }} / {{ totalApps }}</em>
            </div>
            <div id="viewMore">
                <b-button
                    v-if="hasNextPage"
                    block
                    variant="primary"
                    @click="loadNextPage"
                >
                    {{ $t('View more', { locale: this.locale }) }}
                </b-button>
            </div>
        </v-wait>
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../../includes/mixins/clone'
    import paginatorMixin from './../../mixins/paginator'
    import appMixin from './../../mixins/app'

    export default {
        name: 'ListApp',
        props: {
            data: {
                type: String,
                required: true
            },
            filters: {
                type: Object,
                required: false,
                default: {}
            }
        },
        mixins: [ cloneMixin, paginatorMixin, appMixin ],
        data () {
            return {
                languagesAvailable: JSON.parse(this.data).langsAvailable,
                stackPages: true,
                columns: [
                    {
                        key: 'check',
                        label: ''
                    },
                    {
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'title',
                        label: this.$t('Title', this.locale)
                    },
                    {
                        key: 'vue_component',
                        label: this.$t('Component', this.locale)
                    },
                    {
                        key: 'version',
                        label: this.$t('Version', this.locale)
                    },
                    {
                        key: 'type',
                        label: this.$t('Type', this.locale)
                    },
                    {
                        key: 'status',
                        label: this.$t('Status', this.locale)
                    },
                    {
                        key: 'actions',
                        label: ''
                    }
                ],
                isBusy: true,
                selectPerPage: null,
                appToDestroy: null,
                appsWithCheckedAttrAndDefaultData: [],
                checkAll: false
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            hasAppsSelected () {
                return this.appsWithCheckedAttrAndDefaultData.some(app => app.checked)
            },
            isTrashView () {
                return !this.filters.enables
            }
        },
        watch: {
            filters: {
                deep: true,
                handler () {
                    this.page = 1
                    this.refresh()
                }
            }
        },
        methods: {
            // Events
            onToggleCheckAll () {
                this.setAppsCheckAttrAndDefaultData(true)
            },
            onChangePerPage () {
                this.setPerPage(this.selectPerPage)
                this.refresh()
            },
            onDestroy ( app ) {
                this.appToDestroy = app
                this.$refs.confirmDestroy.open()
            },
            // Actions
            async refreshList () {
                this.apps = []
                this.isBusy = true

                for ( let page = 1; page <= this.currentPage; page++ ) {
                    await this.loadPage({
                        page: page,
                        perPage: this.perPage
                    })
                }

                this.isBusy = false
            },
            async refresh () {
                this.apps = []
                this.isBusy = true

                await this.loadPage({
                    page: this.page,
                    perPage: this.perPage
                })

                this.isBusy = false
            },
            async loadNextPage () {
                this.isBusy = true

                await this.loadPage({
                    page: this.currentPage,
                    perPage: this.perPage,
                    url: this.urlNextPage
                })

                this.isBusy = false
                this.$scrollTo(`#viewMore`, 1000, {
                    easing: 'ease-in',
                    offset: 1000
                })
            },
            async destroy () {
                await this.destroyAppRequest(this.appToDestroy)
                await this.refreshList()

                this.$refs.confirmDestroy.close()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Destroy app', { locale: this.locale }),
                    text: this.$t('App has been destroy', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            async destroySelected () {
                for ( const app of this.appsWithCheckedAttrAndDefaultData ) {
                    if ( app.checked ) {
                        await this.destroyAppRequest(app)
                    }
                }

                await this.refreshList()
                this.$refs.confirmDestroySelected.close()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Destroy selected apps', { locale: this.locale }),
                    text: this.$t('Selected apps has been destroyed', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            // Setters
            setAppsCheckAttrAndDefaultData ( force ) {
                let appsWithCheckedAttrAndDefaultData = []
                let localeDefault, item, languages, hasLanguage

                for ( let app of this.apps ) {
                    localeDefault = null
                    languages = []

                    for ( const languageAvailable of this.languagesAvailable ) {
                        hasLanguage = false

                        for ( const locale of app.locales ) {
                            if ( locale.lang === languageAvailable.iso ) {
                                hasLanguage = true
                            }
                        }

                        languages.push({
                            iso: languageAvailable.iso,
                            has: hasLanguage
                        })
                    }

                    for ( const locale of app.locales ) {
                        if ( localeDefault === null || locale.lang === this.locale ) {
                            localeDefault = locale
                        }
                    }

                    item = {
                        id: app.id,
                        title: localeDefault !== null ? localeDefault.title : '',
                        description: localeDefault !== null ? localeDefault.description : '',
                        version: app.version,
                        type: app.type,
                        status: app.status,
                        vue_component: app.vue_component,
                        locales: app.locales,
                        images: app.images,
                        languages,
                        checked: force || typeof app.checked === typeof undefined ? this.checkAll : app.checked
                    }

                    appsWithCheckedAttrAndDefaultData.push(item)
                }

                this.appsWithCheckedAttrAndDefaultData = this.clone(appsWithCheckedAttrAndDefaultData)
            }
        },
        async mounted () {
            this.$wait.start('loader')
            this.selectPerPage = this.perPage
            await this.refresh()
            this.$wait.end('loader')
        }
    }
</script>

<style scoped>
    .custom-checkbox {
        display: inline-block;
    }
</style>
