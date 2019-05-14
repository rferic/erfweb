<template>
    <div>
        <notifications
            group="notify"
            position="top right"
        />
        <b-modal
            ref="modalRedirectionForm"
            scrollable
            size="xl"
            hide-footer
            :title="$t('Redirection form', { locale })"
        >
            <form-redirection ref="redirectionForm" @onCreate="onCreate" @onClose="onCloseForm" />
        </b-modal>
        <b-card>
            <b-row>
                <b-col lg="3" sm="6">
                    <b-nav vertical>
                        <input-text-filter
                            v-model="filters.slug_origin"
                            :placeholder="$t('Search for origin slug...', { locale })"
                            v-debounce:300ms="onChangeOriginFilter"
                        />
                    </b-nav>
                </b-col>
                <b-col lg="3" sm="6">
                    <b-nav vertical>
                        <input-text-filter
                            v-model="filters.slug_destine"
                            :placeholder="$t('Search for destine slug...', { locale })"
                            v-debounce:300ms="onChangeDestineFilter"
                        />
                    </b-nav>
                </b-col>
                <b-col lg="3" sm="6">
                    <b-nav vertical>
                        <select-filter
                            :title="$t('Select a redirection code', { locale })"
                            :options-origin="codes"
                            :value-origin="filters.code"
                            :translate-label="false"
                            v-model="filters.code"
                            @onChangeFilter="onChangeCodeFilter"
                        />
                    </b-nav>
                </b-col>
                <b-col lg="3" sm="6" class="text-right">
                    <b-button
                        variant="success"
                        size="sm"
                        @click="onOpenForm"
                    >
                        <i class="fa fa-plus" />
                        {{ $t('Create redirection', { locale }) }}
                    </b-button>
                </b-col>
                <b-col cols="12">
                    <div v-if="hasRedirections || isBusy" class="mt-2">
                        <b-table
                            id="redirections"
                            ref="table"
                            responsive
                            :fields="columns"
                            :items="redirections"
                            :busy="isBusy"
                            table-class="table align-items-center table-flush light"
                            thead-class="thead-light"
                            tbody-classes="list"
                        >
                            <div
                                slot="table-busy"
                                class="text-center text-primary my-2"
                            >
                                <b-spinner class="align-middle"></b-spinner>
                                <strong>{{ $t('Loading', { locale }) }}...</strong>
                            </div>
                            <template
                                slot="actions"
                                slot-scope="data"
                            >
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
                        v-if="hasRedirections"
                        class="text-right"
                    >
                        <em>{{ redirections.length }} / {{ totalRedirections }}</em>
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
                    <b-alert :show="!hasRedirections" variant="warning">
                        <i class="fa fa-warning" />
                        {{ $t('Redirections not found', { locale }) }}
                    </b-alert>
                </b-col>
            </b-row>
        </b-card>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import InputTextFilter from './../Filters/InputText'
    import SelectFilter from './../Filters/Select'
    import cloneMixin from './../../../includes/mixins/clone'
    import redirectionMixin from './../../mixins/redirection'
    import paginatorMixin from './../../mixins/paginator'
    import FormRedirection from "./Form";

    export default {
        name: 'IndexRedirection',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: {FormRedirection, InputTextFilter, SelectFilter },
        mixins: [ cloneMixin, redirectionMixin, paginatorMixin ],
        data () {
            return {
                isLoaded: false,
                isBusy: false,
                currentRedirection: null,
                filters: {
                    slug_origin: '',
                    slug_destine: '',
                    code: ''
                },
                columns: [
                    {
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'code',
                        label: this.$t('Code', this.locale)
                    },
                    {
                        key: 'slug_origin',
                        label: this.$t('Origin', this.locale)
                    },
                    {
                        key: 'slug_destine',
                        label: this.$t('Destine', this.locale)
                    },
                    {
                        key: 'created_at',
                        label: this.$t('Creation date', this.locale)
                    },
                    {
                        key: 'actions',
                        label: ''
                    }
                ]
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            hasRedirections () {
                return this.redirections.length > 0
            }
        },
        methods: {
            // Events
            onChangeCodeFilter ( code ) {
                this.filters.code = code
                this.refresh()
            },
            onChangeOriginFilter ( slug_origin ) {
                this.filters.slug_origin = slug_origin
                this.refresh()
            },
            onChangeDestineFilter ( slug_destine ) {
                this.filters.slug_destine = slug_destine
                this.refresh()
            },
            async onDestroy ( redirection ) {
                await this.destroyRedirectionRequest(redirection)
                this.refresh()
                this.$notify({
                    group: 'notify',
                    title: this.$t('Destroy redirection', { locale: this.locale }),
                    text: this.$t('Redirection has been destroyed', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            onOpenForm () {
                this.$refs.modalRedirectionForm.show()
                this.$refs.redirectionForm.clearData()
            },
            onCloseForm () {
                this.$refs.modalRedirectionForm.hide()
            },
            onCreate () {
                this.refresh()
                this.$notify({
                    group: 'notify',
                    title: this.$t('Create redirection', { locale: this.locale }),
                    text: this.$t('Redirection has been created', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
                this.onCloseForm()
            },
            // Actions
            async refresh () {
                this.redirections = []
                this.isBusy = true

                await this.loadPage({
                    page: this.page,
                    perPage: this.perPage
                })

                this.isBusy = false
            },
            async loadPage ({ page, perPage, url }) {
                const { current_page, total, next_page_url } = await this.getRedirections({
                    page,
                    perPage,
                    url,
                    filters: this.filters
                })

                this.currentPage = current_page
                this.totalRedirections = total
                this.urlNextPage = next_page_url
                this.setPerPage(perPage)
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
            // Getters
            async getRedirections ({ page, perPage, url, filters }) {
                const { data, current_page, total, next_page_url } = await this.getRedirectionsRequest({
                    page,
                    perPage,
                    url,
                    filters
                })

                for ( const redirection of data ) {
                    this.redirections.push(redirection)
                }

                return { current_page, total, next_page_url }
            },
            // API Request
            async getRedirectionsRequest ({ page, perPage, url, filters }) {
                if ( typeof page === typeof page ) {
                    page = null
                }

                if ( typeof perPage === typeof undefined ) {
                    perPage = null
                }

                if ( typeof url === typeof undefined ) {
                    url = page !== null ? `${this.routes.getRedirections}?page=${page}` : this.routes.getRedirections
                }

                const { data } = await this.axios.post(url, {
                    perPage,
                    filters
                })
                return data
            }
        },
        mounted () {
            this.refresh()
        }
    }
</script>
