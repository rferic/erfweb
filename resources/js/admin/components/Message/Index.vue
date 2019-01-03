<template>
    <b-row>
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
                @onToggleCheck="onToggleCheck"
                :filters="filters"
            />
        </b-col>
    </b-row>
</template>

<script>
    import filterMessageStructure from './../../structures/filterMessage'
    import cloneMixin from './../../mixins/clone'
    import FilterMessage from './Filter'
    import ListMessage from './List'

    export default {
        name: 'IndexMessage',
        props: {
          data: {
              type: String,
              required: true
          }
        },
        components: { FilterMessage, ListMessage },
        mixins: [ cloneMixin ],
        data () {
            return {
                messagesIdsSelected: [],
                filters: this.clone(filterMessageStructure)
            }
        },
        methods: {
            onToggleCheck ( message ) {
                if ( message.checked && !this.messagesIdsSelected.includes(message.id) ) {
                    this.messagesIdsSelected.push(message.id)
                } else if ( !message.checked && this.messagesIdsSelected.includes(message.id) ) {
                    this.messagesIdsSelected.splice(this.messagesIdsSelected.indexOf(message.id), 1)
                }
            },
            onChangeFilters ( filters ) {
                this.filters = filters
            }
        },
        created () {
            this.filters.onlyTrashed = JSON.parse(this.data).onlyTrashed
        }
    }
</script>
