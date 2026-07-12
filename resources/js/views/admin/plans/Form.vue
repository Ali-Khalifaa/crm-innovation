<template>
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between my-4">
            <div>
                <p class="fw-semibold fs-18 mb-0">{{ isEdit ? 'تعديل الباقة' : 'باقة جديدة' }}</p>
            </div>
            <router-link :to="{name:'plans'}" class="btn btn-light btn-wave">
                <i class="bx bx-arrow-back me-1"></i> رجوع
            </router-link>
        </div>

        <div class="card custom-card" style="max-width:720px;">
            <div class="card-body">
                <div v-if="loading" class="d-flex justify-content-center py-4">
                    <div class="spinner-border text-primary"></div>
                </div>
                <form @submit.prevent="submit" v-else>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">اسم الباقة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.slug" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">الوصف</label>
                            <textarea class="form-control" v-model="form.description" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السعر الشهري ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.monthly_price" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السعر السنوي ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.yearly_price" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">أقصى عدد مستخدمين (0 = غير محدود)</label>
                            <input type="number" class="form-control" v-model="form.max_users" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">أقصى عدد جهات اتصال (0 = غير محدود)</label>
                            <input type="number" class="form-control" v-model="form.max_contacts" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">المميزات</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check" v-for="f in allFeatures" :key="f.value">
                                    <input class="form-check-input" type="checkbox" :id="f.value"
                                        :value="f.value" v-model="form.features">
                                    <label class="form-check-label" :for="f.value">{{ f.label }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="0">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" v-model="form.is_active">
                                <label class="form-check-label" for="is_active">نشط</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" v-model="form.is_featured">
                                <label class="form-check-label" for="is_featured">مميز</label>
                            </div>
                        </div>

                        <div class="col-12" v-if="errors">
                            <div class="alert alert-danger py-2">{{ errors }}</div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                                {{ isEdit ? 'حفظ التعديلات' : 'إنشاء الباقة' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import adminApi from '../../../api/adminAxios';

export default {
    setup() {
        const route = useRoute();
        const router = useRouter();
        const isEdit = computed(() => !!route.params.id);
        const loading = ref(false);
        const saving = ref(false);
        const errors = ref(null);

        const allFeatures = [
            { value: 'contacts', label: 'جهات الاتصال' },
            { value: 'deals', label: 'الصفقات' },
            { value: 'tasks', label: 'المهام' },
            { value: 'invoices', label: 'الفواتير' },
            { value: 'reports', label: 'التقارير' },
            { value: 'priority_support', label: 'دعم أولوية' },
        ];

        const form = ref({
            name: '', slug: '', description: '',
            monthly_price: 0, yearly_price: 0,
            max_users: 0, max_contacts: 0,
            features: [], sort_order: 0,
            is_active: true, is_featured: false,
        });

        onMounted(async () => {
            if (isEdit.value) {
                loading.value = true;
                try {
                    const res = await adminApi.get(`dashboard/plans/${route.params.id}`);
                    const p = res.data.data;
                    form.value = {
                        name: p.name, slug: p.slug, description: p.description || '',
                        monthly_price: p.monthly_price, yearly_price: p.yearly_price,
                        max_users: p.max_users, max_contacts: p.max_contacts,
                        features: p.features || [], sort_order: p.sort_order,
                        is_active: p.is_active, is_featured: p.is_featured,
                    };
                } catch(e) { console.error(e); } finally { loading.value = false; }
            }
        });

        const submit = async () => {
            saving.value = true;
            errors.value = null;
            try {
                if (isEdit.value) {
                    await adminApi.put(`dashboard/plans/${route.params.id}`, form.value);
                } else {
                    await adminApi.post('dashboard/plans', form.value);
                }
                router.push({ name: 'plans' });
            } catch (e) {
                errors.value = e.response?.data?.message || 'حدث خطأ';
            } finally {
                saving.value = false;
            }
        };

        return { isEdit, loading, saving, errors, form, allFeatures, submit };
    }
};
</script>
