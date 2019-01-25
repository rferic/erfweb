<template>
    <transition name="bounceRight">
        <div>
            <b-row>
                <b-col cols="12">
                    <b-button
                        variant="primary"
                        size="sm"
                        @click="$emit('onGoToList')"
                    >
                        <i class="fa fa-chevron-left" />
                        {{ $t('Return to list', { locale }) }}
                    </b-button>
                    <hr />
                </b-col>
                <b-col cols="12">
                    asd
                </b-col>
            </b-row>
            <b-row>
                <b-col
                    cols="4"
                    xs="12"
                    class="pull-right"
                >
                    <b-card>
                        asd
                    </b-card>
                </b-col>
                <b-col
                    cols="5"
                    xs="12"
                >
                    asd
                </b-col>
            </b-row>
        </div>
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import messagesMixin from './../../mixins/messages'

    export default {
        name: 'DetailMessage',
        props: {
            messageOrigin: {
                type: Object,
                required: true
            }
        },
        mixins: [ messagesMixin ],
        data () {
            return {
                author: null
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
        },
        methods: {
            async getAuthor () {
                this.author = await this.getAuthorRequest(this.messageOrigin)
            }
        },
        mounted () {
            this.getAuthor()
        }
    }
</script>
