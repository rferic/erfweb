<template>
    <div>
        <b-row>
            <b-col cols="12">
                <b-form-checkbox
                    switch
                    v-model="enable"
                    @change="$emit('onChangeEnable', { enable: !enable, language })"
                >
                    {{ $t(enable ? 'Enable' : 'Disable', { locale }) }}
                </b-form-checkbox>
            </b-col>
        </b-row>
        <hr>
        <div class="overflow-hidden">
            <transition
                enter-active-class="slideInDown"
                leave-active-class="slideOutUp"
            >
                <b-row v-if="enable">
                    <b-col lg="6" sm="12">
                        <b-form-group :label="`${$t('Title', { locale: locale })}: *`">
                            <b-form-input
                                v-model="pageLocale.title"
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
                    <b-col
                        lg="6"
                        sm="12"
                    >
                        <slug-input
                            ref="slug"
                            type="pageLocale"
                            :parent-id="pageLocale.id"
                            :slug-default="pageLocale.slug"
                            :text-sluggable="pageLocale.title"
                            :language="language"
                            @onChangeSlug="onChangeSlug"
                        />
                        <b-alert :show="isHome" variant="primary">
                            {{ $t('Require a slug if in the future disable home attribute', { locale }) }}
                        </b-alert>
                    </b-col>
                    <b-col cols="12">
                        <b-form-group
                            class="mt-3"
                            :label="`${$t('Description', { locale })}:`"
                            label-for="text">
                            <b-form-textarea
                                name="description"
                                v-model="pageLocale.description"
                                :rows="2"
                            />
                        </b-form-group>
                    </b-col>
                    <b-col cols="12">
                        <b-form-group :label="`${$t('SEO Title', { locale: locale })}:`">
                            <b-form-input
                                v-model="pageLocale.seo_title"
                                name="seo_title"
                                type="text"
                            />
                        </b-form-group>
                    </b-col>
                    <b-col cols="12">
                        <b-form-group
                            class="mt-3"
                            :label="`${$t('SEO Description', { locale })}:`"
                            label-for="text">
                            <b-form-textarea
                                name="seo_description"
                                v-model="pageLocale.seo_description"
                                :rows="2"
                            />
                        </b-form-group>
                    </b-col>
                    <b-col cols="12">
                        <label>{{ $t('SEO Keywords', { locale } ) }}:</label>
                        <input-tag v-model="keywords" class="input-tags input-tags-primary" />
                    </b-col>
                    <b-col lg="6">
                        <b-form-group :label="`${$t('Layout', { locale: locale })}: *`">
                            <b-form-select
                                name="layout"
                                v-model="pageLocale.layout"
                                v-validate
                                data-vv-rules="required"
                                :class="{ 'is-invalid' : errors.has(`layout`) }"
                                @change="refreshOptionsDefault"
                            >
                                <template slot="first">
                                    <option
                                        value=""
                                        disabled
                                        :selected="pageLocale.layout === null"
                                    >
                                        {{ $t('Select layout', { locale }) }}
                                    </option>
                                </template>
                                <option
                                    v-for="(layout, index) in layouts"
                                    :key="index"
                                    :value="layout.key"
                                >
                                    {{ $t(layout.title, { locale }) }}
                                </option>
                            </b-form-select>
                        </b-form-group>
                    </b-col>
                    <b-col lg="6">
                        <b-form-group :label="`${$t('Container width', { locale: locale })}: *`">
                            <b-form-select
                                name="width"
                                v-model="options.width"
                                v-validate
                                data-vv-rules="required"
                                :class="{ 'is-invalid' : errors.has(`width`) }"
                                @input="refreshOptionsPageLocale"
                            >
                                <option
                                    v-for="(item, index) in currentLayout.options.width"
                                    :key="index"
                                    :value="item"
                                >
                                    {{ $t(item, { locale }) }}
                                </option>
                            </b-form-select>
                        </b-form-group>
                    </b-col>
                    <b-col cols="12">
                        <hr>
                        <h5>{{ $t('Content', { locale }) }}</h5>
                    </b-col>
                    <b-col cols="12">
                        <content-list
                            :contents-origin="pageLocale.contents"
                            :drag-and-drop="true"
                            @onChangeInContents="onChangeInContents"
                        />
                    </b-col>
                    <b-col cols="12">
                        <hr>
                        <h5>{{ $t('Inject in page', { locale }) }}</h5>
                    </b-col>
                    <b-col cols="12">
                        <b-tabs
                            pills
                            card
                            vertical
                            class="tabs-pills"
                        >
                            <b-tab
                                v-for="language in currentLayout.options.inject"
                                :key="language"
                            >
                                <template slot="title">
                                    {{ language }}
                                </template>
                                <div>
                                    <codemirror
                                        v-model="options.inject[language]"
                                        :options="getCodemirrorConfig(language)"
                                        @input="refreshOptionsPageLocale"
                                    />
                                </div>
                            </b-tab>
                        </b-tabs>
                    </b-col>
                </b-row>
            </transition>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import SlugInput from './../../../components/Slug/Input'
    import ContentList from './../Content/List'
    import { codemirror } from 'vue-codemirror'
    import cloneMixin from './../../../../includes/mixins/clone'
    import codemirrorMixin from './../../../mixins/codemirror'
    import InputTag from 'vue-input-tag'

    export default {
        name: 'LocaleForm',
        props: {
            language: {
                type: Object,
                required: true
            },
            pageLocaleOrigin: {
                type: Object,
                required: true
            },
            layouts: {
                type: Array,
                required: true
            },
            isHome: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        components: { codemirror, SlugInput, ContentList, InputTag },
        mixins: [ cloneMixin, codemirrorMixin ],
        data () {
            return {
                pageLocale: this.clone(this.pageLocaleOrigin),
                enable: this.pageLocaleOrigin.id !== null,
                options: {
                    width: null,
                    inject: {}
                },
                currentLayout: null,
                keywords: []
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            transitionToggle () {
                return this.enable ? 'slideDown' : 'slideDown'
            }
        },
        watch: {
            enable () {
                this.refreshOptionsDefault()
            },
            keywords () {
                this.pageLocale.seo_keywords = JSON.stringify(this.keywords)
            }
        },
        methods: {
            // Events
            onChangeInContents ( contents ) {
                this.pageLocale.contents = this.clone(contents)
            },
            onChangeSlug ( slug ) {
                this.pageLocale.slug = slug
            },
            // Actions
            refreshOptionsDefault () {
                this.pageLocale.deleted_at = this.enable ? null : Vue.moment().locale(this.locale).format('YYYY-MM-DD HH:mm:ss')

                if ( this.enable ) {
                    this.keywords = typeof this.pageLocale.seo_keywords === 'string'
                        ? JSON.parse(this.pageLocale.seo_keywords)
                        : this.pageLocale.seo_keywords

                    const optionsDefault = JSON.parse(this.pageLocale.options)
                    this.refreshCurrentLayout()

                    this.options.width = optionsDefault !== null && typeof optionsDefault.width !== undefined
                        ? optionsDefault.width
                        : this.clone(this.currentLayout.options.width[0])

                    for ( const language of this.currentLayout.options.inject ) {
                        this.options.inject[language] = optionsDefault !== null && typeof optionsDefault.inject[language] !== undefined
                            ? optionsDefault.inject[language]
                            : ''
                    }
                }
            },
            refreshOptionsPageLocale () {
                console.log(1)
                this.pageLocale.options = JSON.stringify(this.clone(this.options))
            },
            refreshCurrentLayout () {
                this.currentLayout = this.pageLocale.layout !== ''
                    ? this.layouts.filter(layout => layout.key === this.pageLocale.layout)[0]
                    : this.layouts[0]
            },
            // Getters
            async getIsValid () {
                return await this.$validator.validate()
            }
        },
        created () {
            this.refreshOptionsDefault()
        }
    }
</script>
