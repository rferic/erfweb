<template>
    <div>
        <h1 class="headline ma-2 mb-4">
            {{ $vuetify.t('Your data')}}
        </h1>
        <v-layout row wrap>
            <v-flex xs12 sm12 md6 lg6>
                <profile-form-base />
            </v-flex>
            <v-flex xs12 sm12 md6 lg6>
                <profile-form-password />
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
    import ProfileFormBase from './Base'
    import ProfileFormPassword from './Password'

    export default {
        name: 'ProfileForm',
        components: { ProfileFormBase, ProfileFormPassword }
    }
</script>
