<template>
    <div>
        <notifications
            group="notify"
            position="top right"
        />
        <b-list-group class="messages">
            <item-conversation
                :key="messageOrigin.id"
                :message-origin="messageOrigin"
                :data="data"
                :author="author"
                :status-editable="true"
                :enable-responses="true"
                @onUpdateMessage="onUpdateMessage"
                @onAddMessage="onAddMessage"
            />
            <div
                v-if="conversationIsOpen"
                class="messages-childs"
            >
                <item-conversation
                    v-for="message in messages"
                    :key="message.id"
                    :message-origin="message"
                    :data="data"
                    :author="author"
                    :subject-is-visible="false"
                    @onUpdateMessage="onUpdateMessage"
                />
                <b-button
                    v-if="hasMoreMessages"
                    variant="primary"
                    block
                    class="mt-2"
                    @click="loadMore"
                >
                    {{ $t('View more', { locale })  }}
                </b-button>
            </div>
        </b-list-group>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import messagesMixin from './../../mixins/messages'
    import ItemConversation from './Item'

    export default {
        name: 'IndexConversation',
        props: {
            data: {
                type: String,
                required: true
            },
            messageOrigin: {
                type: Object,
                required: true
            },
            author: {
                type: Object,
                required: true
            }
        },
        components: { ItemConversation },
        mixins: [ messagesMixin ],
        data () {
            return {
                statusList: JSON.parse(this.data).statusList,
                tagsList: JSON.parse(this.data).tagsList,
                currentPage: 1,
                perPage: 10,
                conversationIsOpen: this.messageOrigin.status === 'readed'
            }
        },
        computed: {
            ...mapState([ 'routesGlobal', 'locale', 'routes' ]),
            filters () {
                return this.author !== null
                    ? {
                        authors: [ this.author.id ],
                        receivers: [ this.author.id ],
                        message_parent: this.messageOrigin.id
                    }
                    : {}
            },
            hasMoreMessages () {
                return this.totalMessages > this.messages.length
            }
        },
        methods: {
            async initialize () {
                await this.getMessages({
                    page: this.currentPage,
                    perPage: this.perPage,
                    stack: true,
                    filters: this.filters,
                    orderBy: this.orderBy
                })
            },
            // Events
            async onAddMessage ({}) {
                this.messages = []

                for ( let page = 1; page <= this.currentPage; page++ ) {
                    await this.getMessages({
                        page: page,
                        perPage: this.perPage,
                        stack: true,
                        filters: this.filters,
                        orderBy: this.orderBy
                    })
                }

                this.$notify({
                    group: 'notify',
                    title: this.$t('Create message'),
                    text: this.$t('Message status has been created'),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            onUpdateMessage ({ message, status }) {
                for ( let item of this.messages ) {
                    if ( item.id === message.id ) {
                        item.status = status
                    }
                }

                this.$notify({
                    group: 'notify',
                    title: this.$t('Update message'),
                    text: this.$t('Message status has been updated'),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            // Actions
            async loadMore () {
                await this.getMessages({
                    page: this.currentPage,
                    perPage: this.perPage,
                    stack: true,
                    filters: this.filters,
                    orderBy: this.orderBy,
                    url: this.urlNextPage
                })
            }
        },
        async mounted () {
            this.initialize()
        }
    }
</script>

<style scoped>
    .messages-childs {
        width: 100%;
        padding: 0 8%;
        margin: 0 5px;
    }
</style>
