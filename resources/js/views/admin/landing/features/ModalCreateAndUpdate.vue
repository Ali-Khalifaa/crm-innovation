<template>
    <div class="modal fade" id="feature-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ type === 'create' ? 'إضافة ميزة جديدة' : 'تعديل الميزة' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">الأيقونة <small class="text-muted">(Font Awesome class، مثال: fa-users)</small></label>
                            <div class="input-group">
                                <span class="input-group-text"><i :class="'fas ' + (form.icon || 'fa-star')"></i></span>
                                <input type="text" class="form-control" v-model="form.icon" placeholder="fa-address-book">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.title_en" :class="{'is-invalid': errors.title_en}" placeholder="Contact Management">
                            <div class="invalid-feedback" v-if="errors.title_en">{{ errors.title_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">العنوان (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.title_ar" :class="{'is-invalid': errors.title_ar}" placeholder="إدارة جهات الاتصال">
                            <div class="invalid-feedback" v-if="errors.title_ar">{{ errors.title_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الوصف (إنجليزي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.description_en" :class="{'is-invalid': errors.description_en}" rows="3" placeholder="Organize all your contacts..."></textarea>
                            <div class="invalid-feedback" v-if="errors.description_en">{{ errors.description_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الوصف (عربي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.description_ar" :class="{'is-invalid': errors.description_ar}" rows="3" placeholder="نظّم جميع جهات اتصالك..."></textarea>
                            <div class="invalid-feedback" v-if="errors.description_ar">{{ errors.description_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end pb-1">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="feature-active" type="checkbox" v-model="form.is_active">
                                <label for="feature-active" class="label-primary"></label>
                                <span class="ms-2">نشط</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" @click.prevent="submit" :disabled="loading">
                        <span v-if="loading"><i class="ri-loader-2-fill me-1"></i>جاري الحفظ...</span>
                        <span v-else>{{ $t('global.Submit') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { ref, watch } from 'vue';
import adminApi from '../../../../api/adminAxios';
export default {
    name: 'FeatureModal',
    props: { type: { default: 'create' }, dataRow: { default: null } },
    emits: ['created'],
    setup(props, { emit }) {
        const loading = ref(false);
        const errors  = ref({});
        const defaultForm = () => ({ icon: 'fa-star', title_en: '', title_ar: '', description_en: '', description_ar: '', sort_order: 0, is_active: true });
        const form = ref(defaultForm());
        watch(() => props.dataRow, (row) => {
            form.value = row && props.type === 'edit'
                ? { icon: row.icon, title_en: row.title_en, title_ar: row.title_ar, description_en: row.description_en, description_ar: row.description_ar, sort_order: row.sort_order, is_active: row.is_active }
                : defaultForm();
        }, { immediate: true });
        const submit = () => {
            loading.value = true; errors.value = {};
            const payload = { ...form.value, is_active: form.value.is_active ? 1 : 0 };
            const req = props.type === 'edit'
                ? adminApi.put(`dashboard/landing/features/${props.dataRow.id}`, payload)
                : adminApi.post('dashboard/landing/features', payload);
            req.then(() => {
                Swal.fire({ icon: 'success', title: props.type === 'edit' ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح', showConfirmButton: false, timer: 1500 });
                emit('created');
                const modal = bootstrap.Modal.getInstance(document.getElementById('feature-modal'));
                if (modal) modal.hide();
            })
            .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
            .finally(() => { loading.value = false; });
        };
        return { loading, errors, form, submit };
    }
};
</script>
