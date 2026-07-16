<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">إعدادات SEO</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">SEO</li>
                </ol></nav>
            </div>
        </div>

        <loader v-if="loading" />

        <form @submit.prevent="submit" v-else>
            <div class="row g-4">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-search-alt me-2"></i>Meta Tags</h6></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">عنوان الصفحة (عربي)</label>
                                <input type="text" class="form-control" v-model="form.meta_title.ar" maxlength="160">
                                <small class="text-muted">{{ (form.meta_title.ar || '').length }}/160</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">عنوان الصفحة (إنجليزي)</label>
                                <input type="text" class="form-control" v-model="form.meta_title.en" maxlength="160">
                                <small class="text-muted">{{ (form.meta_title.en || '').length }}/160</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الوصف (عربي)</label>
                                <textarea class="form-control" v-model="form.meta_description.ar" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الوصف (إنجليزي)</label>
                                <textarea class="form-control" v-model="form.meta_description.en" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الكلمات المفتاحية (عربي)</label>
                                <textarea class="form-control" v-model="form.meta_keywords.ar" rows="2" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الكلمات المفتاحية (إنجليزي)</label>
                                <textarea class="form-control" v-model="form.meta_keywords.en" rows="2" maxlength="500"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bxl-facebook me-2"></i>Open Graph</h6></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">OG Title (عربي)</label>
                                <input type="text" class="form-control" v-model="form.og_title.ar" maxlength="160">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">OG Title (إنجليزي)</label>
                                <input type="text" class="form-control" v-model="form.og_title.en" maxlength="160">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">OG Description (عربي)</label>
                                <textarea class="form-control" v-model="form.og_description.ar" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">OG Description (إنجليزي)</label>
                                <textarea class="form-control" v-model="form.og_description.en" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">OG Image</label>
                                <input type="file" class="form-control" accept="image/*" @change="handleImage('og_image', $event)" ref="ogImageInput">
                                <div class="mt-2" v-if="displayOgImage">
                                    <img :src="displayOgImage" class="img-thumbnail" style="max-height:120px;">
                                    <button type="button" class="btn btn-sm btn-danger-transparent ms-2" @click="clearImage('og_image')">حذف</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bxl-twitter me-2"></i>Twitter Card</h6></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Twitter Title (عربي)</label>
                                <input type="text" class="form-control" v-model="form.twitter_title.ar" maxlength="160">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Twitter Title (إنجليزي)</label>
                                <input type="text" class="form-control" v-model="form.twitter_title.en" maxlength="160">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Twitter Description (عربي)</label>
                                <textarea class="form-control" v-model="form.twitter_description.ar" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Twitter Description (إنجليزي)</label>
                                <textarea class="form-control" v-model="form.twitter_description.en" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Twitter Image</label>
                                <input type="file" class="form-control" accept="image/*" @change="handleImage('twitter_image', $event)" ref="twitterImageInput">
                                <div class="mt-2" v-if="displayTwitterImage">
                                    <img :src="displayTwitterImage" class="img-thumbnail" style="max-height:120px;">
                                    <button type="button" class="btn btn-sm btn-danger-transparent ms-2" @click="clearImage('twitter_image')">حذف</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-cog me-2"></i>إعدادات إضافية</h6></div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Robots</label>
                                <input type="text" class="form-control" v-model="form.robots">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Author</label>
                                <input type="text" class="form-control" v-model="form.author">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Theme Color</label>
                                <input type="color" class="form-control form-control-color w-100" v-model="form.theme_color">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Canonical URL</label>
                                <input type="url" class="form-control" v-model="form.canonical_url" placeholder="https://example.com">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="custom-toggle-switch d-flex align-items-center">
                                    <input id="seo-active" type="checkbox" v-model="form.is_active">
                                    <label for="seo-active" class="label-primary"></label>
                                    <span class="ms-2">SEO نشط</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12" v-if="permission.includes('landing seo edit')">
                    <button type="submit" class="btn btn-primary px-5" :disabled="saving">
                        <span v-if="saving"><i class="ri-loader-2-fill me-1"></i>جاري الحفظ...</span>
                        <span v-else><i class="ri-save-line me-1"></i>حفظ SEO</span>
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

