
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
// Resources
import ArgonDashboard from './../../vue-argon/src/plugins/argon-dashboard'
import BootstrapVue from 'bootstrap-vue'
import VeeValidate from 'vee-validate'
import i18n from 'voo-i18n'
import translations from './../includes/translations'
import VueScrollTo from 'vue-scrollto'
import VueInstant from 'vue-instant'
import VueMoment from 'vue-moment'
import Notifications from 'vue-notification'
import VueWait from 'vue-wait'
import vueDebounce from 'vue-debounce'
import SweetModal from 'sweet-modal-vue/src/plugin.js'
import ToggleButton from 'vue-js-toggle-button'
import BlockUI from 'vue-blockui'
import VueClipboard from 'vue-clipboard2'
import axios from 'axios'
import VueAxios from 'vue-axios'
// CSS
import 'vue-instant/dist/vue-instant.css'
const moment = require('moment')
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

if ( locale !== 'en' ) {
    require(`moment/locale/${locale}`)
}

// Uses
Vue.use(ArgonDashboard)
Vue.use(BootstrapVue)
Vue.use(Notifications)
Vue.use(VeeValidate, { fieldsBagName: 'veeFields' })
Vue.use(i18n, translations)
Vue.use(VueScrollTo)
Vue.use(VueInstant)
Vue.use(VueMoment, { moment })
Vue.use(VueWait)
Vue.use(vueDebounce)
Vue.use(SweetModal)
Vue.use(ToggleButton)
Vue.use(BlockUI)
Vue.use(VueClipboard)
Vue.use(VueAxios, axios)
// Store
import store from './store'
// Layout
import AdminLayout from './Layout'
// filters
import './filters/global'

const app = new Vue({
    el: '#app',
    store,
    wait: new VueWait(),
    components: { AdminLayout }
});
