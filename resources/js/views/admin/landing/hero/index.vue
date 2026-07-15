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

        <div v-else>
            <form @submit.prevent="submitHero" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card custom-card">
                            <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-heading me-2"></i>المحتوى النصي</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">العنوان الفرعي (عربي) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.subtitle.ar" maxlength="100"
                                            :class="{'is-invalid': fieldError('subtitle.ar')}" placeholder="نظام CRM سحابي متكامل">
                                        <div class="invalid-feedback" v-if="fieldError('subtitle.ar')">{{ fieldError('subtitle.ar') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">العنوان الفرعي (إنجليزي) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.subtitle.en" maxlength="100"
                                            :class="{'is-invalid': fieldError('subtitle.en')}" placeholder="Integrated Cloud CRM System">
                                        <div class="invalid-feedback" v-if="fieldError('subtitle.en')">{{ fieldError('subtitle.en') }}</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="form-label fw-semibold mb-0">العنوان الرئيسي (أجزاء + تمييز)</label>
                                            <button type="button" class="btn btn-sm btn-primary-light" @click="addHeadlineSegment">
                                                <i class="ri-add-line me-1"></i>إضافة جزء
                                            </button>
                                        </div>
                                        <div v-for="(seg, index) in form.headline" :key="index" class="border rounded p-3 mb-2">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" v-model="seg.title.ar" maxlength="100"
                                                        :class="{'is-invalid': headlineError(index, 'ar')}" placeholder="النص بالعربي">
                                                    <div class="invalid-feedback" v-if="headlineError(index, 'ar')">{{ headlineError(index, 'ar') }}</div>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" v-model="seg.title.en" maxlength="100"
                                                        :class="{'is-invalid': headlineError(index, 'en')}" placeholder="English text">
                                                    <div class="invalid-feedback" v-if="headlineError(index, 'en')">{{ headlineError(index, 'en') }}</div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center gap-2">
                                                    <label class="mb-0 small">
                                                        <input type="checkbox" v-model="seg.check" class="me-1"> ملوّن
                                                    </label>
                                                    <button type="button" class="btn btn-sm btn-danger-transparent" @click="removeHeadlineSegment(index)" v-if="form.headline.length > 1">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">الوصف (عربي) <span class="text-danger">*</span></label>
                                        <textarea class="form-control" v-model="form.description.ar" rows="3" maxlength="1000"
                                            :class="{'is-invalid': fieldError('description.ar')}"></textarea>
                                        <div class="invalid-feedback" v-if="fieldError('description.ar')">{{ fieldError('description.ar') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">الوصف (إنجليزي) <span class="text-danger">*</span></label>
                                        <textarea class="form-control" v-model="form.description.en" rows="3" maxlength="1000"
                                            :class="{'is-invalid': fieldError('description.en')}"></textarea>
                                        <div class="invalid-feedback" v-if="fieldError('description.en')">{{ fieldError('description.en') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card custom-card">
                            <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-link me-2"></i>الروابط</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">الزر الأول — عربي <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.btn_primary.text.ar" maxlength="100"
                                            :class="{'is-invalid': fieldError('btn_primary.text.ar')}" placeholder="ابدأ مجاناً">
                                        <div class="invalid-feedback" v-if="fieldError('btn_primary.text.ar')">{{ fieldError('btn_primary.text.ar') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">الزر الأول — إنجليزي <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.btn_primary.text.en" maxlength="100"
                                            :class="{'is-invalid': fieldError('btn_primary.text.en')}" placeholder="Start Free">
                                        <div class="invalid-feedback" v-if="fieldError('btn_primary.text.en')">{{ fieldError('btn_primary.text.en') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">رابط الزر الأول <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.btn_primary.url" maxlength="255"
                                            :class="{'is-invalid': fieldError('btn_primary.url')}" placeholder="/register">
                                        <div class="invalid-feedback" v-if="fieldError('btn_primary.url')">{{ fieldError('btn_primary.url') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">الزر الثاني — عربي <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.btn_secondary.text.ar" maxlength="100"
                                            :class="{'is-invalid': fieldError('btn_secondary.text.ar')}" placeholder="كيف يعمل النظام">
                                        <div class="invalid-feedback" v-if="fieldError('btn_secondary.text.ar')">{{ fieldError('btn_secondary.text.ar') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">الزر الثاني — إنجليزي <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.btn_secondary.text.en" maxlength="100"
                                            :class="{'is-invalid': fieldError('btn_secondary.text.en')}" placeholder="How it works">
                                        <div class="invalid-feedback" v-if="fieldError('btn_secondary.text.en')">{{ fieldError('btn_secondary.text.en') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">رابط الزر الثاني <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.btn_secondary.url" maxlength="255"
                                            :class="{'is-invalid': fieldError('btn_secondary.url')}" placeholder="https://youtube.com/...">
                                        <div class="invalid-feedback" v-if="fieldError('btn_secondary.url')">{{ fieldError('btn_secondary.url') }}</div>
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

            <div class="row mt-2">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <h6 class="card-title mb-0"><i class="bx bx-images me-2"></i>شرائح العرض (Slider)</h6>
                            <button v-if="permission.includes('landing hero edit')"
                                @click.prevent="showModelCreate"
                                class="btn btn-sm btn-primary-light">
                                <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة شريحة
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-2">
                                <table class="table text-nowrap table-striped">
                                    <thead><tr>
                                        <th>#</th><th>العنوان (AR)</th><th>العنوان (EN)</th>
                                        <th>الصورة</th><th>الترتيب</th><th>الحالة</th><th>إجراء</th>
                                    </tr></thead>
                                    <tbody v-if="slides.length">
                                        <tr v-for="(item, index) in slides" :key="item.id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ item.title_ar }}</td>
                                            <td>{{ item.title_en }}</td>
                                            <td>
                                                <img v-if="slideImageUrl(item)" :src="slideImageUrl(item)" style="height:40px;border-radius:6px;object-fit:cover;" alt="">
                                                <span v-else class="text-muted">—</span>
                                            </td>
                                            <td>{{ item.sort_order }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-success-transparent" v-if="item.is_active">نشط</span>
                                                <span class="badge rounded-pill bg-danger-transparent" v-else>غير نشط</span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button v-if="permission.includes('landing hero edit')"
                                                        @click.prevent="showEditMode(item)"
                                                        class="btn btn-icon btn-sm btn-info-transparent rounded-pill"><i class="ri-edit-line"></i></button>
                                                    <button v-if="permission.includes('landing hero edit')"
                                                        @click.prevent="deleteSlide(item.id, index)"
                                                        class="btn btn-icon btn-sm btn-danger-transparent rounded-pill"><i class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else><tr><td class="text-center" colspan="7">{{ $t('global.NoDataFound') }}</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ModalCreateAndUpdate
            v-if="slideModalOpen"
            :key="`${slideType}-${slideRow?.id ?? 'new'}`"
            :type="slideType"
            :dataRow="slideRow"
            @created="onSlideSaved"
            @closed="slideModalOpen = false"
        />
    </div>
</template>

<script>
import { ref, onMounted, computed, nextTick } from 'vue';
import { useStore } from 'vuex';
import adminApi from '../../../../api/adminAxios';
import ModalCreateAndUpdate from './ModalCreateAndUpdate.vue';

export default {
    name: 'LandingHero',
    components: { ModalCreateAndUpdate },
    setup() {
        const store      = useStore();
        const permission = computed(() => store.state.authAdmin.permission);

        const loading         = ref(true);
        const saving          = ref(false);
        const errors          = ref({});
        const slides          = ref([]);
        const slideType       = ref('create');
        const slideRow        = ref(null);
        const slideModalOpen  = ref(false);

        const MAX_SHORT = 100;
        const MAX_DESC  = 1000;

        const fieldError = (key) => errors.value[key]?.[0] || '';
        const headlineError = (index, lang) => fieldError(`headline.${index}.${lang}`);

        const defaultForm = () => ({
            subtitle: { ar: '', en: '' },
            headline: [{ title: { ar: '', en: '' }, check: false }],
            description: { ar: '', en: '' },
            btn_primary: { text: { ar: '', en: '' }, url: '', icon: 'fa-plus' },
            btn_secondary: { text: { ar: '', en: '' }, url: '', type: 'video' },
        });

        const form = ref(defaultForm());

        const normalizeHeadline = (headline) => {
            if (!Array.isArray(headline) || !headline.length) {
                return [{ title: { ar: '', en: '' }, check: false }];
            }
            return headline.map(seg => ({
                title: { ar: seg.title?.ar || '', en: seg.title?.en || '' },
                check: Boolean(seg.check),
            }));
        };

        const loadHero = () => adminApi.get('dashboard/landing/hero').then(res => {
            const data = res.data.data;
            form.value = {
                subtitle: data.subtitle || { ar: '', en: '' },
                headline: normalizeHeadline(data.headline),
                description: data.description || { ar: '', en: '' },
                btn_primary: {
                    text: data.btn_primary?.text || { ar: '', en: '' },
                    url: data.btn_primary?.url || '',
                    icon: data.btn_primary?.icon || 'fa-plus',
                },
                btn_secondary: {
                    text: data.btn_secondary?.text || { ar: '', en: '' },
                    url: data.btn_secondary?.url || '',
                    type: data.btn_secondary?.type || 'video',
                },
            };
        });

        const loadSlides = () => adminApi.get('dashboard/landing/hero-slides').then(res => {
            const payload = res.data.data;
            slides.value = Array.isArray(payload) ? payload : (payload?.data ?? []);
        });

        const slideImageUrl = (item) => {
            if (!item?.image && !item?.image_file) return null;
            if (item.image?.startsWith('http')) return item.image;
            if (item.image?.startsWith('/')) return `${window.location.origin}${item.image}`;
            if (item.image_file) {
                return `${window.location.origin}/upload/general/${item.image_file.split('/').map(encodeURIComponent).join('/')}`;
            }
            return item.image;
        };

        const load = () => {
            loading.value = true;
            Promise.all([loadHero(), loadSlides()]).finally(() => { loading.value = false; });
        };

        const addHeadlineSegment = () => {
            form.value.headline.push({ title: { ar: '', en: '' }, check: false });
        };

        const removeHeadlineSegment = (index) => {
            form.value.headline.splice(index, 1);
        };

        const validateHero = () => {
            errors.value = {};
            const add = (key, message) => { errors.value[key] = [message]; };

            ['ar', 'en'].forEach((lang) => {
                const label = lang === 'ar' ? 'عربي' : 'إنجليزي';
                const sub   = (form.value.subtitle[lang] || '').trim();
                if (!sub) add(`subtitle.${lang}`, `العنوان الفرعي (${label}) مطلوب.`);
                else if (sub.length > MAX_SHORT) add(`subtitle.${lang}`, `العنوان الفرعي (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);

                const desc = (form.value.description[lang] || '').trim();
                if (!desc) add(`description.${lang}`, `الوصف (${label}) مطلوب.`);
                else if (desc.length > MAX_DESC) add(`description.${lang}`, `الوصف (${label}) يجب ألا يتجاوز ${MAX_DESC} حرف.`);
            });

            form.value.headline.forEach((seg, index) => {
                ['ar', 'en'].forEach((lang) => {
                    const label = lang === 'ar' ? 'عربي' : 'إنجليزي';
                    const val   = (seg.title[lang] || '').trim();
                    if (!val) add(`headline.${index}.${lang}`, `العنوان الرئيسي — الجزء ${index + 1} (${label}) مطلوب.`);
                    else if (val.length > MAX_SHORT) add(`headline.${index}.${lang}`, `العنوان الرئيسي — الجزء ${index + 1} (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);
                });
            });

            ['btn_primary', 'btn_secondary'].forEach((btnKey, i) => {
                const btnLabel = i === 0 ? 'الزر الأول' : 'الزر الثاني';
                ['ar', 'en'].forEach((lang) => {
                    const label = lang === 'ar' ? 'عربي' : 'إنجليزي';
                    const val   = (form.value[btnKey].text[lang] || '').trim();
                    if (!val) add(`${btnKey}.text.${lang}`, `نص ${btnLabel} (${label}) مطلوب.`);
                    else if (val.length > MAX_SHORT) add(`${btnKey}.text.${lang}`, `نص ${btnLabel} (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);
                });
                const url = (form.value[btnKey].url || '').trim();
                if (!url) add(`${btnKey}.url`, `رابط ${btnLabel} مطلوب.`);
                else if (url.length > 255) add(`${btnKey}.url`, `رابط ${btnLabel} يجب ألا يتجاوز 255 حرف.`);
            });

            return Object.keys(errors.value).length === 0;
        };

        const submitHero = () => {
            if (!validateHero()) return;

            saving.value = true;
            errors.value = {};
            const fd = new FormData();
            const headline = form.value.headline.map(seg => ({
                title: seg.title,
                check: seg.check ? 1 : 0,
            }));

            fd.append('subtitle', JSON.stringify(form.value.subtitle));
            fd.append('headline', JSON.stringify(headline));
            fd.append('description', JSON.stringify(form.value.description));
            fd.append('btn_primary', JSON.stringify(form.value.btn_primary));
            fd.append('btn_secondary', JSON.stringify(form.value.btn_secondary));
            fd.append('_method', 'PUT');

            adminApi.post('dashboard/landing/hero', fd)
                .then(() => {
                    Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', showConfirmButton: false, timer: 1500 });
                    loadHero();
                })
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; });
        };

        const openSlideModal = () => {
            slideModalOpen.value = true;
            nextTick(() => {
                nextTick(() => {
                    const el = document.getElementById('hero-slide-modal');
                    if (!el) return;
                    const modal = bootstrap.Modal.getOrCreateInstance(el);
                    modal.show();
                });
            });
        };

        const onSlideSaved = () => {
            loadSlides();
            slideModalOpen.value = false;
        };

        const showModelCreate = () => {
            slideType.value = 'create';
            slideRow.value  = null;
            openSlideModal();
        };

        const showEditMode = (item) => {
            slideType.value = 'edit';
            slideRow.value  = { ...item };
            openSlideModal();
        };

        const deleteSlide = (id, index) => {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
            }).then(result => {
                if (!result.isConfirmed) return;
                adminApi.delete(`dashboard/landing/hero-slides/${id}`).then(() => {
                    slides.value.splice(index, 1);
                    Swal.fire({ icon: 'success', title: 'تم الحذف', showConfirmButton: false, timer: 1200 });
                });
            });
        };

        onMounted(load);

        return {
            loading, saving, errors, form, slides, permission,
            slideType, slideRow, slideModalOpen,
            fieldError, headlineError, slideImageUrl,
            addHeadlineSegment, removeHeadlineSegment,
            submitHero, loadSlides, onSlideSaved,
            showModelCreate, showEditMode, deleteSlide,
        };
    }
};
</script>
