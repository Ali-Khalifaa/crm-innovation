export default [
    { path: 'plans', name: 'plans', component: () => import('../../views/admin/plans/Index.vue') },
    { path: 'plans/create', name: 'plans.create', component: () => import('../../views/admin/plans/Form.vue') },
    { path: 'plans/:id/edit', name: 'plans.edit', component: () => import('../../views/admin/plans/Form.vue') },
];
