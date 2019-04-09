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
                :show="!hasPages && !isBusy"
                variant="warning"
            >
                {{ $t('Pages not found', { locale: this.locale }) }}
            </b-alert>
            <sweet-modal ref="confirmRedirections">
                <h2>{{ $t('Confirm if require create redirections', { locale }) }}</h2>
                <p>{{ $t('Would you like create redirects for any of the following urls?', { locale }) }}</p>
                <hr>
                <redirections-page
                    :page-locales="localesToCreateRedirections"
                    @onConfirmRedirections="onConfirmRedirections"
                />
            </sweet-modal>
            <sweet-modal ref="confirmDestroy">
                <h2>{{ $t('Confirm destroy page', { locale }) }}</h2>
                <p>{{ $t('Are you sure to destroy the page? You will not be able to restore it.', { locale }) }}</p>
                <b-button
                    size="sm"
                    class="mt-4"
                    variant="danger"
                    @click="onDestroy"
                >
                    {{ $t('Confirm destroy page', { locale }) }}
                </b-button>
            </sweet-modal>
            <sweet-modal ref="confirmDestroySelected">
                <h2>{{ $t('Confirm destroy selected pages', { locale }) }}</h2>
                <p>{{ $t('Are you sure to destroy the selected pages? You will not be able to restore it.', { locale }) }}</p>
                <b-button
                    size="sm"
                    class="mt-4"
                    variant="danger"
                    @click="removeSelected"
                >
                    {{ $t('Confirm destroy selected pages', { locale }) }}
                </b-button>
            </sweet-modal>
            <div v-if="hasPages || isBusy">
                <div class="mb-2">
                    <div class="pull-left">
                        <b-form-checkbox
                            v-model="checkAll"
                            @input="onToggleCheckAll"
                        />
                        <b-button
                            size="sm"
                            variant="danger"
                            @click="onRemoveSelected"
                            :disabled="!hasPagesSelected"
                        >
                            <i class="fa fa-trash" /> {{ $t('Delete selected pages', { locale }) }}
                        </b-button>
                        <b-button
                            size="sm"
                            variant="success"
                            @click="onRestoreSelected"
                            :disabled="!hasPagesSelected"
                        >
                            <i class="fa fa-undo" /> {{ $t('Restore selected pages', { locale }) }}
                        </b-button>
                    </div>
                    <div class="text-right pull-right">
                        <b-button
                            size="sm"
                            variant="success"
                            @click.prevent="$emit('onGoToCreatePage')"
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
                    id="pages"
                    ref="table"
                    responsive
                    small
                    hover
                    striped
                    :fields="columns"
                    :items="pagesWithCheckedAttrAndDefaultData"
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
                        slot="author"
                        slot-scope="data"
                    >
                        {{ data.item.author.name }}
                    </template>
                    <template
                        slot="status"
                        slot-scope="data"
                    >
                        <b-badge
                            v-if="data.item.deleted_at === null"
                            sm
                            variant="success"
                        >
                            {{ $t('Enable', { locale }) }}
                        </b-badge>
                        <b-badge
                            v-else
                            sm
                            variant="danger"
                        >
                            {{ $t('Disable', { locale }) }}
                        </b-badge>
                    </template>
                    <template
                        slot="actions"
                        slot-scope="data"
                    >
                        <a
                            href="#"
                            class="mr-2"
                            @click.prevent="$emit('onGoToPage', data.item)"
                        >
                            <i class="fa fa-pencil text-secondary" />
                        </a>
                        <a
                            href="#"
                            @click.prevent="onRemove(data.item)"
                        >
                            <i class="fa fa-trash text-danger" />
                        </a>
                        <a
                            v-if="data.item.deleted_at !== null"
                            href="#"
                            @click.prevent="onRestore(data.item)"
                        >
                            <i class="fa fa-undo text-success" />
                        </a>
                    </template>
                </b-table>
            </div>
            <div
                v-if="hasPages"
                class="text-right"
            >
                <em>{{ pages.length }} / {{ totalPages }}</em>
            </div>
            <b-button
                v-if="hasNextPage"
                block
                variant="primary"
                @click="loadNextPage"
            >
                {{ $t('View more', { locale: this.locale }) }}
            </b-button>
        </v-wait>
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../mixins/clone'
    import paginatorMixin from './../../mixins/paginator'
    import pageMixin from './../../mixins/page'
    import redirectionMixin from './../../mixins/redirection'
    import RedirectionsPage from './Redirections'

    export default {
        name: 'ListPage',
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
        components: { RedirectionsPage },
        mixins: [ cloneMixin, paginatorMixin, pageMixin, redirectionMixin ],
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
                        key: 'slug',
                        label: this.$t('Slug', this.locale)
                    },
                    {
                        key: 'title',
                        label: this.$t('Title', this.locale)
                    },
                    {
                        key: 'layout',
                        label: this.$t('Layout', this.locale)
                    },
                    {
                        key: 'languages',
                        label: this.$t('Languages', this.locale)
                    },
                    {
                        key: 'author',
                        label: this.$t('Languages', this.locale)
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
                pageToDestroy: null,
                pagesWithCheckedAttrAndDefaultData: [],
                checkAll: false,
                localesToCreateRedirections: [],
                redirectionToCollection: false
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            hasPagesSelected () {
                return this.pagesWithCheckedAttrAndDefaultData.some(page => page.checked)
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
                this.setPagesCheckAttrAndDefaultData(true)
            },
            onChangePerPage () {
                this.setPerPage(this.selectPerPage)
                this.refresh()
            },
            async onRemoveSelected () {
                if ( this.pagesWithCheckedAttrAndDefaultData.some( page => page.checked && page.deleted_at !== null ) ) {
                    // Open confirm modal
                    this.$refs.confirmDestroySelected.open()
                } else {
                    this.confirmRedirectionsToPagesSelected()
                }
            },
            async onRemove ( page ) {
                if ( page.deleted_at !== null ) {
                    // Open confirm modal
                    this.pageToDestroy = page
                    this.$refs.confirmDestroy.open()
                } else {
                    this.pageToDestroy = page
                    this.confirmRedirectionsToPages(page)
                }
            },
            async onRestoreSelected () {
                for ( const page of this.pagesWithCheckedAttrAndDefaultData ) {
                    if ( page.checked && page.deleted_at !== null ) {
                        await this.restorePageRequest(page)
                        await this.removeRedirectionToPage(page)
                    }
                }

                await this.refreshList()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Restore selected pages'),
                    text: this.$t('Selected pages has been restored and moved to list', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            async onRestore ( page ) {
                await this.restorePageRequest(page)
                await this.removeRedirectionToPage(page)
                await this.refreshList()

                this.$notify({
                    group: 'notify',
                    title: this.$t('Restore page'),
                    text: this.$t('Page has been restored and moved to list', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            async onConfirmRedirections () {
                this.$refs.confirmRedirections.close()

                if ( this.redirectionToCollection ) {
                    await this.removeSelected()
                } else {
                    let currentPage = null

                    this.pages.some(page => {
                        const scapeCondition = page.id === this.localesToCreateRedirections[0].id

                        if ( scapeCondition ) {
                            currentPage = page
                        }

                        return scapeCondition
                    })

                    if ( currentPage !== null ) {
                        await this.remove(currentPage)
                    }
                }
            },
            async onDestroy () {
                await this.remove(this.pageToDestroy)
                this.$refs.confirmDestroy.close()
            },
            // Actions
            async refreshList () {
                this.pages = []
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
                this.pages = []
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
            },
            async confirmRedirectionsToPages ( page ) {
                this.localesToCreateRedirections = []
                this.redirectionToCollection = false

                if ( page.locales.length > 0 && page.deleted_at === null ) {
                    this.localesToCreateRedirections = page.locales
                }

                if ( this.localesToCreateRedirections.length > 0 ) {
                    this.$refs.confirmRedirections.open()
                } else {
                    this.remove(page)
                }
            },
            confirmRedirectionsToPagesSelected () {
                this.localesToCreateRedirections = []
                this.redirectionToCollection = true

                for ( const page of this.pagesWithCheckedAttrAndDefaultData ) {
                    if ( page.deleted_at === null && page.checked ) {
                        this.localesToCreateRedirections = this.localesToCreateRedirections.concat(page.locales)
                    }
                }

                if ( this.localesToCreateRedirections.length > 0 ) {
                    this.$refs.confirmRedirections.open()
                } else {
                    this.removeSelected()
                }
            },
            async remove ( page ) {
                const toDestroy = page.deleted_at !== null

                if ( !toDestroy ) {
                    await this.removePageRequest(page)
                } else {
                    await this.destroyPageRequest(page)
                }

                await this.refreshList()

                this.$notify({
                    group: 'notify',
                    title: this.$t(toDestroy ? 'Destroy page' : 'Delete page'),
                    text: this.$t(toDestroy ? 'Page has been destroy' : 'Page has been deleted and moved to trash', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            async removeSelected () {
                let anyRemove = false
                let anyDestroy = false
                let title, text

                for ( const page of this.pagesWithCheckedAttrAndDefaultData ) {
                    if ( page.checked ) {
                        if ( page.deleted_at === null ) {
                            anyRemove = true
                            await this.removePageRequest(page)
                        } else {
                            anyDestroy = true
                            await this.destroyPageRequest(page)
                        }
                    }
                }

                await this.refreshList()
                this.$refs.confirmDestroySelected.close()

                if ( anyRemove || anyDestroy ) {
                    if ( anyRemove && anyDestroy ) {
                        title = this.$t('Delete and destroy pages', { locale: this.locale })
                        text = this.$t('Any pages has been deleted and moved to trash and others selected pages has been destroyed ', { locale: this.locale })
                    } else if ( anyRemove ) {
                        title = this.$t('Delete selected pages', { locale: this.locale })
                        text = this.$t('Selected pages has been deleted and moved to trash', { locale: this.locale })
                    } else {
                        title = this.$t('Destroy selected pages', { locale: this.locale })
                        text = this.$t('Selected pages has been destroyed', { locale: this.locale })
                    }

                    this.$notify({
                        group: 'notify',
                        title,
                        text,
                        type: 'success',
                        config: {
                            closeOnClick: true
                        }
                    })
                }
            },
            async destroy ( page ) {
                await this.destroyPageRequest(page)
                await this.refreshList()
                this.$refs.confirmDestroy.close()
                this.$notify({
                    group: 'notify',
                    title: this.$t('Destroy page', { locale: this.locale }),
                    text: this.$t('Page has been destroyed', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            // Setters
            setPagesCheckAttrAndDefaultData ( force ) {
                let pagesWithCheckedAttrAndDefaultData = []
                let localeDefault, item, languages, hasLanguage

                for ( let page of this.pages ) {
                    localeDefault = null
                    languages = []

                    for ( const languageAvailable of this.languagesAvailable ) {
                        hasLanguage = false

                        for ( const locale of page.locales ) {
                            if ( locale.lang === languageAvailable.iso ) {
                                hasLanguage = true
                            }
                        }

                        languages.push({
                            iso: languageAvailable.iso,
                            has: hasLanguage
                        })
                    }

                    for ( const locale of page.locales ) {
                        if ( localeDefault === null || locale.lang === this.locale ) {
                            localeDefault = locale
                        }
                    }

                    item = {
                        id: page.id,
                        slug: localeDefault !== null ? localeDefault.slug : '',
                        title: localeDefault !== null ? localeDefault.title : '',
                        description: localeDefault !== null ? localeDefault.description : '',
                        layout: localeDefault !== null ? localeDefault.layout : '',
                        author: page.author,
                        deleted_at: page.deleted_at,
                        locales: page.locales,
                        contents: page.contents,
                        languages,
                        checked: force || typeof page.checked === typeof undefined ? this.checkAll : page.checked
                    }

                    pagesWithCheckedAttrAndDefaultData.push(item)
                }

                this.pagesWithCheckedAttrAndDefaultData = this.clone(pagesWithCheckedAttrAndDefaultData)
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
