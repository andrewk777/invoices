export default [
    {
      title: 'Clients',
      to: { name: 'ClientsView' },
      icon: { icon: 'tabler-user-dollar' },
      access: 'admin-only'
    },

    {
      title: 'Invoices',
      to: { name: 'InvoicesView' },
      icon: { icon: 'tabler-credit-card-pay' },
      access: 'any'
    },

    {
      title: 'Subscriptions',
      to: { name: 'SubscriptionsView' },
      icon: { icon: 'tabler-cash' },
      access: 'admin-only'
    },

    // {
    //     title: 'Users',
    //     to: { name: 'UsersView' },
    //     icon: { icon: 'tabler-user-circle' },
    // },
  ]
