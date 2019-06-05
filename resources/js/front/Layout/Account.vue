<template>
    <v-container class="pa-0" fill-height fluid>
        <v-layout row wrap>
            <profile-navigation :current-view="currentView" @changeView="changeView" />
            <v-flex class="pa-3">
                <collapse-transition>
                    <component :is="currentView" key="component" />
                </collapse-transition>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import ProfileNavigation from './../components/Profile/Navigation'
    import ProfileForm from './../components/Profile/Form/'
    import ProfileMessages from './../components/Profile/Messages'
    import ProfileApps from './../components/Profile/Apps'

    export default {
        name: 'AccountLayout',
        components: {
            ProfileNavigation,
            ProfileForm,
            ProfileMessages,
            ProfileApps
        },
        data () {
            return {
                currentView: 'profile-form'
            }
        },
        methods: {
            changeView ( view ) {
                this.currentView = view
            }
        }
    }
</script>
