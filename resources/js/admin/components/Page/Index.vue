<template>
    <div>
        <BlockUI v-if="isVisibleBlockui" :message="messageBlockui">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
        </BlockUI>
        <notifications
            group="notify"
            position="top right"
        />
        <b-row v-if="viewList">
            <b-col
                lg="3"
                md="12">
                <b-card>
                    <filter-page
                        :data="data"
                        @onChangeFilters="onChangeFilters"
                    />
                </b-card>
            </b-col>
            <b-col
                lg="9"
                md="12">
                <b-card>
                    <list-page
                        ref="listPages"
                        :data="data"
                        :filters="filters"
                        :pages-parent-origin="pagesParent"
                        :page-paginator-default="pagePaginatorDefault"
                        @onGoToPage="goToPage"
                        @onGoToCreatePage="goToCreatePage"
                        @onChangePagePaginator="onChangePagePaginator"
                    />
                </b-card>
            </b-col>
        </b-row>
        <b-row v-if="viewForm">
            <form-page
                class="col-12"
                :data="data"
                :page-origin="currentPage"
                :pages-parent-origin="pagesParent"
                @onGoToList="goToList"
                @onSavePage="onSavePage"
            />
        </b-row>
    </div>
</template>

<script>
    import filterPageStructure from './../../structures/filterPage'
    import cloneMixin from './../../mixins/clone'
    import pageMixin from './../../mixins/page'
    import FilterPage from './Filter'
    import ListPage from './List'
    import FormPage from './Form'
    import { mapState } from 'vuex'

    export default {
        name: 'IndexPage',
        props: {
            data: {
                type: String,
                required: true,
            }
        },
        components: { FilterPage, ListPage, FormPage },
        mixins: [ cloneMixin, pageMixin ],
        data () {
            return {
                filters: this.clone(filterPageStructure),
                currentPage: null,
                isLoaded: false,
                languagesAvailable: JSON.parse(this.data).langsAvailable,
                pagePaginatorDefault: 1
            }
        },
        computed: {
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            viewList () {
                return this.currentPage === null && this.isLoaded
            },
            viewForm () {
                return this.currentPage !== null && this.isLoaded
            },
            hasPagesParent () {
                return this.pagesParent.length > 0
            }
        },
        methods: {
            // Events
            onChangeFilters ( filters ) {
                this.filters = filters
            },
            onSavePage ({ isNew }) {
                this.$notify({
                    group: 'notify',
                    title: this.$t('Save page', { locale: this.locale }),
                    text: this.$t(isNew ? 'Page has been created' : 'Page has been updated', { locale: this.locale }),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })

                this.goToList()
            },
            goToPage ( page ) {
                this.currentPage = page
            },
            goToCreatePage () {
                this.currentPage = this.getNewPage()
            },
            goToList () {
                this.currentPage = null
            },
            onChangePagePaginator ( pagePaginator ) {
              this.pagePaginatorDefault = pagePaginator
            },
            // Getters
            getNewPage () {
                let languages = []

                for ( const languageAvailable of this.languagesAvailable ) {
                    languages.push({
                        iso: languageAvailable.iso,
                        has: false
                    })
                }

                return {
                    id: null,
                    page_id: '',
                    locales: [],
                    languages
                }
            }
        },
        created () {
            this.getAllPagesParent()
            this.filters.onlyTrashed = JSON.parse(this.data).onlyTrashed
        },
        mounted () {
            this.isLoaded = true
        }
    }
</script>
