<template>
    <div class="modal fade" id="howitworks-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ type === 'create' ? 'إضافة خطوة جديدة' : 'تعديل الخطوة' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">رقم الخطوة <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.step_number"
                                :class="{'is-invalid': errors.step_number}" min="1" placeholder="1">
                            <div class="invalid-feedback" v-if="errors.step_number">{{ errors.step_number[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.title_en"
                                :class="{'is-invalid': errors.title_en}" placeholder="Create Your Account">
                            <div class="invalid-feedback" v-if="errors.title_en">{{ errors.title_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">العنوان (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.title_ar"
                                :class="{'is-invalid': errors.title_ar}" placeholder="أنشئ حسابك">
                            <div class="invalid-feedback" v-if="errors.title_ar">{{ errors.title_ar[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الوصف (إنجليزي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.description_en"
                                :class="{'is-invalid': errors.description_en}" rows="3"
                                placeholder="Sign up in minutes..."></textarea>
                            <div class="invalid-feedback" v-if="errors.description_en">{{ errors.description_en[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الوصف (عربي) <span class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="form.description_ar"
                                :class="{'is-invalid': errors.description_ar}" rows="3"
                                placeholder="سجّل خلال دقائق..."></textarea>
                            <div class="invalid-feedback" v-if="errors.description_ar">{{ errors.description_ar[0] }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">صورة الخطوة <small class="text-muted">(اختياري)</small></label>
                            <input type="file" class="form-control" ref="imageInput"
                                accept="image/*" @change="handleImage">
                            <div class="mt-2" v-if="imagePreview || (type === 'edit' && form.current_image)">
                                <img :src="imagePreview || form.current_image"
                                    style="height:80px;border-radius:8px;border:1px solid #e2e8f0;" alt="">
                                <button type="button" class="btn btn-sm btn-danger-light ms-2" @click="clearImage">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="howitworks-active" type="checkbox" v-model="form.is_active">
                                <label for="howitworks-active" class="label-primary"></label>
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
    name: 'HowItWorksModal',
    props: { type: { default: 'create' }, dataRow: { default: null } },
    emits: ['created'],
    setup(props, { emit }) {
        const loading      = ref(false);
        const errors       = ref({});
        const imageInput   = ref(null);
        const imagePreview = ref(null);
        const imageFile    = ref(null);

        const defaultForm = () => ({
            step_number: 1, title_en: '', title_ar: '',
            description_en: '', description_ar: '',
            sort_order: 0, is_active: true, current_image: null
        });
        const form = ref(defaultForm());

        watch(() => props.dataRow, (row) => {
            imagePreview.value = null;
            imageFile.value    = null;
            if (imageInput.value) imageInput.value.value = '';
            if (row && props.type === 'edit') {
                form.value = {
                    step_number: row.step_number, title_en: row.title_en, title_ar: row.title_ar,
                    description_en: row.description_en, description_ar: row.description_ar,
                    sort_order: row.sort_order, is_active: row.is_active,
                    current_image: row.image || null
                };
            } else {
                form.value = defaultForm();
            }
        }, { immediate: true });

        const handleImage = (e) => {
            const file = e.target.files[0];
            if (!file) return;
            imageFile.value    = file;
            imagePreview.value = URL.createObjectURL(file);
        };

        const clearImage = () => {
            imageFile.value    = null;
            imagePreview.value = null;
            form.value.current_image = null;
            if (imageInput.value) imageInput.value.value = '';
        };

        const submit = () => {
            loading.value = true; errors.value = {};
            const fd = new FormData();
            fd.append('step_number',    form.value.step_number);
            fd.append('title_en',       form.value.title_en);
            fd.append('title_ar',       form.value.title_ar);
            fd.append('description_en', form.value.description_en);
            fd.append('description_ar', form.value.description_ar);
            fd.append('sort_order',     form.value.sort_order);
            fd.append('is_active',      form.value.is_active ? 1 : 0);
            if (imageFile.value) fd.append('image', imageFile.value);
            if (props.type === 'edit') fd.append('_method', 'PUT');

            const url = props.type === 'edit'
                ? `dashboard/landing/how-it-works/${props.dataRow.id}`
                : 'dashboard/landing/how-it-works';

            adminApi.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
                    Swal.fire({ icon: 'success', title: props.type === 'edit' ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح', showConfirmButton: false, timer: 1500 });
                    emit('created');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('howitworks-modal'));
                    if (modal) modal.hide();
                })
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { loading.value = false; });
        };

        return { loading, errors, form, imageInput, imagePreview, handleImage, clearImage, submit };
    }
};
</script>
