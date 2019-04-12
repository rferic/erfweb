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
                cols="2"
                xs="12">
                <filter-app
                    :data="data"
                    @onChangeFilters="onChangeFilters"
                />
            </b-col>
            <b-col
                cols="10"
                xs="12">
                <list-app
                    ref="listApps"
                    :data="data"
                    :filters="filters"
                    @onGoToPage="goToPage"
                    @onGoToCreatePage="goToCreatePage"
                />
            </b-col>
        </b-row>
        <b-row v-if="viewForm">
            <form-app
                class="col-12"
                :data="data"
                :page-origin="currentPage"
                @onGoToList="goToList"
                @onSavePage="onSavePage"
            />
        </b-row>
    </div>
</template>

<script>
    import filterAppStructure from './../../structures/filterApp'
    import cloneMixin from './../../mixins/clone'
    import FilterApp from './Filter'
    import ListApp from './List'
    import FormApp from './Form'
    import { mapState } from 'vuex'

    export default {
        name: 'IndexApp',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { FilterApp, ListApp, FormApp },
        mixins: [ cloneMixin ],
        data () {
            return {
                filters: this.clone(filterAppStructure),
                currentApp: null,
                isLoaded: false,
                languagesAvailable: JSON.parse(this.data).langsAvailable
            }
        },
        computed: {
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            viewList () {
                return this.currentApp === null && this.isLoaded
            },
            viewForm () {
                return this.currentApp !== null && this.isLoaded
            }
        },
        mixins: [ cloneMixin ],
        methods: {
            onChangeFilters ( filters ) {
                this.filters = filters
            },
            onSavePage ({ isNew }) {
                this.$notify({
                    group: 'notify',
                    title: this.$t('Save app', { locale: this.locale }),
                    text: this.$t(isNew ? 'App has been created' : 'App has been updated', { locale: this.locale }),
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
                this.currentPage = this.getNewApp()
            },
            goToList () {
                this.currentPage = null
            },
            getNewApp () {
                let languages = []

                for ( const languageAvailable of this.languagesAvailable ) {
                    languages.push({
                        iso: languageAvailable.iso,
                        has: false
                    })
                }

                return {
                    id: null,
                    locales: [],
                    languages
                }
            }
        },
        created () {
            this.filters.onlyTrashed = JSON.parse(this.data).onlyTrashed
        },
        mounted () {
            this.isLoaded = true
        }
    }
</script>
