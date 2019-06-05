<template>
    <div>
        <v-dialog v-if="!isLogged" ref="authModal" v-model="isOpen" max-width="600px" lazy @input="toggleModal">
            <v-card color="white">
                <v-card-text>
                    <auth-tab
                        @onLogged="onLogged"
                        @onErrorLogged="onErrorLogged"
                        @onRegistered="onRegistered"
                        @onErrorRegistered="onErrorRegistered"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-snackbar v-model="snackbar.show" :color="snackbar.color" :multi-line="true" top>
            <span class="snackbar-text">{{ $vuetify.t(snackbar.message) }}</span>
        </v-snackbar>
    </div>
</template>

<script>
    import { mapState, mapGetters, mapActions } from 'vuex'
    import AuthTab from './Tab'
    import cloneMixin from './../../../includes/mixins/clone'

    export default {
        name: 'AuthIndex',
        components: { AuthTab },
        mixins:[  cloneMixin ],
        data () {
            return {
                isOpen: false,
                snackbar: {
                    show: false,
                    color: '',
                    message: ''
                }
            }
        },
        computed: {
            ...mapState({
                auth: state => state.auth.user,
                modal: state => state.auth.modal
            }),
            ...mapGetters({
                isLogged: 'auth/isLogged'
            })
        },
        watch: {
            modal () {
                this.isOpen = this.clone(this.modal)
            }
        },
        methods: {
            ...mapActions({
                open: 'auth/open',
                close: 'auth/close'
            }),
            toggleModal () {
                if ( this.isOpen ) {
                    this.open()
                } else {
                    this.close()
                }
            },
            onLogged () {
                this.snackbar.color = 'success'
                this.snackbar.message = 'Hi! You have logged in.'
                this.snackbar.show = true
            },
            onErrorLogged () {
                this.snackbar.color = 'error'
                this.snackbar.message = 'User not exists. Check the email and the password.'
                this.snackbar.show = true
            },
            onRegistered () {
                this.snackbar.color = 'success'
                this.snackbar.message = 'Welcome! Remember vto verify your email.'
                this.snackbar.show = true
            },
            onErrorRegistered () {

                this.snackbar.color = 'error'
                this.snackbar.message = 'Error in registration.'
                this.snackbar.show = true
            }
        },
        created () {
            this.isOpen = this.clone(this.modal)
        }
    }
</script>

<style scoped>
    .snackbar-text {
        font-family: Roboto, sans-serif !important;
    }
</style>
