const filterAppStructure = {
    text: '',
    enables: true,
    types: [
        {
            key: 'public',
            class: 'success',
            checked: false
        },
        {
            key: 'protected',
            class: 'warning',
            checked: false
        },
        {
            key: 'private',
            class: 'danger',
            checked: false
        }
    ],
    status: [
        {
            key: 'success',
            class: 'success',
            checked: false
        },
        {
            key: 'working',
            class: 'warning',
            checked: false
        },
        {
            key: 'error',
            class: 'danger',
            checked: false
        }
    ]
}

export default filterAppStructure
