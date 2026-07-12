<template>
    <div class="modal fade" id="blog-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ selectedItem ? 'تعديل المقال' : 'إضافة مقال جديد' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form @submit.prevent="submit">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Titles -->
                            <div class="col-md-6">
                                <label class="form-label">العنوان (EN) <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.title_en" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">العنوان (AR) <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.title_ar" class="form-control" dir="rtl" required>
                            </div>
                            <!-- Excerpts -->
                            <div class="col-md-6">
                                <label class="form-label">المقتطف (EN)</label>
                                <textarea v-model="form.excerpt_en" class="form-control" rows="2" placeholder="Short summary..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المقتطف (AR)</label>
                                <textarea v-model="form.excerpt_ar" class="form-control" rows="2" dir="rtl" placeholder="ملخص قصير..."></textarea>
                            </div>
                            <!-- Content -->
                            <div class="col-md-6">
                                <label class="form-label">المحتوى الكامل (EN)</label>
                                <textarea v-model="form.content_en" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المحتوى الكامل (AR)</label>
                                <textarea v-model="form.content_ar" class="form-control" rows="5" dir="rtl"></textarea>
                            </div>
                            <!-- Author, Date, Sort -->
                            <div class="col-md-4">
                                <label class="form-label">اسم الكاتب</label>
                                <input type="text" v-model="form.author_name" class="form-control" placeholder="Ahmed Al-Rashid">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">تاريخ النشر</label>
                                <input type="datetime-local" v-model="form.published_at" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الترتيب</label>
                                <input type="number" v-model="form.sort_order" class="form-control" min="0">
                            </div>
                            <!-- Published toggle -->
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" v-model="form.is_published" id="blogPublished">
                                    <label class="form-check-label" for="blogPublished">منشور</label>
                                </div>
                            </div>
                            <!-- Image -->
                            <div class="col-12">
                                <label class="form-label">صورة المقال</label>
                                <div v-if="imagePreview || (selectedItem && selectedItem.image)" class="mb-2 d-flex align-items-center gap-2">
                                    <img :src="imagePreview || '/upload/general/' + selectedItem?.image"
                                        alt="" style="width:120px;height:80px;border-radius:8px;object-fit:cover;border:1px solid #E2E8F0;">
                                    <button type="button" class="btn btn-sm btn-danger-light" @click="clearImage">
                                        <i class="ri-close-line"></i> إزالة
                                    </button>
                                </div>
                                <input ref="imgRef" type="file" accept="image/*" class="form-control" @change="onImage">
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

const emptyForm = () => ({ title_en: '', title_ar: '', excerpt_en: '', excerpt_ar: '', content_en: '', content_ar: '', author_name: '', published_at: '', sort_order: 0, is_published: true });

export default {
    name: 'BlogModal',
    props: { selectedItem: { default: null }, uri: { type: Object } },
    emits: ['refresh'],
    data() { return { saving: false, imagePreview: null, imageFile: null, form: emptyForm() }; },
    watch: {
        selectedItem(val) {
            this.imagePreview = null; this.imageFile = null;
            if (this.$refs.imgRef) this.$refs.imgRef.value = '';
            if (val) {
                this.form = { title_en: val.title_en, title_ar: val.title_ar, excerpt_en: val.excerpt_en || '', excerpt_ar: val.excerpt_ar || '', content_en: val.content_en || '', content_ar: val.content_ar || '', author_name: val.author_name || '', published_at: val.published_at ? val.published_at.substring(0, 16) : '', sort_order: val.sort_order || 0, is_published: !!val.is_published };
            } else { this.form = emptyForm(); }
        },
    },
    methods: {
        onImage(e) { const f = e.target.files[0]; if (!f) return; this.imageFile = f; this.imagePreview = URL.createObjectURL(f); },
        clearImage() { this.imageFile = null; this.imagePreview = null; if (this.$refs.imgRef) this.$refs.imgRef.value = ''; },
        async submit() {
            this.saving = true;
            try {
                const fd = new FormData();
                if (this.selectedItem) fd.append('_method', 'PUT');
                Object.entries(this.form).forEach(([k, v]) => fd.append(k, typeof v === 'boolean' ? (v ? 1 : 0) : (v ?? '')));
                if (this.imageFile) fd.append('image', this.imageFile);
                const url = this.selectedItem ? `${this.uri.value}/${this.selectedItem.id}` : this.uri.value;
                await adminApi.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
                bootstrap.Modal.getInstance(document.getElementById('blog-modal'))?.hide();
                this.$emit('refresh');
                Swal.fire({ icon: 'success', title: this.selectedItem ? 'تم التعديل' : 'تمت الإضافة', timer: 1500, showConfirmButton: false });
            } catch (err) {
                Swal.fire({ icon: 'error', title: err.response?.data?.message || 'حدث خطأ' });
            }
            this.saving = false;
        },
    },
};
</script>
