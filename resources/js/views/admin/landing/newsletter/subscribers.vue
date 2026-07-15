<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">مشتركو النشرة</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">مشتركو النشرة</li>
                </ol></nav>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card custom-card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-primary-transparent"><i class="ri-mail-send-line fs-18"></i></div>
                        <div>
                            <p class="mb-0 text-muted small">إجمالي المشتركين</p>
                            <h4 class="mb-0 fw-bold">{{ totalAll }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card custom-card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-info-transparent"><i class="ri-file-list-line fs-18"></i></div>
                        <div>
                            <p class="mb-0 text-muted small">النتائج المعروضة</p>
                            <h4 class="mb-0 fw-bold">{{ pagination.from || 0 }}–{{ pagination.to || 0 }} <small class="text-muted fw-normal">/ {{ pagination.total || 0 }}</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <loader v-if="loading" />

        <div v-else class="card custom-card">
            <div class="card-header justify-content-between flex-wrap gap-2">
                <h6 class="card-title mb-0">قائمة المشتركين</h6>
                <div class="d-flex gap-2">
                    <input type="search" v-model="search" @keyup.enter="applyFilters" class="form-control form-control-sm" style="width:200px;" placeholder="بحث بالبريد...">
                    <select v-model="perPage" @change="applyFilters" class="form-select form-select-sm" style="width:110px;">
                        <option :value="10">10 / صفحة</option>
                        <option :value="15">15 / صفحة</option>
                        <option :value="25">25 / صفحة</option>
                        <option :value="50">50 / صفحة</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-primary-light" @click="applyFilters"><i class="ri-search-line"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div v-if="subscribers.length === 0" class="text-center text-muted py-5">
                    <i class="ri-mail-line fs-2 d-block mb-2"></i>
                    لا يوجد مشتركون
                </div>
                <div class="table-responsive" v-else>
                    <table class="table table-striped mb-0">
                        <thead><tr><th>#</th><th>البريد الإلكتروني</th><th>IP</th><th>تاريخ الاشتراك</th><th v-if="permission.includes('landing newsletter delete')">إجراء</th></tr></thead>
                        <tbody>
                            <tr v-for="(item, i) in subscribers" :key="item.id">
                                <td>{{ (page - 1) * perPage + i + 1 }}</td>
                                <td>{{ item.email }}</td>
                                <td class="text-muted small">{{ item.ip_address || '—' }}</td>
                                <td class="text-muted small">{{ item.created_at }}</td>
                                <td v-if="permission.includes('landing newsletter delete')">
                                    <button class="btn btn-sm btn-danger-light" @click="deleteSub(item.id)"><i class="ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2" v-if="pageCount > 1">
                <small class="text-muted">صفحة {{ page }} من {{ pageCount }}</small>
                <pagination :pageCount="pageCount" :value="page" @input="changePage" />
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import adminApi from '../../../../api/adminAxios';

export default {
    name: 'LandingNewsletterSubscribers',
    setup() {
        const store = useStore();
        const permission = computed(() => store.state.authAdmin.permission);
        const loading = ref(true);
        const subscribers = ref([]);
        const page = ref(1);
        const pageCount = ref(1);
        const perPage = ref(15);
        const search = ref('');
        const totalAll = ref(0);
        const pagination = ref({});

        const loadSubscribers = () => {
            loading.value = true;
            const params = { page: page.value, per_page: perPage.value };
            if (search.value.trim()) params.search = search.value.trim();
            return adminApi.get('dashboard/landing/newsletter-subscribers', { params }).then(res => {
                subscribers.value = res.data.data || [];
                const pag = res.data.pagination || {};
                pageCount.value = pag.last_page || 1;
                pagination.value = pag;
                totalAll.value = pag.total_all ?? pag.total ?? 0;
            }).finally(() => { loading.value = false; });
        };

        const applyFilters = () => { page.value = 1; loadSubscribers(); };
        const changePage = (p) => { page.value = p; loadSubscribers(); };

        const deleteSub = async (id) => {
            const confirmed = await Swal.fire({ icon: 'warning', title: 'حذف المشترك؟', showCancelButton: true, confirmButtonText: 'حذف', cancelButtonText: 'إلغاء', confirmButtonColor: '#EF4444' });
            if (!confirmed.isConfirmed) return;
            await adminApi.delete(`dashboard/landing/newsletter-subscribers/${id}`);
            loadSubscribers();
            Swal.fire({ icon: 'success', title: 'تم الحذف', timer: 1200, showConfirmButton: false });
        };

        onMounted(loadSubscribers);

        return {
            permission, loading, subscribers, page, pageCount, perPage, search,
            totalAll, pagination, applyFilters, changePage, deleteSub,
        };
    },
};
</script>
