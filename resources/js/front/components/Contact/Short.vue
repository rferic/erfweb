<template>
    <div>
        <form>
            <v-text-field
                name="subject"
                type="text"
                v-model="subject"
                :label="$vuetify.t('Subject')"
                v-validate="'required'"
                data-vv-name="subject"
                :error-messages="errors.collect('subject')"
                @input="$validator.reset()"
            />
            <v-textarea
                name="message"
                :label="$vuetify.t('Message')"
                v-model="text"
                v-validate="'required'"
                data-vv-name="text"
                :error-messages="errors.collect('text')"
                @input="$validator.reset()"
            />
            <div class="text-xs-right">
                <v-btn color="grey darken-4 white--text" @click="onSubmit">
                    {{ $vuetify.t('Send') }}
                    <v-icon small class="ml-2">mdi-send</v-icon>
                </v-btn>
            </div>
        </form>
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :multi-line="true" bottom right>
            {{ $vuetify.t(snackbar.message) }}
        </v-snackbar>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'
    import messageMixin from './../../mixins/message'

    export default {
        name: 'ShortContact',
        mixins: [ messageMixin ],
        data () {
            return {
                subject: '',
                text: '',
                onHold: false,
                snackbar: {
                    show: false,
                    color: '',
                    message: ''
                }
            }
        },
        computed: {
            ...mapGetters({
                isLogged: 'auth/isLogged'
            })
        },
        watch: {
            isLogged () {
                if ( this.onHold && this.isLogged ) {
                    this.onSubmit()
                }
            }
        },
        methods: {
            ...mapActions({
                openAuthModal: 'auth/open',
                closeAuthModal: 'auth/close',
                changeTab: 'auth/changeTab'
            }),
            async onSubmit () {
                this.onHold = true

                if ( await this.$validator.validateAll() ) {
                    if ( !this.isLogged ) {
                        this.changeTab('login')
                        this.openAuthModal()
                    } else {
                        this.sendMessage()
                    }
                }
            },
            async sendMessage () {
                this.onHold = false

                const { result  } = await this.sendMessageRequest({
                    subject: this.subject,
                    text: this.text,
                    tag: 'contact'
                })

                if ( result ) {
                    this.subject = ''
                    this.text = ''
                    this.$validator.reset()
                    this.snackbar.color = 'success'
                    this.snackbar.message = 'Your message has been sended.'
                    this.snackbar.show = true
                } else {
                    this.snackbar.color = 'error'
                    this.snackbar.message = 'There was an error with your message.'
                    this.snackbar.show = true
                }
            }
        }
    }
</script>
