const srcMixin = {
    methods: {
        getAbosluteSrc ( src ) {
            let pat = /^https?:\/\//i

            if ( pat.test(src) ) {
                return src
            } else {
                return '//' + window.location.hostname + '/' + src
            }
        }
    }
}

export default srcMixin
