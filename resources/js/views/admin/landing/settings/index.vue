<template>
    <div>
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">الإعدادات العامة للموقع</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">الإعدادات العامة</li>
                </ol></nav>
            </div>
        </div>

        <loader v-if="loading" />

        <form @submit.prevent="submit" v-else>
            <div class="row g-4">

                <!-- Contact Info -->
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-info-circle me-2"></i>معلومات التواصل</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">اسم الموقع (إنجليزي) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="form.site_name_en" :class="{'is-invalid': errors.site_name_en}">
                                    <div class="invalid-feedback" v-if="errors.site_name_en">{{ errors.site_name_en[0] }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">اسم الموقع (عربي) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" v-model="form.site_name_ar" :class="{'is-invalid': errors.site_name_ar}">
                                    <div class="invalid-feedback" v-if="errors.site_name_ar">{{ errors.site_name_ar[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" v-model="form.email" :class="{'is-invalid': errors.email}">
                                    <div class="invalid-feedback" v-if="errors.email">{{ errors.email[0] }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">رقم الهاتف</label>
                                    <input type="text" class="form-control" v-model="form.phone" placeholder="+20 100 000 0000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">واتساب</label>
                                    <input type="text" class="form-control" v-model="form.whatsapp" placeholder="+20 100 000 0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان (إنجليزي)</label>
                                    <input type="text" class="form-control" v-model="form.address_en" placeholder="Cairo, Egypt">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان (عربي)</label>
                                    <input type="text" class="form-control" v-model="form.address_ar" placeholder="القاهرة، مصر">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-share-alt me-2"></i>روابط السوشيال ميديا</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bx bxl-facebook text-primary me-1"></i>فيسبوك</label>
                                    <input type="url" class="form-control" v-model="form.facebook" placeholder="https://facebook.com/...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bx bxl-twitter text-info me-1"></i>تويتر / X</label>
                                    <input type="url" class="form-control" v-model="form.twitter" placeholder="https://twitter.com/...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bx bxl-linkedin text-primary me-1"></i>لينكد إن</label>
                                    <input type="url" class="form-control" v-model="form.linkedin" placeholder="https://linkedin.com/...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bx bxl-instagram text-danger me-1"></i>انستجرام</label>
                                    <input type="url" class="form-control" v-model="form.instagram" placeholder="https://instagram.com/...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bx bxl-youtube text-danger me-1"></i>يوتيوب</label>
                                    <input type="url" class="form-control" v-model="form.youtube" placeholder="https://youtube.com/...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-search-alt me-2"></i>إعدادات SEO</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">عنوان الصفحة (إنجليزي)</label>
                                    <input type="text" class="form-control" v-model="form.meta_title_en" placeholder="CRM Innovation — Smart CRM">
                                    <small class="text-muted">{{ (form.meta_title_en || '').length }}/160</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">عنوان الصفحة (عربي)</label>
                                    <input type="text" class="form-control" v-model="form.meta_title_ar" placeholder="CRM إنوفيشن">
                                    <small class="text-muted">{{ (form.meta_title_ar || '').length }}/160</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">وصف الصفحة (إنجليزي)</label>
                                    <textarea class="form-control" v-model="form.meta_description_en" rows="3" placeholder="Manage contacts, track deals..."></textarea>
                                    <small class="text-muted">{{ (form.meta_description_en || '').length }}/320</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">وصف الصفحة (عربي)</label>
                                    <textarea class="form-control" v-model="form.meta_description_ar" rows="3" placeholder="أدر جهات الاتصال..."></textarea>
                                    <small class="text-muted">{{ (form.meta_description_ar || '').length }}/320</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12">
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
import { ref, onMounted } from 'vue';
import adminApi from '../../../../api/adminAxios';

export default {
    name: 'LandingSettings',
    setup() {
        const loading = ref(true);
        const saving  = ref(false);
        const errors  = ref({});
        const form    = ref({
            site_name_en: '', site_name_ar: '',
            email: '', phone: '', whatsapp: '',
            address_en: '', address_ar: '',
            facebook: '', twitter: '', linkedin: '', instagram: '', youtube: '',
            meta_title_en: '', meta_title_ar: '',
            meta_description_en: '', meta_description_ar: '',
        });

        const load = () => {
            loading.value = true;
            adminApi.get('dashboard/landing/settings')
                .then(res => { Object.assign(form.value, res.data.data); })
                .finally(() => { loading.value = false; });
        };

        const submit = () => {
            saving.value = true;
            errors.value = {};
            adminApi.put('dashboard/landing/settings', form.value)
                .then(() => {
                    Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', showConfirmButton: false, timer: 1500 });
                })
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; });
        };

        onMounted(load);
        return { loading, saving, errors, form, submit };
    }
};
</script>
