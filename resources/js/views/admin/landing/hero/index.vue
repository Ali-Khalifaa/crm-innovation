<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">قسم الهيرو</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">قسم الهيرو</li>
                </ol></nav>
            </div>
        </div>

        <loader v-if="loading" />

        <form @submit.prevent="submit" enctype="multipart/form-data" v-else>
            <div class="row g-4">

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-heading me-2"></i>العناوين</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان الفرعي (إنجليزي)</label>
                                    <input type="text" class="form-control" v-model="form.subtitle_en" placeholder="Smart CRM for Modern Businesses">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان الفرعي (عربي)</label>
                                    <input type="text" class="form-control" v-model="form.subtitle_ar" placeholder="CRM ذكي للشركات الحديثة">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان الرئيسي (إنجليزي) <span class="text-danger">*</span>
                                        <small class="text-muted fw-normal"> — يدعم HTML مثل &lt;span&gt;</small>
                                    </label>
                                    <textarea class="form-control" v-model="form.title_en" rows="3"
                                        :class="{'is-invalid': errors.title_en}"
                                        placeholder='Grow Your &lt;span&gt;Sales Pipeline&lt;/span&gt;'></textarea>
                                    <div class="invalid-feedback" v-if="errors.title_en">{{ errors.title_en[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان الرئيسي (عربي) <span class="text-danger">*</span></label>
                                    <textarea class="form-control" v-model="form.title_ar" rows="3"
                                        :class="{'is-invalid': errors.title_ar}"
                                        placeholder='طوّر &lt;span&gt;خط مبيعاتك&lt;/span&gt;'></textarea>
                                    <div class="invalid-feedback" v-if="errors.title_ar">{{ errors.title_ar[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">الوصف (إنجليزي)</label>
                                    <textarea class="form-control" v-model="form.description_en" rows="3" placeholder="Manage contacts, track deals..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">الوصف (عربي)</label>
                                    <textarea class="form-control" v-model="form.description_ar" rows="3" placeholder="أدر جهات الاتصال..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-link me-2"></i>أزرار CTA</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">نص الزر الأول (إنجليزي)</label>
                                    <input type="text" class="form-control" v-model="form.btn_primary_text_en" placeholder="Start Free Trial">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">نص الزر الأول (عربي)</label>
                                    <input type="text" class="form-control" v-model="form.btn_primary_text_ar" placeholder="ابدأ مجاناً">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">رابط الزر الأول</label>
                                    <input type="text" class="form-control" v-model="form.btn_primary_url" placeholder="/register">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">نص الزر الثاني (إنجليزي)</label>
                                    <input type="text" class="form-control" v-model="form.btn_secondary_text_en" placeholder="View Pricing">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">نص الزر الثاني (عربي)</label>
                                    <input type="text" class="form-control" v-model="form.btn_secondary_text_ar" placeholder="عرض الأسعار">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">رابط الزر الثاني</label>
                                    <input type="text" class="form-control" v-model="form.btn_secondary_url" placeholder="/pricing">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-image me-2"></i>الصورة والحالة</h6></div>
                        <div class="card-body">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">صورة الهيرو (اختياري)</label>
                                    <input type="file" class="form-control" @change="onImage" accept="image/*">
                                    <div v-if="form.image || imagePreview" class="mt-2">
                                        <img :src="imagePreview || form.image" alt="" style="max-height:120px;border-radius:8px;">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end pb-1">
                                    <div class="custom-toggle-switch d-flex align-items-center">
                                        <input id="hero-active" type="checkbox" v-model="form.is_active">
                                        <label for="hero-active" class="label-primary"></label>
                                        <span class="ms-2 fw-semibold">نشط</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-5" :disabled="saving">
                        <span v-if="saving"><i class="ri-loader-2-fill me-1"></i>جاري الحفظ...</span>
                        <span v-else><i class="ri-save-line me-1"></i>حفظ قسم الهيرو</span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import adminApi from '../../../../api/adminAxios';

export default {
    name: 'LandingHero',
    setup() {
        const loading      = ref(true);
        const saving       = ref(false);
        const errors       = ref({});
        const imageFile    = ref(null);
        const imagePreview = ref(null);
        const form = ref({
            title_en: '', title_ar: '',
            subtitle_en: '', subtitle_ar: '',
            description_en: '', description_ar: '',
            btn_primary_text_en: '', btn_primary_text_ar: '', btn_primary_url: '',
            btn_secondary_text_en: '', btn_secondary_text_ar: '', btn_secondary_url: '',
            image: null, is_active: true,
        });

        const load = () => {
            loading.value = true;
            adminApi.get('dashboard/landing/hero')
                .then(res => { Object.assign(form.value, res.data.data); })
                .finally(() => { loading.value = false; });
        };

        const onImage = (e) => {
            imageFile.value = e.target.files[0];
            imagePreview.value = URL.createObjectURL(imageFile.value);
        };

        const submit = () => {
            saving.value = true;
            errors.value = {};
            const fd = new FormData();
            Object.entries(form.value).forEach(([k, v]) => {
                if (v !== null && v !== undefined && k !== 'image') fd.append(k, v);
            });
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (imageFile.value) fd.append('image', imageFile.value);
            fd.append('_method', 'PUT');
            adminApi.post('dashboard/landing/hero', fd)
                .then(() => {
                    Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', showConfirmButton: false, timer: 1500 });
                    load();
                })
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; });
        };

        onMounted(load);
        return { loading, saving, errors, form, imagePreview, onImage, submit };
    }
};
</script>
