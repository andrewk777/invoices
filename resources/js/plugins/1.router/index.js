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
    children: [
          {
              name: "CustomersView",
              path: "/customers",
              component: () => import('@/views/pages/customers/CustomersView.vue'),
              beforeEnter: (to, from, next) => {
                  RouteService.authenticateUser(
                      '/api/authenticate',
                      next,
                      '/',
                  )
              },
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
