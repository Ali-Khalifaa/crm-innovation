<template>
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between my-4">
            <div>
                <p class="fw-semibold fs-18 mb-0">مرحباً، {{ $store.state.authAdmin.user?.name }} 👋</p>
                <span class="text-muted fs-14">لوحة تحكم CRM Innovation</span>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row g-3 mb-4" v-if="stats">
            <div class="col-xl-3 col-md-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fs-13">إجمالي الشركات</p>
                                <h3 class="fw-bold mb-0">{{ stats.total_tenants }}</h3>
                                <small class="text-success">{{ stats.active_tenants }} نشط · {{ stats.trial_tenants }} تجريبي</small>
                            </div>
                            <div class="avatar avatar-lg bg-primary-transparent rounded-circle">
                                <i class="bx bx-buildings fs-22 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fs-13">إجمالي جهات الاتصال</p>
                                <h3 class="fw-bold mb-0">{{ stats.total_contacts }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-success-transparent rounded-circle">
                                <i class="bx bx-user fs-22 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fs-13">إجمالي الصفقات</p>
                                <h3 class="fw-bold mb-0">{{ stats.total_deals }}</h3>
                            </div>
                            <div class="avatar avatar-lg bg-warning-transparent rounded-circle">
                                <i class="bx bx-trending-up fs-22 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fs-13">إجمالي الإيرادات</p>
                                <h3 class="fw-bold mb-0">${{ Number(stats.total_revenue).toLocaleString() }}</h3>
                                <small class="text-muted">{{ stats.total_invoices }} فاتورة</small>
                            </div>
                            <div class="avatar avatar-lg bg-danger-transparent rounded-circle">
                                <i class="bx bx-dollar fs-22 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Tenants by Plan -->
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header"><div class="card-title">الشركات حسب الباقة</div></div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>الباقة</th><th>عدد الشركات</th><th>السعر الشهري</th></tr></thead>
                            <tbody>
                                <tr v-for="p in stats?.tenants_by_plan" :key="p.plan">
                                    <td>{{ p.plan }}</td>
                                    <td><span class="badge bg-primary">{{ p.tenants_count }}</span></td>
                                    <td>${{ p.monthly_price }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Upgrade Requests -->
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-title">طلبات الترقية المعلقة</div>
                        <span class="badge bg-danger" v-if="stats?.pending_upgrades > 0">{{ stats.pending_upgrades }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="upgradeRequests.length === 0" class="p-4 text-center text-muted">
                            لا توجد طلبات معلقة
                        </div>
                        <table class="table table-hover mb-0" v-else>
                            <thead><tr><th>الشركة</th><th>الباقة المطلوبة</th><th></th></tr></thead>
                            <tbody>
                                <tr v-for="r in upgradeRequests" :key="r.id">
                                    <td>{{ r.tenant_name }}</td>
                                    <td><span class="badge bg-info">{{ r.requested_plan }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-success" @click="approveUpgrade(r.id)" :disabled="approvingId === r.id">
                                            <span v-if="approvingId === r.id" class="spinner-border spinner-border-sm"></span>
                                            <span v-else>موافقة</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import adminApi from '../../../api/adminAxios';

export default {
    setup() {
        const stats = ref(null);
        const upgradeRequests = ref([]);
        const loading = ref(true);
        const approvingId = ref(null);

        const load = async () => {
            try {
                const [s, u] = await Promise.all([
                    adminApi.get('dashboard/crm-statistics'),
                    adminApi.get('dashboard/crm-upgrade-requests'),
                ]);
                stats.value = s.data.data;
                upgradeRequests.value = u.data.data || [];
            } catch (e) {
                console.error(e);
            } finally {
                loading.value = false;
            }
        };

        const approveUpgrade = async (id) => {
            approvingId.value = id;
            try {
                await adminApi.post(`dashboard/crm-upgrade-requests/${id}/approve`);
                upgradeRequests.value = upgradeRequests.value.filter(r => r.id !== id);
                if (stats.value) stats.value.pending_upgrades--;
            } catch (e) {
                console.error(e);
            } finally {
                approvingId.value = null;
            }
        };

        onMounted(load);

        return { stats, upgradeRequests, loading, approvingId, approveUpgrade };
    }
};
</script>
