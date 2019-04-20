<template>
    <b-row>
        <b-col cols="6" xs="12">
            <b-form-group :label="`${$t('Title', { locale: locale })}: *`">
                <b-form-input
                    v-model="appLocale.title"
                    name="title"
                    type="text"
                    v-validate
                    data-vv-rules="required"
                    :class="{ 'is-invalid' : errors.has(`title`) }"
                />
                <div
                    v-if="errors.has(`title`)"
                    class="invalid-feedback">
                    <i
                        v-show="errors.has('title')"
                        class="fa fa-warning text-danger"
                    />
                    {{ errors.first('title') }}
                </div>
            </b-form-group>
        </b-col>
        <b-col cols="12">
            <b-form-group
                class="mt-3"
                :label="`${$t('Description', { locale })}:`"
                label-for="text">
                <b-form-textarea
                    name="description"
                    v-model="appLocale.description"
                    :rows="4"
                />
            </b-form-group>
        </b-col>
    </b-row>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../../mixins/clone'

    export default {
        name: 'FormAppLocale',
        props: {
            language: {
                type: Object,
                required: true
            },
            appLocaleOrigin: {
                type: Object,
                required: true
            },
        },
        mixins: [ cloneMixin ],
        data () {
            return {
                appLocale: this.clone(this.appLocaleOrigin)
            }
        },
        computed: {
            ...mapState(['locale'])
        },
        methods: {
            async getIsValid () {
                return await this.$validator.validate()
            }
        }
    }
</script>
