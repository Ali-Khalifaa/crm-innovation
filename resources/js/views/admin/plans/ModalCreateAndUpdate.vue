<template>
    <div class="modal fade" id="plans-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ type === 'create' ? 'إضافة باقة جديدة' : 'تعديل الباقة' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">اسم الباقة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.name"
                                :class="{'is-invalid': errors.name}" @input="autoSlug" placeholder="مثال: Professional">
                            <div class="invalid-feedback" v-if="errors.name">{{ errors.name[0] }}</div>
                        </div>
                        <!-- Slug -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.slug"
                                :class="{'is-invalid': errors.slug}" placeholder="مثال: pro">
                            <div class="invalid-feedback" v-if="errors.slug">{{ errors.slug[0] }}</div>
                        </div>
                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">الوصف</label>
                            <textarea class="form-control" v-model="form.description" rows="2" placeholder="وصف مختصر للباقة"></textarea>
                        </div>
                        <!-- Prices -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السعر الشهري ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.monthly_price"
                                :class="{'is-invalid': errors.monthly_price}" min="0" step="0.01">
                            <div class="invalid-feedback" v-if="errors.monthly_price">{{ errors.monthly_price[0] }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">السعر السنوي ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="form.yearly_price"
                                :class="{'is-invalid': errors.yearly_price}" min="0" step="0.01">
                            <div class="invalid-feedback" v-if="errors.yearly_price">{{ errors.yearly_price[0] }}</div>
                        </div>
                        <!-- Limits -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">أقصى مستخدمين <small class="text-muted">(0 = غير محدود)</small></label>
                            <input type="number" class="form-control" v-model="form.max_users" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">أقصى جهات اتصال <small class="text-muted">(0 = غير محدود)</small></label>
                            <input type="number" class="form-control" v-model="form.max_contacts" min="0">
                        </div>
                        <!-- Features -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">المميزات</label>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                <div class="form-check" v-for="f in allFeatures" :key="f.value">
                                    <input class="form-check-input" type="checkbox" :id="'f_'+f.value"
                                        :value="f.value" v-model="form.features">
                                    <label class="form-check-label" :for="'f_'+f.value">{{ f.label }}</label>
                                </div>
                            </div>
                        </div>
                        <!-- Sort + Toggles -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">الترتيب</label>
                            <input type="number" class="form-control" v-model="form.sort_order" min="0">
                        </div>
                        <div class="col-md-4 d-flex align-items-end pb-1">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="plan-is-active" type="checkbox" v-model="form.is_active">
                                <label for="plan-is-active" class="label-primary"></label>
                                <span class="ms-2">نشط</span>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end pb-1">
                            <div class="custom-toggle-switch d-flex align-items-center">
                                <input id="plan-is-featured" type="checkbox" v-model="form.is_featured">
                                <label for="plan-is-featured" class="label-primary"></label>
                                <span class="ms-2">باقة مميزة</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" @click="submit" :disabled="saving">
                        <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                        {{ type === 'create' ? 'إنشاء الباقة' : 'حفظ التعديلات' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watch, onMounted, nextTick } from 'vue';
import adminApi from '../../../api/adminAxios';

export default {
    props: {
        type: { default: 'create' },
        dataRow: { default: null },
    },
    emits: ['saved'],
    setup(props, { emit }) {
        const saving = ref(false);
        const errors = ref({});

        const defaultForm = () => ({
            name: '', slug: '', description: '',
            monthly_price: 0, yearly_price: 0,
            max_users: 0, max_contacts: 0,
            features: [], sort_order: 0,
            is_active: true, is_featured: false,
        });

        const form = ref(defaultForm());

        const allFeatures = [
            { value: 'contacts', label: 'جهات الاتصال' },
            { value: 'deals', label: 'الصفقات' },
            { value: 'tasks', label: 'المهام' },
            { value: 'invoices', label: 'الفواتير' },
            { value: 'reports', label: 'التقارير' },
            { value: 'priority_support', label: 'دعم أولوية' },
        ];

        const autoSlug = () => {
            if (props.type === 'create') {
                form.value.slug = form.value.name
                    .toLowerCase().trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9-]/g, '');
            }
        };

        // Populate form when dataRow prop changes (edit mode)
        watch(() => props.dataRow, (newVal) => {
            if (newVal && props.type === 'edit') {
                form.value = {
                    name: newVal.name,
                    slug: newVal.slug,
                    description: newVal.description || '',
                    monthly_price: newVal.monthly_price,
                    yearly_price: newVal.yearly_price,
                    max_users: newVal.max_users,
                    max_contacts: newVal.max_contacts,
                    features: [...(newVal.features || [])],
                    sort_order: newVal.sort_order,
                    is_active: newVal.is_active,
                    is_featured: newVal.is_featured,
                };
            } else {
                form.value = defaultForm();
            }
            errors.value = {};
        });

        onMounted(() => {
            const el = document.getElementById('plans-modal');
            if (el) {
                el.addEventListener('hidden.bs.modal', () => {
                    errors.value = {};
                    form.value = defaultForm();
                });
            }
        });

        const submit = async () => {
            saving.value = true;
            errors.value = {};
            try {
                if (props.type === 'edit' && props.dataRow) {
                    await adminApi.put(`dashboard/plans/${props.dataRow.id}`, form.value);
                } else {
                    await adminApi.post('dashboard/plans', form.value);
                }
                // close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('plans-modal'));
                if (modal) modal.hide();

                Swal.fire({
                    icon: 'success',
                    title: props.type === 'create' ? 'تم إنشاء الباقة بنجاح' : 'تم تعديل الباقة بنجاح',
                    showConfirmButton: false,
                    timer: 1500,
                });
                emit('saved');
            } catch (e) {
                errors.value = e.response?.data?.errors || {};
                if (!Object.keys(errors.value).length && e.response?.data?.message) {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: e.response.data.message });
                }
            } finally {
                saving.value = false;
            }
        };

        return { form, errors, saving, allFeatures, autoSlug, submit };
    }
};
</script>
