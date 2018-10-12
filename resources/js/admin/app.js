
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');

window.Vue = require('vue');
// Resources
import BootstrapVue from 'bootstrap-vue'
import VeeValidate from 'vee-validate'
import i18n from 'voo-i18n'
import translations from './../includes/translations'
import VueScrollTo from 'vue-scrollto'
import VueInstant from 'vue-instant'
import VueMoment from 'vue-moment'
import Notifications from 'vue-notification'
// CSS
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'vue-instant/dist/vue-instant.css'

// Uses
Vue.use(BootstrapVue)
Vue.use(Notifications)
Vue.use(VeeValidate, { fieldsBagName: 'veeFields' })
Vue.use(i18n, translations)
Vue.use(VueScrollTo)
Vue.use(VueInstant)
Vue.use(VueMoment)

// Store
import store from './store'
// Components
import NavIndex from './components/Nav/Index'
import NavRight from './components/Nav/Right'
import NavMessageNotify from './components/Nav/MessageNotify'

const app = new Vue({
    el: '#app',
    store,
    components: {
        NavIndex,
        NavRight,
        NavMessageNotify
    },

});
