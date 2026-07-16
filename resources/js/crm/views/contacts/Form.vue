<template>
  <CrmLayout>
    <div style="max-width:700px;margin:0 auto">

      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">
            {{ isEdit ? (isRtl ? 'تعديل جهة الاتصال' : 'Edit Contact') : (isRtl ? 'إضافة جهة اتصال' : 'New Contact') }}
          </h4>
          <p class="text-muted small mt-1 mb-0">
            {{ isRtl ? 'أدخل بيانات جهة الاتصال' : 'Fill in the contact information' }}
          </p>
        </div>
        <router-link to="/crm/contacts" class="btn btn-sm btn-outline-secondary flex-shrink-0">
          <i class="bi bi-arrow-left me-1"></i>{{ isRtl ? 'رجوع' : 'Back' }}
        </router-link>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:300px">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <form v-else @submit.prevent="save" class="card card-border">
        <div class="card-body">

          <!-- Basic Info -->
          <div class="fsect-title"><i class="bi bi-person me-2 text-primary"></i>{{ isRtl ? 'المعلومات الأساسية' : 'Basic Information' }}</div>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'الاسم الأول' : 'First Name' }} <span class="text-danger">*</span></label>
              <input v-model="form.first_name" type="text" class="form-control" :class="{'is-invalid': v$.first_name.$error}" @blur="v$.first_name.$touch()" :placeholder="isRtl?'محمد':'John'" />
              <div class="invalid-feedback" v-if="v$.first_name.$error">{{ v$.first_name.$errors[0]?.$message }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'الاسم الأخير' : 'Last Name' }}</label>
              <input v-model="form.last_name" type="text" class="form-control" :placeholder="isRtl?'أحمد':'Smith'" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'البريد الإلكتروني' : 'Email' }}</label>
              <input v-model="form.email" type="email" class="form-control" :class="{'is-invalid': v$.email.$error}" @blur="v$.email.$touch()" :placeholder="isRtl?'example@domain.com':'john@example.com'" />
              <div class="invalid-feedback" v-if="v$.email.$error">{{ v$.email.$errors[0]?.$message }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'الهاتف' : 'Phone' }}</label>
              <input v-model="form.phone" type="text" class="form-control" :placeholder="isRtl?'+20 10 xxxx xxxx':'+1 555 000 0000'" />
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'الحالة' : 'Status' }}</label>
              <select v-model="form.status" class="form-select">
                <option value="lead">{{ isRtl ? 'عميل محتمل' : 'Lead' }}</option>
                <option value="customer">{{ isRtl ? 'عميل' : 'Customer' }}</option>
                <option value="inactive">{{ isRtl ? 'غير نشط' : 'Inactive' }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'مصدر العميل' : 'Lead Source' }}</label>
              <select v-model="form.lead_source" class="form-select">
                <option :value="null">{{ isRtl ? 'غير محدد' : 'Not specified' }}</option>
                <option value="website">{{ isRtl ? 'الموقع الإلكتروني' : 'Website' }}</option>
                <option value="referral">{{ isRtl ? 'توصية' : 'Referral' }}</option>
                <option value="social_media">{{ isRtl ? 'التواصل الاجتماعي' : 'Social Media' }}</option>
                <option value="email_campaign">{{ isRtl ? 'حملة بريدية' : 'Email Campaign' }}</option>
                <option value="cold_call">{{ isRtl ? 'اتصال بارد' : 'Cold Call' }}</option>
                <option value="event">{{ isRtl ? 'فعالية' : 'Event' }}</option>
                <option value="other">{{ isRtl ? 'أخرى' : 'Other' }}</option>
              </select>
            </div>
          </div>

          <!-- Company & Address -->
          <div class="fsect-title"><i class="bi bi-building me-2 text-primary"></i>{{ isRtl ? 'الشركة والعنوان' : 'Company & Address' }}</div>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'ربط بشركة' : 'Link to Company' }}</label>
              <select v-model="form.company_id" class="form-select">
                <option :value="null">{{ isRtl ? 'بدون شركة' : 'No company' }}</option>
                <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">{{ isRtl ? 'اسم الشركة (نص حر)' : 'Company Name (text)' }}</label>
              <input v-model="form.company" type="text" class="form-control" :placeholder="isRtl?'اسم الشركة':'Company name'" />
            </div>
            <div class="col-12">
              <label class="form-label fw-medium">{{ isRtl ? 'العنوان' : 'Address' }}</label>
              <input v-model="form.address" type="text" class="form-control" :placeholder="isRtl?'العنوان الكامل':'Full address'" />
            </div>
          </div>

          <!-- Notes -->
          <div class="fsect-title"><i class="bi bi-journal me-2 text-primary"></i>{{ isRtl ? 'ملاحظات' : 'Notes' }}</div>
          <textarea v-model="form.notes" class="form-control" rows="4" :placeholder="isRtl?'إضافة ملاحظات...':'Add any notes...'"></textarea>

          <div v-if="error" class="alert alert-danger mt-3 py-2">{{ error }}</div>

        </div>

        <div class="card-footer d-flex gap-2 justify-content-end border-0 pt-0">
          <router-link to="/crm/contacts" class="btn btn-light">{{ isRtl ? 'إلغاء' : 'Cancel' }}</router-link>
          <button type="submit" class="btn btn-primary px-4" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
            {{ saving ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : (isEdit ? (isRtl ? 'حفظ التغييرات' : 'Save Changes') : (isRtl ? 'إضافة جهة الاتصال' : 'Add Contact')) }}
          </button>
        </div>
      </form>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { required, email as emailValidator, helpers } from '@vuelidate/validators'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store   = useStore()
