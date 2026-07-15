<template>
    <div class="modal fade" id="stat-item-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ modalTitle }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">القيمة (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.value_ar" maxlength="50"
                                :class="{'is-invalid': errors.value_ar}" placeholder="2500">
                            <div class="invalid-feedback" v-if="errors.value_ar">{{ errors.value_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">القيمة (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.value_en" maxlength="50"
                                :class="{'is-invalid': errors.value_en}" placeholder="2500">
                            <div class="invalid-feedback" v-if="errors.value_en">{{ errors.value_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">اللاحقة <small class="text-muted">(+، %، M+)</small></label>
                            <input type="text" class="form-control" v-model="form.suffix" maxlength="10"
                                :class="{'is-invalid': errors.suffix}" placeholder="+">
                            <div class="invalid-feedback" v-if="errors.suffix">{{ errors.suffix[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="1"
                                :class="{'is-invalid': errors.sort_order}" placeholder="1">
                            <div class="invalid-feedback" v-if="errors.sort_order">{{ errors.sort_order[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">التسمية (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.label_ar" maxlength="100"
                                :class="{'is-invalid': errors.label_ar}" placeholder="مستخدم نشط">
                            <div class="invalid-feedback" v-if="errors.label_ar">{{ errors.label_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">التسمية (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.label_en" maxlength="100"
                                :class="{'is-invalid': errors.label_en}" placeholder="Active Users">
                            <div class="invalid-feedback" v-if="errors.label_en">{{ errors.label_en[0] }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">الأيقونة / الصورة <small class="text-muted">(اختياري)</small></label>
                            <input type="file" class="form-control" ref="imageInput" accept="image/*" @change="handleImage"
                                :class="{'is-invalid': errors.image}">
                            <div class="invalid-feedback" v-if="errors.image">{{ errors.image[0] }}</div>
                            <div class="mt-2" v-if="displayImage">
                                <img :src="displayImage" style="height:60px;border-radius:8px;border:1px solid #e2e8f0;object-fit:contain;" alt="">
                                <button type="button" class="btn btn-sm btn-danger-light ms-2" @click="clearImage">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="stat-item-active" type="checkbox" v-model="form.is_active">
                                <label for="stat-item-active" class="label-primary"></label>
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

const buildImageUrl = (row) => {
    if (!row) return null;
    let url = row.image || null;
    if (!url && row.image_file) {
        url = `/upload/general/${row.image_file.split('/').map(encodeURIComponent).join('/')}`;
    }
    if (!url) return null;
    if (url.startsWith('http') || url.startsWith('blob:')) return url;
    if (url.startsWith('/')) return `${window.location.origin}${url}`;
    return url;
};

export default {
    name: 'StatItemModal',
    props: { type: { default: 'create' }, dataRow: { default: null } },
    emits: ['created', 'closed'],
    setup(props, { emit }) {
        const loading      = ref(false);
        const errors       = ref({});
        const imageInput   = ref(null);
        const imagePreview = ref(null);
        const imageFile    = ref(null);

        const defaultForm = () => ({
            value_ar: '', value_en: '', suffix: '+',
            label_ar: '', label_en: '',
            sort_order: 1, is_active: true, current_image: null,
        });
        const form = ref(defaultForm());

        const modalTitle = computed(() => {
            if (props.type === 'create') return 'إضافة إحصاء جديد';
            const title = props.dataRow?.label_ar || props.dataRow?.label_en || props.dataRow?.value_ar || '';
            return title ? `تعديل الإحصاء # ${title}` : 'تعديل الإحصاء';
        });

        const displayImage = computed(() => {
            if (imagePreview.value) return imagePreview.value;
            return form.value.current_image || null;
        });

        const loadForm = () => {
            imagePreview.value = null;
            imageFile.value    = null;
            if (imageInput.value) imageInput.value.value = '';

            const row  = props.dataRow;
            const type = props.type;

            if (row && type === 'edit') {
                form.value = {
                    value_ar: row.value_ar || '',
                    value_en: row.value_en || '',
                    suffix: row.suffix || '+',
                    label_ar: row.label_ar || '',
                    label_en: row.label_en || '',
                    sort_order: row.sort_order ?? 1,
                    is_active: !!row.is_active,
                    current_image: buildImageUrl(row),
                };
            } else if (type === 'create') {
                form.value = defaultForm();
            }
        };

        watch(() => [props.type, props.dataRow], () => loadForm(), { immediate: true, deep: true });

        const hideModal = () => {
            const el = document.getElementById('stat-item-modal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;
            if (modal) modal.hide();
        };

        const onHidden = () => emit('closed');

        onMounted(() => {
            const el = document.getElementById('stat-item-modal');
            if (el) el.addEventListener('hidden.bs.modal', onHidden);
        });

        onBeforeUnmount(() => {
            const el = document.getElementById('stat-item-modal');
            if (el) el.removeEventListener('hidden.bs.modal', onHidden);
        });

        const handleImage = (e) => {
            const file = e.target.files[0];
            if (!file) return;
            imageFile.value    = file;
            imagePreview.value = URL.createObjectURL(file);
        };

        const clearImage = () => {
            imageFile.value = null;
            imagePreview.value = null;
            form.value.current_image = null;
            if (imageInput.value) imageInput.value.value = '';
        };

        const validateItem = () => {
            errors.value = {};
            const add = (key, message) => { errors.value[key] = [message]; };

            if (!(form.value.value_ar || '').trim()) add('value_ar', 'القيمة (عربي) مطلوبة.');
            if (!(form.value.value_en || '').trim()) add('value_en', 'القيمة (إنجليزي) مطلوبة.');
            if (!(form.value.label_ar || '').trim()) add('label_ar', 'التسمية (عربي) مطلوبة.');
            if (!(form.value.label_en || '').trim()) add('label_en', 'التسمية (إنجليزي) مطلوبة.');

            const order = Number(form.value.sort_order);
            if (!form.value.sort_order && form.value.sort_order !== 0) add('sort_order', 'الترتيب مطلوب.');
            else if (!Number.isInteger(order) || order < 1) add('sort_order', 'الترتيب يجب أن يكون 1 على الأقل.');

            return Object.keys(errors.value).length === 0;
        };

        const submit = () => {
            if (!validateItem()) return;

            loading.value = true;
            errors.value  = {};
            const fd = new FormData();
            fd.append('value_ar', form.value.value_ar.trim());
            fd.append('value_en', form.value.value_en.trim());
            fd.append('suffix', form.value.suffix || '');
            fd.append('label_ar', form.value.label_ar.trim());
            fd.append('label_en', form.value.label_en.trim());
            fd.append('sort_order', form.value.sort_order);
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (imageFile.value) fd.append('image', imageFile.value);
            if (props.type === 'edit') fd.append('_method', 'PUT');

            const url = props.type === 'edit'
                ? `dashboard/landing/stat-items/${props.dataRow.id}`
                : 'dashboard/landing/stat-items';

            adminApi.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
                    Swal.fire({ icon: 'success', title: props.type === 'edit' ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح', showConfirmButton: false, timer: 1500 });
                    emit('created');
                    hideModal();
                })
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { loading.value = false; });
        };

        return { loading, errors, form, imageInput, imagePreview, displayImage, modalTitle, handleImage, clearImage, submit };
    }
};
</script>
