<template>
    <div>
        <b-form-group
            v-if="subjectIsEnable"
            class="mt-3"
            :label="`${$t('Subject', { locale })}:`"
            label-for="subject">
            <b-form-input
                id="subject"
                name="subject"
                v-model="subject"
                :rows="2"
                v-validate
                data-vv-rules="required"
                :class="{'input': true, 'is-invalid': errors.has('subject') }"
            />
        </b-form-group>
        <b-form-group
            class="mt-3"
            :label="`${$t('Text', { locale })}:`"
            label-for="text">
            <b-form-textarea
                id="text"
                name="text"
                v-model="text"
                :rows="2"
                v-validate
                data-vv-rules="required"
                :class="{'input': true, 'is-invalid': errors.has('text') }"
            />
        </b-form-group>
        <b-row>
            <b-col cols="6">
                <b-button
                    v-if="enableButtonCancel"
                    variant="danger"
                    @click="$emit('onCancelMessageForm')"
                >
                    <i
                        v-if="buttonCancelHasIcon"
                        class="fa"
                        :class="buttonCancel.icon"
                    />
                    <span v-if="buttonCancelHasLabel">{{ buttonCancel.label }}</span>
                </b-button>
            </b-col>
            <b-col cols="6">
                <b-button
                    variant="primary"
                    class="pull-right"
                    @click="submit"
                >
                    <i class="fa fa-send" />
                    {{ $t('Send', { locale }) }}
                </b-button>
                <p class="clearfix" />
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import { Validator } from 'vee-validate'

    export default {
        name: 'FormConversation',
        props: {
            messageParent: {
                type: Object,
                required: false,
                default: null
            },
            buttonCancel: {
                type: Object,
                required: false,
                default: null
            },
            subjectIsEnable: {
                type: Boolean,
                required: false,
                default: false
            },
            status: {
                type: String,
                required: false,
                default: ''
            },
            tag: {
                type: String,
                required: false,
                default: ''
            },
            receiver: {
                type: Object,
                required: false,
                default: null
            }
        },
        data () {
            return {
                subject: '',
                text: ''
            }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            hasMessageParent () {
                return this.messageParent !== null
            },
            hasReceiver () {
                return this.receiver !== null
            },
            enableButtonCancel () {
                return this.buttonCancel !== null
            },
            buttonCancelHasIcon () {
                return this.enableButtonCancel && typeof this.buttonCancel.icon !== typeof undefined
            },
            buttonCancelHasLabel () {
                return this.enableButtonCancel && typeof this.buttonCancel.label !== typeof undefined
            }
        },
        methods: {
            async submit () {
                this.$validator.validate().then(async result => {
                    if ( result ) {
                        await this.save()
                    }
                })
            },
            async save () {
                const message = {
                    subject: this.subjectIsEnable && this.hasMessageParent ? this.subject : this.messageParent.subject,
                    text: this.text,
                    status: this.status,
                    tag: this.tag,
                    message_parent_id: this.hasMessageParent ? this.messageParent.id : null,
                    receiver_id: this.receiver.id
                }

                const { data } = await this.createRequest(message)
                this.$emit('onSaveMessageSuccess', data)
            },
            async createRequest ( message ) {
                return await axios.post(this.routes.createMessage, message)
            }
        }
    }
</script>
