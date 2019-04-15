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
                    @onGoToApp="goToApp"
                    @onGoToCreateApp="goToCreateApp"
                />
            </b-col>
        </b-row>
        <b-row v-if="viewForm">
            <index-form-app
                class="col-12"
                :data="data"
                :app-origin="currentApp"
                @onGoToList="goToList"
                @onSaveApp="onSaveApp"
            />
        </b-row>
    </div>
</template>

<script>
    import filterAppStructure from './../../structures/filterApp'
    import cloneMixin from './../../mixins/clone'
    import FilterApp from './Filter'
    import ListApp from './List'
    import IndexFormApp from './Form/Index'
    import { mapState } from 'vuex'
    import { appStructure, appLocaleStructure } from './../../structures/app'

    export default {
        name: 'IndexApp',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { FilterApp, ListApp, IndexFormApp },
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
            onSaveApp ({ isNew }) {
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
            goToApp ( page ) {
                this.currentApp = page
            },
            goToCreateApp () {
                this.currentApp = this.getNewApp()
            },
            goToList () {
                this.currentApp = null
            },
            getNewApp () {
                let app = appStructure
                let locales = []

                for ( const languageAvailable of this.languagesAvailable ) {
                    locales.push(appLocaleStructure)
                    locales[locales.length - 1].lang = languageAvailable.iso
                }

                app.locales = locales

                return app
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
