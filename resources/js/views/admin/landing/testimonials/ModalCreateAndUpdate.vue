<template>
    <div class="modal fade" id="testimonial-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ selectedItem ? 'تعديل رأي العميل' : 'إضافة رأي جديد' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form @submit.prevent="submit">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-12">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.name" class="form-control" required placeholder="Ahmed Al-Rashid">
                            </div>
                            <!-- Designation -->
                            <div class="col-md-6">
                                <label class="form-label">المسمى الوظيفي (EN)</label>
                                <input type="text" v-model="form.designation_en" class="form-control" placeholder="CEO, Company">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المسمى الوظيفي (AR)</label>
                                <input type="text" v-model="form.designation_ar" class="form-control" dir="rtl" placeholder="المدير التنفيذي">
                            </div>
                            <!-- Reviews -->
                            <div class="col-md-6">
                                <label class="form-label">الرأي (EN) <span class="text-danger">*</span></label>
                                <textarea v-model="form.review_en" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرأي (AR) <span class="text-danger">*</span></label>
                                <textarea v-model="form.review_ar" class="form-control" rows="3" dir="rtl" required></textarea>
                            </div>
                            <!-- Rating -->
                            <div class="col-md-4">
                                <label class="form-label">التقييم (1-5)</label>
                                <select v-model="form.rating" class="form-select">
                                    <option v-for="n in [1,2,3,4,5]" :key="n" :value="n">{{ n }} نجمة</option>
                                </select>
                            </div>
                            <!-- Sort -->
                            <div class="col-md-4">
                                <label class="form-label">الترتيب</label>
                                <input type="number" v-model="form.sort_order" class="form-control" min="0">
                            </div>
                            <!-- Active -->
                            <div class="col-md-4 d-flex align-items-end pb-1">
                                <div class="form-check form-switch ms-1">
                                    <input class="form-check-input" type="checkbox" v-model="form.is_active" id="testimonialActive">
                                    <label class="form-check-label" for="testimonialActive">مفعّل</label>
                                </div>
                            </div>
                            <!-- Photo -->
                            <div class="col-12">
                                <label class="form-label">صورة العميل</label>
                                <div v-if="imagePreview || (selectedItem && selectedItem.photo)" class="mb-2 d-flex align-items-center gap-2">
                                    <img :src="imagePreview || '/upload/general/' + selectedItem?.photo"
                                        alt="" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:1px solid #E2E8F0;">
                                    <button type="button" class="btn btn-sm btn-danger-light" @click="clearPhoto">
                                        <i class="ri-close-line"></i> إزالة
                                    </button>
                                </div>
                                <input ref="photoRef" type="file" accept="image/*" class="form-control" @change="onPhoto">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                            {{ selectedItem ? 'حفظ التعديلات' : 'إضافة' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import adminApi from '@/api/adminAxios';

export default {
    name: 'TestimonialModal',
    props: { selectedItem: { default: null }, uri: { type: Object } },
    emits: ['refresh'],
    data() {
        return {
            saving: false,
            imagePreview: null,
            photoFile: null,
            form: { name: '', designation_en: '', designation_ar: '', review_en: '', review_ar: '', rating: 5, sort_order: 0, is_active: true },
        };
    },
    watch: {
        selectedItem(val) {
            this.imagePreview = null;
            this.photoFile = null;
            if (this.$refs.photoRef) this.$refs.photoRef.value = '';
            if (val) {
                this.form = { name: val.name, designation_en: val.designation_en || '', designation_ar: val.designation_ar || '', review_en: val.review_en || '', review_ar: val.review_ar || '', rating: val.rating || 5, sort_order: val.sort_order || 0, is_active: !!val.is_active };
            } else {
                this.form = { name: '', designation_en: '', designation_ar: '', review_en: '', review_ar: '', rating: 5, sort_order: 0, is_active: true };
            }
        },
    },
    methods: {
        onPhoto(e) {
            const f = e.target.files[0];
            if (!f) return;
            this.photoFile = f;
            this.imagePreview = URL.createObjectURL(f);
        },
        clearPhoto() {
            this.photoFile = null;
            this.imagePreview = null;
            if (this.$refs.photoRef) this.$refs.photoRef.value = '';
        },
        async submit() {
            this.saving = true;
            try {
                const fd = new FormData();
                if (this.selectedItem) fd.append('_method', 'PUT');
                Object.entries(this.form).forEach(([k, v]) => fd.append(k, typeof v === 'boolean' ? (v ? 1 : 0) : (v ?? '')));
                if (this.photoFile) fd.append('photo', this.photoFile);

                const url = this.selectedItem ? `${this.uri.value}/${this.selectedItem.id}` : this.uri.value;
                await adminApi.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } });

                bootstrap.Modal.getInstance(document.getElementById('testimonial-modal'))?.hide();
                this.$emit('refresh');
                Swal.fire({ icon: 'success', title: this.selectedItem ? 'تم التعديل' : 'تمت الإضافة', timer: 1500, showConfirmButton: false });
            } catch (err) {
                const msg = err.response?.data?.message || 'حدث خطأ';
                Swal.fire({ icon: 'error', title: msg });
            }
            this.saving = false;
        },
    },
};
</script>
