<template>
    <div>
        <BlockUI v-if="isVisibleBlockui" :message="messageBlockui">
            <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
        </BlockUI>
        <b-row v-if="viewList">
            <b-col
                cols="3"
                xs="12">
                <filter-message
                    :data="data"
                    @onChangeFilters="onChangeFilters"
                />
            </b-col>
            <b-col
                cols="9"
                xs="12">
                <list-message
                    ref="listMessage"
                    :data="data"
                    :filters="filters"
                    @onGoToMessage="goToMessage"
                />
            </b-col>
        </b-row>
        <b-row v-if="viewForm">
            <detail-message
                class="col-12"
                :data="data"
                :message-origin="currentMessage"
                @onGoToList="goToList"
            />
        </b-row>
    </div>
</template>

<script>
    import filterMessageStructure from './../../structures/filterMessage'
    import cloneMixin from './../../mixins/clone'
    import FilterMessage from './Filter'
    import ListMessage from './List'
    import DetailMessage from './Detail'
    import { mapState } from 'vuex'

    export default {
        name: 'IndexMessage',
        props: {
          data: {
              type: String,
              required: true
          }
        },
        components: { FilterMessage, ListMessage, DetailMessage },
        mixins: [ cloneMixin ],
        data () {
            return {
                filters: this.clone(filterMessageStructure),
                currentMessage: null,
                isLoaded: false
            }
        },
        computed: {
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            viewList () {
                return this.currentMessage === null && this.isLoaded
            },
            viewForm () {
                return this.currentMessage !== null && this.isLoaded
            }
        },
        methods: {
            onChangeFilters ( filters ) {
                this.filters = filters
            },
            goToMessage ( message ) {
                this.currentMessage = message
            },
            goToList () {
                this.currentMessage = null
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
