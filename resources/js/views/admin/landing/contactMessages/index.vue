<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">رسائل التواصل</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">رسائل التواصل</li>
                </ol></nav>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card custom-card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-primary-transparent"><i class="ri-mail-line fs-18"></i></div>
                        <div>
                            <p class="mb-0 text-muted small">إجمالي الرسائل</p>
                            <h4 class="mb-0 fw-bold">{{ totalAll }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card custom-card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-danger-transparent"><i class="ri-mail-unread-line fs-18"></i></div>
                        <div>
                            <p class="mb-0 text-muted small">رسائل جديدة</p>
                            <h4 class="mb-0 fw-bold text-danger">{{ totalNew }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
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

        <div class="row">
            <div class="col-xl-12">
                <loader v-if="loading" />
                <div class="card custom-card">
                    <div class="card-header justify-content-between flex-wrap gap-2">
                        <h6 class="card-title mb-0">صندوق الوارد</h6>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <input type="search" v-model="search" @keyup.enter="applyFilters" class="form-control form-control-sm" style="width:200px;" placeholder="بحث بالاسم أو البريد...">
                            <select v-model="filterStatus" @change="applyFilters" class="form-select form-select-sm" style="width:130px;">
                                <option value="">كل الحالات</option>
                                <option value="new">جديد</option>
                                <option value="read">مقروء</option>
                                <option value="replied">تم الرد</option>
                            </select>
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
                        <div v-if="!loading && items.length === 0" class="text-center text-muted py-5">
                            <i class="ri-mail-line fs-2 d-block mb-2"></i>
                            لا توجد رسائل
                        </div>
                        <div v-for="item in items" :key="item.id"
                            class="message-row d-flex align-items-center gap-3 p-3 border-bottom"
                            :class="item.status === 'new' ? 'bg-primary-transparent' : ''"
                            @click="openMessage(item)"
                            style="cursor:pointer;">
                            <div class="avatar flex-shrink-0 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-semibold" :class="item.status === 'new' ? 'text-primary' : ''">{{ item.name }}</span>
                                    <small class="text-muted flex-shrink-0 ms-2">{{ formatDate(item.created_at) }}</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">{{ item.email }}</small>
                                    <span v-if="item.phone" class="text-muted">· {{ item.phone }}</span>
                                </div>
                                <p class="mb-0 text-muted text-truncate" style="font-size:13px;">
                                    {{ item.message?.substring(0, 100) }}{{ (item.message?.length || 0) > 100 ? '...' : '' }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 d-flex flex-column align-items-end gap-1">
                                <span :class="statusBadge(item.status)">{{ statusLabel(item.status) }}</span>
                                <button v-if="permission.includes('landing contact delete')"
                                    class="btn btn-xs btn-danger-light" @click.stop="deleteMsg(item.id)">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2" v-if="pageCount > 1">
                        <small class="text-muted">صفحة {{ page }} من {{ pageCount }}</small>
                        <pagination :pageCount="pageCount" :value="page" @input="changePage" />
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="msg-detail-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" v-if="selected">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ selected.subject || 'رسالة جديدة' }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6"><label class="form-label text-muted small mb-1">الاسم</label><p class="fw-semibold mb-0">{{ selected.name }}</p></div>
                            <div class="col-md-6"><label class="form-label text-muted small mb-1">البريد</label><p class="mb-0"><a :href="'mailto:' + selected.email">{{ selected.email }}</a></p></div>
                            <div class="col-md-6" v-if="selected.phone"><label class="form-label text-muted small mb-1">الهاتف</label><p class="mb-0">{{ selected.phone }}</p></div>
                            <div class="col-md-6"><label class="form-label text-muted small mb-1">التاريخ</label><p class="mb-0">{{ selected.created_at }}</p></div>
                        </div>
                        <div class="p-3 bg-light rounded border"><p class="mb-0" style="white-space:pre-wrap;line-height:1.8;">{{ selected.message }}</p></div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <div>
                            <span :class="statusBadge(selected.status) + ' me-2'">{{ statusLabel(selected.status) }}</span>
                            <button v-if="selected.status !== 'replied'" class="btn btn-sm btn-success-light" @click="markReplied(selected)" :disabled="markingReplied">
                                <i class="ri-reply-line me-1"></i>تحديد كـ تم الرد
                            </button>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import adminApi from '@/api/adminAxios';

export default {
    name: 'ContactMessagesIndex',
    data() {
        return {
            loading: true,
            items: [],
            page: 1,
            pageCount: 1,
            perPage: 15,
            search: '',
            filterStatus: '',
            totalAll: 0,
            totalNew: 0,
            pagination: {},
            selected: null,
            markingReplied: false,
            permission: [],
        };
    },
    mounted() {
        this.permission = this.$store.getters['auth/permissions'] || [];
        this.getItems();
    },
    methods: {
        async getItems() {
            this.loading = true;
            try {
                const params = { page: this.page, per_page: this.perPage };
                if (this.filterStatus) params.status = this.filterStatus;
                if (this.search.trim()) params.search = this.search.trim();
                const { data } = await adminApi.get('dashboard/landing/contact-messages', { params });
                const payload = data.data;
                this.items = Array.isArray(payload) ? payload : (payload?.data ?? []);
                const pag = data.pagination || {};
                this.pageCount = pag.last_page || 1;
                this.pagination = pag;
                this.totalAll = pag.total_all ?? pag.total ?? 0;
                this.totalNew = pag.total_new ?? 0;
            } catch (_) {}
            this.loading = false;
        },
        applyFilters() { this.page = 1; this.getItems(); },
        changePage(p) { this.page = p; this.getItems(); },
        async openMessage(item) {
            this.selected = item;
            new bootstrap.Modal(document.getElementById('msg-detail-modal')).show();
            if (item.status === 'new') {
                try {
                    const { data } = await adminApi.get(`dashboard/landing/contact-messages/${item.id}`);
                    if (data?.data) {
                        const idx = this.items.findIndex(i => i.id === item.id);
                        if (idx !== -1) this.items[idx].status = data.data.status;
                        this.selected = { ...this.selected, status: data.data.status };
                        this.totalNew = Math.max(0, this.totalNew - 1);
                    }
                } catch (_) {}
            }
        },
        async markReplied(item) {
            this.markingReplied = true;
            try {
                await adminApi.post(`dashboard/landing/contact-messages/${item.id}/mark-replied`);
                const idx = this.items.findIndex(i => i.id === item.id);
                if (idx !== -1) this.items[idx].status = 'replied';
                this.selected = { ...this.selected, status: 'replied' };
                Swal.fire({ icon: 'success', title: 'تم التحديث', timer: 1200, showConfirmButton: false });
            } catch (_) {}
            this.markingReplied = false;
        },
        async deleteMsg(id) {
            const confirmed = await Swal.fire({ icon: 'warning', title: 'تأكيد الحذف', showCancelButton: true, confirmButtonText: 'حذف', cancelButtonText: 'إلغاء', confirmButtonColor: '#EF4444' });
            if (!confirmed.isConfirmed) return;
            try {
                await adminApi.delete(`dashboard/landing/contact-messages/${id}`);
                this.getItems();
                Swal.fire({ icon: 'success', title: 'تم الحذف', timer: 1200, showConfirmButton: false });
            } catch (_) {}
        },
        statusLabel(s) { return { new: 'جديد', read: 'مقروء', replied: 'تم الرد' }[s] || s; },
        statusBadge(s) {
            return { new: 'badge bg-danger', read: 'badge bg-info-transparent', replied: 'badge bg-success-transparent' }[s] || 'badge bg-secondary';
        },
        formatDate(d) {
            if (!d) return '';
            return new Date(d).toLocaleDateString('ar-EG', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },
    },
};
</script>

<style scoped>
.message-row:hover { background: #F1F5F9 !important; }
</style>
