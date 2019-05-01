<template>
    <transition name="bounceRight">
        <div class="filters">
            <b-nav vertical>
                <div class="dropdown-divider" />
                <input-text-filter @onChangeFilter="onChangeTextFilter" class="mb-2" />
                <div class="dropdown-divider" />
                <checkboxes-filter
                    :title="$t('Status', { locale })"
                    :options="statusList"
                    :translate-label="true"
                    @onChangeFilter="onChangeStatusFilter"
                    class="mb-2"
                />
                <div class="dropdown-divider" />
                <checkboxes-filter
                    :title="$t('Tags', { locale })"
                    :options="tagsList"
                    :translate-label="true"
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
    import InputTextFilter from './../Filters/InputText'
    import CheckboxesFilter from './../Filters/Checkboxes'

    export default {
        name: 'FilterMessage',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        components: { InputTextFilter, CheckboxesFilter },
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
