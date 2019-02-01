<template>
    <transition name="bounceRight">
        <div>
            <b-nav vertical>
                <div class="dropdown-divider"></div>
                <input-text-filter-message
                    @onChangeFilter="onChangeTextFilter"
                />
                <div class="dropdown-divider"></div>
                <checkboxes-filter-message
                    :options="statusList"
                    @onChangeFilter="onChangeStatusFilter"
                />
                <checkboxes-filter-message
                    :options="tagsList"
                    @onChangeFilter="onChangeTagsFilter"
                />
            </b-nav>
        </div>
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import filterMessageStructure from './../../structures/filterMessage'
    import cloneMixin from './../../mixins/clone'
    import InputTextFilterMessage from './InputTextFilter'
    import CheckboxesFilterMessage from './CheckboxesFilter'

    export default {
        name: 'FilterMessage',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { InputTextFilterMessage, CheckboxesFilterMessage },
        mixins: [ cloneMixin ],
        data () {
            return {
                statusList: JSON.parse(this.data).statusList,
                tagsList: JSON.parse(this.data).tagsList,
                filters: this.clone(filterMessageStructure)
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        },
        methods: {
            onChangeTextFilter ( filter ) {
                this.filters.text = filter
                this.$emit('onChangeFilters', this.filters)
            },
            onChangeStatusFilter ( filter ) {
                this.filters.status = this.toggleCheckboxFilter(this.filters.status, filter)
                this.$emit('onChangeFilters', this.filters)
            },
            onChangeTagsFilter ( filter ) {
                this.filters.tags = this.toggleCheckboxFilter(this.filters.tags, filter)
                this.$emit('onChangeFilters', this.filters)
            },
            toggleCheckboxFilter ( optionsSelected, filter ) {
                if ( filter.checked ) {
                     if ( !optionsSelected.some(optionSelected => optionSelected === filter.key) ) {
                         optionsSelected.push(filter.key)
                     }
                } else {
                    let indexOption = null

                    optionsSelected.some((optionSelected, index) => {
                        const scapeCondition = optionSelected === filter.key

                        if ( scapeCondition ) {
                            indexOption = index
                        }

                        return scapeCondition
                    })

                    if ( indexOption !== null ) {
                        optionsSelected.splice(indexOption, 1)
                    }
                }

                return optionsSelected
            }
        },
        created () {
            this.statusList.forEach(status => {
                status.checked = false
            })

            this.tagsList.forEach(tag => {
                tag.checked = false
            })
            this.filters.onlyTrashed = JSON.parse(this.data).onlyTrashed
        }
    }
</script>
