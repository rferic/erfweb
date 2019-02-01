<template>
    <b-card>
        <b-nav-form
            v-for="checkbox in checkboxes"
            :key="checkbox.key"
        >
            <div class="nav-form-item-color">
                <b-form-checkbox
                    v-model="checkbox.checked"
                    @input="onToggleCheck(checkbox)"
                >
                    <span :class="`text-${checkbox.class}`">{{ $t(checkbox.key, { locale }) }}</span>
                    <i
                        v-if="typeof checkbox.icon !== typeof undefined"
                        class="fa ml-2"
                        :class="`${checkbox.icon} text-${checkbox.class}`"
                    />
                </b-form-checkbox>
            </div>
        </b-nav-form>
    </b-card>
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
