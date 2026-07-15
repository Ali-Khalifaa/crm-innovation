<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">شريط الاشتراك</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">شريط الاشتراك</li>
                </ol></nav>
            </div>
        </div>

        <loader v-if="loading" />

        <form v-else @submit.prevent="submitSection">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-envelope me-2"></i>إعدادات شريط الاشتراك</h6></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان (عربي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.title.ar" maxlength="100"
                                    :class="{'is-invalid': fieldError('title.ar')}" placeholder="اشترك في نشرتنا البريدية">
                                <div class="invalid-feedback" v-if="fieldError('title.ar')">{{ fieldError('title.ar') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.title.en" maxlength="100"
                                    :class="{'is-invalid': fieldError('title.en')}" placeholder="Subscribe to Our Newsletter">
                                <div class="invalid-feedback" v-if="fieldError('title.en')">{{ fieldError('title.en') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Placeholder (عربي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.placeholder.ar" maxlength="100"
                                    :class="{'is-invalid': fieldError('placeholder.ar')}" placeholder="بريدك الإلكتروني">
                                <div class="invalid-feedback" v-if="fieldError('placeholder.ar')">{{ fieldError('placeholder.ar') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Placeholder (إنجليزي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.placeholder.en" maxlength="100"
                                    :class="{'is-invalid': fieldError('placeholder.en')}" placeholder="Your Email">
                                <div class="invalid-feedback" v-if="fieldError('placeholder.en')">{{ fieldError('placeholder.en') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">نص الزر (عربي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.button_text.ar" maxlength="100"
                                    :class="{'is-invalid': fieldError('button_text.ar')}" placeholder="اشترك الآن">
                                <div class="invalid-feedback" v-if="fieldError('button_text.ar')">{{ fieldError('button_text.ar') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">نص الزر (إنجليزي) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.button_text.en" maxlength="100"
                                    :class="{'is-invalid': fieldError('button_text.en')}" placeholder="Subscribe Now">
                                <div class="invalid-feedback" v-if="fieldError('button_text.en')">{{ fieldError('button_text.en') }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">صورة الخلفية <small class="text-muted">(اختياري)</small></label>
                                <input type="file" class="form-control" ref="bgImageInput" accept="image/*" @change="handleBgImage"
                                    :class="{'is-invalid': fieldError('bg_image')}">
                                <div class="invalid-feedback" v-if="fieldError('bg_image')">{{ fieldError('bg_image') }}</div>
                                <div class="mt-2" v-if="displayBgImage">
                                    <img :src="displayBgImage" style="height:80px;border-radius:8px;border:1px solid #e2e8f0;object-fit:cover;" alt="">
                                    <button type="button" class="btn btn-sm btn-danger-light ms-2" @click="clearBgImage">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="custom-toggle-switch d-flex align-items-center">
                                    <input id="nl-section-active" type="checkbox" v-model="form.is_active">
                                    <label for="nl-section-active" class="label-primary"></label>
                                    <span class="ms-2">القسم نشط (يظهر في اللاندينج)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12" v-if="permission.includes('landing newsletter edit')">
                    <button type="submit" class="btn btn-primary px-5" :disabled="saving">
                        <span v-if="saving"><i class="ri-loader-2-fill me-1"></i>جاري الحفظ...</span>
                        <span v-else><i class="ri-save-line me-1"></i>حفظ الإعدادات</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import adminApi from '../../../../api/adminAxios';

export default {
    name: 'LandingNewsletterSettings',
    setup() {
        const store = useStore();
        const permission = computed(() => store.state.authAdmin.permission);
        const loading = ref(true);
        const saving = ref(false);
        const errors = ref({});
        const bgImageFile = ref(null);
        const bgImagePreview = ref(null);
        const bgImageInput = ref(null);

        const form = ref({
            title: { ar: '', en: '' },
            placeholder: { ar: '', en: '' },
            button_text: { ar: '', en: '' },
            is_active: true,
            current_bg_image: null,
        });

        const fieldError = (key) => errors.value[key]?.[0] || '';
        const displayBgImage = computed(() => bgImagePreview.value || form.value.current_bg_image || null);

        const loadSection = () => adminApi.get('dashboard/landing/newsletter').then(res => {
            const d = res.data.data;
            bgImagePreview.value = null;
            bgImageFile.value = null;
            if (bgImageInput.value) bgImageInput.value.value = '';
            form.value = {
                title: d.title || { ar: '', en: '' },
                placeholder: d.placeholder || { ar: '', en: '' },
                button_text: d.button_text || { ar: '', en: '' },
                is_active: !!d.is_active,
                current_bg_image: d.bg_image || null,
            };
        });

        const handleBgImage = (e) => {
            const file = e.target.files[0];
            if (!file) return;
            bgImageFile.value = file;
            bgImagePreview.value = URL.createObjectURL(file);
        };

        const clearBgImage = () => {
            bgImageFile.value = null;
            bgImagePreview.value = null;
            form.value.current_bg_image = null;
            if (bgImageInput.value) bgImageInput.value.value = '';
        };

        const submitSection = () => {
            saving.value = true;
            errors.value = {};
            const fd = new FormData();
            fd.append('title', JSON.stringify(form.value.title));
            fd.append('placeholder', JSON.stringify(form.value.placeholder));
            fd.append('button_text', JSON.stringify(form.value.button_text));
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (bgImageFile.value) fd.append('bg_image', bgImageFile.value);
            fd.append('_method', 'PUT');

            adminApi.post('dashboard/landing/newsletter', fd)
                .then(() => Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', showConfirmButton: false, timer: 1500 }))
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; loadSection(); });
        };

        onMounted(() => {
            loadSection().finally(() => { loading.value = false; });
        });

        return {
            permission, loading, saving, form, errors, fieldError,
            displayBgImage, bgImageInput, handleBgImage, clearBgImage, submitSection,
        };
    },
};
</script>
