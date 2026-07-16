<template>
    <div>
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

        <form @submit.prevent="submit" v-else enctype="multipart/form-data">
            <div class="row g-4">

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-image me-2"></i>الهوية البصرية</h6></div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">اللوجو (Navbar / Auth)</label>
                                    <input type="file" class="form-control" accept="image/*" @change="handleFile('logo', $event)" ref="logoInput">
                                    <div class="mt-2" v-if="displayLogo">
                                        <img :src="displayLogo" alt="logo" class="img-thumbnail" style="max-height:80px;">
                                        <button type="button" class="btn btn-sm btn-danger-transparent ms-2" @click="clearFile('logo')">حذف</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">لوجو الفوتر</label>
                                    <input type="file" class="form-control" accept="image/*" @change="handleFile('logo_footer', $event)" ref="logoFooterInput">
                                    <div class="mt-2" v-if="displayLogoFooter">
                                        <img :src="displayLogoFooter" alt="footer logo" class="img-thumbnail" style="max-height:80px;background:#1e293b;">
                                        <button type="button" class="btn btn-sm btn-danger-transparent ms-2" @click="clearFile('logo_footer')">حذف</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Favicon</label>
                                    <input type="file" class="form-control" accept="image/*,.ico" @change="handleFile('favicon', $event)" ref="faviconInput">
                                    <div class="mt-2" v-if="displayFavicon">
                                        <img :src="displayFavicon" alt="favicon" class="img-thumbnail" style="max-height:48px;">
                                        <button type="button" class="btn btn-sm btn-danger-transparent ms-2" @click="clearFile('favicon')">حذف</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <input type="text" class="form-control" v-model="form.phone">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">واتساب</label>
                                    <input type="text" class="form-control" v-model="form.whatsapp">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان (إنجليزي)</label>
                                    <input type="text" class="form-control" v-model="form.address_en">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">العنوان (عربي)</label>
                                    <input type="text" class="form-control" v-model="form.address_ar">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-share-alt me-2"></i>روابط السوشيال ميديا</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-semibold">فيسبوك</label><input type="url" class="form-control" v-model="form.facebook"></div>
                                <div class="col-md-6"><label class="form-label fw-semibold">تويتر / X</label><input type="url" class="form-control" v-model="form.twitter"></div>
                                <div class="col-md-6"><label class="form-label fw-semibold">لينكد إن</label><input type="url" class="form-control" v-model="form.linkedin"></div>
                                <div class="col-md-6"><label class="form-label fw-semibold">انستجرام</label><input type="url" class="form-control" v-model="form.instagram"></div>
                                <div class="col-md-6"><label class="form-label fw-semibold">يوتيوب</label><input type="url" class="form-control" v-model="form.youtube"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12" v-if="permission.includes('landing settings edit')">
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
    name: 'LandingSettings',
    setup() {
        const store = useStore();
        const permission = computed(() => store.state.authAdmin.permission);
        const loading = ref(true);
        const saving  = ref(false);
        const errors  = ref({});
        const logoInput = ref(null);
        const logoFooterInput = ref(null);
        const faviconInput = ref(null);
        const files = ref({ logo: null, logo_footer: null, favicon: null });
        const previews = ref({ logo: null, logo_footer: null, favicon: null });
        const form = ref({
            site_name_en: '', site_name_ar: '',
            email: '', phone: '', whatsapp: '',
            address_en: '', address_ar: '',
            facebook: '', twitter: '', linkedin: '', instagram: '', youtube: '',
            current_logo: null, current_logo_footer: null, current_favicon: null,
        });

        const displayLogo = computed(() => previews.value.logo || form.value.current_logo);
        const displayLogoFooter = computed(() => previews.value.logo_footer || form.value.current_logo_footer);
        const displayFavicon = computed(() => previews.value.favicon || form.value.current_favicon);

        const load = () => {
            loading.value = true;
            adminApi.get('dashboard/landing/settings')
                .then(res => {
                    const d = res.data.data;
                    form.value = {
                        site_name_en: d.site_name_en || '', site_name_ar: d.site_name_ar || '',
                        email: d.email || '', phone: d.phone || '', whatsapp: d.whatsapp || '',
                        address_en: d.address_en || '', address_ar: d.address_ar || '',
                        facebook: d.facebook || '', twitter: d.twitter || '',
                        linkedin: d.linkedin || '', instagram: d.instagram || '', youtube: d.youtube || '',
                        current_logo: d.logo || null, current_logo_footer: d.logo_footer || null, current_favicon: d.favicon || null,
                    };
                })
                .finally(() => { loading.value = false; });
        };

        const handleFile = (field, e) => {
            const file = e.target.files[0];
            if (!file) return;
            files.value[field] = file;
            previews.value[field] = URL.createObjectURL(file);
        };

        const clearFile = (field) => {
            files.value[field] = null;
            previews.value[field] = null;
            form.value[`current_${field}`] = null;
            const inputMap = { logo: logoInput, logo_footer: logoFooterInput, favicon: faviconInput };
            if (inputMap[field]?.value) inputMap[field].value.value = '';
        };

        const submit = () => {
            saving.value = true;
            errors.value = {};
            const fd = new FormData();
            ['site_name_en','site_name_ar','email','phone','whatsapp','address_en','address_ar','facebook','twitter','linkedin','instagram','youtube'].forEach(k => {
                fd.append(k, form.value[k] ?? '');
            });
            ['logo','logo_footer','favicon'].forEach(k => {
                if (files.value[k]) fd.append(k, files.value[k]);
            });
            fd.append('_method', 'PUT');

            adminApi.post('dashboard/landing/settings', fd)
                .then(() => Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', showConfirmButton: false, timer: 1500 }))
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; load(); });
        };

        onMounted(load);
        return {
            permission, loading, saving, errors, form, submit,
            logoInput, logoFooterInput, faviconInput,
            displayLogo, displayLogoFooter, displayFavicon,
            handleFile, clearFile,
        };
    }
};
</script>
