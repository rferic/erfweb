<template>
    <b-nav-form>
        <b-form-input
            class="filter-input"
            v-model="text"
            type="text"
            size="sm"
            :placeholder="placeholderComputed"
            v-debounce:500ms="onChange"
        />
    </b-nav-form>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'InputTextFilter',
        props: {
            placeholder: {
                type: String,
                required: false,
                default:  null
            }
        },
        data () {
            return {
                text: ''
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            placeholderComputed () {
                return this.placeholder !== null ? this.placeholder : this.$t('Search text...', { locale: this.locale })
            }
        },
        methods: {
            onChange () {
                this.$emit('onChangeFilter', this.text)
            }
        }
    }
</script>

<style scoped>
    .filter-input {
        width: 100%;
    }
</style>
