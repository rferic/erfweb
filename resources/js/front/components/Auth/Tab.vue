<template>
    <v-tabs v-model="currentTab" light centered slider-color="grey darken-4" @change="changeCurrentTab" >
        <v-tab href="#login">
            <v-icon light small class="pr-1">mdi-account-arrow-right</v-icon>
            {{ $vuetify.t('Login') }}
        </v-tab>
        <v-tab href="#register">
            <v-icon light small class="pr-1">mdi-account-plus</v-icon>
            {{ $vuetify.t('Register') }}
        </v-tab>

        <v-tab-item value="login">
            <auth-login @onLogged="$emit('onLogged')" @onErrorLogged="$emit('onErrorLogged')" />
        </v-tab-item>

        <v-tab-item value="register">
            <auth-register @onRegistered="$emit('onRegistered')" @onErrorRegistered="$emit('onErrorRegistered')" />
        </v-tab-item>
    </v-tabs>
</template>

<script>
    import { mapState, mapActions } from 'vuex'
    import AuthLogin from './Login'
    import AuthRegister from './Register'
    import cloneMixin from './../../../includes/mixins/clone'

    export default {
        name: 'AuthTab',
        components: { AuthLogin, AuthRegister },
        mixins: [ cloneMixin ],
        data () {
            return {
                currentTab: 'login'
            }
        },
        computed:{
            ...mapState({
                tab: state => state.auth.tab
            })
        },
        watch: {
            tab () {
                this.currentTab = this.clone(this.tab)
            }
        },
        methods: {
            ...mapActions({
                changeTab: 'auth/changeTab'
            }),
            changeCurrentTab () {
                this.changeTab(this.currentTab)
            }
        },
        mounted () {
            this.currentTab = this.clone(this.tab)
        }
    }
</script>
