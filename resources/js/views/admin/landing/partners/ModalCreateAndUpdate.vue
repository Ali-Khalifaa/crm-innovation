<template>
    <div class="modal fade" id="partner-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ type === 'create' ? 'إضافة شريك جديد' : 'تعديل الشريك' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Logo Upload -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                لوجو الشريك
                                <span v-if="type === 'create'" class="text-danger">*</span>
                                <small v-else class="text-muted">(اتركه فارغاً للإبقاء على الحالي)</small>
                            </label>
                            <input type="file" class="form-control" ref="imageInput"
                                accept="image/*" @change="handleImage"
                                :class="{'is-invalid': errors.image}">
                            <div class="invalid-feedback" v-if="errors.image">{{ errors.image[0] }}</div>
                            <div class="mt-2" v-if="imagePreview || form.current_image">
                                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;display:inline-block;">
                                    <img :src="imagePreview || form.current_image"
                                        style="height:60px;max-width:160px;object-fit:contain;" alt="">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger-light ms-2"
                                    v-if="imagePreview" @click="clearImage">
                                    <i class="ri-close-line"></i> إلغاء
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">اسم الشريك <small class="text-muted">(للـ alt text)</small></label>
                            <input type="text" class="form-control" v-model="form.name" placeholder="مثال: Google">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">رابط الموقع <small class="text-muted">(اختياري)</small></label>
                            <input type="url" class="form-control" v-model="form.url"
                                :class="{'is-invalid': errors.url}"
                                placeholder="https://example.com">
                            <div class="invalid-feedback" v-if="errors.url">{{ errors.url[0] }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="0">
                        </div>

                        <div class="col-md-6 d-flex align-items-end pb-1">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="partner-active" type="checkbox" v-model="form.is_active">
                                <label for="partner-active" class="label-primary"></label>
                                <span class="ms-2">نشط (يظهر في السلايدر)</span>
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
    name: 'PartnerModal',
    props: { type: { default: 'create' }, dataRow: { default: null } },
    emits: ['created'],
    setup(props, { emit }) {
        const loading      = ref(false);
        const errors       = ref({});
        const imageInput   = ref(null);
        const imagePreview = ref(null);
        const imageFile    = ref(null);

        const defaultForm = () => ({
            name: '', url: '', sort_order: 0, is_active: true, current_image: null
        });
        const form = ref(defaultForm());

        watch(() => props.dataRow, (row) => {
            imagePreview.value = null;
            imageFile.value    = null;
            if (imageInput.value) imageInput.value.value = '';

            if (row && props.type === 'edit') {
                form.value = {
                    name:          row.name  || '',
                    url:           row.url   || '',
                    sort_order:    row.sort_order,
                    is_active:     row.is_active,
                    current_image: row.image || null,
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
            if (imageInput.value) imageInput.value.value = '';
        };

        const submit = () => {
            loading.value = true;
            errors.value  = {};

            const fd = new FormData();
            if (form.value.name)        fd.append('name',       form.value.name);
            if (form.value.url)         fd.append('url',        form.value.url);
            fd.append('sort_order', form.value.sort_order);
            fd.append('is_active',  form.value.is_active ? 1 : 0);
            if (imageFile.value)        fd.append('image', imageFile.value);
            if (props.type === 'edit')  fd.append('_method', 'PUT');

            const url = props.type === 'edit'
                ? `dashboard/landing/partners/${props.dataRow.id}`
                : 'dashboard/landing/partners';

            adminApi.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: props.type === 'edit' ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    emit('created');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('partner-modal'));
                    if (modal) modal.hide();
                })
                .catch(err => {
                    if (err.response?.data?.errors) errors.value = err.response.data.errors;
                })
                .finally(() => { loading.value = false; });
        };

        return { loading, errors, form, imageInput, imagePreview, handleImage, clearImage, submit };
    }
};
</script>
