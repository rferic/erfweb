import VueMoment from "vue-moment";

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
// Resources
import Vuetify from 'vuetify'
import en from './i18n/en'
import es from './i18n/es'
import ca from './i18n/ca'
import VeeValidate from 'vee-validate'
import axios from 'axios'
import VueAxios from 'vue-axios'
import vueDebounce from 'vue-debounce'
// CSS
import 'vuetify/dist/vuetify.min.css'
import '@mdi/font/css/materialdesignicons.css'
// Requires
const moment = require('moment')
if ( locale !== 'en' ) {
    require(`moment/locale/${locale}`)
}
// Uses
Vue.use(Vuetify, {
    iconfont: 'mdi',
    lang: {
        locales: {en, es, ca},
        current: locale
    }
})
Vue.use(VeeValidate, { fieldsBagName: 'veeFields' })
Vue.use(VueAxios, axios)
Vue.use(VueMoment, { moment })
Vue.use(vueDebounce)
// Store
import store from './store'
// Layouts
import HomeLayout from './Layout/Home'
import WhoIAmLayout from './Layout/WhoIAm'
// Menu
import ToolbarMenu from './components/Menu/Toolbar'
import MobileMenu from './components/Menu/Mobile'
import IndexFooter from './components/Footer'
// Auth
import AuthIndex from './components/Auth/'
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data: () => ({
        drawer: false
    }),
    props: {
        source: String
    },
    store,
    components: {
        ToolbarMenu,
        MobileMenu,
        IndexFooter,
        AuthIndex,
        HomeLayout,
        WhoIAmLayout
    }
});
