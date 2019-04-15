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
                <b-col cols="12">
                    <multilanguage-tab
                        ref="multilanguageTab"
                        :languages-default="languagesAvailable"
                        :show-enable-icons="true"
                        :default-active="locale"
                    >
                        <template slot-scope="{ language }">
                            <locale-form
                                :ref="`localeForm${language.iso}`"
                                :language="language"
                                :page-locale-origin="getPageLocale(language)"
                                :layouts="layouts"
                                @onChangeEnable="onChangeEnable"
                            />
                        </template>
                    </multilanguage-tab>
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
    import MultilanguageTab from './../Multilanguage/Tab'
    import LocaleForm from './Locale/Form'
    import FormButtons from '../FormResources/Buttons'
    import pageLocaleStructure from './../../structures/pageLocale'
    import cloneMixin from './../../mixins/clone'
    import pageMixin from './../../mixins/page'

    export default {
        name: 'FormPage',
        props: {
            data: {
                type: String,
                required: true
            },
            pageOrigin: {
                type: Object,
                required: true
            }
        },
        components: { MultilanguageTab, LocaleForm, FormButtons },
        mixins: [ cloneMixin, pageMixin ],
        data () {
            return {
                languagesAvailable: JSON.parse(this.data).langsAvailable,
                layouts: JSON.parse(this.data).layouts
            }
        },
        computed: {
            ...mapState([ 'locale' ]),
            ...mapState({
                isVisibleBlockui: state => state.blockui.isVisible,
                messageBlockui: state => state.blockui.message
            }),
            isNew () {
                return this.pageOrigin.id === null
            }
        },
        methods: {
            ...mapActions({
                toogleBlockui : 'blockui/toggleIsVisible'
            }),
            // Events
            onChangeEnable ({ enable, language }) {
                this.$refs.multilanguageTab.toggleLanguage({ enable, language })
            },
            async onSave () {
                this.toogleBlockui(true)

                const { isValid, anyEnable } = await this.getIsValid()

                if ( isValid ) {
                    await this.processForm()
                    this.successForm()
                } else {
                    this.toogleBlockui(false)
                    this.$notify({
                        group: 'notify',
                        title: this.$t('Save page', { locale: this.locale }),
                        text: this.$t(anyEnable ? 'Page form is not valid' : 'Require has any language', { locale: this.locale }),
                        type: 'error',
                        config: {
                            closeOnClick: true
                        }
                    })
                }
            },
            // Actions
            async processForm () {
                let page = {
                    id: this.pageOrigin.id,
                    locales: []
                }

                for ( const language of this.languagesAvailable ) {
                    page.locales.push(this.$refs[`localeForm${language.iso}`].pageLocale)
                }

                await this.storePageRequest(page)
            },
            successForm () {
                this.toogleBlockui(false)
                this.$emit('onSavePage', { isNew: this.isNew })
            },
            // Getters
            getPageLocale ( language ) {
                let pageLocale = this.clone(pageLocaleStructure)

                this.pageOrigin.locales.some(localeOrigin => {
                    const scapeCondition = localeOrigin.lang === language.iso

                    if ( scapeCondition ) {
                        pageLocale = localeOrigin
                    }

                    return scapeCondition
                })

                pageLocale.lang = language.iso
                pageLocale.contents = this.getContentsToPageLocale(pageLocale)

                return pageLocale
            },
            getContentsToPageLocale ( pageLocale ) {
                return pageLocale.id !== null
                    ? this.pageOrigin.contents.filter(content => content.page_locale_id === pageLocale.id)
                    : []
            },
            async getIsValid () {
                const defaultLanguageSelected = this.$refs.multilanguageTab.getCurrentLanguage()
                const anyEnable = this.$refs.multilanguageTab.getAnyEnable()
                let firstNotValid = null

                for ( const language of this.languagesAvailable ) {
                    this.$refs.multilanguageTab.changeLanguageTab(language)

                    if ( !await this.$refs[`localeForm${language.iso}`].getIsValid() && firstNotValid === null ) {
                        firstNotValid = language
                    }
                }

                this.$refs.multilanguageTab.changeLanguageTab(firstNotValid === null ? defaultLanguageSelected : firstNotValid)

                return { isValid: firstNotValid === null && anyEnable, anyEnable, firstNotValid }
            }
        },
        created () {
            for ( let languageAvailable of this.languagesAvailable ) {
                languageAvailable.enable = this.pageOrigin.locales.some(locale => locale.lang === languageAvailable.iso && locale.deleted_at === null)
            }
        }
    }
</script>
