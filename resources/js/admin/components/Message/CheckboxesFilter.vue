<template>
    <div>
        <b-nav-form
            v-for="checkbox in checkboxes"
            :key="checkbox.key"
        >
            <div
                class="nav-form-item-color"
                :class="`bg-${checkbox.class}`"
            >
                <b-form-checkbox
                    v-model="checkbox.checked"
                    @input="onToggleCheck(checkbox)"
                >
                    {{ $t(checkbox.key, { locale }) }}
                    <i
                        v-if="typeof checkbox.icon !== typeof undefined"
                        class="fa ml-2"
                        :class="checkbox.icon"
                    />
                </b-form-checkbox>
            </div>
        </b-nav-form>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../mixins/clone'

    export default {
        name: 'CheckboxesFilterMessage',
        props: {
            options: {
                type: Array,
                required: true
            }
        },
        mixins: [ cloneMixin ],
        data () {
            return {
                checkboxes: []
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        },
        methods: {
            onToggleCheck ( checkbox ) {
                this.$emit('onChangeFilter', checkbox)
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
</style>
