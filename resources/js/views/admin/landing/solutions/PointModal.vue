<template>
    <div class="modal fade" id="solution-point-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ modalTitle }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">النص (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.text_ar" maxlength="120"
                                :class="{'is-invalid': errors.text_ar}" placeholder="لوحة تحكم شاملة">
                            <div class="invalid-feedback" v-if="errors.text_ar">{{ errors.text_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">النص (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.text_en" maxlength="120"
                                :class="{'is-invalid': errors.text_en}" placeholder="Comprehensive dashboard">
                            <div class="invalid-feedback" v-if="errors.text_en">{{ errors.text_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="1"
                                :class="{'is-invalid': errors.sort_order}" placeholder="1">
                            <div class="invalid-feedback" v-if="errors.sort_order">{{ errors.sort_order[0] }}</div>
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="solution-point-active" type="checkbox" v-model="form.is_active">
                                <label for="solution-point-active" class="label-primary"></label>
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
    name: 'SolutionPointModal',
    props: { type: { default: 'create' }, dataRow: { default: null } },
    emits: ['created', 'closed'],
    setup(props, { emit }) {
        const loading = ref(false);
        const errors  = ref({});

        const defaultForm = () => ({
            text_ar: '', text_en: '',
            sort_order: 1,
            is_active: true,
        });
        const form = ref(defaultForm());

        const modalTitle = computed(() => {
            if (props.type === 'create') return 'إضافة نقطة جديدة';

            const text = props.dataRow?.text_ar || props.dataRow?.text_en || '';

            return text ? `تعديل النقطة # ${text}` : 'تعديل النقطة';
        });

        const loadForm = () => {
            const row  = props.dataRow;
            const type = props.type;

            if (row && type === 'edit') {
                form.value = {
                    text_ar: row.text_ar || '',
                    text_en: row.text_en || '',
                    sort_order: row.sort_order ?? 1,
                    is_active: !!row.is_active,
                };
            } else if (type === 'create') {
                form.value = defaultForm();
            }
        };

        watch(
            () => [props.type, props.dataRow],
            () => loadForm(),
            { immediate: true, deep: true }
        );

        const hideModal = () => {
            const el = document.getElementById('solution-point-modal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;
            if (modal) modal.hide();
        };

        const onModalHidden = () => {
            emit('closed');
        };

        onMounted(() => {
            const el = document.getElementById('solution-point-modal');
            if (el) el.addEventListener('hidden.bs.modal', onModalHidden);
        });

        onBeforeUnmount(() => {
            const el = document.getElementById('solution-point-modal');
            if (el) el.removeEventListener('hidden.bs.modal', onModalHidden);
        });

        const validatePoint = () => {
            errors.value = {};
            const add = (key, message) => { errors.value[key] = [message]; };
            const MAX = 120;

            if (!(form.value.text_ar || '').trim()) add('text_ar', 'النص (عربي) مطلوب.');
            else if (form.value.text_ar.length > MAX) add('text_ar', `النص (عربي) يجب ألا يتجاوز ${MAX} حرف.`);

            if (!(form.value.text_en || '').trim()) add('text_en', 'النص (إنجليزي) مطلوب.');
            else if (form.value.text_en.length > MAX) add('text_en', `النص (إنجليزي) يجب ألا يتجاوز ${MAX} حرف.`);

            const order = Number(form.value.sort_order);
            if (!form.value.sort_order && form.value.sort_order !== 0) add('sort_order', 'الترتيب مطلوب.');
            else if (!Number.isInteger(order) || order < 1) add('sort_order', 'الترتيب يجب أن يكون 1 على الأقل.');

            return Object.keys(errors.value).length === 0;
        };

        const submit = () => {
            if (!validatePoint()) return;

            loading.value = true;
            errors.value  = {};
            const fd = new FormData();
            fd.append('text_ar', form.value.text_ar.trim());
            fd.append('text_en', form.value.text_en.trim());
            fd.append('sort_order', form.value.sort_order);
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (props.type === 'edit') fd.append('_method', 'PUT');

            const url = props.type === 'edit'
                ? `dashboard/landing/solution-points/${props.dataRow.id}`
                : 'dashboard/landing/solution-points';

            adminApi.post(url, fd)
                .then(() => {
                    Swal.fire({ icon: 'success', title: props.type === 'edit' ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح', showConfirmButton: false, timer: 1500 });
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
