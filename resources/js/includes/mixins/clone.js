const cloneMixin = {
    methods: {
        clone ( object ) {
            if ( Object.prototype.toString.call(object) === '[object Array]' ) {
                let clone = []

                for (let i = 0; i < object.length; i++) {
                    clone[i] = this.clone(object[i])
                }

                return clone
            } else if ( object === null ) {
                return null
            } else if ( typeof(object) === 'object' ) {
                let clone = {}

                for ( let prop in object ) {
                    if ( object.hasOwnProperty(prop) ) {
                        clone[prop] = this.clone(object[prop])
                    }
                }

                return clone
            } else {
                return object
            }
        }
    }
}

export default cloneMixin
