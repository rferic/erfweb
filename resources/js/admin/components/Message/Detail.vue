<template>
    <transition name="bounceRight">
        <div class="mb-2">
            <b-row class="mb-2">
                <b-col cols="12">
                    <b-button
                        variant="primary"
                        size="sm"
                        @click="$emit('onGoToList')"
                    >
                        <i class="fa fa-chevron-left" />
                        {{ $t('Return to list', { locale }) }}
                    </b-button>
                </b-col>
            </b-row>
            <b-row v-if="author !== null">
                <b-col lg="4" md="12" class="order-xl-2 mt-2">
                    <b-card class="card-profile shadow">
                        <div class="row justify-content-center">
                            <div class="col-lg-5 order-lg-4 pb-5">
                                <div class="card-profile-image">
                                    <img :src="`/${author.avatar}`" class="rounded-circle">
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                            <div class="text-center">
                                <h3>
                                    {{ author.name }}
                                </h3>
                                <div class="h5 font-weight-300">
                                    <i class="ni ni-email-83" /> {{ author.email }}
                                </div>

                                <b-row v-if="counters !== null" class="row">
                                    <b-col cols="12">
                                        <div class="card-profile-stats d-flex justify-content-center">
                                            <div
                                                v-for="tag in tagsList"
                                                :key="tag.key"
                                            >
                                                <span class="heading" :class="`text-${tag.class}`">
                                                    {{ counters.tags[tag.key] }}
                                                </span>
                                                        <span class="description" :class="`text-${tag.class}`">
                                                    <i
                                                        v-if="typeof tag.icon !== typeof undefined"
                                                        class="fa ml-2"
                                                        :class="`${tag.icon} text-${tag.class}`"
                                                    />
                                                    {{ $t(tag.key, { locale }) | capitalizeFilter }}
                                                </span>
                                            </div>
                                        </div>
                                    </b-col>
                                </b-row>
                            </div>
                        </div>
                    </b-card>
                </b-col>
                <b-col lg="8" md="12" class="order-xl-1 mt-2">
                    <index-conversation
                        :data="data"
                        :message-origin="messageOrigin"
                        :author="author"
                        @onGoToList="$emit('onGoToList')"
                    />
                </b-col>
            </b-row>
        </div>
    </transition>
</template>

<script>
    import { mapState } from 'vuex'
    import datetimeFilter from '../../../includes/filters/datetimeFilter'
    import capitalizeFilter from '../../../includes/filters/capitalizeFilter'
    import IndexConversation from './../../components/Conversation/Index'
    import messageMixin from '../../mixins/message'

    export default {
        name: 'DetailMessage',
        props: {
            data: {
                type: String,
                required: true
            },
            messageOrigin: {
                type: Object,
                required: true
            }
        },
        components: { IndexConversation },
        mixins: [ messageMixin ],
        filters: { datetimeFilter, capitalizeFilter },
        data () {
            return {
                statusList: JSON.parse(this.data).statusList,
                tagsList: JSON.parse(this.data).tagsList,
                author: null,
                counters: null
            }
        },
        computed: {
            ...mapState([ 'routesGlobal', 'locale', 'routes' ])
        },
        methods: {
            async initialize () {
                await this.getAuthor()
                this.counters = await this.getMessageStateRequest({ filters: this.filters })
            },
            async getAuthor () {
                this.author = await this.getAuthorRequest(this.messageOrigin)
            }
        },
        async mounted () {
            this.initialize()
        }
    }
</script>
