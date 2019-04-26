<template>
    <div class="main-content bg-default">
        <auth-navbar
            v-if="showMenu"
            menu-data="menuData"
        />
        <!-- Header -->
        <div class="header bg-gradient-success py-7 py-lg-8">
            <div class="container">
                <div class="header-body text-center mb-7">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-6">
                            <h1 class="text-white">{{ $t('Welcome!', { locale }) }}</h1>
                            <p class="text-lead text-white">
                                {{ $t(slogan, { locale }) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator separator-bottom separator-skew zindex-100">
                <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1"
                     xmlns="http://www.w3.org/2000/svg">
                    <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </div>
        <div class="container mt--9 pb-5">
            <slide-y-up-transition mode="out-in" origin="center top">
                <component :is="component" :data="componentData"/>
            </slide-y-up-transition>
        </div>
        <footer class="py-7">
            <div class="container">
                <div class="row align-items-center justify-content-xl-between">
                    <div class="col-xl-6">
                        <div class="copyright text-center text-xl-left text-muted">
                            &copy; {{ yearsCopyright }} <b>ERFWeb</b>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <ul class="nav nav-footer justify-content-center justify-content-xl-end">
                            <li class="nav-item">
                                <a :href="links.github" class="nav-link" target="_blank">
                                    <i class="fa fa-github" aria-hidden="true" />
                                    Github
                                </a>
                            </li>
                            <li class="nav-item">
                                <a :href="links.linkedin" class="nav-link" target="_blank">
                                    <i class="fa fa-linkedin" aria-hidden="true" />
                                    Linkedin
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import { SlideYUpTransition } from 'vue2-transitions'
    import AuthNavbar from './components/Navbar'
    import Login from './components/Login'
    import Register from './components/Register'
    import ResetEmail from './components/ResetEmail'
    import ResetPassword from './components/ResetPassword'

    export default {
        name: 'AuthLayout',
        props: {
            component: {
                type: String,
                required: true
            },
            componentData: {
                type: String,
                required: false,
                default: null
            },
            showMenu: {
                type: Boolean,
                required: false,
                default: true
            },
            menuData: {
                type: Array,
                required: false,
                default: Array
            }
        },
        components: { SlideYUpTransition, AuthNavbar, Login, Register, ResetEmail, ResetPassword },
        data () {
            return {
                links: links
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            yearsCopyright () {
                const originYear = 2019
                const currentYear = new Date().getFullYear()

                return currentYear > originYear ? `${originYear} / ${currentYear}` : currentYear
            },
            slogan () {
                if ( this.component === 'login' ) {
                    return 'Identify if we already know each other'
                } else if ( this.component === 'register' ) {
                    return 'You and I don\t know each other ... yet ;)'
                } else {
                    return 'Don\'t worry, with age things are forgotten'
                }
            }
        }
    }
</script>
