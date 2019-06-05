<template>
        <transition name="bounceRight">
        <div>
            <BlockUI v-if="isVisibleBlockui" :message="messageBlockui">
                <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
            </BlockUI>
            <notifications
                group="notify"
                position="top right"
            />
            <b-row>
                <b-col cols="12">
                    <b-button
                        variant="primary"
                        size="sm"
                        @click="$emit('onGoToList')"
                    >
                        <i class="fa fa-chevron-left" />
                        {{ $t('Return to list', { locale }) }}
                    </b-button>
                    <hr />
                </b-col>
            </b-row>
            <form-buttons
                :show-cancel="false"
                @onSave="onSave"
            />
            <b-row>
                <b-col lg="2" sm="6">
                    <b-form-group :label="`Vue ${$t('component', { locale: locale })}: *`">
                        <b-form-input
                            v-model="app.vue_component"
                            name="vue_component"
                            type="text"
                            v-validate
                            data-vv-rules="required"
                            :class="{ 'is-invalid' : errors.has(`vue_component`) }"
                        />
                        <div
                            v-if="errors.has(`vue_component`)"
                            class="invalid-feedback">
                            <i
                                v-show="errors.has('vue_component')"
                                class="fa fa-warning text-danger"
                            />
                            {{ errors.first('vue_component') }}
                        </div>
                    </b-form-group>
                </b-col>
                <b-col lg="2" sm="6">
                    <b-form-group :label="`${$t('Version', { locale: locale })}: *`">
                        <b-form-input
                            v-model="app.version"
                            name="version"
                            type="text"
                            v-validate
                            data-vv-rules="required"
                            :class="{ 'is-invalid' : errors.has(`version`) }"
                        />
                        <div
                            v-if="errors.has(`version`)"
                            class="invalid-feedback">
                            <i
                                v-show="errors.has('version')"
                                class="fa fa-warning text-danger"
                            />
                            {{ errors.first('version') }}
                        </div>
                    </b-form-group>
                </b-col>
                <b-col lg="2" sm="12">
                    <b-form-group :label="`${$t('Page', { locale: locale })}: *`">
                        <b-form-select
                            name="page"
                            v-model="app.page_id"
                            v-validate
                            data-vv-rules="required"
                            :class="{ 'is-invalid' : errors.has(`page`) }"
                        >
                            <option
                                v-for="page in pagesAvailable"
                                v-if="page.locales.length > 0"
                                :key="page.id"
                                :value="page.id"
                            >
                                {{ getPageLocale(page).title }}
                            </option>
                        </b-form-select>
                    </b-form-group>
                </b-col>
                <b-col lg="3" sm="12">
                    <b-form-group :label="`${$t('Type', { locale: locale })}: *`">
                        <b-form-select
                            name="type"
                            v-model="app.type"
                            v-validate
                            data-vv-rules="required"
                            :class="{ 'is-invalid' : errors.has(`type`) }"
                        >
                            <template slot="first">
                                <option
                                    value=""
                                    disabled
                                    :selected="app.type === null"
                                >
                                    {{ $t('Select type', { locale }) }}
                                </option>
                            </template>
                            <option
                                v-for="(type, index) in typesOptions"
                                :key="index"
                                :value="type.key"
                            >
                                {{ $t(type.key, { locale }) }}
                            </option>
                        </b-form-select>
                    </b-form-group>
                </b-col>
                <b-col lg="3" sm="12">
                    <b-form-group :label="`${$t('Status', { locale: locale })}: *`">
                        <b-form-select
                            name="status"
                            v-model="app.status"
                            v-validate
                            data-vv-rules="required"
                            :class="{ 'is-invalid' : errors.has(`status`) }"
                        >
                            <template slot="first">
                                <option
                                    value=""
                                    disabled
                                    :selected="app.status === null"
                                >
                                    {{ $t('Select status', { locale }) }}
                                </option>
                            </template>
                            <option
                                v-for="(status, index) in statusOptions"
                                :key="index"
                                :value="status.key"
                            >
                                {{ $t(status.key, { locale }) }}
                            </option>
                        </b-form-select>
                    </b-form-group>
                </b-col>
                <b-col cols="12">
                    <hr>
                    <h5>{{ $t('Translates', { locale }) }}</h5>
                </b-col>
                <b-col cols="12">
                    <multilanguage-tab
                        ref="multilanguageTab"
                        :languages-default="languagesAvailable"
                        :show-enable-icons="false"
                        :default-active="locale"
                    >
                        <template slot-scope="{ language }">
                            <form-app-locale
                                :ref="`localeForm${language.iso}`"
                                :language="language"
                                :app-locale-origin="getAppLocale(language)"
                            />
                        </template>
                    </multilanguage-tab>
                </b-col>
                <b-col cols="12">
                    <hr>
                    <h5>{{ $t('Images', { locale }) }}</h5>
                </b-col>
                <b-col cols="12">
                    <form-app-images
                        ref="formAppImages"
                        :images-origin="app.images"
                        :languages-available="languagesAvailable"
                    />
                </b-col>
            </b-row>
            <b-row>
                <b-col cols="12">
                    <hr>
                </b-col>
            </b-row>
            <form-buttons
                @onSave="onSave"
                @onCancel="$emit('onGoToList')"
            />
        </div>
    </transition>
