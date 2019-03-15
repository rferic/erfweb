<template>
    <div>
        <BlockUI v-if="isVisibleBlockui" :message="messageBlockui">
            <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
        </BlockUI>
        <b-row v-if="viewList">
            <b-col
                cols="2"
                xs="12">
                <filter-page
                    :data="data"
                    @onChangeFilters="onChangeFilters"
                />
            </b-col>
            <b-col
                cols="10"
                xs="12">
                <list-page
                    ref="listMessage"
                    :data="data"
                    :filters="filters"
                    @onGoToPage="goToPage"
                />
            </b-col>
        </b-row>
        <b-row v-if="viewForm">
            <form-page
                class="col-12"
                :data="data"
                :page-origin="currentPage"
                @onGoToList="goToList"
            />
        </b-row>
    </div>
</template>

<script>
    import filterPageStructure from './../../structures/filterPage'
    import cloneMixin from './../../mixins/clone'
    import FilterPage from './Filter'
    import ListPage from './List'
    import FormPage from './Form'
    import { mapState } from 'vuex'

    export default {
        name: 'IndexPage',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { FilterPage, ListPage, FormPage },
        mixins: [ cloneMixin ],
        data () {
            return {
                filters: this.clone(filterPageStructure),
                currentPage: null,
                isLoaded: false
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
            }
        },
        mixins: [ cloneMixin ],
        methods: {
            onChangeFilters ( filters ) {
                this.filters = filters
            },
            goToPage ( page ) {
                this.currentPage = page
            },
            goToList () {
                this.currentPage = null
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
