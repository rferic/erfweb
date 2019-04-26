
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
// Resources
import BootstrapVue from 'bootstrap-vue'
import ArgonDashboard from './../../vue-argon/src/plugins/argon-dashboard'
import VeeValidate from 'vee-validate'
import i18n from 'voo-i18n'
import translations from './../includes/translations'
import VueScrollTo from 'vue-scrollto'
import VueInstant from 'vue-instant'
import VueMoment from 'vue-moment'
import Notifications from 'vue-notification'
// CSS
import 'vue-instant/dist/vue-instant.css'

// Uses
Vue.use(BootstrapVue)
Vue.use(ArgonDashboard)
Vue.use(Notifications)
Vue.use(VeeValidate, { fieldsBagName: 'veeFields' })
Vue.use(i18n, translations)
Vue.use(VueScrollTo)
Vue.use(VueInstant)
Vue.use(VueMoment)

// Store
import store from './store'
// Components
import AuthLayout from './Layout'

const app = new Vue({
    el: '#app',
    store,
    components: { AuthLayout },

});
