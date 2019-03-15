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
                <div class="d-inline-block avatar avatar-50 pull-right ml-2">
                    <img :src="`/${messageOrigin.author.avatar}`" />
                </div>
                <div class="pull-right text-right">
                    <div>
                        <small>{{ message.created_at | momentTime }}</small>
                    </div>
                    <div>
                        <small>{{ author.name }}</small>
                    </div>
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
                <div>
                    <a
                        v-if="iCanRemove"
                        href="#"
                        class="pull-left text-danger"
                        @click.prevent="remove"
                    >
                        {{ $t('Remove', { locale }) }}
                    </a>
                    <a
                        v-if="enableResponses && !replyIsOpen"
                        href="#"
                        class="pull-right"
                        @click.prevent="toggleReply"
                    >
                        {{ $t('Reply', { locale }) }}
                    </a>
                    <p class="clearfix" />
                </div>
                <div v-if="enableResponses && replyIsOpen">
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
    import messageMixin from '../../mixins/message'
    import FormConversation from './Form'
    import { mapState, mapActions } from 'vuex'

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
            },
            enableRemove: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        components: { FormConversation },
        mixins: [ cloneMixin, messageMixin ],
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
                return this.messageOrigin.author.roles.some(role => role.name === 'admin')
            },
            iCanRemove () {
                return this.enableRemove && (this.auth.user.roles.some(role => role === 'admin') || this.auth.user.id === this.message.author.id)
            }
        },
        methods: {
            ...mapActions({
                toogleBlockui : 'blockui/toggleIsVisible'
            }),
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
            },
            async remove () {
                this.toogleBlockui(true)
                await this.removeMessageRequest(this.messageOrigin)
                this.$emit('onRemoveMessage')
                this.toogleBlockui(false)
            }
        }
    }
</script>
