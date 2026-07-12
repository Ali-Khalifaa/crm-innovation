import store from "../../store/admin";

function guard(permission) {
    return (to, from, next) => {
        const perms = store.state.authAdmin.permission;
        perms.includes(permission) ? next() : next({ name: 'Page404' });
    };
}

export default [
    {
        path: 'landing/settings',
        name: 'landing.settings',
        component: () => import('../../views/admin/landing/settings/index.vue'),
        beforeEnter: guard('landing settings read'),
    },
    {
        path: 'landing/hero',
        name: 'landing.hero',
        component: () => import('../../views/admin/landing/hero/index.vue'),
        beforeEnter: guard('landing hero read'),
    },
    {
        path: 'landing/stats',
        name: 'landing.stats',
        component: () => import('../../views/admin/landing/stats/index.vue'),
        beforeEnter: guard('landing stat read'),
    },
    {
        path: 'landing/features',
        name: 'landing.features',
        component: () => import('../../views/admin/landing/features/index.vue'),
        beforeEnter: guard('landing feature read'),
    },
    {
        path: 'landing/how-it-works',
        name: 'landing.how_it_works',
        component: () => import('../../views/admin/landing/howItWorks/index.vue'),
        beforeEnter: guard('landing how it works read'),
    },
    {
        path: 'landing/faqs',
        name: 'landing.faqs',
        component: () => import('../../views/admin/landing/faqs/index.vue'),
        beforeEnter: guard('landing faq read'),
    },
    {
        path: 'landing/partners',
        name: 'landing.partners',
        component: () => import('../../views/admin/landing/partners/index.vue'),
        beforeEnter: guard('landing partner read'),
    },
    {
        path: 'landing/about',
        name: 'landing.about',
        component: () => import('../../views/admin/landing/about/index.vue'),
        beforeEnter: guard('landing about read'),
    },
    {
        path: 'landing/testimonials',
        name: 'landing.testimonials',
        component: () => import('../../views/admin/landing/testimonials/index.vue'),
        beforeEnter: guard('landing testimonial read'),
    },
    {
        path: 'landing/team',
        name: 'landing.team',
        component: () => import('../../views/admin/landing/team/index.vue'),
        beforeEnter: guard('landing team read'),
    },
    {
        path: 'landing/blog',
        name: 'landing.blog',
        component: () => import('../../views/admin/landing/blog/index.vue'),
        beforeEnter: guard('landing blog read'),
    },
    {
        path: 'landing/contact-messages',
        name: 'landing.contact_messages',
        component: () => import('../../views/admin/landing/contactMessages/index.vue'),
        beforeEnter: guard('landing contact read'),
    },
];
