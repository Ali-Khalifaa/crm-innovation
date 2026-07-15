<template>
    <div class="modal fade" id="testimonial-item-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ modalTitle }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الاسم (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.name_ar" maxlength="100"
                                :class="{'is-invalid': errors.name_ar}" placeholder="أحمد محمد">
                            <div class="invalid-feedback" v-if="errors.name_ar">{{ errors.name_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الاسم (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.name_en" maxlength="100"
                                :class="{'is-invalid': errors.name_en}" placeholder="Ahmed Mohamed">
                            <div class="invalid-feedback" v-if="errors.name_en">{{ errors.name_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المسمى الوظيفي (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.designation_ar" maxlength="120"
                                :class="{'is-invalid': errors.designation_ar}" placeholder="مدير شركة">
                            <div class="invalid-feedback" v-if="errors.designation_ar">{{ errors.designation_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المسمى الوظيفي (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.designation_en" maxlength="120"
                                :class="{'is-invalid': errors.designation_en}" placeholder="Company Manager">
                            <div class="invalid-feedback" v-if="errors.designation_en">{{ errors.designation_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الرأي (عربي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.review_ar" rows="3" maxlength="1000"
                                :class="{'is-invalid': errors.review_ar}"></textarea>
                            <div class="invalid-feedback" v-if="errors.review_ar">{{ errors.review_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الرأي (إنجليزي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.review_en" rows="3" maxlength="1000"
                                :class="{'is-invalid': errors.review_en}"></textarea>
                            <div class="invalid-feedback" v-if="errors.review_en">{{ errors.review_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">التقييم <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="form.rating" :class="{'is-invalid': errors.rating}">
                                <option v-for="n in 5" :key="n" :value="n">{{ n }} نجمة</option>
                            </select>
                            <div class="invalid-feedback" v-if="errors.rating">{{ errors.rating[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="1"
                                :class="{'is-invalid': errors.sort_order}" placeholder="1">
                            <div class="invalid-feedback" v-if="errors.sort_order">{{ errors.sort_order[0] }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">الصورة <small class="text-muted">(اختياري)</small></label>
                            <input type="file" class="form-control" ref="imageInput" accept="image/*" @change="handleImage"
                                :class="{'is-invalid': errors.image}">
                            <div class="invalid-feedback" v-if="errors.image">{{ errors.image[0] }}</div>
                            <div class="mt-2" v-if="displayImage">
                                <img :src="displayImage" style="height:60px;width:60px;border-radius:50%;border:1px solid #e2e8f0;object-fit:cover;" alt="">
                                <button type="button" class="btn btn-sm btn-danger-light ms-2" @click="clearImage">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="testimonial-item-active" type="checkbox" v-model="form.is_active">
                                <label for="testimonial-item-active" class="label-primary"></label>
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
    name: 'TestimonialItemModal',
    props: {
        type: { default: 'create' },
        dataRow: { default: null },
    },
    emits: ['created', 'closed'],
    setup(props, { emit }) {
        const loading      = ref(false);
        const errors       = ref({});
        const imageInput   = ref(null);
        const imagePreview = ref(null);
        const imageFile    = ref(null);

        const defaultForm = () => ({
            name_ar: '', name_en: '',
            designation_ar: '', designation_en: '',
            review_ar: '', review_en: '',
            rating: 5,
            sort_order: 1,
            is_active: true,
            current_image: null,
        });
        const form = ref(defaultForm());

        const modalTitle = computed(() => {
            if (props.type === 'create') return 'إضافة رأي جديد';
            const name = props.dataRow?.name_ar || props.dataRow?.name_en || '';
            return name ? `تعديل الرأي # ${name}` : 'تعديل الرأي';
        });

        const displayImage = computed(() => {
            if (imagePreview.value) return imagePreview.value;
            return form.value.current_image || null;
        });

        const loadForm = () => {
            imagePreview.value = null;
            imageFile.value    = null;
            if (imageInput.value) imageInput.value.value = '';

            const row = props.dataRow;
            if (row) {
                form.value = {
                    name_ar: row.name_ar || '',
                    name_en: row.name_en || '',
                    designation_ar: row.designation_ar || '',
                    designation_en: row.designation_en || '',
                    review_ar: row.review_ar || '',
                    review_en: row.review_en || '',
                    rating: row.rating ?? 5,
                    sort_order: row.sort_order ?? 1,
                    is_active: !!row.is_active,
                    current_image: buildImageUrl(row),
                };
            } else {
                form.value = defaultForm();
            }
        };

        watch(() => [props.type, props.dataRow], () => loadForm(), { immediate: true, deep: true });

        const hideModal = () => {
            const el = document.getElementById('testimonial-item-modal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;
            if (modal) modal.hide();
        };

        const onHidden = () => emit('closed');

        onMounted(() => {
            const el = document.getElementById('testimonial-item-modal');
            if (el) el.addEventListener('hidden.bs.modal', onHidden);
        });

        onBeforeUnmount(() => {
            const el = document.getElementById('testimonial-item-modal');
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

            if (!(form.value.name_ar || '').trim()) add('name_ar', 'الاسم (عربي) مطلوب.');
            else if (form.value.name_ar.length > 100) add('name_ar', 'الاسم (عربي) يجب ألا يتجاوز 100 حرف.');

            if (!(form.value.name_en || '').trim()) add('name_en', 'الاسم (إنجليزي) مطلوب.');
            else if (form.value.name_en.length > 100) add('name_en', 'الاسم (إنجليزي) يجب ألا يتجاوز 100 حرف.');

            if (!(form.value.designation_ar || '').trim()) add('designation_ar', 'المسمى الوظيفي (عربي) مطلوب.');
            else if (form.value.designation_ar.length > 120) add('designation_ar', 'المسمى الوظيفي (عربي) يجب ألا يتجاوز 120 حرف.');

            if (!(form.value.designation_en || '').trim()) add('designation_en', 'المسمى الوظيفي (إنجليزي) مطلوب.');
            else if (form.value.designation_en.length > 120) add('designation_en', 'المسمى الوظيفي (إنجليزي) يجب ألا يتجاوز 120 حرف.');

            if (!(form.value.review_ar || '').trim()) add('review_ar', 'الرأي (عربي) مطلوب.');
            else if (form.value.review_ar.length > 1000) add('review_ar', 'الرأي (عربي) يجب ألا يتجاوز 1000 حرف.');

            if (!(form.value.review_en || '').trim()) add('review_en', 'الرأي (إنجليزي) مطلوب.');
            else if (form.value.review_en.length > 1000) add('review_en', 'الرأي (إنجليزي) يجب ألا يتجاوز 1000 حرف.');

            const rating = Number(form.value.rating);
            if (!Number.isInteger(rating) || rating < 1 || rating > 5) add('rating', 'التقييم يجب أن يكون بين 1 و 5.');

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
            fd.append('name_ar', form.value.name_ar.trim());
            fd.append('name_en', form.value.name_en.trim());
            fd.append('designation_ar', form.value.designation_ar.trim());
            fd.append('designation_en', form.value.designation_en.trim());
            fd.append('review_ar', form.value.review_ar.trim());
            fd.append('review_en', form.value.review_en.trim());
            fd.append('rating', form.value.rating);
            fd.append('sort_order', form.value.sort_order);
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (imageFile.value) fd.append('image', imageFile.value);

            const isEdit = props.type === 'edit' && props.dataRow?.id;
            const url = isEdit
                ? `dashboard/landing/testimonial-items/${props.dataRow.id}`
                : 'dashboard/landing/testimonial-items';
            if (isEdit) fd.append('_method', 'PUT');

            adminApi.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
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

        return { loading, errors, form, imageInput, displayImage, modalTitle, handleImage, clearImage, submit };
    }
};
</script>
