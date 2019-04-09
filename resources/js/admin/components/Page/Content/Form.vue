<template>
    <div>
        <b-row>
            <b-col cols="4" xs="6">
                <b-form-group :label="`${$t('Key', { locale: locale })}: *`">
                    <b-form-input
                        v-model="content.key"
                        name="key"
                        type="text"
                        v-validate
                        data-vv-rules="required|keyIsFree"
                        :class="{ 'is-invalid' : errors.has(`key`) }"
                    />
                    <div
                        v-if="errors.has(`key`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('key')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('key') }}
                    </div>
                </b-form-group>
            </b-col>
            <b-col cols="4" xs="6">
                <b-form-group :label="`${$t('ID HTML', { locale: locale })}: *`">
                    <b-form-input
                        v-model="content.id_html"
                        name="id_html"
                        type="text"
                        v-validate
                        data-vv-rules="required"
                        :class="{ 'is-invalid' : errors.has(`id_html`) }"
                    />
                    <div
                        v-if="errors.has(`id_html`)"
                        class="invalid-feedback">
                        <i
                            v-show="errors.has('id_html')"
                            class="fa fa-warning text-danger"
                        />
                        {{ errors.first('id_html') }}
                    </div>
                </b-form-group>
            </b-col>
            <b-col cols="4" xs="12">
                <b-form-group :label="`${$t('Class HTML', { locale: locale })}:`">
                    <b-form-input
                        v-model="content.class_html"
                        name="class_html"
                        type="text"
                    />
                </b-form-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col cols="12">
                <label>{{ $t('Content', { locale }) }}:</label>
                <codemirror
                    v-model="content.text"
                    :options="getCodemirrorConfig('html')"
                />
            </b-col>
        </b-row>
        <b-row class="mt-2">
            <b-col cols="6" xs="12">
                <label>{{ $t('CSS', { locale }) }}:</label>
                <codemirror
                    v-model="content.header_inject"
                    :options="getCodemirrorConfig('css')"
                />
            </b-col>
            <b-col cols="6" xs="12">
                <label>{{ $t('JS', { locale }) }}:</label>
                <codemirror
                    v-model="content.footer_inject"
                    :options="getCodemirrorConfig('js')"
                />
            </b-col>
        </b-row>
        <b-row v-if="showButtons">
            <b-col cols="12">
                <hr>
            </b-col>
            <b-col cols="6">
                <b-button
                    variant="danger"
                    sm
                    @click="$emit('onCloseModal')"
                >
                    <i class="fa fa-close" />
                    {{ $t('Cancel', { locale }) }}
                </b-button>
            </b-col>
            <b-col cols="6" class="text-right">
                <b-button
                    variant="success"
                    sm
                    @click="onSave"
                >
                    <i class="fa fa-save" />
                    {{ $t('Update', { locale }) }}
                </b-button>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import cloneMixin from './../../../mixins/clone'
    import { codemirror } from 'vue-codemirror'
    import codemirrorMixin from './../../../mixins/codemirror'
    import contentStructure from './../../../structures/content'
    import { Validator } from 'vee-validate'

    export default {
        name: 'ContentForm',
        props: {
            contentOrigin: {
                type: Object,
                required: false,
                default: null
            },
            index: {
                type: Number,
                required: false,
                default: null
            },
            keysInUse: {
                type: Array,
                required: false,
                default: Array
            },
            showButtons: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        components: { codemirror, Validator },
        mixins: [ cloneMixin, codemirrorMixin ],
        data () {
          return {
              content: this.contentOrigin !== null ? this.clone(this.contentOrigin) : this.clone(contentStructure)
          }
        },
        computed: {
            ...mapState([ 'locale' ]),
            keyInUse () {
                return this.keysInUse.some(item => item.key === this.content.key && item.index !== this.index)
            }
        },
        watch: {
            contentOrigin: {
                deep: true,
                handler () {
                    this.content = this.contentOrigin !== null ? this.clone(this.contentOrigin) : this.clone(contentStructure)
                }
            },
            keysInUse: {
                deep: true,
                handler () {
                    this.setValidateKeyIsFree()
                }
            }
        },
        methods: {
            async onSave () {
                this.setValidateKeyIsFree()

                if ( await this.$validator.validate() ) {
                    this.$emit('onSaveContent', { content: this.content, index: this.index })
                }
            },
            setValidateKeyIsFree () {
                const context = this

                Validator.extend('keyIsFree', {
                    getMessage: field => context.$t(`The ${field} already exists in this page`, context.locale),
                    validate: () => {
                        return !context.keyInUse
                    }
                })
            }
        },
        created () {
            this.setValidateKeyIsFree()
        }
    }
</script>
