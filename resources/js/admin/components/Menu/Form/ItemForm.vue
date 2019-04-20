<template>
    <div>
        <b-row>
            <b-col cols="6" xs="12">
                <b-form-group :label="`${$t('Text', { locale: locale })}: *`">
                    <b-form-input
                        v-model="item.label"
                        name="label"
                        type="text"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`label`) }"
                    />
                    <div
                        v-if="errors.has(`label`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('label')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('label') }}
                    </div>
                </b-form-group>
            </b-col>
            <b-col cols="6" xs="12">
                <b-form-group :label="`${$t('Type', { locale: locale })}: *`">
                    <b-form-select
                        name="type"
                        v-model="item.type"
                        :options="types"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`type`) }"
                    />
                    <div
                        v-if="errors.has(`type`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('type')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('type') }}
                    </div>
                </b-form-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col v-if="item.type === 'external'" cols="12">
                <b-form-group :label="`${$t('URL', { locale: locale })}: *`">
                    <b-form-input
                        v-model="item.url_external"
                        name="url_external"
                        type="text"
                        v-validate
                        data-vv-rules="required|url: {require_protocol: true }"
                        :class="{ 'is-invalid' : errors.has(`url_external`) }"
                    />
                    <div
                        v-if="errors.has(`url_external`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('url_external')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('url_external') }}
                    </div>
                </b-form-group>
            </b-col>
            <b-col v-if="item.type === 'internal'" cols="12">
                <b-form-group :label="`${$t('Page', { locale: locale })}: *`">
                    <b-form-select
                        name="page_locale_id"
                        v-model="item.page_locale_id"
                        :options="pageLocalesOptions"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`page_locale_id`) }"
                    />
                    <div
                        v-if="errors.has(`page_locale_id`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('page_locale_id')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('page_locale_id') }}
                    </div>
                </b-form-group>
            </b-col>
        </b-row>
        <form-buttons
            @onSave="onSave"
            @onCancel="$emit('onCancelForm')"
        />
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import FormButtons from './../../FormResources/Buttons'
    import cloneMixin from './../../../mixins/clone'

    export default {
        name: 'ItemFormMenu',
        props: {
            index: {
                type: Number,
                required: false,
                default: null
            },
            language: {
                type: Object,
                required: true
            },
            itemOrigin: {
                type: Object,
                required: false,
                default: null
            }
        },
        components: { FormButtons },
        mixins: [ cloneMixin ],
        data () {
             return {
                 item: this.clone(this.itemOrigin),
                 types: [
                     {
                         value: 'internal',
                         text: this.$t('Internal', { locale: this.locale })
                     },
                     {
                         value: 'external',
                         text: this.$t('External', { locale: this.locale })
                     }
                 ],
                 pageLocales: []
             }
        },
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            hasPageLocales () {
                return this.pageLocales.length > 0
            },
            pageLocalesOptions () {
                let options = []

                for ( const pageLocale of this.pageLocales ) {
                    options.push({
                        value: pageLocale.id,
                        text: pageLocale.title
                    })
                }

                return options
            }
        },
        watch: {
            itemOrigin () {
                this.item = this.clone(this.itemOrigin)
                this.item.lang = this.language.iso
            }
        },
        methods: {
            // Events
            async onSave () {
                if ( await this.$validator.validate() ) {
                    this.item.lang = this.language.iso
                    this.$emit('onSaveItem', {
                        index: this.index,
                        item: this.clone(this.item)
                    })
                }
            },
            // Getters
            async getPageLocales () {
                this.pageLocales = await this.getPageLocalesRequest({
                    filters: {
                        'langs': [this.language.iso]
                    }
                })
            },
            // API Request
            async getPageLocalesRequest ({ filters }) {
                const { data } = await axios.post(this.routes.getPageLocales, { filters })
                return data
            }
        },
        mounted () {
            this.getPageLocales()
        }
    }
</script>
