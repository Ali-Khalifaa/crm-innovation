import {createRouter, createWebHistory} from 'vue-router';
import middlewarePipeline from "./middlewarePipeline";
import AuthLayout from "../layouts/Auth.vue";
import DashboardLayout from "../layouts/Dashboard.vue";
import Login from "../views/admin/auth/Login.vue";
import Page404 from "../views/admin/errors/Page404.vue";
import store from "../store/admin.js";
import auth from "../middleware/admin/auth.js";
import guest from "../middleware/admin/guest";
import admin from "./adminRoute/admin.js";
import role from "./adminRoute/role.js";
import plans from "./adminRoute/plans.js";
import tenants from "./adminRoute/tenants.js";
import landing from "./adminRoute/landing.js";

const routes = [
    {
        path: '/',
        redirect: { name: 'login' },
    },
    {
        path: '/',
        component: DashboardLayout,
        meta: { middleware: [auth] },
        children:[
            {path: 'dashboard', name: 'dashboard', component: () => import('../views/admin/dashboard/index.vue')},
            ...admin,
            ...role,
            ...plans,
            ...tenants,
            ...landing,
        ]
    },
    {
        path: '/login',
        component: AuthLayout,
        children:[
            {
                path: '',
                name: 'login',
                component: Login,
                meta: {middleware: [guest]}
            },
        ]
    },

    // {
    //     path: 'forget-password',
    //     name: 'forgetPassword',
    //     component: forgetPassword,
    //     meta: {
    //         middleware: [guest]
    //     }
    // },
    // {
    //     path: 'reset-password',
    //     name: 'resetPassword',
    //     component: resetPassword,
    //     meta: {
    //         middleware: [guest]
    //     }
    // },

    {
        path: '/:pathMatch(.*)*',
        name: 'Page404',
        component: Page404
    },
];

const router = createRouter({
    history: createWebHistory('/admin'),
    linkExactActiveClass: 'active',
    routes
});


router.beforeEach((to, from, next) => {

    if (!to.meta.middleware) return next();
    const middleware = to.meta.middleware;
    const context = {
        to,
        from,
        next,
        store
    };
    return middleware[0]({
        ...context,
        next: middlewarePipeline(context, middleware, 1)
    });
});

export default router;
