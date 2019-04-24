<template>
    <div>
        <b-row>
            <b-col
                cols="2"
                xs="12">
            </b-col>
            <b-col
                cols="10"
                xs="12">
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: 'IndexUser',
        props: {
            data: {
                type: String,
                required: true
            }
        },
        data () {
            return {
                isLoaded: false,
                isBusy: false,
                role: JSON.parse(this.data).role,
                filters: {
                    banned: false,
                    text: ''
                },
                columns: [
                    {
                        key: 'avatar',
                        label: ''
                    },
                    {
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'name',
                        label: this.$t('Name', this.locale)
                    },
                    {
                        key: 'email',
                        label: this.$t('Email', this.locale)
                    },
                    {
                        key: 'roles',
                        label: this.$t('Roles', this.locale)
                    },
                    {
                        key: 'status',
                        label: this.$t('Status', this.locale)
                    },
                    {
                        key: 'actions',
                        label: ''
                    }
                ]
            }
        },
        methods: {

        }
    }
</script>
