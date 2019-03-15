<template>
    <transition name="bounceRight">
        <div>
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
            <b-row>
                <b-col cols="12">
                    <multilanguage-tab
                        :languages="languagesAvailable"
                        :show-enable-icons="true"
                        :default-active="locale"
                    >
                        <template slot-scope="{ language }">
                            <locale-form
                                :language="language"
                                :page-locale-origin="getPageLocale(language)"
                                :layouts="layouts"
                            />
                        </template>
                    </multilanguage-tab>
                </b-col>
            </b-row>
        </div>
    </transition>
</template>

<script>
    import { mapState }  from 'vuex'
    import MultilanguageTab from './../Multilanguage/Tab'
    import LocaleForm from './Locale/Form'
    import pageLocaleStructure from './../../structures/pageLocale'
    import cloneMixin from './../../mixins/clone'

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
        components: { MultilanguageTab, LocaleForm },
        mixins: [ cloneMixin ],
        data () {
            return {
                languagesAvailable: JSON.parse(this.data).langsAvailable,
                layouts: JSON.parse(this.data).layouts
            }
        },
        computed: {
            ...mapState([ 'locale' ])
        },
        methods: {
            getPageLocale ( language ) {
                let locale = this.clone(pageLocaleStructure)

                this.pageOrigin.locales.some(localeOrigin => {
                    const scapeCondition = localeOrigin.lang === language.iso

                    if ( scapeCondition ) {
                        locale = localeOrigin
                    }

                    return scapeCondition
                })

                return locale
            }
        },
        created () {
            for ( let languageAvailable of this.languagesAvailable ) {
                languageAvailable.enable = this.pageOrigin.locales.some(locale => locale.lang === languageAvailable.iso && locale.deleted_at === null)
            }
        }
    }
</script>
