<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">معاينة لوحة التحكم</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">معاينة لوحة التحكم</li>
                </ol></nav>
            </div>
        </div>

        <loader v-if="loading" />

        <div v-else>
            <form @submit.prevent="submitSection">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card custom-card">
                            <div class="card-header"><h6 class="card-title mb-0"><i class="bx bx-heading me-2"></i>المحتوى النصي</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">العنوان الفرعي (عربي) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.subtitle.ar" maxlength="100"
                                            :class="{'is-invalid': fieldError('subtitle.ar')}" placeholder="لوحة التحكم">
                                        <div class="invalid-feedback" v-if="fieldError('subtitle.ar')">{{ fieldError('subtitle.ar') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">العنوان الفرعي (إنجليزي) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.subtitle.en" maxlength="100"
                                            :class="{'is-invalid': fieldError('subtitle.en')}" placeholder="Dashboard">
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
                                                    <label class="mb-0 small"><input type="checkbox" v-model="seg.check" class="me-1"> ملوّن</label>
                                                    <button type="button" class="btn btn-sm btn-danger-transparent" @click="removeHeadlineSegment(index)" v-if="form.headline.length > 1">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">الوصف (عربي) <span class="text-danger">*</span></label>
                                        <textarea class="form-control" v-model="form.description.ar" rows="3" maxlength="500"
                                            :class="{'is-invalid': fieldError('description.ar')}"></textarea>
                                        <div class="invalid-feedback" v-if="fieldError('description.ar')">{{ fieldError('description.ar') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">الوصف (إنجليزي) <span class="text-danger">*</span></label>
                                        <textarea class="form-control" v-model="form.description.en" rows="3" maxlength="500"
                                            :class="{'is-invalid': fieldError('description.en')}"></textarea>
                                        <div class="invalid-feedback" v-if="fieldError('description.en')">{{ fieldError('description.en') }}</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">نص الزر (عربي) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.button.text.ar" maxlength="100"
                                            :class="{'is-invalid': fieldError('button.text.ar')}" placeholder="جرب النظام الآن">
                                        <div class="invalid-feedback" v-if="fieldError('button.text.ar')">{{ fieldError('button.text.ar') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">نص الزر (إنجليزي) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.button.text.en" maxlength="100"
                                            :class="{'is-invalid': fieldError('button.text.en')}" placeholder="Try the System Now">
                                        <div class="invalid-feedback" v-if="fieldError('button.text.en')">{{ fieldError('button.text.en') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">رابط الزر <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.button.url" maxlength="255"
                                            :class="{'is-invalid': fieldError('button.url')}" placeholder="#contact">
                                        <div class="invalid-feedback" v-if="fieldError('button.url')">{{ fieldError('button.url') }}</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="custom-toggle-switch d-flex align-items-center">
                                            <input id="solution-section-active" type="checkbox" v-model="form.is_active">
                                            <label for="solution-section-active" class="label-primary"></label>
                                            <span class="ms-2">القسم نشط (يظهر في اللاندينج)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-5" :disabled="saving">
                            <span v-if="saving"><i class="ri-loader-2-fill me-1"></i>جاري الحفظ...</span>
                            <span v-else><i class="ri-save-line me-1"></i>حفظ إعدادات القسم</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="row mt-2">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <h6 class="card-title mb-0"><i class="bx bx-images me-2"></i>شرائح العرض</h6>
                            <button v-if="permission.includes('landing solutions edit')"
                                @click.prevent="showSlideCreate"
                                class="btn btn-sm btn-primary-light">
                                <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة شريحة
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-2">
                                <table class="table text-nowrap table-striped">
                                    <thead><tr>
                                        <th>#</th><th>العنوان (AR)</th><th>الصورة</th>
                                        <th>الترتيب</th><th>الحالة</th><th>إجراء</th>
                                    </tr></thead>
                                    <tbody v-if="slides.length">
                                        <tr v-for="(item, index) in slides" :key="item.id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ item.title_ar }}</td>
                                            <td>
                                                <img v-if="itemImageUrl(item)" :src="itemImageUrl(item)" style="height:40px;border-radius:6px;object-fit:cover;" alt="">
                                                <span v-else class="text-muted">—</span>
                                            </td>
                                            <td>{{ item.sort_order }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-success-transparent" v-if="item.is_active">نشط</span>
                                                <span class="badge rounded-pill bg-danger-transparent" v-else>غير نشط</span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button v-if="permission.includes('landing solutions edit')"
                                                        @click.prevent="showSlideEdit(item)"
                                                        class="btn btn-icon btn-sm btn-info-transparent rounded-pill"><i class="ri-edit-line"></i></button>
                                                    <button v-if="permission.includes('landing solutions edit')"
                                                        @click.prevent="deleteSlide(item.id, index)"
                                                        class="btn btn-icon btn-sm btn-danger-transparent rounded-pill"><i class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else><tr><td class="text-center" colspan="6">{{ $t('global.NoDataFound') }}</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <h6 class="card-title mb-0"><i class="bx bx-list-check me-2"></i>نقاط القسم</h6>
                            <button v-if="permission.includes('landing solutions edit')"
                                @click.prevent="showPointCreate"
                                class="btn btn-sm btn-primary-light">
                                <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة نقطة
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-2">
                                <table class="table text-nowrap table-striped">
                                    <thead><tr>
                                        <th>#</th><th>النص (AR)</th><th>النص (EN)</th>
                                        <th>الترتيب</th><th>الحالة</th><th>إجراء</th>
                                    </tr></thead>
                                    <tbody v-if="points.length">
                                        <tr v-for="(item, index) in points" :key="item.id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ item.text_ar }}</td>
                                            <td>{{ item.text_en }}</td>
                                            <td>{{ item.sort_order }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-success-transparent" v-if="item.is_active">نشط</span>
                                                <span class="badge rounded-pill bg-danger-transparent" v-else>غير نشط</span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button v-if="permission.includes('landing solutions edit')"
                                                        @click.prevent="showPointEdit(item)"
                                                        class="btn btn-icon btn-sm btn-info-transparent rounded-pill"><i class="ri-edit-line"></i></button>
                                                    <button v-if="permission.includes('landing solutions edit')"
                                                        @click.prevent="deletePoint(item.id, index)"
                                                        class="btn btn-icon btn-sm btn-danger-transparent rounded-pill"><i class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else><tr><td class="text-center" colspan="6">{{ $t('global.NoDataFound') }}</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <SlideModal
            v-if="slideModalOpen"
            :key="`${slideType}-${slideRow?.id ?? 'new'}`"
            :type="slideType"
            :dataRow="slideRow"
            @created="onSlideSaved"
            @closed="slideModalOpen = false"
        />

        <PointModal
            v-if="pointModalOpen"
            :key="`${pointType}-${pointRow?.id ?? 'new'}`"
            :type="pointType"
            :dataRow="pointRow"
            @created="onPointSaved"
            @closed="pointModalOpen = false"
        />
    </div>
</template>

<script>
import { ref, onMounted, computed, nextTick } from 'vue';
import { useStore } from 'vuex';
import adminApi from '../../../../api/adminAxios';
import SlideModal from './SlideModal.vue';
import PointModal from './PointModal.vue';

export default {
    name: 'LandingSolutions',
    components: { SlideModal, PointModal },
    setup() {
        const store      = useStore();
        const permission = computed(() => store.state.authAdmin.permission);

        const loading        = ref(true);
        const saving         = ref(false);
        const errors         = ref({});
        const slides         = ref([]);
        const points         = ref([]);
        const slideType      = ref('create');
        const slideRow       = ref(null);
        const slideModalOpen = ref(false);
        const pointType      = ref('create');
        const pointRow       = ref(null);
        const pointModalOpen = ref(false);

        const MAX_SHORT = 100;
        const MAX_DESC  = 500;
        const MAX_URL   = 255;

        const fieldError = (key) => errors.value[key]?.[0] || '';
        const headlineError = (index, lang) => fieldError(`headline.${index}.${lang}`);

        const defaultForm = () => ({
            subtitle: { ar: '', en: '' },
            headline: [{ title: { ar: '', en: '' }, check: false }],
            description: { ar: '', en: '' },
            button: { text: { ar: '', en: '' }, url: '' },
            is_active: true,
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

        const loadSection = () => adminApi.get('dashboard/landing/solutions').then(res => {
            const data = res.data.data;
            form.value = {
                subtitle: data.subtitle || { ar: '', en: '' },
                headline: normalizeHeadline(data.headline),
                description: data.description || { ar: '', en: '' },
                button: {
                    text: data.button?.text || { ar: '', en: '' },
                    url: data.button?.url || '',
                },
                is_active: !!data.is_active,
            };
        });

        const loadSlides = () => adminApi.get('dashboard/landing/solution-slides', { params: { per_page: 100 } }).then(res => {
            const payload = res.data.data;
            slides.value = Array.isArray(payload) ? payload : (payload?.data ?? []);
        });

        const loadPoints = () => adminApi.get('dashboard/landing/solution-points', { params: { per_page: 100 } }).then(res => {
            const payload = res.data.data;
            points.value = Array.isArray(payload) ? payload : (payload?.data ?? []);
        });

        const itemImageUrl = (item) => {
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
            Promise.all([loadSection(), loadSlides(), loadPoints()]).finally(() => { loading.value = false; });
        };

        const addHeadlineSegment = () => {
            form.value.headline.push({ title: { ar: '', en: '' }, check: false });
        };

        const removeHeadlineSegment = (index) => {
            form.value.headline.splice(index, 1);
        };

        const validateSection = () => {
            errors.value = {};
            const add = (key, message) => { errors.value[key] = [message]; };

            ['ar', 'en'].forEach((lang) => {
                const label = lang === 'ar' ? 'عربي' : 'إنجليزي';
                const sub = (form.value.subtitle[lang] || '').trim();
                if (!sub) add(`subtitle.${lang}`, `العنوان الفرعي (${label}) مطلوب.`);
                else if (sub.length > MAX_SHORT) add(`subtitle.${lang}`, `العنوان الفرعي (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);

                const desc = (form.value.description[lang] || '').trim();
                if (!desc) add(`description.${lang}`, `الوصف (${label}) مطلوب.`);
                else if (desc.length > MAX_DESC) add(`description.${lang}`, `الوصف (${label}) يجب ألا يتجاوز ${MAX_DESC} حرف.`);

                const btnText = (form.value.button.text[lang] || '').trim();
                if (!btnText) add(`button.text.${lang}`, `نص الزر (${label}) مطلوب.`);
                else if (btnText.length > MAX_SHORT) add(`button.text.${lang}`, `نص الزر (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);
            });

            form.value.headline.forEach((seg, index) => {
                ['ar', 'en'].forEach((lang) => {
                    const label = lang === 'ar' ? 'عربي' : 'إنجليزي';
                    const val = (seg.title[lang] || '').trim();
                    if (!val) add(`headline.${index}.${lang}`, `العنوان الرئيسي — الجزء ${index + 1} (${label}) مطلوب.`);
                    else if (val.length > MAX_SHORT) add(`headline.${index}.${lang}`, `العنوان الرئيسي — الجزء ${index + 1} (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);
                });
            });

            const url = (form.value.button.url || '').trim();
            if (!url) add('button.url', 'رابط الزر مطلوب.');
            else if (url.length > MAX_URL) add('button.url', `رابط الزر يجب ألا يتجاوز ${MAX_URL} حرف.`);

            return Object.keys(errors.value).length === 0;
        };

        const submitSection = () => {
            if (!validateSection()) return;

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
            fd.append('button', JSON.stringify(form.value.button));
            fd.append('is_active', form.value.is_active ? 1 : 0);
            fd.append('_method', 'PUT');

            adminApi.post('dashboard/landing/solutions', fd)
                .then(() => {
                    Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', showConfirmButton: false, timer: 1500 });
                    loadSection();
                })
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; });
        };

        const openSlideModal = () => {
            slideModalOpen.value = true;
            nextTick(() => {
                nextTick(() => {
                    const el = document.getElementById('solution-slide-modal');
                    if (!el) return;
                    bootstrap.Modal.getOrCreateInstance(el).show();
                });
            });
        };

        const openPointModal = () => {
            pointModalOpen.value = true;
            nextTick(() => {
                nextTick(() => {
                    const el = document.getElementById('solution-point-modal');
                    if (!el) return;
                    bootstrap.Modal.getOrCreateInstance(el).show();
                });
            });
        };

        const onSlideSaved = () => {
            loadSlides();
            slideModalOpen.value = false;
        };

        const onPointSaved = () => {
            loadPoints();
            pointModalOpen.value = false;
        };

        const showSlideCreate = () => {
            slideType.value = 'create';
            slideRow.value  = null;
            openSlideModal();
        };

        const showSlideEdit = (item) => {
            slideType.value = 'edit';
            slideRow.value  = { ...item };
            openSlideModal();
        };

        const showPointCreate = () => {
            pointType.value = 'create';
            pointRow.value  = null;
            openPointModal();
        };

        const showPointEdit = (item) => {
            pointType.value = 'edit';
            pointRow.value  = { ...item };
            openPointModal();
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
                adminApi.delete(`dashboard/landing/solution-slides/${id}`).then(() => {
                    slides.value.splice(index, 1);
                    Swal.fire({ icon: 'success', title: 'تم الحذف', showConfirmButton: false, timer: 1200 });
                });
            });
        };

        const deletePoint = (id, index) => {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
            }).then(result => {
                if (!result.isConfirmed) return;
                adminApi.delete(`dashboard/landing/solution-points/${id}`).then(() => {
                    points.value.splice(index, 1);
                    Swal.fire({ icon: 'success', title: 'تم الحذف', showConfirmButton: false, timer: 1200 });
                });
            });
        };

        onMounted(load);

        return {
            loading, saving, errors, form, slides, points, permission,
            slideType, slideRow, slideModalOpen,
            pointType, pointRow, pointModalOpen,
            fieldError, headlineError, itemImageUrl,
            addHeadlineSegment, removeHeadlineSegment,
            submitSection, onSlideSaved, onPointSaved,
            showSlideCreate, showSlideEdit, showPointCreate, showPointEdit,
            deleteSlide, deletePoint,
        };
    }
};
</script>
