<template>
    <div v-if="modalOpen" class="modal fade" id="partner-modal" tabindex="-1" aria-hidden="true" :key="modalKey">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ modalTitle }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

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
                            <div class="mt-2" v-if="displayImage">
                                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;display:inline-block;">
                                    <img :src="displayImage"
                                        style="height:60px;max-width:160px;object-fit:contain;" alt="">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger-light ms-2"
                                    v-if="imagePreview" @click="clearImage">
                                    <i class="ri-close-line"></i> إلغاء
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">اسم الشريك <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.name" maxlength="120"
                                :class="{'is-invalid': errors.name}"
                                placeholder="مثال: Google">
                            <div class="invalid-feedback" v-if="errors.name">{{ errors.name[0] }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">رابط الموقع <small class="text-muted">(اختياري)</small></label>
                            <input type="url" class="form-control" v-model="form.url" maxlength="255"
                                :class="{'is-invalid': errors.url}"
                                placeholder="https://example.com">
                            <div class="invalid-feedback" v-if="errors.url">{{ errors.url[0] }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الترتيب <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="1"
                                :class="{'is-invalid': errors.sort_order}"
                                placeholder="1">
                            <div class="invalid-feedback" v-if="errors.sort_order">{{ errors.sort_order[0] }}</div>
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
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue';
import adminApi from '../../../../api/adminAxios';

const buildImageUrl = (row) => {
    if (!row) return null;

    let url = row.image || null;

    if (!url && row.image_path) {
        url = `/upload/general/${row.image_path.split('/').map(encodeURIComponent).join('/')}`;
    }

    if (!url) return null;
    if (url.startsWith('http') || url.startsWith('blob:')) return url;
    if (url.startsWith('/')) return `${window.location.origin}${url}`;

    return `${window.location.origin}/upload/general/${url.split('/').map(encodeURIComponent).join('/')}`;
};

export default {
    name: 'PartnerModal',
    props: {
        type: { default: 'create' },
        dataRow: { default: null },
        modalOpen: { type: Boolean, default: false },
        modalKey: { type: [String, Number], default: 0 },
    },
    emits: ['created', 'closed'],
    setup(props, { emit }) {
        const loading      = ref(false);
        const errors       = ref({});
        const imageInput   = ref(null);
        const imagePreview = ref(null);
        const imageFile    = ref(null);

        const defaultForm = () => ({
            name: '', url: '', sort_order: 1, is_active: true, current_image: null, current_image_path: null
        });
        const form = ref(defaultForm());

        const modalTitle = computed(() => {
            if (props.type === 'create') {
                return 'إضافة شريك جديد';
            }

            const name = props.dataRow?.name || form.value.name || '';

            return name ? `تعديل الشريك # ${name}` : 'تعديل الشريك';
        });

        const displayImage = computed(() => {
            if (imagePreview.value) return imagePreview.value;
            return buildImageUrl({ image: form.value.current_image, image_path: form.value.current_image_path });
        });

        const loadForm = () => {
            imagePreview.value = null;
            imageFile.value    = null;
            if (imageInput.value) imageInput.value.value = '';

            const row  = props.dataRow;
            const type = props.type;

            if (row && type === 'edit') {
                form.value = {
                    name:               row.name || '',
                    url:                row.url || '',
                    sort_order:         row.sort_order ?? 1,
                    is_active:          !!row.is_active,
                    current_image:      row.image || null,
                    current_image_path: row.image_path || null,
                };
            } else if (type === 'create') {
                form.value = defaultForm();
            }
        };

        watch(
            () => [props.type, props.dataRow, props.modalOpen],
            () => { if (props.modalOpen) loadForm(); },
            { immediate: true, deep: true }
        );

        const hideModal = () => {
            const el = document.getElementById('partner-modal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;
            if (modal) modal.hide();
        };

        const onModalHidden = () => {
            emit('closed');
        };

        onMounted(() => {
            const el = document.getElementById('partner-modal');
            if (el) el.addEventListener('hidden.bs.modal', onModalHidden);
        });

        onBeforeUnmount(() => {
            const el = document.getElementById('partner-modal');
            if (el) el.removeEventListener('hidden.bs.modal', onModalHidden);
        });

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

        const validatePartner = () => {
            errors.value = {};
            const add = (key, message) => { errors.value[key] = [message]; };
            const MAX_NAME = 120;

            if (!(form.value.name || '').trim()) add('name', 'اسم الشريك مطلوب.');
            else if (form.value.name.length > MAX_NAME) add('name', `اسم الشريك يجب ألا يتجاوز ${MAX_NAME} حرف.`);

            if ((form.value.url || '').trim() && !/^https?:\/\/.+/i.test(form.value.url)) {
                add('url', 'رابط الموقع غير صالح.');
            } else if ((form.value.url || '').length > 255) {
                add('url', 'رابط الموقع طويل جداً.');
            }

            const order = Number(form.value.sort_order);
            if (form.value.sort_order === '' || form.value.sort_order === null || form.value.sort_order === undefined) {
                add('sort_order', 'الترتيب مطلوب.');
            } else if (!Number.isInteger(order) || order < 1) {
                add('sort_order', 'الترتيب يجب أن يكون 1 على الأقل.');
            }

            if (props.type === 'create' && !imageFile.value) add('image', 'لوجو الشريك مطلوب.');

            return Object.keys(errors.value).length === 0;
        };

        const submit = () => {
            if (!validatePartner()) return;

            loading.value = true;
            errors.value  = {};

            const fd = new FormData();
            fd.append('name', form.value.name.trim());
            fd.append('url', form.value.url || '');
            fd.append('sort_order', form.value.sort_order);
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (imageFile.value) fd.append('image', imageFile.value);
            if (props.type === 'edit') fd.append('_method', 'PUT');

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
                    hideModal();
                })
                .catch(err => {
                    if (err.response?.data?.errors) errors.value = err.response.data.errors;
                })
                .finally(() => { loading.value = false; });
        };

        return { loading, errors, form, imageInput, imagePreview, displayImage, modalTitle, handleImage, clearImage, submit, hideModal };
    }
};
</script>
