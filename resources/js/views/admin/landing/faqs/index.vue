<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">الأسئلة الشائعة</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">الأسئلة الشائعة</li>
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
                                            :class="{'is-invalid': fieldError('subtitle.ar')}" placeholder="دعمنا">
                                        <div class="invalid-feedback" v-if="fieldError('subtitle.ar')">{{ fieldError('subtitle.ar') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">العنوان الفرعي (إنجليزي) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" v-model="form.subtitle.en" maxlength="100"
                                            :class="{'is-invalid': fieldError('subtitle.en')}" placeholder="Support">
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
                                        <label class="form-label fw-semibold">المقدمة (عربي) <span class="text-danger">*</span></label>
                                        <textarea class="form-control" v-model="form.intro.ar" rows="3" maxlength="500"
                                            :class="{'is-invalid': fieldError('intro.ar')}"></textarea>
                                        <div class="invalid-feedback" v-if="fieldError('intro.ar')">{{ fieldError('intro.ar') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">المقدمة (إنجليزي) <span class="text-danger">*</span></label>
                                        <textarea class="form-control" v-model="form.intro.en" rows="3" maxlength="500"
                                            :class="{'is-invalid': fieldError('intro.en')}"></textarea>
                                        <div class="invalid-feedback" v-if="fieldError('intro.en')">{{ fieldError('intro.en') }}</div>
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
                                            <input id="faq-section-active" type="checkbox" v-model="form.is_active">
                                            <label for="faq-section-active" class="label-primary"></label>
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
                            <h6 class="card-title mb-0"><i class="bx bx-help-circle me-2"></i>قائمة الأسئلة</h6>
                            <button v-if="permission.includes('landing faq edit')"
                                @click.prevent="showModelCreate"
                                class="btn btn-sm btn-primary-light">
                                <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة سؤال
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-2">
                                <table class="table text-nowrap table-striped">
                                    <thead><tr>
                                        <th>#</th><th>السؤال (AR)</th><th>السؤال (EN)</th>
                                        <th>الترتيب</th><th>الحالة</th><th>إجراء</th>
                                    </tr></thead>
                                    <tbody v-if="items.length">
                                        <tr v-for="(item, index) in items" :key="item.id">
                                            <td>{{ index + 1 }}</td>
                                            <td style="max-width:240px;white-space:normal;">{{ item.question_ar }}</td>
                                            <td style="max-width:240px;white-space:normal;">{{ item.question_en }}</td>
                                            <td>{{ item.sort_order }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-success-transparent" v-if="item.is_active">نشط</span>
                                                <span class="badge rounded-pill bg-danger-transparent" v-else>غير نشط</span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button v-if="permission.includes('landing faq edit')"
                                                        @click.prevent="showEditMode(item)"
                                                        class="btn btn-icon btn-sm btn-info-transparent rounded-pill"><i class="ri-edit-line"></i></button>
                                                    <button v-if="permission.includes('landing faq edit')"
                                                        @click.prevent="deleteItem(item.id, index)"
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

        <ModalCreateAndUpdate
            v-if="itemModalOpen"
            :key="`${itemType}-${itemRow?.id ?? 'new'}`"
            :type="itemType"
            :dataRow="itemRow"
            @created="onItemSaved"
            @closed="itemModalOpen = false"
        />
    </div>
</template>

<script>
import { ref, onMounted, computed, nextTick } from 'vue';
import { useStore } from 'vuex';
import adminApi from '../../../../api/adminAxios';
import ModalCreateAndUpdate from './ModalCreateAndUpdate.vue';

const buildBgImageUrl = (data) => {
    if (!data) return null;
    let url = data.bg_image || null;
    if (!url && data.bg_image_file) {
        url = `/upload/general/${data.bg_image_file.split('/').map(encodeURIComponent).join('/')}`;
    }
    if (!url) return null;
    if (url.startsWith('http') || url.startsWith('blob:')) return url;
    if (url.startsWith('/')) return `${window.location.origin}${url}`;
    return url;
};

export default {
    name: 'LandingFaqs',
    components: { ModalCreateAndUpdate },
    setup() {
        const store      = useStore();
        const permission = computed(() => store.state.authAdmin.permission);

        const loading       = ref(true);
        const saving        = ref(false);
        const errors        = ref({});
        const items         = ref([]);
        const itemType      = ref('create');
        const itemRow       = ref(null);
        const itemModalOpen = ref(false);
        const bgImageInput  = ref(null);
        const bgImagePreview = ref(null);
        const bgImageFile   = ref(null);

        const MAX_SHORT = 100;
        const MAX_INTRO = 500;

        const fieldError = (key) => errors.value[key]?.[0] || '';
        const headlineError = (index, lang) => fieldError(`headline.${index}.${lang}`);

        const defaultForm = () => ({
            subtitle: { ar: '', en: '' },
            headline: [{ title: { ar: '', en: '' }, check: false }],
            intro: { ar: '', en: '' },
            is_active: true,
            current_bg_image: null,
        });

        const form = ref(defaultForm());

        const displayBgImage = computed(() => bgImagePreview.value || form.value.current_bg_image || null);

        const normalizeHeadline = (headline) => {
            if (!Array.isArray(headline) || !headline.length) {
                return [{ title: { ar: '', en: '' }, check: false }];
            }
            return headline.map(seg => ({
                title: { ar: seg.title?.ar || '', en: seg.title?.en || '' },
                check: Boolean(seg.check),
            }));
        };

        const loadSection = () => adminApi.get('dashboard/landing/faqs').then(res => {
            const data = res.data.data;
            bgImagePreview.value = null;
            bgImageFile.value    = null;
            if (bgImageInput.value) bgImageInput.value.value = '';

            form.value = {
                subtitle: data.subtitle || { ar: '', en: '' },
                headline: normalizeHeadline(data.headline),
                intro: data.intro || { ar: '', en: '' },
                is_active: !!data.is_active,
                current_bg_image: buildBgImageUrl(data),
            };
        });

        const loadItems = () => adminApi.get('dashboard/landing/faq-items', { params: { per_page: 100 } }).then(res => {
            const payload = res.data.data;
            items.value = Array.isArray(payload) ? payload : (payload?.data ?? []);
        });

        const load = () => {
            loading.value = true;
            Promise.all([loadSection(), loadItems()]).finally(() => { loading.value = false; });
        };

        const addHeadlineSegment = () => {
            form.value.headline.push({ title: { ar: '', en: '' }, check: false });
        };

        const removeHeadlineSegment = (index) => {
            form.value.headline.splice(index, 1);
        };

        const handleBgImage = (e) => {
            const file = e.target.files[0];
            if (!file) return;
            bgImageFile.value    = file;
            bgImagePreview.value = URL.createObjectURL(file);
        };

        const clearBgImage = () => {
            bgImageFile.value = null;
            bgImagePreview.value = null;
            form.value.current_bg_image = null;
            if (bgImageInput.value) bgImageInput.value.value = '';
        };

        const validateSection = () => {
            errors.value = {};
            const add = (key, message) => { errors.value[key] = [message]; };

            ['ar', 'en'].forEach((lang) => {
                const label = lang === 'ar' ? 'عربي' : 'إنجليزي';
                const sub = (form.value.subtitle[lang] || '').trim();
                if (!sub) add(`subtitle.${lang}`, `العنوان الفرعي (${label}) مطلوب.`);
                else if (sub.length > MAX_SHORT) add(`subtitle.${lang}`, `العنوان الفرعي (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);

                const intro = (form.value.intro[lang] || '').trim();
                if (!intro) add(`intro.${lang}`, `المقدمة (${label}) مطلوبة.`);
                else if (intro.length > MAX_INTRO) add(`intro.${lang}`, `المقدمة (${label}) يجب ألا تتجاوز ${MAX_INTRO} حرف.`);
            });

            form.value.headline.forEach((seg, index) => {
                ['ar', 'en'].forEach((lang) => {
                    const label = lang === 'ar' ? 'عربي' : 'إنجليزي';
                    const val = (seg.title[lang] || '').trim();
                    if (!val) add(`headline.${index}.${lang}`, `العنوان الرئيسي — الجزء ${index + 1} (${label}) مطلوب.`);
                    else if (val.length > MAX_SHORT) add(`headline.${index}.${lang}`, `العنوان الرئيسي — الجزء ${index + 1} (${label}) يجب ألا يتجاوز ${MAX_SHORT} حرف.`);
                });
            });

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
            fd.append('intro', JSON.stringify(form.value.intro));
            fd.append('is_active', form.value.is_active ? 1 : 0);
            if (bgImageFile.value) fd.append('bg_image', bgImageFile.value);
            fd.append('_method', 'PUT');

            adminApi.post('dashboard/landing/faqs', fd)
                .then(() => {
                    Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', showConfirmButton: false, timer: 1500 });
                    loadSection();
                })
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; });
        };

        const openItemModal = () => {
            itemModalOpen.value = true;
            nextTick(() => {
                nextTick(() => {
                    const el = document.getElementById('faq-item-modal');
                    if (!el) return;
                    bootstrap.Modal.getOrCreateInstance(el).show();
                });
            });
        };

        const onItemSaved = () => {
            loadItems();
            itemModalOpen.value = false;
        };

        const showModelCreate = () => {
            itemType.value = 'create';
            itemRow.value  = null;
            openItemModal();
        };

        const showEditMode = (item) => {
            itemType.value = 'edit';
            itemRow.value  = { ...item };
            openItemModal();
        };

        const deleteItem = (id, index) => {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف هذا السؤال نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
            }).then(result => {
                if (!result.isConfirmed) return;
                adminApi.delete(`dashboard/landing/faq-items/${id}`).then(() => {
                    items.value.splice(index, 1);
                    Swal.fire({ icon: 'success', title: 'تم الحذف', timer: 1500, showConfirmButton: false });
                });
            });
        };

        onMounted(load);

        return {
            loading, saving, errors, form, items, permission,
            itemType, itemRow, itemModalOpen, bgImageInput,
            displayBgImage,
            fieldError, headlineError,
            addHeadlineSegment, removeHeadlineSegment,
            handleBgImage, clearBgImage,
            submitSection, onItemSaved, showModelCreate, showEditMode, deleteItem,
        };
    }
};
</script>
