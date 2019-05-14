/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
// Resources
import Vuetify from 'vuetify'
import VeeValidate from 'vee-validate'
import axios from 'axios'
import VueAxios from 'vue-axios'
// CSS
import 'vuetify/dist/vuetify.min.css'
// Requires
const moment = require('moment')
if ( locale !== 'en' ) {
    require(`moment/locale/${locale}`)
}
// Uses
Vue.use(Vuetify)
Vue.use(VeeValidate, { fieldsBagName: 'veeFields' })
Vue.use(VueAxios, axios)
// Store
import store from './store'
// Layouts
import HomeLayout from './Layout/Home'
// Menu
import IndexMenu from './components/Menu'
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    store,
    components: {
        IndexMenu,
        HomeLayout
    }
});
