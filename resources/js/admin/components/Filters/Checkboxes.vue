<template>
    <b-card class="filters">
        <h5
            v-if="hasTitle"
            class="text-center"
        >
            {{ title }}
        </h5>
        <hr>
        <b-nav-form
            v-for="checkbox in checkboxes"
            :key="checkbox.key"
        >
            <div class="nav-form-item-color">
                <b-form-checkbox
                    v-model="checkbox.checked"
                    @input="onToggleCheck(checkbox)"
                >
                    <i
                        v-if="typeof checkbox.icon !== typeof undefined"
                        class="fa mr-2"
                        :class="`${checkbox.icon} text-${checkbox.class}`"
                    />
                    <span :class="`text-${checkbox.class}`">{{ $t(getLabelCheckbox(checkbox), { locale }) | capitalizeFilter }}</span>
                </b-form-checkbox>
            </div>
        </b-nav-form>
    </b-card>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../mixins/clone'
    import capitalizeFilter from '../../../includes/filters/capitalizeFilter'

    export default {
        name: 'CheckboxesFilterMessage',
        props: {
            options: {
                type: Array,
                required: true
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
                checkboxes: []
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            hasTitle () {
                return this.title !== ''
            }
        },
        methods: {
            onToggleCheck ( checkbox ) {
                this.$emit('onChangeFilter', checkbox)
            },
            getLabelCheckbox ( checkbox ) {
                const label = typeof checkbox.label !== typeof undefined ? checkbox.label : checkbox.key

                return this.translateLabel ? this.$t(label, { locale: this.locale }) : label
            }
        },
        created () {
            this.checkboxes = this.clone(this.options)
        }
    }
</script>

<style scoped>
    .nav-form-item-color {
        width: 100%;
        padding: 5px 10px;
        margin: 2px 0;
    }

    h5 {
        font-size: 1rem;
    }
</style>
