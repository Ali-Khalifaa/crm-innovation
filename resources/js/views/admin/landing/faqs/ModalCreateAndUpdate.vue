<template>
    <div class="modal fade" id="faq-item-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ modalTitle }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السؤال (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.question_ar" maxlength="500"
                                :class="{'is-invalid': errors.question_ar}" placeholder="ما هو نظام CRM؟">
                            <div class="invalid-feedback" v-if="errors.question_ar">{{ errors.question_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السؤال (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.question_en" maxlength="500"
                                :class="{'is-invalid': errors.question_en}" placeholder="What is a CRM system?">
                            <div class="invalid-feedback" v-if="errors.question_en">{{ errors.question_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الإجابة (عربي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.answer_ar" rows="4" maxlength="2000"
                                :class="{'is-invalid': errors.answer_ar}"></textarea>
                            <div class="invalid-feedback" v-if="errors.answer_ar">{{ errors.answer_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الإجابة (إنجليزي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.answer_en" rows="4" maxlength="2000"
                                :class="{'is-invalid': errors.answer_en}"></textarea>
                            <div class="invalid-feedback" v-if="errors.answer_en">{{ errors.answer_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="1"
                                :class="{'is-invalid': errors.sort_order}" placeholder="1">
                            <div class="invalid-feedback" v-if="errors.sort_order">{{ errors.sort_order[0] }}</div>
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="faq-item-active" type="checkbox" v-model="form.is_active">
                                <label for="faq-item-active" class="label-primary"></label>
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
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue';
import adminApi from '../../../../api/adminAxios';

export default {
    name: 'FaqItemModal',
    props: {
        type: { default: 'create' },
        dataRow: { default: null },
    },
    emits: ['created', 'closed'],
    setup(props, { emit }) {
        const loading = ref(false);
        const errors  = ref({});

        const defaultForm = () => ({
            question_ar: '', question_en: '',
            answer_ar: '', answer_en: '',
            sort_order: 1, is_active: true,
        });
        const form = ref(defaultForm());

        const modalTitle = computed(() => {
            if (props.type === 'create') return 'إضافة سؤال جديد';
            const q = props.dataRow?.question_ar || props.dataRow?.question_en || '';
            return q ? `تعديل السؤال # ${q}` : 'تعديل السؤال';
        });

        const loadForm = () => {
            const row = props.dataRow;
            if (row && props.type === 'edit') {
                form.value = {
                    question_ar: row.question_ar || '',
                    question_en: row.question_en || '',
                    answer_ar: row.answer_ar || '',
                    answer_en: row.answer_en || '',
                    sort_order: row.sort_order ?? 1,
                    is_active: !!row.is_active,
                };
            } else {
                form.value = defaultForm();
            }
        };

        watch(() => [props.type, props.dataRow], () => loadForm(), { immediate: true, deep: true });

        const hideModal = () => {
            const el = document.getElementById('faq-item-modal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;
            if (modal) modal.hide();
        };

        const onHidden = () => emit('closed');

        onMounted(() => {
            const el = document.getElementById('faq-item-modal');
            if (el) el.addEventListener('hidden.bs.modal', onHidden);
        });

        onBeforeUnmount(() => {
            const el = document.getElementById('faq-item-modal');
            if (el) el.removeEventListener('hidden.bs.modal', onHidden);
        });

        const validateItem = () => {
            errors.value = {};
            const add = (key, message) => { errors.value[key] = [message]; };

            if (!(form.value.question_ar || '').trim()) add('question_ar', 'السؤال (عربي) مطلوب.');
            if (!(form.value.question_en || '').trim()) add('question_en', 'السؤال (إنجليزي) مطلوب.');
            if (!(form.value.answer_ar || '').trim()) add('answer_ar', 'الإجابة (عربي) مطلوبة.');
            if (!(form.value.answer_en || '').trim()) add('answer_en', 'الإجابة (إنجليزي) مطلوبة.');

            const order = Number(form.value.sort_order);
            if (!form.value.sort_order && form.value.sort_order !== 0) add('sort_order', 'الترتيب مطلوب.');
            else if (!Number.isInteger(order) || order < 1) add('sort_order', 'الترتيب يجب أن يكون 1 على الأقل.');

            return Object.keys(errors.value).length === 0;
        };

        const submit = () => {
            if (!validateItem()) return;

            loading.value = true;
            errors.value  = {};
            const payload = {
                question_ar: form.value.question_ar.trim(),
                question_en: form.value.question_en.trim(),
                answer_ar: form.value.answer_ar.trim(),
                answer_en: form.value.answer_en.trim(),
                sort_order: form.value.sort_order,
                is_active: form.value.is_active ? 1 : 0,
            };

            const isEdit = props.type === 'edit' && props.dataRow?.id;
            const req = isEdit
                ? adminApi.put(`dashboard/landing/faq-items/${props.dataRow.id}`, payload)
                : adminApi.post('dashboard/landing/faq-items', payload);

            req.then(() => {
                Swal.fire({
                    icon: 'success',
                    title: isEdit ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح',
                    showConfirmButton: false,
                    timer: 1500,
                });
                emit('created');
                hideModal();
            })
            .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
            .finally(() => { loading.value = false; });
        };

        return { loading, errors, form, modalTitle, submit };
    }
};
</script>
