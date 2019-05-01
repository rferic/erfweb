<template>
    <main id="main-wrapper" :class="{ 'nav-open': showSidebar }">
        <sidebar-index />
        <div class="main-content" :data="sidebarBackground">
            <base-nav class="navbar-top navbar-dark" id="navbar-main" :show-toggle-button="false" expand>
                <span class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block active router-link-active">
                    {{ titlePage }}
                </span>
                <ul class="navbar-nav align-items-center d-none d-md-flex">
                    <nav-index />
                </ul>
            </base-nav>
            <div>
                <fade-transition :duration="200" origin="center top" mode="out-in">
                    <div>
                        <base-header type="gradient-success" class="pb-6 pb-8 pt-5 pt-md-8">
                            <!-- Mask -->
                            <span class="mask bg-gradient-success opacity-8"></span>
                            <!-- Header container -->
                            <div class="container-fluid d-flex align-items-center">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <h1 class="display-2 text-white">
                                            {{ titlePage }}
                                        </h1>
                                        <p v-if="descriptionPage !== ''" class="text-white mt-0 mb-5">
                                            {{ descriptionPage }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </base-header>
                        <div class="container-fluid mt--7">
                            <component :is="component" :data="data" />
                        </div>
                    </div>
                </fade-transition>
            </div>
        </div>
    </main>
</template>

<script>
    import { FadeTransition } from 'vue2-transitions'
    import NavIndex from './components/Nav/Index'
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
    import IndexUser from './components/User/Index'

    export default {
        name: 'AdminLayout',
        props: {
            component: {
                type: String,
                required: true
            },
            titlePage: {
                type: String,
                required: false,
                default: ''
            },
            descriptionPage: {
                type: String,
                required: false,
                default: ''
            },
            data: {
                type: String,
                required: false,
                default: ''
            }
        },
        components: {
            FadeTransition,
            SidebarIndex,
            NavIndex,
            NavMessageNotify,
            IndexDashboard,
            IndexProfile,
            IndexMessage,
            IndexPage,
            IndexApp,
            IndexImage,
            IndexMenu,
            IndexRedirection,
            IndexUser
        },
        data () {
            return {
                sidebarBackground: 'vue', //vue|blue|orange|green|red|primary
                showSidebar: false
            }
        }
    }
</script>
