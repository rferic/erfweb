<template>
    <div>
        <b-row>
            <b-col cols="4" xs="12">
                <b-form-group :label="`${$t('Code', { locale: locale })}: *`">
                    <b-form-select
                        name="code"
                        v-model="code"
                        :options="codes"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`code`) }"
                    />
                    <div
                        v-if="errors.has(`code`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('code')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('code') }}
                    </div>
                </b-form-group>
            </b-col>
            <b-col cols="4" xs="12">
                <b-form-group :label="`${$t('Origin', { locale: locale })}: *`">
                    <b-form-input
                        v-model="slug_origin"
                        name="slug_origin"
                        type="text"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`slug_origin`) }"
                    />
                    <div
                        v-if="errors.has(`slug_origin`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('slug_origin')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('slug_origin') }}
                    </div>
                </b-form-group>
            </b-col>
            <b-col cols="4" xs="12">
                <b-form-group :label="`${$t('Destine', { locale: locale })}: *`">
                    <b-form-input
                        v-model="slug_destine"
                        name="slug_destine"
                        type="text"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`slug_destine`) }"
                    />
                    <div
                        v-if="errors.has(`slug_destine`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('slug_destine')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('slug_destine') }}
                    </div>
                </b-form-group>
            </b-col>
        </b-row>
        <form-buttons
            @onSave="onSave"
            @onCancel="$emit('onClose')"
        />
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import FormButtons from './../FormResources/Buttons'
    import redirectionMixin from './../../mixins/redirection'

    export default {
        name: 'FormRedirection',
        components: { FormButtons },
        mixins: [ redirectionMixin ],
        data () {
            return {
                code: '',
                slug_origin: '',
                slug_destine: ''
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        },
        methods: {
            // Events
            async onSave () {
                if ( await this.$validator.validate() ) {
                    await this.processForm()
                    this.successForm()
                }
            },
            // Actions
            clearData () {
                const errors = this.errors
                this.code = ''
                this.slug_origin = ''
                this.slug_destine = ''

                setTimeout(() => {
                    errors.clear()
                }, 100)
            },
            async processForm () {
                await this.createRedirectionRequest({
                    code: this.code,
                    slug_origin: this.slug_origin,
                    slug_destine: this.slug_destine
                })
            },
            successForm () {
                this.$emit('onCreate')
            }
        }
    }
</script>