const emptyLocale = () => ({ ar: '', en: '' });

export default {
    name: 'LandingSeo',
    setup() {
        const store = useStore();
        const permission = computed(() => store.state.authAdmin.permission);
        const loading = ref(true);
        const saving = ref(false);
        const errors = ref({});
        const ogImageInput = ref(null);
        const twitterImageInput = ref(null);
        const imageFiles = ref({ og_image: null, twitter_image: null });
        const imagePreviews = ref({ og_image: null, twitter_image: null });

        const form = ref({
            meta_title: emptyLocale(),
            meta_description: emptyLocale(),
            meta_keywords: emptyLocale(),
            og_title: emptyLocale(),
            og_description: emptyLocale(),
            twitter_title: emptyLocale(),
            twitter_description: emptyLocale(),
            robots: 'index, follow, max-image-preview:large',
            author: '',
            theme_color: '#246bfd',
            canonical_url: '',
            is_active: true,
            current_og_image: null,
            current_twitter_image: null,
        });

        const displayOgImage = computed(() => imagePreviews.value.og_image || form.value.current_og_image);
        const displayTwitterImage = computed(() => imagePreviews.value.twitter_image || form.value.current_twitter_image);

        const load = () => {
            loading.value = true;
            adminApi.get('dashboard/landing/seo').then(res => {
                const d = res.data.data;
                form.value = {
                    meta_title: d.meta_title || emptyLocale(),
                    meta_description: d.meta_description || emptyLocale(),
                    meta_keywords: d.meta_keywords || emptyLocale(),
                    og_title: d.og_title || emptyLocale(),
                    og_description: d.og_description || emptyLocale(),
                    twitter_title: d.twitter_title || emptyLocale(),
                    twitter_description: d.twitter_description || emptyLocale(),
                    robots: d.robots || 'index, follow, max-image-preview:large',
                    author: d.author || '',
                    theme_color: d.theme_color || '#246bfd',
                    canonical_url: d.canonical_url || '',
                    is_active: !!d.is_active,
                    current_og_image: d.og_image || null,
                    current_twitter_image: d.twitter_image || null,
                };
            }).finally(() => { loading.value = false; });
        };

        const handleImage = (field, e) => {
            const file = e.target.files[0];
            if (!file) return;
            imageFiles.value[field] = file;
            imagePreviews.value[field] = URL.createObjectURL(file);
        };

        const clearImage = (field) => {
            imageFiles.value[field] = null;
            imagePreviews.value[field] = null;
            form.value[`current_${field}`] = null;
            if (field === 'og_image' && ogImageInput.value) ogImageInput.value.value = '';
            if (field === 'twitter_image' && twitterImageInput.value) twitterImageInput.value.value = '';
        };

        const submit = () => {
            saving.value = true;
            errors.value = {};
            const fd = new FormData();
            ['meta_title','meta_description','meta_keywords','og_title','og_description','twitter_title','twitter_description'].forEach(k => {
                fd.append(k, JSON.stringify(form.value[k]));
            });
            fd.append('robots', form.value.robots || '');
            fd.append('author', form.value.author || '');
            fd.append('theme_color', form.value.theme_color || '');
            fd.append('canonical_url', form.value.canonical_url || '');
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (imageFiles.value.og_image) fd.append('og_image', imageFiles.value.og_image);
            if (imageFiles.value.twitter_image) fd.append('twitter_image', imageFiles.value.twitter_image);
            fd.append('_method', 'PUT');

            adminApi.post('dashboard/landing/seo', fd)
                .then(() => Swal.fire({ icon: 'success', title: 'تم الحفظ', timer: 1500, showConfirmButton: false }))
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; load(); });
        };

        onMounted(load);
        return {
            permission, loading, saving, errors, form, submit,
            ogImageInput, twitterImageInput,
            displayOgImage, displayTwitterImage,
            handleImage, clearImage,
        };
    },
};
</script>
