<template>
    <div class="modal fade" id="faq-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ type === 'create' ? 'إضافة سؤال جديد' : 'تعديل السؤال' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السؤال (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.question_en"
                                :class="{'is-invalid': errors.question_en}"
                                placeholder="What is CRM Innovation?">
                            <div class="invalid-feedback" v-if="errors.question_en">{{ errors.question_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السؤال (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.question_ar"
                                :class="{'is-invalid': errors.question_ar}"
                                placeholder="ما هو CRM Innovation؟">
                            <div class="invalid-feedback" v-if="errors.question_ar">{{ errors.question_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الإجابة (إنجليزي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.answer_en"
                                :class="{'is-invalid': errors.answer_en}" rows="4"
                                placeholder="CRM Innovation is a..."></textarea>
                            <div class="invalid-feedback" v-if="errors.answer_en">{{ errors.answer_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الإجابة (عربي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.answer_ar"
                                :class="{'is-invalid': errors.answer_ar}" rows="4"
                                placeholder="CRM Innovation هو..."></textarea>
                            <div class="invalid-feedback" v-if="errors.answer_ar">{{ errors.answer_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end pb-1">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="faq-active" type="checkbox" v-model="form.is_active">
                                <label for="faq-active" class="label-primary"></label>
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
    name: 'FaqModal',
    props: { type: { default: 'create' }, dataRow: { default: null } },
    emits: ['created'],
    setup(props, { emit }) {
        const loading = ref(false);
        const errors  = ref({});
        const defaultForm = () => ({ question_en: '', question_ar: '', answer_en: '', answer_ar: '', sort_order: 0, is_active: true });
        const form = ref(defaultForm());

        watch(() => props.dataRow, (row) => {
            form.value = row && props.type === 'edit'
                ? { question_en: row.question_en, question_ar: row.question_ar, answer_en: row.answer_en, answer_ar: row.answer_ar, sort_order: row.sort_order, is_active: row.is_active }
                : defaultForm();
        }, { immediate: true });

        const submit = () => {
            loading.value = true; errors.value = {};
            const payload = { ...form.value, is_active: form.value.is_active ? 1 : 0 };
            const req = props.type === 'edit'
                ? adminApi.put(`dashboard/landing/faqs/${props.dataRow.id}`, payload)
                : adminApi.post('dashboard/landing/faqs', payload);
            req.then(() => {
                Swal.fire({ icon: 'success', title: props.type === 'edit' ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح', showConfirmButton: false, timer: 1500 });
                emit('created');
                const modal = bootstrap.Modal.getInstance(document.getElementById('faq-modal'));
                if (modal) modal.hide();
            })
            .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
            .finally(() => { loading.value = false; });
        };

        return { loading, errors, form, submit };
    }
};
</script>
