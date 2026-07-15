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
        path: 'landing/seo',
        name: 'landing.seo',
        component: () => import('../../views/admin/landing/seo/index.vue'),
        beforeEnter: guard('landing seo read'),
    },
    {
        path: 'landing/hero',
        name: 'landing.hero',
        component: () => import('../../views/admin/landing/hero/index.vue'),
        beforeEnter: guard('landing hero read'),
    },
    {
        path: 'landing/partners',
        name: 'landing.partners',
        component: () => import('../../views/admin/landing/partners/index.vue'),
        beforeEnter: guard('landing partner read'),
    },
    {
        path: 'landing/problems',
        name: 'landing.problems',
        component: () => import('../../views/admin/landing/problems/index.vue'),
        beforeEnter: guard('landing problem read'),
    },
    {
        path: 'landing/features',
        name: 'landing.features',
        component: () => import('../../views/admin/landing/features/index.vue'),
        beforeEnter: guard('landing feature read'),
    },
    {
        path: 'landing/solutions',
        name: 'landing.solutions',
        component: () => import('../../views/admin/landing/solutions/index.vue'),
        beforeEnter: guard('landing solutions read'),
    },
    {
        path: 'landing/how-it-works',
        name: 'landing.how_it_works',
        component: () => import('../../views/admin/landing/howItWorks/index.vue'),
        beforeEnter: guard('landing how it works read'),
    },
    {
        path: 'landing/why-us',
        name: 'landing.why',
        component: () => import('../../views/admin/landing/whyUs/index.vue'),
        beforeEnter: guard('landing why read'),
    },
    {
        path: 'landing/stats',
        name: 'landing.stats',
        component: () => import('../../views/admin/landing/stats/index.vue'),
        beforeEnter: guard('landing stat read'),
    },
    {
        path: 'landing/faqs',
        name: 'landing.faqs',
        component: () => import('../../views/admin/landing/faqs/index.vue'),
        beforeEnter: guard('landing faq read'),
    },
    {
        path: 'landing/testimonials',
        name: 'landing.testimonials',
        component: () => import('../../views/admin/landing/testimonials/index.vue'),
        beforeEnter: guard('landing testimonial read'),
    },
    {
        path: 'landing/contact',
        name: 'landing.contact',
        component: () => import('../../views/admin/landing/contact/index.vue'),
        beforeEnter: guard('landing contact read'),
    },
    {
        path: 'landing/newsletter',
        name: 'landing.newsletter',
        component: () => import('../../views/admin/landing/newsletter/settings.vue'),
        beforeEnter: guard('landing newsletter read'),
    },
    {
        path: 'landing/newsletter-subscribers',
        name: 'landing.newsletter_subscribers',
        component: () => import('../../views/admin/landing/newsletter/subscribers.vue'),
        beforeEnter: guard('landing newsletter read'),
    },
    {
        path: 'landing/legal-pages',
        name: 'landing.legal_pages',
        component: () => import('../../views/admin/landing/legalPages/index.vue'),
        beforeEnter: guard('landing legal read'),
    },
    {
        path: 'landing/contact-messages',
        name: 'landing.contact_messages',
        component: () => import('../../views/admin/landing/contactMessages/index.vue'),
        beforeEnter: guard('landing contact read'),
    },
];
