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

        <div class="row">
            <div class="col-xl-12">
                <loader v-if="loading" />
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h6 class="card-title mb-0">صندوق الوارد <span v-if="unreadCount" class="badge bg-danger ms-2">{{ unreadCount }} جديد</span></h6>
                        <div class="d-flex gap-2">
                            <select v-model="filterStatus" @change="getItems" class="form-select form-select-sm" style="width:130px;">
                                <option value="">الكل</option>
                                <option value="new">جديد</option>
                                <option value="read">مقروء</option>
                                <option value="replied">تم الرد</option>
                            </select>
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
                            style="cursor:pointer;transition:background 0.2s;">
                            <div class="avatar flex-shrink-0" style="width:42px;height:42px;border-radius:50%;background:#E2E8F0;display:flex;align-items:center;justify-content:center;">
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
                                <p class="mb-0 text-muted" style="font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <strong v-if="item.subject">{{ item.subject }}: </strong>{{ item.message?.substring(0, 80) }}...
                                </p>
                            </div>
                            <div class="flex-shrink-0 d-flex flex-column align-items-end gap-1">
                                <span :class="{
                                    'badge bg-danger': item.status === 'new',
                                    'badge bg-info-transparent': item.status === 'read',
                                    'badge bg-success-transparent': item.status === 'replied'
                                }">{{ statusLabel(item.status) }}</span>
                                <button v-if="permission.includes('landing contact delete')"
                                    class="btn btn-xs btn-danger-light"
                                    @click.stop="deleteMsg(item.id)"
                                    style="padding:2px 6px;font-size:11px;">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" v-if="pageCount > 1">
                        <pagination :pageCount="pageCount" :value="page" @input="changePage" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Detail Modal -->
        <div class="modal fade" id="msg-detail-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" v-if="selected">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ selected.subject || 'رسالة جديدة' }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">الاسم</label>
                                <p class="fw-semibold mb-0">{{ selected.name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">البريد الإلكتروني</label>
                                <p class="mb-0"><a :href="'mailto:' + selected.email">{{ selected.email }}</a></p>
                            </div>
                            <div class="col-md-6" v-if="selected.phone">
                                <label class="form-label text-muted small mb-1">رقم الهاتف</label>
                                <p class="mb-0">{{ selected.phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">التاريخ</label>
                                <p class="mb-0">{{ selected.created_at }}</p>
                            </div>
                        </div>
                        <div class="p-3" style="background:#F8FAFC;border-radius:8px;border:1px solid #E2E8F0;">
                            <p class="mb-0" style="line-height:1.8;white-space:pre-wrap;">{{ selected.message }}</p>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <div>
                            <span :class="{
                                'badge bg-danger me-2': selected.status === 'new',
                                'badge bg-info-transparent me-2': selected.status === 'read',
                                'badge bg-success-transparent me-2': selected.status === 'replied'
                            }">{{ statusLabel(selected.status) }}</span>
                            <button v-if="selected.status !== 'replied'" class="btn btn-sm btn-success-light" @click="markReplied(selected)" :disabled="markingReplied">
                                <span v-if="markingReplied" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="ri-reply-line me-1"></i>تحديد كـ "تم الرد"
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
            filterStatus: '',
            selected: null,
            markingReplied: false,
            permission: [],
        };
    },
    computed: {
        unreadCount() { return this.items.filter(i => i.status === 'new').length; },
    },
    mounted() {
        this.permission = this.$store.getters['auth/permissions'] || [];
        this.getItems();
    },
    methods: {
        async getItems() {
            this.loading = true;
            try {
                const params = { page: this.page };
                if (this.filterStatus) params.status = this.filterStatus;
                const { data } = await adminApi.get('landing/contact-messages', { params });
                this.items = data.data || [];
                this.pageCount = data.meta?.last_page || 1;
            } catch (_) {}
            this.loading = false;
        },
        changePage(p) { this.page = p; this.getItems(); },
        async openMessage(item) {
            this.selected = item;
            const modal = new bootstrap.Modal(document.getElementById('msg-detail-modal'));
            modal.show();
            if (item.status === 'new') {
                try {
                    const { data } = await adminApi.get(`landing/contact-messages/${item.id}`);
                    if (data?.data) {
                        const idx = this.items.findIndex(i => i.id === item.id);
                        if (idx !== -1) this.items[idx].status = data.data.status;
                        this.selected = { ...this.selected, status: data.data.status };
                    }
                } catch (_) {}
            }
        },
        async markReplied(item) {
            this.markingReplied = true;
            try {
                await adminApi.post(`landing/contact-messages/${item.id}/mark-replied`);
                const idx = this.items.findIndex(i => i.id === item.id);
                if (idx !== -1) this.items[idx].status = 'replied';
                this.selected = { ...this.selected, status: 'replied' };
                Swal.fire({ icon: 'success', title: 'تم التحديث', timer: 1200, showConfirmButton: false });
            } catch (_) {}
            this.markingReplied = false;
        },
        async deleteMsg(id) {
            const confirmed = await Swal.fire({ icon: 'warning', title: 'تأكيد الحذف', text: 'هل أنت متأكد؟', showCancelButton: true, confirmButtonText: 'حذف', cancelButtonText: 'إلغاء', confirmButtonColor: '#EF4444' });
            if (!confirmed.isConfirmed) return;
            try {
                await adminApi.delete(`landing/contact-messages/${id}`);
                this.items = this.items.filter(i => i.id !== id);
                Swal.fire({ icon: 'success', title: 'تم الحذف', timer: 1200, showConfirmButton: false });
            } catch (_) {}
        },
        statusLabel(s) { return { new: 'جديد', read: 'مقروء', replied: 'تم الرد' }[s] || s; },
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
