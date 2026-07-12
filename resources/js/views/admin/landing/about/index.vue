<template>
  <div class="container-fluid">
    <div class="page-header mb-4 d-flex align-items-center justify-content-between">
      <div>
        <h4 class="page-title mb-0">{{ $t('landing.about') }}</h4>
        <p class="text-muted small mb-0">{{ $t('landing.about_desc') }}</p>
      </div>
      <button class="btn btn-primary" @click="save" :disabled="saving">
        <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
        <i v-else class="fas fa-save me-2"></i>{{ $t('common.save') }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <form v-else @submit.prevent="save">
      <!-- Basic Content -->
      <div class="card mb-4">
        <div class="card-header"><h6 class="mb-0">{{ $t('landing.section_content') }}</h6></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.subtitle_en') }}</label>
              <input type="text" v-model="form.subtitle_en" class="form-control" placeholder="ABOUT US">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.subtitle_ar') }}</label>
              <input type="text" v-model="form.subtitle_ar" class="form-control" dir="rtl" placeholder="من نحن">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.title_en') }} <small class="text-muted">(HTML ok)</small></label>
              <input type="text" v-model="form.title_en" class="form-control" placeholder="The <span>Smart CRM</span>...">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.title_ar') }} <small class="text-muted">(HTML ok)</small></label>
              <input type="text" v-model="form.title_ar" class="form-control" dir="rtl">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.description_en') }}</label>
              <textarea v-model="form.description_en" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.description_ar') }}</label>
              <textarea v-model="form.description_ar" class="form-control" rows="3" dir="rtl"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Image -->
      <div class="card mb-4">
        <div class="card-header"><h6 class="mb-0">{{ $t('common.image') }}</h6></div>
        <div class="card-body">
          <div class="d-flex align-items-start gap-3">
            <div v-if="imagePreview || form.image" class="position-relative">
              <img :src="imagePreview || '/upload/general/' + form.image" alt="" style="width:200px;height:140px;object-fit:cover;border-radius:12px;border:1px solid #E2E8F0;">
              <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" @click="clearImage" style="padding:2px 6px;">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <div>
              <input ref="imageInput" type="file" accept="image/*" class="form-control" @change="onImageChange" style="width:280px;">
              <small class="text-muted d-block mt-1">{{ $t('landing.about_image_hint') }}</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Box 1 -->
      <div class="card mb-4">
        <div class="card-header"><h6 class="mb-0">{{ $t('landing.about_box1') }}</h6></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">{{ $t('common.icon') }} <small class="text-muted">(fa-bullseye)</small></label>
              <div class="input-group">
                <span class="input-group-text"><i :class="'fas ' + (form.box1_icon || 'fa-bullseye')"></i></span>
                <input type="text" v-model="form.box1_icon" class="form-control" placeholder="fa-bullseye">
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ $t('common.title_en') }}</label>
              <input type="text" v-model="form.box1_title_en" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ $t('common.title_ar') }}</label>
              <input type="text" v-model="form.box1_title_ar" class="form-control" dir="rtl">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.description_en') }}</label>
              <textarea v-model="form.box1_desc_en" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.description_ar') }}</label>
              <textarea v-model="form.box1_desc_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Box 2 -->
      <div class="card mb-4">
        <div class="card-header"><h6 class="mb-0">{{ $t('landing.about_box2') }}</h6></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">{{ $t('common.icon') }} <small class="text-muted">(fa-rocket)</small></label>
              <div class="input-group">
                <span class="input-group-text"><i :class="'fas ' + (form.box2_icon || 'fa-rocket')"></i></span>
                <input type="text" v-model="form.box2_icon" class="form-control" placeholder="fa-rocket">
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ $t('common.title_en') }}</label>
              <input type="text" v-model="form.box2_title_en" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">{{ $t('common.title_ar') }}</label>
              <input type="text" v-model="form.box2_title_ar" class="form-control" dir="rtl">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.description_en') }}</label>
              <textarea v-model="form.box2_desc_en" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ $t('common.description_ar') }}</label>
              <textarea v-model="form.box2_desc_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Status -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" v-model="form.is_active" id="aboutActive">
            <label class="form-check-label" for="aboutActive">{{ $t('common.active') }}</label>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import adminApi from '@/api/adminAxios';

export default {
  name: 'LandingAboutIndex',
  data() {
    return {
      loading: true,
      saving: false,
      imagePreview: null,
      imageFile: null,
      form: {
        subtitle_en: '', subtitle_ar: '',
        title_en: '', title_ar: '',
        description_en: '', description_ar: '',
        image: null,
        box1_icon: 'fa-bullseye', box1_title_en: '', box1_title_ar: '', box1_desc_en: '', box1_desc_ar: '',
        box2_icon: 'fa-rocket',   box2_title_en: '', box2_title_ar: '', box2_desc_en: '', box2_desc_ar: '',
        is_active: true,
      },
    };
  },
  mounted() {
    this.fetch();
  },
  methods: {
    async fetch() {
      try {
        const { data } = await adminApi.get('landing/about');
        if (data?.data) Object.assign(this.form, data.data);
      } catch (_) {}
      this.loading = false;
    },
    onImageChange(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.imageFile = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    clearImage() {
      this.imageFile = null;
      this.imagePreview = null;
      this.form.image = null;
      if (this.$refs.imageInput) this.$refs.imageInput.value = '';
    },
    async save() {
      this.saving = true;
      try {
        const fd = new FormData();
        fd.append('_method', 'PUT');
        Object.entries(this.form).forEach(([k, v]) => {
          if (k !== 'image' && v !== null && v !== undefined) fd.append(k, v ? 1 : (typeof v === 'boolean' ? 0 : v));
        });
        // Boolean fix
        fd.set('is_active', this.form.is_active ? 1 : 0);
        if (this.imageFile) fd.append('image', this.imageFile);
        const { data } = await adminApi.post('landing/about', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        if (data?.data) Object.assign(this.form, data.data);
        this.imageFile = null; this.imagePreview = null;
        Swal.fire({ icon: 'success', title: this.$t('common.saved'), timer: 1500, showConfirmButton: false });
      } catch (err) {
        const msg = err.response?.data?.message || this.$t('common.error');
        Swal.fire({ icon: 'error', title: msg });
      }
      this.saving = false;
    },
  },
};
</script>
