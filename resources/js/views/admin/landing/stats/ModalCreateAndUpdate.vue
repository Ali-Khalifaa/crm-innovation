<template>
    <div class="modal fade" id="stat-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ type === 'create' ? 'إضافة إحصاء جديد' : 'تعديل الإحصاء' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">القيمة (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.value_en" :class="{'is-invalid': errors.value_en}" placeholder="2500">
                            <div class="invalid-feedback" v-if="errors.value_en">{{ errors.value_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">القيمة (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.value_ar" :class="{'is-invalid': errors.value_ar}" placeholder="2500">
                            <div class="invalid-feedback" v-if="errors.value_ar">{{ errors.value_ar[0] }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">اللاحقة <small class="text-muted">(+، %، M+)</small></label>
                            <input type="text" class="form-control" v-model="form.suffix" placeholder="+">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">التسمية (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.label_en" :class="{'is-invalid': errors.label_en}" placeholder="Active Users">
                            <div class="invalid-feedback" v-if="errors.label_en">{{ errors.label_en[0] }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">التسمية (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.label_ar" :class="{'is-invalid': errors.label_ar}" placeholder="مستخدم نشط">
                            <div class="invalid-feedback" v-if="errors.label_ar">{{ errors.label_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end pb-1">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="stat-active" type="checkbox" v-model="form.is_active">
                                <label for="stat-active" class="label-primary"></label>
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
    name: 'StatModal',
    props: { type: { default: 'create' }, dataRow: { default: null } },
    emits: ['created'],
    setup(props, { emit }) {
        const loading = ref(false);
        const errors  = ref({});
        const defaultForm = () => ({ value_en: '', value_ar: '', suffix: '+', label_en: '', label_ar: '', sort_order: 0, is_active: true });
        const form = ref(defaultForm());

        watch(() => props.dataRow, (row) => {
            if (row && props.type === 'edit') {
                form.value = { value_en: row.value_en, value_ar: row.value_ar, suffix: row.suffix, label_en: row.label_en, label_ar: row.label_ar, sort_order: row.sort_order, is_active: row.is_active };
            } else {
                form.value = defaultForm();
            }
        }, { immediate: true });

        const submit = () => {
            loading.value = true;
            errors.value  = {};
            const payload = { ...form.value, is_active: form.value.is_active ? 1 : 0 };
            const req = props.type === 'edit'
                ? adminApi.put(`dashboard/landing/stats/${props.dataRow.id}`, payload)
                : adminApi.post('dashboard/landing/stats', payload);
            req.then(() => {
                Swal.fire({ icon: 'success', title: props.type === 'edit' ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح', showConfirmButton: false, timer: 1500 });
                emit('created');
                const modal = bootstrap.Modal.getInstance(document.getElementById('stat-modal'));
                if (modal) modal.hide();
            })
            .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
            .finally(() => { loading.value = false; });
        };

        return { loading, errors, form, submit };
    }
};
</script>
