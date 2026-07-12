<template>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between my-4">
            <div>
                <p class="fw-semibold fs-18 mb-0">{{ tenant?.name || '...' }}</p>
                <span class="text-muted fs-13">تفاصيل الشركة</span>
            </div>
            <router-link :to="{name:'tenants'}" class="btn btn-light btn-wave">
                <i class="bx bx-arrow-back me-1"></i> رجوع
            </router-link>
        </div>

        <div v-if="loading" class="d-flex justify-content-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>

        <div class="row g-3" v-else-if="tenant">
            <!-- Tenant Info -->
            <div class="col-xl-5">
                <div class="card custom-card">
                    <div class="card-header"><div class="card-title">بيانات الشركة</div></div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted">الاسم</td><td class="fw-semibold">{{ tenant.name }}</td></tr>
                            <tr><td class="text-muted">الإيميل</td><td>{{ tenant.email }}</td></tr>
                            <tr><td class="text-muted">الهاتف</td><td>{{ tenant.phone || '—' }}</td></tr>
                            <tr><td class="text-muted">Slug</td><td><code>{{ tenant.slug }}</code></td></tr>
                            <tr>
                                <td class="text-muted">الحالة</td>
                                <td><span class="badge" :class="statusClass(tenant.status)">{{ statusLabel(tenant.status) }}</span></td>
                            </tr>
                            <tr><td class="text-muted">تاريخ الإنشاء</td><td>{{ formatDate(tenant.created_at) }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Subscription Management -->
            <div class="col-xl-7">
                <div class="card custom-card">
                    <div class="card-header"><div class="card-title">إدارة الاشتراك</div></div>
                    <div class="card-body">
                        <form @submit.prevent="saveSubscription">
                            <div class="row g-3">
                                <!-- Plan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">الباقة</label>
                                    <Select
                                        v-model="sub.plan_id"
                                        :options="plans"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="اختر الباقة"
                                        class="w-100" />
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">الحالة</label>
                                    <Select
                                        v-model="sub.status"
                                        :options="statusOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="اختر الحالة"
                                        class="w-100" />
                                </div>

                                <!-- Billing Cycle -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">دورة الفوترة</label>
                                    <Select
                                        v-model="sub.billing_cycle"
                                        :options="cycleOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="اختر الدورة"
                                        class="w-100" />
                                </div>

                                <!-- Plan Ends At -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">انتهاء الباقة</label>
                                    <DatePicker
                                        v-model="sub.plan_ends_at_date"
                                        dateFormat="dd-mm-yy"
                                        placeholder="يوم - شهر - سنة"
                                        class="w-100"
                                        showIcon
                                        iconDisplay="input" />
                                </div>

                                <!-- Alert -->
                                <div class="col-12" v-if="saveMsg">
                                    <div class="alert py-2" :class="saveMsg.type === 'success' ? 'alert-success' : 'alert-danger'">
                                        {{ saveMsg.text }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" :disabled="saving">
                                        <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                                        حفظ التغييرات
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import adminApi from '../../../api/adminAxios';

export default {
    setup() {
        const route = useRoute();
        const tenant = ref(null);
        const plans = ref([]);
        const loading = ref(true);
        const saving = ref(false);
        const saveMsg = ref(null);

        const statusOptions = [
            { label: 'تجريبي',  value: 'trial' },
            { label: 'نشط',     value: 'active' },
            { label: 'موقوف',   value: 'suspended' },
            { label: 'ملغي',    value: 'cancelled' },
        ];

        const cycleOptions = [
            { label: 'شهري',   value: 'monthly' },
            { label: 'سنوي',   value: 'yearly' },
            { label: 'مخصص',   value: 'custom' },
        ];

        const sub = ref({
            plan_id: null,
            status: 'active',
            billing_cycle: 'monthly',
            plan_ends_at_date: null,   // Date object for DatePicker
        });

        // Convert ISO string → Date object
        const toDate = (str) => str ? new Date(str) : null;

        // Convert Date object → "YYYY-MM-DD" string for API
        const toApiDate = (d) => {
            if (!d) return null;
            const date = d instanceof Date ? d : new Date(d);
            if (isNaN(date)) return null;
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        };

        onMounted(async () => {
            try {
                const [tRes, pRes] = await Promise.all([
                    adminApi.get(`dashboard/tenants/${route.params.id}`),
                    adminApi.get('dashboard/plans?per_page=100'),
                ]);
                tenant.value = tRes.data.data;
                plans.value = pRes.data.data || [];
                sub.value = {
                    plan_id: tenant.value.plan?.id || null,
                    status: tenant.value.status,
                    billing_cycle: tenant.value.billing_cycle,
                    plan_ends_at_date: toDate(tenant.value.plan_ends_at),
                };
            } catch (e) { console.error(e); }
            finally { loading.value = false; }
        });

        const saveSubscription = async () => {
            saving.value = true;
            saveMsg.value = null;
            try {
                await adminApi.put(`dashboard/tenants/${route.params.id}/subscription`, {
                    plan_id:       sub.value.plan_id,
                    status:        sub.value.status,
                    billing_cycle: sub.value.billing_cycle,
                    plan_ends_at:  toApiDate(sub.value.plan_ends_at_date),
                });
                saveMsg.value = { type: 'success', text: 'تم تحديث الاشتراك بنجاح' };
                if (tenant.value) tenant.value.status = sub.value.status;
            } catch (e) {
                saveMsg.value = { type: 'error', text: e.response?.data?.message || 'حدث خطأ' };
            } finally {
                saving.value = false;
            }
        };

        const statusClass = (s) => ({
            trial: 'bg-info', active: 'bg-success',
            suspended: 'bg-warning', cancelled: 'bg-danger',
        }[s] || 'bg-secondary');

        const statusLabel = (s) => ({
            trial: 'تجريبي', active: 'نشط',
            suspended: 'موقوف', cancelled: 'ملغي',
        }[s] || s);

        const formatDate = (d) => {
            if (!d) return '—';
            const date = new Date(d);
            const day  = String(date.getDate()).padStart(2, '0');
            const mon  = String(date.getMonth() + 1).padStart(2, '0');
            const yr   = date.getFullYear();
            return `${day}-${mon}-${yr}`;
        };

        return {
            tenant, plans, loading, saving, saveMsg,
            sub, statusOptions, cycleOptions,
            saveSubscription, statusClass, statusLabel, formatDate,
        };
    }
};
</script>
