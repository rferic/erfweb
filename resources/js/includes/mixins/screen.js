const screenMixin = {
    data () {
        return {
             screenVersions: {
                mobile: {
                    max: 768
                },
                tablet: {
                    min: 768,
                    max: 992
                },
                desktop: {
                    min: 992
                }
            }
        }
    },
    methods: {
        isScreen ( requirements ) {
            if ( typeof requirements === 'string' && typeof this.screenVersions[requirements] !== undefined) {
                // If requirements is a String compare with version default
                return this.checkIsValidWidth(this.screenVersions[requirements])
            } else if ( typeof requirements === 'object' && ( typeof requirements.min !== typeof undefined || typeof requirements.max !== typeof undefined) ) {
                // Else if requirements is a Object compare if screen is between requirements
                return this.checkIsValidWidth(requirements)
            }
        },
        checkIsValidWidth ( requirements ) {
            return (
                (
                    typeof requirements.min === typeof undefined ||
                    requirements.min <= window.innerWidth
                ) &&
                (
                    typeof requirements.max === typeof undefined ||
                    requirements.max > window.innerWidth
                )
            )
        }
    }
}

export default screenMixin
