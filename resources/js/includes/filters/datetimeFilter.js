import Vue from 'vue'

export default function (value) {
    return Vue.moment(value).format('DD/MM/YYYY')
}