</template>

<script>
    import { mapState, mapActions }  from 'vuex'
    import MultilanguageTab from './../../Multilanguage/Tab'
    import FormAppLocale from './Locale'
    import FormAppImages from './Images'
    import FormButtons from './../../FormResources/Buttons'
    import cloneMixin from './../../../../includes/mixins/clone'
    import { appLocaleStructure } from './../../../structures/app'

    export default {
        name: 'FormApp',
        props: {
            data: {
                type: String,
                required: true
            },
            appOrigin: {
                type: Object,
                required: true
            },
            pages: {
                type: Array,
                required: false,
                default: Array
            }
        },
        data () {
            return {
                languagesAvailable: JSON.parse(this.data).langsAvailable,
                typesOptions: JSON.parse(this.data).types,
                statusOptions: JSON.parse(this.data).status,
                app: this.clone(this.appOrigin)
            }
        },
        components: { MultilanguageTab, FormAppLocale, FormAppImages, FormButtons },
        mixins: [ cloneMixin ],
        computed: {
            ...mapState([ 'locale', 'routes' ]),
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            isNew () {
                return this.app.id === null
            },
            pagesAvailable () {
                return this.pages.filter(page => page.app === null || page.app.id === this.app.id)
            }
        },
        methods: {
            ...mapActions({
                toogleBlockui : 'blockui/toggleIsVisible'
            }),
            // Events
            async onSave () {
                this.toogleBlockui(true)

                const validateData = await this.$validator.validate()
                const { isValid } = await this.getValidateLocales()
                const validateImages = await this.$refs.formAppImages.getIsValid()

                if ( validateData && isValid && validateImages ) {
                    this.processForm()
                } else {
                    this.toogleBlockui(false)
                    this.$notify({
                        group: 'notify',
                        title: this.$t('Save page', { locale: this.locale }),
                        text: this.$t('App form is not valid', { locale: this.locale }),
                        type: 'error',
                        config: {
                            closeOnClick: true
                        }
                    })
                }
            },
            // Actions
            async processForm () {
                let locales = []

                for ( const language of this.languagesAvailable ) {
                    locales.push(this.$refs[`localeForm${language.iso}`].appLocale)
                }

                this.app.locales = locales
                this.app.images = this.$refs.formAppImages.images

                await this.storeAppRequest(this.app)
                this.successForm()
            },
            successForm () {
                this.toogleBlockui(false)
                this.$emit('onSaveApp', { isNew: this.isNew })
            },
            // Getters
            getAppLocale ( language ) {
                let appLocale = this.clone(appLocaleStructure)

                this.appOrigin.locales.some(localeOrigin => {
                    const scapeCondition = localeOrigin.lang === language.iso

                    if ( scapeCondition ) {
                        appLocale = localeOrigin
                    }

                    return scapeCondition
                })

                appLocale.lang = language.iso

                return appLocale
            },
            async getValidateLocales () {
                const defaultLanguageSelected = this.$refs.multilanguageTab.getCurrentLanguage()
                let firstNotValid = null

                for ( const language of this.languagesAvailable ) {
                    this.$refs.multilanguageTab.changeLanguageTab(language)

                    if ( !await this.$refs[`localeForm${language.iso}`].getIsValid() && firstNotValid === null ) {
                        firstNotValid = language
                    }
                }

                this.$refs.multilanguageTab.changeLanguageTab(firstNotValid === null ? defaultLanguageSelected : firstNotValid)

                return { isValid: firstNotValid === null, firstNotValid }
            },
            getPageLocale ( page ) {
                let locale = null

                for ( const item of page.locales ) {
                    if ( locale === null || item.lang === this.locale ) {
                        locale = item
                    }
                }

                return locale
            },
            // API Request
            async storeAppRequest ( app ) {
                const { data } = await this.axios.post(`${this.routes.storeApp}`, app)
                return data
            }
        }
    }
</script>
