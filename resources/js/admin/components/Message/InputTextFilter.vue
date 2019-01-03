<template>
    <b-nav-form>
        <b-form-input
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
