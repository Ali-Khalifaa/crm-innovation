<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">تواصل معنا</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">تواصل معنا</li>
                </ol></nav>
            </div>
        </div>

        <loader v-if="loading" />

        <form v-else @submit.prevent="submitSection">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card custom-card h-100">
                        <div class="card-header"><h6 class="card-title mb-0">جانب CTA (يسار)</h6></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان الفرعي (عربي) *</label>
                                <input type="text" class="form-control" v-model="form.cta_subtitle.ar" maxlength="100" :class="{'is-invalid': fieldError('cta_subtitle.ar')}">
                                <div class="invalid-feedback" v-if="fieldError('cta_subtitle.ar')">{{ fieldError('cta_subtitle.ar') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان الفرعي (إنجليزي) *</label>
                                <input type="text" class="form-control" v-model="form.cta_subtitle.en" maxlength="100" :class="{'is-invalid': fieldError('cta_subtitle.en')}">
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between mb-2">
                                    <label class="form-label fw-semibold mb-0">العنوان الرئيسي (أجزاء)</label>
                                    <button type="button" class="btn btn-sm btn-primary-light" @click="addHeadlineSegment"><i class="ri-add-line"></i></button>
                                </div>
                                <div v-for="(seg, index) in form.cta_headline" :key="index" class="border rounded p-2 mb-2">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-5"><input class="form-control" v-model="seg.title.ar" maxlength="100" placeholder="عربي"></div>
                                        <div class="col-md-5"><input class="form-control" v-model="seg.title.en" maxlength="100" placeholder="English"></div>
                                        <div class="col-md-2"><label class="small"><input type="checkbox" v-model="seg.check"> ملوّن</label>
                                            <button type="button" class="btn btn-sm btn-danger-transparent ms-1" v-if="form.cta_headline.length > 1" @click="form.cta_headline.splice(index,1)"><i class="ri-delete-bin-line"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الوصف (عربي) *</label>
                                <textarea class="form-control" v-model="form.cta_intro.ar" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الوصف (إنجليزي) *</label>
                                <textarea class="form-control" v-model="form.cta_intro.en" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">زر 1 (عربي/إنجليزي)</label>
                                <input class="form-control mb-1" v-model="form.cta_btn1_text.ar" placeholder="عربي">
                                <input class="form-control" v-model="form.cta_btn1_text.en" placeholder="English">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">رابط الزر 1</label>
                                <input class="form-control" v-model="form.cta_btn1_link" placeholder="/register">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">زر 2 (عربي/إنجليزي)</label>
                                <input class="form-control mb-1" v-model="form.cta_btn2_text.ar" placeholder="عربي">
                                <input class="form-control" v-model="form.cta_btn2_text.en" placeholder="English">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">رابط الزر 2</label>
                                <input class="form-control" v-model="form.cta_btn2_link" placeholder="#demo">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card custom-card h-100">
                        <div class="card-header"><h6 class="card-title mb-0">جانب النموذج (يمين)</h6></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان الفرعي (عربي) *</label>
                                <input class="form-control" v-model="form.form_subtitle.ar" maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان الفرعي (إنجليزي) *</label>
                                <input class="form-control" v-model="form.form_subtitle.en" maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان (عربي) *</label>
                                <input class="form-control" v-model="form.form_title.ar" maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">العنوان (إنجليزي) *</label>
                                <input class="form-control" v-model="form.form_title.en" maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الوصف (عربي) *</label>
                                <textarea class="form-control" v-model="form.form_intro.ar" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">الوصف (إنجليزي) *</label>
                                <textarea class="form-control" v-model="form.form_intro.en" rows="3" maxlength="500"></textarea>
                            </div>
                            <div class="col-12">
                                <p class="text-muted small mb-0">حقول النموذج (اسم، بريد، هاتف، رسالة) تُترجم تلقائياً من ملفات اللغة.</p>
                            </div>
                            <div class="col-12">
                                <div class="custom-toggle-switch d-flex align-items-center">
                                    <input id="contact-section-active" type="checkbox" v-model="form.is_active">
                                    <label for="contact-section-active" class="label-primary"></label>
                                    <span class="ms-2">القسم نشط</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12" v-if="permission.includes('landing contact edit')">
                    <button type="submit" class="btn btn-primary px-5" :disabled="saving">
                        <span v-if="saving"><i class="ri-loader-2-fill me-1"></i>جاري الحفظ...</span>
                        <span v-else><i class="ri-save-line me-1"></i>حفظ</span>
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
    name: 'LandingContact',
    setup() {
        const store = useStore();
        const permission = computed(() => store.state.authAdmin.permission);
        const loading = ref(true);
        const saving = ref(false);
        const errors = ref({});

        const defaultForm = () => ({
            cta_subtitle: { ar: '', en: '' },
            cta_headline: [{ title: { ar: '', en: '' }, check: false }],
            cta_intro: { ar: '', en: '' },
            cta_btn1_text: { ar: '', en: '' },
            cta_btn1_link: '',
            cta_btn2_text: { ar: '', en: '' },
            cta_btn2_link: '',
            form_subtitle: { ar: '', en: '' },
            form_title: { ar: '', en: '' },
            form_intro: { ar: '', en: '' },
            is_active: true,
        });

        const form = ref(defaultForm());
        const fieldError = (key) => errors.value[key]?.[0] || '';

        const normalizeHeadline = (headline) => {
            if (!Array.isArray(headline) || !headline.length) return [{ title: { ar: '', en: '' }, check: false }];
            return headline.map(seg => ({ title: { ar: seg.title?.ar || '', en: seg.title?.en || '' }, check: Boolean(seg.check) }));
        };

        const load = () => {
            loading.value = true;
            adminApi.get('dashboard/landing/contact').then(res => {
                const d = res.data.data;
                form.value = {
                    cta_subtitle: d.cta_subtitle || { ar: '', en: '' },
                    cta_headline: normalizeHeadline(d.cta_headline),
                    cta_intro: d.cta_intro || { ar: '', en: '' },
                    cta_btn1_text: d.cta_btn1_text || { ar: '', en: '' },
                    cta_btn1_link: d.cta_btn1_link || '',
                    cta_btn2_text: d.cta_btn2_text || { ar: '', en: '' },
                    cta_btn2_link: d.cta_btn2_link || '',
                    form_subtitle: d.form_subtitle || { ar: '', en: '' },
                    form_title: d.form_title || { ar: '', en: '' },
                    form_intro: d.form_intro || { ar: '', en: '' },
                    is_active: !!d.is_active,
                };
            }).finally(() => { loading.value = false; });
        };

        const addHeadlineSegment = () => form.value.cta_headline.push({ title: { ar: '', en: '' }, check: false });

        const submitSection = () => {
            saving.value = true;
            errors.value = {};
            const fd = new FormData();
            const headline = form.value.cta_headline.map(seg => ({ title: seg.title, check: seg.check ? 1 : 0 }));
            fd.append('cta_subtitle', JSON.stringify(form.value.cta_subtitle));
            fd.append('cta_headline', JSON.stringify(headline));
            fd.append('cta_intro', JSON.stringify(form.value.cta_intro));
            fd.append('cta_btn1_text', JSON.stringify(form.value.cta_btn1_text));
            fd.append('cta_btn1_link', form.value.cta_btn1_link || '');
            fd.append('cta_btn2_text', JSON.stringify(form.value.cta_btn2_text));
            fd.append('cta_btn2_link', form.value.cta_btn2_link || '');
            fd.append('form_subtitle', JSON.stringify(form.value.form_subtitle));
            fd.append('form_title', JSON.stringify(form.value.form_title));
            fd.append('form_intro', JSON.stringify(form.value.form_intro));
            fd.append('is_active', form.value.is_active ? 1 : 0);
            fd.append('_method', 'PUT');

            adminApi.post('dashboard/landing/contact', fd)
                .then(() => Swal.fire({ icon: 'success', title: 'تم الحفظ', timer: 1500, showConfirmButton: false }))
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; load(); });
        };

        onMounted(load);

        return { permission, loading, saving, form, fieldError, addHeadlineSegment, submitSection };
    },
};
</script>
