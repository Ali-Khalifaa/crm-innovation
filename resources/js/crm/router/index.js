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
    { path: '/crm/contacts/create', redirect: '/crm/contacts' },
    {
        path: '/crm/contacts/:id',
        name: 'crm.contacts.show',
        component: () => import('../views/contacts/Show.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/contacts/:id/edit',
        name: 'crm.contacts.edit',
        redirect: to => ({ path: '/crm/contacts', query: { edit: to.params.id } }),
    },
    {
        path: '/crm/companies',
        name: 'crm.companies',
        component: () => import('../views/companies/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/companies/:id',
        name: 'crm.companies.show',
        component: () => import('../views/companies/Show.vue'),
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
        path: '/crm/tasks/calendar',
        name: 'crm.tasks.calendar',
        component: () => import('../views/tasks/Calendar.vue'),
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
    {
        path: '/crm/settings/profile',
        name: 'crm.settings.profile',
        component: () => import('../views/settings/Profile.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/settings/stages',
        name: 'crm.settings.stages',
        component: () => import('../views/settings/Stages.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/meetings',
        name: 'crm.meetings',
        component: () => import('../views/meetings/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/calls',
        name: 'crm.calls',
        component: () => import('../views/calls/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/products',
        name: 'crm.products',
        component: () => import('../views/products/Index.vue'),
        meta: { auth: true },
    },
    {
        path: '/crm/notifications',
        name: 'crm.notifications',
        component: () => import('../views/notifications/Index.vue'),
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
