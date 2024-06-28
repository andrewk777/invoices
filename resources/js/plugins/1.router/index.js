// import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router/auto'
import RouteService from "@/utils/route-service.js";

// function recursiveLayouts(route) {
//   if (route.children) {
//     for (let i = 0; i < route.children.length; i++)
//       route.children[i] = recursiveLayouts(route.children[i])
//
//     return route
//   }
//
//   return setupLayouts([route])[0]
// }

// Define custom routes
const customRoutes = [

  {
    path: '/',
    name: 'LoginView',
    component: () => import('@/views/pages/authentication/LoginView.vue'),
  },
    
  {
    path: '/',
    name: 'LayoutView',
    component: () => import('@/views/pages/LayoutView.vue'),
    beforeEnter: (to, from, next) => {
          RouteService.authenticateUser(
              '/api/authenticate',
              next,
              '/',
          )
    },
    children: [
        {
              name: "ClientsView",
              path: "/clients",
              component: () => import('@/views/pages/clients/ClientsView.vue'),
        },

        {
            name: "ClientsCreate",
            path: '/clients/create',
            component: () => import('@/views/pages/clients/ClientsForm.vue'),
        },

        {
            name: "ClientsEdit",
            path: '/clients/edit/:hash',
            component: () => import('@/views/pages/clients/ClientsForm.vue'),
        },

        {
            name: "InvoicesView",
            path: "/invoices",
            component: () => import('@/views/pages/invoices/InvoicesView.vue'),
        },

        {
            name: "InvoicesCreate",
            path: "/invoices/create",
            component: () => import('@/views/pages/invoices/InvoicesForm.vue'),
        },

        {
            name: "InvoicesEdit",
            path: "/invoices/edit/:hash",
            component: () => import('@/views/pages/invoices/InvoicesForm.vue'),
        },

        {
            name: "SubscriptionsView",
            path: "/subscriptions",
            component: () => import('@/views/pages/subscriptions/SubscriptionsView.vue'),
        },

        {
            name: "SubscriptionsCreate",
            path: "/subscriptions/create",
            component: () => import('@/views/pages/subscriptions/SubscriptionsForm.vue'),
        },

        {
            name: "SubscriptionsEdit",
            path: "/subscriptions/edit/:hash",
            component: () => import('@/views/pages/subscriptions/SubscriptionsForm.vue'),
        },

        {
            name: "UsersView",
            path: "/users",
            component: () => import('@/views/pages/users/UsersView.vue'),
        },

    ]
  },

  // Add more custom routes as needed
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to) {
    if (to.hash)
      return { el: to.hash, behavior: 'smooth', top: 60 }
    
    return { top: 0 }
  },
  extendRoutes: pages => [
    ...customRoutes,

    // page based routes
    // ...[...pages].map(route => recursiveLayouts(route)),

  ],
})

export { router }
export default function (app) {
  app.use(router)
}
