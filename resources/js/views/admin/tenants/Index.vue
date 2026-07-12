<template>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between my-4">
            <div>
                <p class="fw-semibold fs-18 mb-0">الشركات</p>
                <span class="text-muted fs-13">إدارة حسابات العملاء</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="card custom-card mb-3">
            <div class="card-body py-2">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" placeholder="بحث بالاسم أو الإيميل..." v-model="search" @input="debouncedLoad">
                    </div>
                    <div class="col-md-3">
                        <Select v-model="statusFilter" :options="statusOptions"
                            optionLabel="label" optionValue="value"
                            placeholder="كل الحالات" class="w-100"
                            @change="load" showClear />
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-body p-0">
                <div v-if="loading" class="d-flex justify-content-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
                <table class="table table-hover mb-0" v-else>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الشركة</th>
                            <th>الإيميل</th>
                            <th>الباقة</th>
                            <th>الحالة</th>
                            <th>الدورة</th>
                            <th>تاريخ الإنشاء</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in tenants" :key="t.id">
                            <td>{{ t.id }}</td>
                            <td class="fw-semibold">{{ t.name }}</td>
                            <td>{{ t.email }}</td>
                            <td><span class="badge bg-primary">{{ t.plan?.name || '—' }}</span></td>
                            <td>
                                <span class="badge" :class="statusClass(t.status)">{{ statusLabel(t.status) }}</span>
                            </td>
                            <td>{{ t.billing_cycle === 'monthly' ? 'شهري' : t.billing_cycle === 'yearly' ? 'سنوي' : 'مخصص' }}</td>
                            <td>{{ formatDate(t.created_at) }}</td>
                            <td>
                                <router-link :to="{name:'tenants.show', params:{id:t.id}}" class="btn btn-sm btn-light">
                                    <i class="bx bx-show"></i> عرض
                                </router-link>
                            </td>
                        </tr>
                        <tr v-if="tenants.length === 0">
                            <td colspan="8" class="text-center py-4 text-muted">لا توجد شركات</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center py-3" v-if="meta.last_page > 1">
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item" :class="{disabled: meta.current_page === 1}">
                                <button class="page-link" @click="goPage(meta.current_page - 1)">السابق</button>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">{{ meta.current_page }} / {{ meta.last_page }}</span>
                            </li>
                            <li class="page-item" :class="{disabled: meta.current_page === meta.last_page}">
                                <button class="page-link" @click="goPage(meta.current_page + 1)">التالي</button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import adminApi from '../../../api/adminAxios';

export default {
    setup() {
        const tenants = ref([]);
        const loading = ref(true);
        const search = ref('');
        const statusFilter = ref('');
        const meta = ref({ current_page: 1, last_page: 1 });
        let debounceTimer = null;

        const statusOptions = [
            { label: 'تجريبي', value: 'trial' },
            { label: 'نشط', value: 'active' },
            { label: 'موقوف', value: 'suspended' },
            { label: 'ملغي', value: 'cancelled' },
        ];

        const load = async (page = 1) => {
            loading.value = true;
            try {
                const res = await adminApi.get('dashboard/tenants', {
                    params: { page, search: search.value, status: statusFilter.value }
                });
                tenants.value = res.data.data || [];
                meta.value = res.data.meta || { current_page: 1, last_page: 1 };
            } catch (e) { console.error(e); } finally { loading.value = false; }
        };

        const debouncedLoad = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => load(), 400);
        };

        const goPage = (p) => load(p);

        const statusClass = (s) => ({
            trial: 'bg-info', active: 'bg-success',
            suspended: 'bg-warning', cancelled: 'bg-danger'
        }[s] || 'bg-secondary');

        const statusLabel = (s) => ({
            trial: 'تجريبي', active: 'نشط',
            suspended: 'موقوف', cancelled: 'ملغي'
        }[s] || s);

        const formatDate = (d) => d ? new Date(d).toLocaleDateString('ar-EG') : '—';

        onMounted(() => load());
        return { tenants, loading, search, statusFilter, statusOptions, meta, load, debouncedLoad, goPage, statusClass, statusLabel, formatDate };
    }
};
</script>
