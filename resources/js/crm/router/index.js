import { createRouter, createWebHistory } from 'vue-router'
import store from '../store/index.js'

const routes = [
    { path: '/crm/login', name: 'crm.login', component: () => import('../views/auth/Login.vue'), meta: { guest: true } },

    { path: '/crm', redirect: '/crm/dashboard' },
    {
        path: '/crm/dashboard',
        name: 'crm.dashboard',
        component: () => import('../views/dashboard/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/contacts',
        name: 'crm.contacts',
        component: () => import('../views/contacts/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/contacts/create',
        name: 'crm.contacts.create',
        component: () => import('../views/contacts/Form.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/contacts/:id',
        name: 'crm.contacts.show',
        component: () => import('../views/contacts/Show.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/contacts/:id/edit',
        name: 'crm.contacts.edit',
        component: () => import('../views/contacts/Form.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/deals',
        name: 'crm.deals',
        component: () => import('../views/deals/Kanban.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/deals/list',
        name: 'crm.deals.list',
        component: () => import('../views/deals/List.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/tasks',
        name: 'crm.tasks',
        component: () => import('../views/tasks/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/invoices',
        name: 'crm.invoices',
        component: () => import('../views/invoices/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/invoices/create',
        name: 'crm.invoices.create',
        component: () => import('../views/invoices/Create.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/invoices/:id',
        name: 'crm.invoices.show',
        component: () => import('../views/invoices/Show.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/reports',
        name: 'crm.reports',
        component: () => import('../views/reports/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/settings/company',
        name: 'crm.settings.company',
        component: () => import('../views/settings/Company.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/settings/team',
        name: 'crm.settings.team',
        component: () => import('../views/settings/Team.vue'),
        meta: { auth: true },
    },
    { path: '/crm/:pathMatch(.*)*', redirect: '/crm/dashboard' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const isLoggedIn = store.getters['auth/isLoggedIn']
    if (to.meta.auth && !isLoggedIn) return next('/crm/login')
    if (to.meta.guest && isLoggedIn) return next('/crm/dashboard')
    next()
})

export default router
