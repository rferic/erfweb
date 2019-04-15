<template>
    <b-row>
        <b-col cols="6">
            <b-button
                v-if="showCancel"
                variant="danger"
                sm
                @click="$emit('onCancel')"
            >
                <i class="fa fa-close" />
                {{ $t('Cancel', { locale }) }}
            </b-button>
        </b-col>
        <b-col cols="6" class="text-right">
            <b-button
                variant="success"
                sm
                @click="$emit('onSave')"
            >
                <i class="fa fa-save" />
                {{ $t('Save', { locale }) }}
            </b-button>
        </b-col>
    </b-row>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'FormButtons',
        props: {
            showCancel: {
                type: Boolean,
                required: false,
                default: true
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        }
    }
</script>
