const adminMenuStructure = [
    {
        key: 'messages',
        label: 'Messages',
        icon: 'fa-envelope',
        childrens: [
            {
                key: 'messages-inbox',
                label: 'Inbox',
                icon: null
            },
            {
                key: 'messages-trash',
                label: 'Trash',
                icon: null
            }
        ]
    }
]

export default adminMenuStructure
