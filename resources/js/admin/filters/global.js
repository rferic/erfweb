import Vue from "vue"
import moment from 'moment'

Vue.filter('momentTime', function (time) {
    const now = moment()
    const momentTime = moment(time)

    if ( now.diff(momentTime, 'days') < 1 && now.days() === momentTime.days() ) {
        return momentTime.format('HH:mm DD/MM/YYYY')
    }

    return momentTime.format('MM-DD-YYYY')
})
