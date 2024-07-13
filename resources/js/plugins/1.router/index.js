import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router/auto'
import RouteService from "@/utils/route-service.js";

function recursiveLayouts(route) {
    if (route.children) {
        for (let i = 0; i < route.children.length; i++)
            route.children[i] = recursiveLayouts(route.children[i])

        return route
    }

    return setupLayouts([route])[0]
}

function authenticateRoute(authUrl, redirectUrl) {
    return (to, from, next) => {
        RouteService.authenticateUser(authUrl, next, redirectUrl);
    };
}

function applyAuthGuard(routes) {
    routes.forEach(route => {
        if (!route.beforeEnter) {
            route.beforeEnter = authenticateRoute('/api/authenticate', '/login');
        }
        if (route.children) {
            applyAuthGuard(route.children);
        }
    });
}

// Define custom routes
const authenticatedRoutes = [
    {
        path: '/clients',
        name: 'ClientsView',
        component: () => import('@/views/pages/clients/ClientsIndex.vue'),
        meta: {
            title: 'Clients',
            navActiveLink: 'ClientsView'
        },
    },
    {
        path: '/clients/create',
        name: 'ClientsCreate',
        component: () => import('@/views/pages/clients/ClientsForm.vue'),
        meta: {
            title: 'Create Client',
            navActiveLink: 'ClientsView'
        },
    },
    {
        path: '/clients/edit/:hash',
        name: 'ClientsEdit',
        component: () => import('@/views/pages/clients/ClientsForm.vue'),
        meta: {
            title: 'Edit Client',
            navActiveLink: 'ClientsView'
        },
    },
    {
        path: '/invoices',
        name: 'InvoicesView',
        component: () => import('@/views/pages/invoices/InvoicesIndex.vue'),
        meta: { title: 'Invoices' },
    },
    {
        path: '/invoices/create',
        name: 'InvoicesCreate',
        component: () => import('@/views/pages/invoices/InvoicesForm.vue'),
        meta: {
            title: 'Create Invoice',
            navActiveLink: 'InvoicesView'
        },
    },
    {
        path: '/invoices/edit/:hash',
        name: 'InvoicesEdit',
        component: () => import('@/views/pages/invoices/InvoicesForm.vue'),
        meta: {
            title: 'Edit Invoice',
            navActiveLink: 'InvoicesView'
        },
    },
    {
        path: '/subscriptions',
        name: 'SubscriptionsView',
        component: () => import('@/views/pages/subscriptions/SubscriptionsIndex.vue'),
        meta: { title: 'Subscriptions' },
    },
    {
        path: '/subscriptions/create',
        name: 'SubscriptionsCreate',
        component: () => import('@/views/pages/subscriptions/SubscriptionsForm.vue'),
        meta: {
            title: 'Create Subscription',
            navActiveLink: 'SubscriptionsView'
        },
    },
    {
        path: '/subscriptions/edit/:hash',
        name: 'SubscriptionsEdit',
        component: () => import('@/views/pages/subscriptions/SubscriptionsForm.vue'),
        meta: {
            title: 'Edit Subscription',
            navActiveLink: 'SubscriptionsView'
        },
    },
    // {
    //     path: '/users',
    //     name: 'UsersView',
    //     component: () => import('@/views/pages/users/UsersIndex.vue'),
    //     meta: { title: 'Users' },
    // },
    // {
    //     path: '/users/create',
    //     name: 'UsersCreate',
    //     component: () => import('@/views/pages/users/UsersForm.vue'),
    //     meta: { title: 'Create User' },
    // },
    // {
    //     path: '/users/edit/:hash',
    //     name: 'UsersEdit',
    //     component: () => import('@/views/pages/users/UsersForm.vue'),
    //     meta: { title: 'Edit User' },
    // }
];

// unauthenticated routes
const unauthenticatedRoutes = [
    {
        path: '/',
        redirect: '/login',
        name: 'LoginView',
        component: () => import('@/views/pages/authentication/LoginView.vue'),
        meta: { title: 'Login' },
    }
];

applyAuthGuard(authenticatedRoutes);

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    scrollBehavior(to) {
        if (to.hash)
            return { el: to.hash, behavior: 'smooth', top: 60 }

        return { top: 0 }
    },
    extendRoutes: pages => [
        // unauthenticated routes
        ...unauthenticatedRoutes,

        // authenticated routes
        ...authenticatedRoutes.map(route => recursiveLayouts(route)),

        // page-based routes
        ...[...pages].map(route => recursiveLayouts(route)),
    ],
});

// Set up navigation guard to update page title
router.beforeEach((to, from, next) => {
    const pageTitle = to.meta.title;
    if (pageTitle) {
        document.title = pageTitle + ' - OAD SOFT';
    } else {
        document.title = 'OAD SOFT';
    }
    next();
});

export { router }
export default function (app) {
    app.use(router)
}
