<template>
    <div
        class="mb-4 message"
        :class="{'is-mine': message.author.id === auth.user.id }"
    >
        <b-list-group-item
            :variant="getDataTagSelected(message.tag).class"
            class="message-item"
        >
            <div>
                <div class="pull-left">
                    <div class="d-inline-block avatar avatar-40">
                        <img :src="`/${messageOrigin.author.avatar}`" />
                    </div>
                    <i
                        class="fa"
                        :class="getDataTagSelected(message.tag).icon"
                    />
                    <span
                        v-if="!authorIsAdmin && statusEditable"
                        v-for="status in statusList"
                        :key="status.key"
                        class="ml-2 status-label"
                        :class="{ 'status-label-selected': status.key === message.status }"
                        @click="onChangeStatusMessage(status.key)"
                    >
                        {{ $t(status.key, { locale }) | capitalizeFilter }}
                    </span>
                </div>
                <div class="pull-right">
                    {{ message.created_at | momentTime }}
                </div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div v-if="subjectIsVisible && message.subject !== ''">
                <b>{{ message.subject }}</b>
                <hr>
            </div>
            <div v-if="message.status !== 'pending' || !statusEditable">
                {{ message.text }}
                <hr>
                <a
                    v-if="enableResponses && !replyIsOpen"
                    href="#"
                    @click.prevent="toggleReply"
                >
                    {{ $t('Reply', { locale }) }}
                </a>
                <div v-else-if="enableResponses">
                    <form-conversation
                        :message-parent="messageOrigin"
                        :button-cancel="buttonCancelForm"
                        :status="messageOrigin.status"
                        :tag="messageOrigin.tag"
                        :receiver="author"
                        @onCancelMessageForm="toggleReply"
                        @onSaveMessageSuccess="onSaveMessageSuccess"
                    />
                </div>
            </div>
            <div v-else>
                <b-button
                    @click.prevent="onChangeStatusMessage('readed')"
                    variant="outline-info"
                    block
                    sm
                >
                    {{ $t('Open message', { locale }) }}
                </b-button>
            </div>
        </b-list-group-item>
        <div
            v-if="enableResponses"
            class="count-total-messages"
            :class="{ 'text-right' : messageOrigin.author_id === author.id }"
        >
            <i>{{ `${this.$parent.totalMessages} ${$t('respuestas', { locale })}` }} </i>
        </div>
    </div>
</template>

<script>
    import capitalizeFilter from '../../../includes/filters/capitalizeFilter'
    import cloneMixin from './../../mixins/clone'
    import messagesMixin from './../../mixins/messages'
    import FormConversation from './Form'
    import { mapState } from 'vuex'

    export default {
        name: 'ItemConversation',
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
            },
            statusEditable: {
              type: Boolean,
              required: false,
              default: false,
            },
            enableResponses: {
                type: Boolean,
                required: false,
                default: false
            },
            subjectIsVisible: {
                type: Boolean,
                required: false,
                default: true
            }
        },
        components: { FormConversation },
        mixins: [ cloneMixin, messagesMixin ],
        filters: { capitalizeFilter },
        data () {
            return {
                message: this.clone(this.messageOrigin),
                statusList: JSON.parse(this.data).statusList,
                tagsList: JSON.parse(this.data).tagsList,
                replyIsOpen: false,
                buttonCancelForm: {
                    icon: 'fa-close',
                    label: this.$t('Cancel', { locale })
                }
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes', 'auth' ]),
            authorIsAdmin () {
                return this.messageOrigin.author.roles.some(role => { role.name === 'admin' })
            }
        },
        methods: {
            async onChangeStatusMessage ( status ) {
                this.message.status = status

                await this.updateMessageRequest(this.message)
                this.$emit('onUpdateMessage', {
                    message: this.message,
                    status: status
                })
            },
            onSaveMessageSuccess ({ message }) {
                this.$emit('onAddMessage', { message })
                this.toggleReply()
            },
            toggleReply () {
                this.replyIsOpen = !this.replyIsOpen
            }
        }
    }
</script>

<style scoped>
    .message > .message-item,
    .message > .count-total-messages {
        margin: 5px 20px 0 0;
    }
    .message.is-mine > .message-item,
    .message.is-mine > .count-total-messages {
        margin-left: 20px;
        margin-right: 0;
    }

    .message > .message-item {
        border-radius: 0 10px 10px 0;
    }

    .message.is-mine > .message-item {
        border-radius: 10px 0 0 10px;
    }

    .message .status-label {
        cursor: pointer;
        opacity: .3;
    }

    .message .status-label.status-label-selected {
        cursor: default;
    }

    .message .status-label:hover,
    .message .status-label.status-label-selected {
        opacity: 1;
    }
</style>
