<template>
    <div>
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
