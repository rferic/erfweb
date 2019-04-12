<template>
    <transition name="bounceRight">
        <div>
            <b-nav vertical>
                <div class="dropdown-divider" />
                <input-text-filter @onChangeFilter="onChangeTextFilter" />
                <div class="dropdown-divider" />
                <checkboxes-filter
                    :title="$t('Types', { locale })"
                    :options="filters.types"
                    :translate-label="true"
                    @onChangeFilter="onChangeTypesFilter"
                />
                <checkboxes-filter
                    :title="$t('Status', { locale })"
                    :options="filters.status"
                    :translate-label="true"
                    @onChangeFilter="onChangeStatusFilter"
                />
            </b-nav>
        </div>
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import filterAppStructure from './../../structures/filterApp'
    import InputTextFilter from './../Filters/InputText'
    import CheckboxesFilter from './../Filters/Checkboxes'
    import cloneMixin from './../../mixins/clone'

    export default {
        name: 'FilterApp',
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
                filters: this.clone(filterAppStructure)
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ])
        },
        methods: {
            onChangeTextFilter ( filter ) {
                this.filters.text = filter
                this.$emit('onChangeFilters', this.filters)
            },
            onChangeTypesFilter ( filter ) {
                this.filters.types.some(item => {
                    const scapeCondition = item.key === filter.key

                    if ( scapeCondition ) {
                        item.checked = filter.checked
                    }

                    return scapeCondition
                })

                this.$emit('onChangeFilters', this.filters)
            },
            onChangeStatusFilter ( filter ) {
                this.filters.status.some(item => {
                    const scapeCondition = item.key === filter.key

                    if ( scapeCondition ) {
                        item.checked = filter.checked
                    }

                    return scapeCondition
                })

                this.$emit('onChangeFilters', this.filters)
            }
        }
    }
</script>
