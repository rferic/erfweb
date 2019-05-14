<template>
    <div>
        <notifications
            group="notify"
            position="top right"
        />
        <b-list-group class="messages">
            <item-conversation
                :key="messageParent.id"
                :message-origin="messageParent"
                :data="data"
                :author="author"
                :status-editable="true"
                :enable-responses="true"
                :enable-remove="true"
                @onUpdateMessage="onUpdateMessage"
                @onAddMessage="onAddMessage"
                @onRemoveMessage="onRemoveMessageParent"
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
                    :enable-remove="true"
                    @onUpdateMessage="onUpdateMessage"
                    @onRemoveMessage="onRemoveMessage"
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
    import cloneMixin from './../../../includes/mixins/clone'
    import messageMixin from '../../mixins/message'
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
        mixins: [ cloneMixin, messageMixin ],
        data () {
            return {
                statusList: JSON.parse(this.data).statusList,
                tagsList: JSON.parse(this.data).tagsList,
                currentPage: 1,
                perPage: 10,
                messageParent: this.clone(this.messageOrigin)
            }
        },
        computed: {
            ...mapState([ 'routesGlobal', 'locale', 'routes' ]),
            filters () {
                return this.author !== null
                    ? {
                        authors: [ this.author.id ],
                        receivers: [ this.author.id ],
                        message_parent: this.messageParent.id
                    }
                    : {}
            },
            hasMoreMessages () {
                return this.totalMessages > this.messages.length
            },
            conversationIsOpen () {
                return this.messageParent.status !== 'pending'
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
            async refreshConversation ({}) {
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
            },
            async onAddMessage ({}) {
                await this.refreshConversation({})
                this.$notify({
                    group: 'notify',
                    title: this.$t('Create message'),
                    text: this.$t('Message has been updated'),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            onUpdateMessage ({ message, status }) {
                if ( message.id === this.messageParent.id ) {
                    this.messageParent.status = status
                } else {
                    for ( let item of this.messages ) {
                        if ( item.id === message.id ) {
                            item.status = status
                        }
                    }
                }

                this.notifyMessageUpdated()
            },
            async onRemoveMessage () {
                await this.refreshConversation({})
                this.notifyMessageDeleted()
            },
            onRemoveMessageParent () {
                this.$emit('onGoToList')
            },
            notifyMessageDeleted () {
                this.$notify({
                    group: 'notify',
                    title: this.$t('Update message'),
                    text: this.$t('Message has been deleted'),
                    type: 'success',
                    config: {
                        closeOnClick: true
                    }
                })
            },
            notifyMessageUpdated () {
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
