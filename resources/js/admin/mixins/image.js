const imageMixin = {
    methods: {
        getCalculatedSrcFromAdmin ( image ) {
            return this.getIsExternal(image.src) ? image.src : `./../${image.src}`
        },
        getIsExternal ( url ) {
            const match = url.match(/^([^:\/?#]+:)?(?:\/\/([^\/?#]*))?([^?#]+)?(\?[^#]*)?(#.*)?/);
            if (typeof match[1] === "string" && match[1].length > 0 && match[1].toLowerCase() !== location.protocol) return true;
            if (typeof match[2] === "string" && match[2].length > 0 && match[2].replace(new RegExp(":("+{"http:":80,"https:":443}[location.protocol]+")?$"), "") !== location.host) return true;
            return false;
        }
    }
}

export default imageMixin