const route   = useRoute()
const router  = useRouter()
const toast   = useToast()
const isRtl   = computed(() => store.state.ui?.locale === 'ar')
const isEdit  = computed(() => !!route.params.id)

const companies = ref([])
const loading   = ref(isEdit.value)
const saving    = ref(false)
const error     = ref('')

const form = ref({
  first_name: '', last_name: '', email: '', phone: '',
  company: '', address: '', notes: '', status: 'lead',
  lead_source: null, company_id: null,
})

// ── Validation ────────────────────────────────────────────
const rules = computed(() => ({
  first_name: { required: helpers.withMessage(isRtl.value ? 'الاسم مطلوب' : 'First name is required', required) },
  email: { email: helpers.withMessage(isRtl.value ? 'البريد غير صالح' : 'Invalid email', emailValidator) },
}))
const v$ = useVuelidate(rules, form)

// ── Load data ─────────────────────────────────────────────
async function fetchCompanies() {
  const { data } = await api.get('/companies/dropdown')
  companies.value = data.data || []
}

async function fetchContact() {
  const { data } = await api.get(`/contacts/${route.params.id}`)
  const c = data.data
  form.value = {
    first_name: c.first_name || '', last_name: c.last_name || '',
    email: c.email || '', phone: c.phone || '',
    company: c.company || '', address: c.address || '',
    notes: c.notes || '', status: c.status || 'lead',
    lead_source: c.lead_source || null, company_id: c.company_id || null,
  }
}

// ── Save ──────────────────────────────────────────────────
async function save() {
  const valid = await v$.value.$validate()
  if (!valid) return
  saving.value = true
  error.value  = ''
  try {
    if (isEdit.value) {
      await api.put(`/contacts/${route.params.id}`, form.value)
      toast.success(isRtl.value ? 'تم تحديث جهة الاتصال' : 'Contact updated')
    } else {
      await api.post('/contacts', form.value)
      toast.success(isRtl.value ? 'تمت إضافة جهة الاتصال' : 'Contact added')
    }
    router.push('/crm/contacts')
  } catch (err) {
    error.value = err.response?.data?.message || (isRtl.value ? 'حدث خطأ' : 'Something went wrong')
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await fetchCompanies()
  if (isEdit.value) {
    await fetchContact()
    loading.value = false
  }
})
</script>

<style scoped>
.fsect-title {
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .5px;
  color: var(--bs-secondary-color);
  padding-bottom: .5rem; margin-bottom: 1rem;
  border-bottom: 1px solid var(--bs-border-color);
  margin-top: .25rem;
}
</style>
