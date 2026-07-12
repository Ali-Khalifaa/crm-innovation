<template>
    <div class="modal fade" id="team-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ selectedItem ? 'تعديل عضو الفريق' : 'إضافة عضو جديد' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form @submit.prevent="submit">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم (EN) <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.name_en" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الاسم (AR) <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.name_ar" class="form-control" dir="rtl" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المنصب (EN) <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.role_en" class="form-control" required placeholder="CEO">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المنصب (AR) <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.role_ar" class="form-control" dir="rtl" required placeholder="المدير التنفيذي">
                            </div>
                            <!-- Social Links -->
                            <div class="col-12"><h6 class="text-muted small fw-semibold border-top pt-3 mt-1">روابط التواصل الاجتماعي</h6></div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-facebook text-primary me-1"></i>Facebook</label>
                                <input type="url" v-model="form.facebook" class="form-control" placeholder="https://facebook.com/...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-twitter text-info me-1"></i>Twitter</label>
                                <input type="url" v-model="form.twitter" class="form-control" placeholder="https://twitter.com/...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-linkedin text-primary me-1"></i>LinkedIn</label>
                                <input type="url" v-model="form.linkedin" class="form-control" placeholder="https://linkedin.com/in/...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fab fa-instagram text-danger me-1"></i>Instagram</label>
                                <input type="url" v-model="form.instagram" class="form-control" placeholder="https://instagram.com/...">
                            </div>
                            <!-- Sort & Active -->
                            <div class="col-md-4">
                                <label class="form-label">الترتيب</label>
                                <input type="number" v-model="form.sort_order" class="form-control" min="0">
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-1">
                                <div class="form-check form-switch ms-1">
                                    <input class="form-check-input" type="checkbox" v-model="form.is_active" id="teamActive">
                                    <label class="form-check-label" for="teamActive">مفعّل</label>
                                </div>
                            </div>
                            <!-- Photo -->
                            <div class="col-12">
                                <label class="form-label">صورة العضو</label>
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

const emptyForm = () => ({ name_en: '', name_ar: '', role_en: '', role_ar: '', facebook: '', twitter: '', linkedin: '', instagram: '', sort_order: 0, is_active: true });

export default {
    name: 'TeamModal',
    props: { selectedItem: { default: null }, uri: { type: Object } },
    emits: ['refresh'],
    data() { return { saving: false, imagePreview: null, photoFile: null, form: emptyForm() }; },
    watch: {
        selectedItem(val) {
            this.imagePreview = null; this.photoFile = null;
            if (this.$refs.photoRef) this.$refs.photoRef.value = '';
            if (val) {
                this.form = { name_en: val.name_en, name_ar: val.name_ar, role_en: val.role_en, role_ar: val.role_ar, facebook: val.facebook || '', twitter: val.twitter || '', linkedin: val.linkedin || '', instagram: val.instagram || '', sort_order: val.sort_order || 0, is_active: !!val.is_active };
            } else { this.form = emptyForm(); }
        },
    },
    methods: {
        onPhoto(e) { const f = e.target.files[0]; if (!f) return; this.photoFile = f; this.imagePreview = URL.createObjectURL(f); },
        clearPhoto() { this.photoFile = null; this.imagePreview = null; if (this.$refs.photoRef) this.$refs.photoRef.value = ''; },
        async submit() {
            this.saving = true;
            try {
                const fd = new FormData();
                if (this.selectedItem) fd.append('_method', 'PUT');
                Object.entries(this.form).forEach(([k, v]) => fd.append(k, typeof v === 'boolean' ? (v ? 1 : 0) : (v ?? '')));
                if (this.photoFile) fd.append('photo', this.photoFile);
                const url = this.selectedItem ? `${this.uri.value}/${this.selectedItem.id}` : this.uri.value;
                await adminApi.post(url, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
                bootstrap.Modal.getInstance(document.getElementById('team-modal'))?.hide();
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
