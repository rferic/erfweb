
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
import VueWait from 'vue-wait'
import vueDebounce from 'vue-debounce'
import SweetModal from 'sweet-modal-vue/src/plugin.js'
import ToggleButton from 'vue-js-toggle-button'
import BlockUI from 'vue-blockui'
import VueClipboard from 'vue-clipboard2'
// CSS
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'vue-instant/dist/vue-instant.css'
const moment = require('moment')
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

if ( locale !== 'en' ) {
    require(`moment/locale/${locale}`)
}

// Uses
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
// Store
import store from './store'
// Components
import NavIndex from './components/Nav/Index'
import NavRight from './components/Nav/Right'
import SidebarIndex from './components/Sidebar/Index'
import NavMessageNotify from './components/Nav/MessageNotify'
import IndexDashboard from './components/Dashboard/Index'
import IndexProfile from './components/Profile/Index'
import IndexMessage from './components/Message/Index'
import IndexPage from './components/Page/Index'
import IndexApp from './components/App/Index'
import IndexImage from './components/Image/Index'
import IndexMenu from './components/Menu/Index'
import IndexRedirection from './components/Redirection/Index'
// filters
import './filters/global'

const app = new Vue({
    el: '#app',
    store,
    wait: new VueWait(),
    components: {
        SidebarIndex,
        NavIndex,
        NavRight,
        NavMessageNotify,
        IndexDashboard,
        IndexProfile,
        IndexMessage,
        IndexPage,
        IndexApp,
        IndexImage,
        IndexMenu,
        IndexRedirection
    }
});
