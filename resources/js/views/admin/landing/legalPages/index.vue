<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">الصفحات القانونية</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">الصفحات القانونية</li>
                </ol></nav>
            </div>
        </div>

        <loader v-if="loading" />

        <div v-else>
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item" v-for="page in pages" :key="page.type">
                    <button type="button" class="nav-link" :class="{ active: activeType === page.type }" @click="selectPage(page.type)">
                        {{ page.type_label }}
                    </button>
                </li>
            </ul>

            <form v-if="form" @submit.prevent="submit">
                <div class="card custom-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="bx bx-file me-2"></i>{{ activePageLabel }}</h6>
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">العنوان (عربي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.title.ar" maxlength="200"
                                :class="{'is-invalid': fieldError('title.ar')}">
                            <div class="invalid-feedback" v-if="fieldError('title.ar')">{{ fieldError('title.ar') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.title.en" maxlength="200"
                                :class="{'is-invalid': fieldError('title.en')}">
                            <div class="invalid-feedback" v-if="fieldError('title.en')">{{ fieldError('title.en') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المحتوى (عربي) <span class="text-danger">*</span></label>
                            <RichTextEditor
                                :key="`content-ar-${activeType}`"
                                v-model="forms[activeType].content.ar"
                                :rtl="true"
                                :invalid="!!fieldError('content.ar')"
                            />
                            <div class="invalid-feedback d-block" v-if="fieldError('content.ar')">{{ fieldError('content.ar') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المحتوى (إنجليزي) <span class="text-danger">*</span></label>
                            <RichTextEditor
                                :key="`content-en-${activeType}`"
                                v-model="forms[activeType].content.en"
                                :invalid="!!fieldError('content.en')"
                            />
                            <div class="invalid-feedback d-block" v-if="fieldError('content.en')">{{ fieldError('content.en') }}</div>
                        </div>
                        <div class="col-12">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input :id="'legal-active-' + form.id" type="checkbox" v-model="form.is_active">
                                <label :for="'legal-active-' + form.id" class="label-primary"></label>
                                <span class="ms-2">الصفحة نشطة (تظهر للزوار)</span>
                            </div>
                        </div>
                        <div class="col-12" v-if="previewUrl">
                            <a :href="previewUrl" target="_blank" class="btn btn-sm btn-primary-light">
                                <i class="ri-external-link-line me-1"></i>معاينة الصفحة
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-3" v-if="permission.includes('landing legal edit')">
                    <button type="submit" class="btn btn-primary px-5" :disabled="saving">
                        <span v-if="saving"><i class="ri-loader-2-fill me-1"></i>جاري الحفظ...</span>
                        <span v-else><i class="ri-save-line me-1"></i>حفظ {{ activePageLabel }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import RichTextEditor from '../../../../components/general/RichTextEditor.vue';
import adminApi from '../../../../api/adminAxios';

const TYPE_PRIVACY = 1;
const TYPE_TERMS = 2;

const toEditorHtml = (value) => {
    if (!value) return '';
    if (/<[a-z][\s\S]*>/i.test(value)) return value;

    return value
        .split(/\n{2,}/)
        .map((paragraph) => `<p>${paragraph.trim().replace(/\n/g, '<br>')}</p>`)
        .join('');
};

export default {
    name: 'LandingLegalPages',
    components: { RichTextEditor },
    setup() {
        const store = useStore();
        const permission = computed(() => store.state.authAdmin.permission);
        const loading = ref(true);
        const saving = ref(false);
        const errors = ref({});
        const pages = ref([]);
        const activeType = ref(TYPE_PRIVACY);
        const forms = ref({});

        const fieldError = (key) => errors.value[key]?.[0] || '';

        const form = computed(() => forms.value[activeType.value] || null);

        const activePageLabel = computed(() => {
            const p = pages.value.find(x => x.type === activeType.value);
            return p?.type_label || '';
        });

        const previewUrl = computed(() => {
            if (!form.value?.is_active) return '';
            const path = activeType.value === TYPE_PRIVACY ? '/privacy' : '/terms';
            return `${window.location.origin}${path}`;
        });

        const mapPageToForm = (page) => ({
            id: page.id,
            type: page.type,
            title: { ar: page.title?.ar || '', en: page.title?.en || '' },
            content: {
                ar: toEditorHtml(page.content?.ar || ''),
                en: toEditorHtml(page.content?.en || ''),
            },
            is_active: !!page.is_active,
        });

        const load = () => {
            loading.value = true;
            adminApi.get('dashboard/landing/legal-pages').then(res => {
                const list = res.data.data || [];
                pages.value = list;
                const mapped = {};
                list.forEach(p => { mapped[p.type] = mapPageToForm(p); });
                forms.value = mapped;
                if (!mapped[activeType.value] && list.length) {
                    activeType.value = list[0].type;
                }
            }).finally(() => { loading.value = false; });
        };

        const selectPage = (type) => {
            activeType.value = type;
            errors.value = {};
        };

        const submit = () => {
            if (!form.value) return;
            saving.value = true;
            errors.value = {};

            const payload = {
                title: JSON.stringify(form.value.title),
                content: JSON.stringify(form.value.content),
                is_active: form.value.is_active ? 1 : 0,
            };

            adminApi.put(`dashboard/landing/legal-pages/${form.value.id}`, payload)
                .then(() => Swal.fire({ icon: 'success', title: 'تم الحفظ بنجاح', timer: 1500, showConfirmButton: false }))
                .catch(err => { if (err.response?.data?.errors) errors.value = err.response.data.errors; })
                .finally(() => { saving.value = false; load(); });
        };

        onMounted(load);

        return {
            permission, loading, saving, pages, activeType, form, forms,
            fieldError, activePageLabel, previewUrl, selectPage, submit,
        };
    },
};
</script>
