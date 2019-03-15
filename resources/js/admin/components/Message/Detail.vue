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
            </b-row>
            <b-row v-if="author !== null">
                <b-col
                    cols="4"
                    xs="12"
                    class="pull-right"
                >
                    <b-card>
                        <div class="avatar avatar-100">
                            <img :src="`/${author.avatar}`" />
                        </div>
                        <hr>
                        <p class="text-sm-center mt-2 mb-1">{{ author.name }}</p>
                        <p class="text-sm-center mt-2 mb-1">
                            <i>{{ author.email }}</i>
                        </p>
                        <p class="text-sm-center mt-2 mb-1">
                            <i>{{ author.created_at | datetimeFilter }}</i>
                        </p>
                        <ul
                            v-if="counters !== null"
                            class="list-group list-group-flush mt-4"
                        >
                            <li
                                v-for="tag in tagsList"
                                :key="tag.key"
                                class="list-group-item"
                            >
                                <span :class="`text-${tag.class}`">
                                    <i
                                        v-if="typeof tag.icon !== typeof undefined"Fthis
                                        class="fa ml-2"
                                        :class="`${tag.icon} text-${tag.class}`"
                                    />
                                    {{ $t(tag.key, { locale }) | capitalizeFilter }}
                                </span>
                                <span
                                    class="badge pull-right"
                                    :class="`badge-${tag.class}`"
                                >
                                    {{ counters.tags[tag.key] }}
                                </span>
                            </li>
                        </ul>
                    </b-card>
                </b-col>
                <b-col
                    cols="8"
                    xs="12"
                >
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
