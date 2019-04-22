<template>
    <b-form-select
        v-model="value"
        :options="options"
        size="sm"
        @input="onChange"
    />
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../mixins/clone'
    import capitalizeFilter from '../../../includes/filters/capitalizeFilter'

    export default {
        name: 'SelectFilter',
        props: {
            optionsOrigin: {
                type: Array,
                required: true
            },
            valueOrigin: {
                type: [ String, Number ],
                required: false,
                default: ''
            },
            title: {
                type: String,
                required: false,
                default: ''
            },
            translateLabel: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        mixins: [ cloneMixin ],
        filters: { capitalizeFilter },
        data () {
            return {
                 value: ''
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            hasTitle () {
                return this.title !== ''
            },
            options () {
                let options = [{
                    value: '',
                    text: this.title
                }]

                options = options.concat(this.optionsOrigin)

                return options
            }
        },
        methods: {
            // Events
            onChange () {
                this.$emit('onChangeFilter', this.clone(this.value))
            }
        },
        mounted () {
            this.value = this.clone(this.valueOrigin)
        }
    }
</script>
