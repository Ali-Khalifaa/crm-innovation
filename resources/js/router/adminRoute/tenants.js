export default [
    { path: 'tenants', name: 'tenants', component: () => import('../../views/admin/tenants/Index.vue') },
    { path: 'tenants/:id', name: 'tenants.show', component: () => import('../../views/admin/tenants/Show.vue') },
];
