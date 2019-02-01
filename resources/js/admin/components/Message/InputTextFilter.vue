<template>
    <b-nav-form>
        <b-form-input
            class="filter-input"
            v-model="text"
            type="text"
            size="sm"
            :placeholder="$t('Search text...', { locale })"
            v-debounce:500ms="onChange"
        />
    </b-nav-form>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'InputTextFilterMessage',
        data () {
            return {
                text: ''
            }
        },
        computed: {
            ...mapState([ 'locale' ])
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
