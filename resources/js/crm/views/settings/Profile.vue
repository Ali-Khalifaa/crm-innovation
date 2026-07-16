<template>
  <CrmLayout>
    <div class="profile-page">

      <!-- Header -->
      <div class="page-header mb-4">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">
            {{ isRtl ? 'ملفي الشخصي' : 'My Profile' }}
          </h4>
          <p class="text-muted small mt-1 mb-0">
            {{ isRtl ? 'إدارة معلوماتك الشخصية وكلمة المرور' : 'Manage your personal information and password' }}
          </p>
        </div>
      </div>

      <div class="row g-4">

        <!-- Profile Info -->
        <div class="col-md-7">
          <div class="card card-border">
            <div class="card-header card-header-action">
              <h6 class="mb-0">{{ isRtl ? 'المعلومات الشخصية' : 'Personal Information' }}</h6>
            </div>
            <div class="card-body">
              <!-- Avatar + name display -->
              <div class="profile-avatar-row mb-4">
                <div class="profile-avatar" :style="{ background: avatarColor }">
                  {{ avatarInitials }}
                </div>
                <div>
                  <div class="fw-bold fs-6">{{ profileForm.name || currentUser?.name }}</div>
                  <div class="text-muted small">{{ currentUser?.email }}</div>
                  <div v-if="currentUser?.role" class="badge badge-soft-primary mt-1">{{ currentUser.role }}</div>
                </div>
              </div>

              <form @submit.prevent="saveProfile">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label fw-medium">
                      {{ isRtl ? 'الاسم الكامل' : 'Full Name' }} <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="profileForm.name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': pv$.name.$error }"
                      :placeholder="isRtl ? 'مثال: محمد أحمد' : 'e.g. John Smith'"
                      @blur="pv$.name.$touch()"
                    />
                    <div class="invalid-feedback" v-if="pv$.name.$error">
                      {{ pv$.name.$errors[0]?.$message }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-medium">{{ isRtl ? 'رقم الهاتف' : 'Phone' }}</label>
                    <input
                      v-model="profileForm.phone"
                      type="text"
                      class="form-control"
                      :placeholder="isRtl ? '+966 5X XXX XXXX' : '+1 (555) 000-0000'"
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-medium">{{ isRtl ? 'المسمى الوظيفي' : 'Job Title' }}</label>
                    <input
                      v-model="profileForm.title"
                      type="text"
                      class="form-control"
                      :placeholder="isRtl ? 'مثال: مدير مبيعات' : 'e.g. Sales Manager'"
                    />
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-medium">{{ isRtl ? 'البريد الإلكتروني' : 'Email' }}</label>
                    <input
                      :value="currentUser?.email"
                      type="email"
                      class="form-control bg-body-secondary"
                      disabled
                    />
                    <div class="form-text">{{ isRtl ? 'لا يمكن تغيير البريد الإلكتروني.' : 'Email cannot be changed.' }}</div>
                  </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                  <button type="submit" class="btn btn-primary px-4" :disabled="savingProfile">
                    <span v-if="savingProfile" class="spinner-border spinner-border-sm me-2"></span>
                    {{ savingProfile ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : (isRtl ? 'حفظ التغييرات' : 'Save Changes') }}
                  </button>
                  <button type="button" class="btn btn-light" @click="resetProfile">
                    {{ isRtl ? 'إلغاء' : 'Cancel' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-5">
          <div class="card card-border">
            <div class="card-header card-header-action">
              <h6 class="mb-0">{{ isRtl ? 'تغيير كلمة المرور' : 'Change Password' }}</h6>
            </div>
            <div class="card-body">
              <form @submit.prevent="savePassword">
                <div class="d-flex flex-column gap-3">
                  <div>
                    <label class="form-label fw-medium">
                      {{ isRtl ? 'كلمة المرور الحالية' : 'Current Password' }} <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <input
                        v-model="pwForm.current_password"
                        :type="showCurrent ? 'text' : 'password'"
                        class="form-control"
                        :class="{ 'is-invalid': pwv$.current_password.$error }"
                        :placeholder="isRtl ? 'كلمة المرور الحالية' : 'Enter current password'"
                        @blur="pwv$.current_password.$touch()"
                      />
                      <button type="button" class="btn btn-outline-secondary" @click="showCurrent = !showCurrent">
                        <i class="bi" :class="showCurrent ? 'bi-eye-slash' : 'bi-eye'"></i>
                      </button>
                      <div class="invalid-feedback" v-if="pwv$.current_password.$error">
                        {{ pwv$.current_password.$errors[0]?.$message }}
                      </div>
                    </div>
                  </div>
                  <div>
                    <label class="form-label fw-medium">
                      {{ isRtl ? 'كلمة المرور الجديدة' : 'New Password' }} <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <input
                        v-model="pwForm.password"
                        :type="showNew ? 'text' : 'password'"
                        class="form-control"
                        :class="{ 'is-invalid': pwv$.password.$error }"
                        :placeholder="isRtl ? '8 أحرف على الأقل' : 'Min 8 characters'"
                        @blur="pwv$.password.$touch()"
                      />
                      <button type="button" class="btn btn-outline-secondary" @click="showNew = !showNew">
                        <i class="bi" :class="showNew ? 'bi-eye-slash' : 'bi-eye'"></i>
                      </button>
                      <div class="invalid-feedback" v-if="pwv$.password.$error">
                        {{ pwv$.password.$errors[0]?.$message }}
                      </div>
                    </div>
                    <!-- Password strength -->
                    <div v-if="pwForm.password" class="mt-2">
                      <div class="pw-strength-bar">
                        <div class="pw-strength-fill" :class="'pw-' + pwStrength.level" :style="{ width: pwStrength.pct + '%' }"></div>
                      </div>
                      <div class="form-text mt-1" :class="'text-' + (pwStrength.level === 'strong' ? 'success' : pwStrength.level === 'medium' ? 'warning' : 'danger')">
                        {{ isRtl ? pwStrength.labelAr : pwStrength.labelEn }}
                      </div>
                    </div>
                  </div>
                  <div>
                    <label class="form-label fw-medium">
                      {{ isRtl ? 'تأكيد كلمة المرور الجديدة' : 'Confirm New Password' }} <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <input
                        v-model="pwForm.password_confirmation"
                        :type="showConfirm ? 'text' : 'password'"
                        class="form-control"
                        :class="{ 'is-invalid': pwv$.password_confirmation.$error }"
                        :placeholder="isRtl ? 'كرّر كلمة المرور الجديدة' : 'Repeat new password'"
                        @blur="pwv$.password_confirmation.$touch()"
                      />
                      <button type="button" class="btn btn-outline-secondary" @click="showConfirm = !showConfirm">
                        <i class="bi" :class="showConfirm ? 'bi-eye-slash' : 'bi-eye'"></i>
                      </button>
                      <div class="invalid-feedback" v-if="pwv$.password_confirmation.$error">
                        {{ pwv$.password_confirmation.$errors[0]?.$message }}
                      </div>
                    </div>
                  </div>
                  <div v-if="pwError" class="alert alert-danger py-2 small mb-0">{{ pwError }}</div>
                  <button type="submit" class="btn btn-primary" :disabled="savingPw">
                    <span v-if="savingPw" class="spinner-border spinner-border-sm me-2"></span>
                    {{ savingPw ? (isRtl ? 'جاري التغيير...' : 'Updating...') : (isRtl ? 'تغيير كلمة المرور' : 'Update Password') }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Account Info Card -->
          <div class="card card-border mt-4">
            <div class="card-body">
              <h6 class="fw-semibold mb-3">{{ isRtl ? 'معلومات الحساب' : 'Account Info' }}</h6>
              <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted small">{{ isRtl ? 'الدور' : 'Role' }}</span>
                  <span class="badge badge-soft-primary text-capitalize">{{ currentUser?.role }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted small">{{ isRtl ? 'الحالة' : 'Status' }}</span>
                  <span class="badge" :class="currentUser?.status === 'active' ? 'badge-soft-success' : 'badge-soft-secondary'">
                    {{ currentUser?.status === 'active' ? (isRtl ? 'نشط' : 'Active') : (isRtl ? 'غير نشط' : 'Inactive') }}
                  </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted small">{{ isRtl ? 'تاريخ الانضمام' : 'Joined' }}</span>
                  <span class="small fw-medium">{{ formatDate(currentUser?.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, sameAs, helpers } from '@vuelidate/validators'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store = useStore()
const toast = useToast()

const isRtl       = computed(() => store.state.ui?.locale === 'ar')
const currentUser = computed(() => store.state.auth?.user)

const savingProfile = ref(false)
const savingPw      = ref(false)
const pwError       = ref('')
const showCurrent   = ref(false)
const showNew       = ref(false)
const showConfirm   = ref(false)

const profileForm = ref({ name: '', phone: '', title: '' })
const pwForm      = ref({ current_password: '', password: '', password_confirmation: '' })

// ─── Avatar ──────────────────────────────────────────────
const COLORS = ['#2563EB','#7C3AED','#DB2777','#D97706','#059669','#DC2626','#0891B2']
const avatarColor = computed(() => {
  const name = profileForm.value.name || currentUser.value?.name || ''
  let h = 0
  for (let i = 0; i < name.length; i++) h += name.charCodeAt(i)
  return COLORS[h % COLORS.length]
})
const avatarInitials = computed(() => {
  const name = (profileForm.value.name || currentUser.value?.name || '?').trim()
  const p = name.split(' ')
  return (p.length >= 2 ? p[0][0] + p[1][0] : p[0].slice(0, 2)).toUpperCase()
})

// ─── Password strength ────────────────────────────────────
const pwStrength = computed(() => {
  const p = pwForm.value.password
  if (!p) return { level: 'weak', pct: 0, labelEn: '', labelAr: '' }
  let score = 0
  if (p.length >= 8)  score++
  if (p.length >= 12) score++
  if (/[A-Z]/.test(p)) score++
  if (/[0-9]/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++
  if (score <= 1) return { level: 'weak',   pct: 30,  labelEn: 'Weak password',   labelAr: 'كلمة مرور ضعيفة' }
  if (score <= 3) return { level: 'medium', pct: 60,  labelEn: 'Medium strength',  labelAr: 'قوة متوسطة' }
  return              { level: 'strong', pct: 100, labelEn: 'Strong password',  labelAr: 'كلمة مرور قوية' }
})

// ─── Validation: Profile ─────────────────────────────────
const profileRules = computed(() => ({
  name: {
    required: helpers.withMessage(isRtl.value ? 'الاسم مطلوب' : 'Name is required', required),
    minLength: helpers.withMessage(isRtl.value ? 'على الأقل حرفان' : 'At least 2 characters', minLength(2)),
  },
}))
const pv$ = useVuelidate(profileRules, profileForm)

// ─── Validation: Password ────────────────────────────────
const pwRules = computed(() => ({
  current_password: {
    required: helpers.withMessage(isRtl.value ? 'كلمة المرور الحالية مطلوبة' : 'Current password is required', required),
  },
  password: {
    required:  helpers.withMessage(isRtl.value ? 'كلمة المرور الجديدة مطلوبة' : 'New password is required', required),
    minLength: helpers.withMessage(isRtl.value ? '8 أحرف على الأقل' : 'At least 8 characters', minLength(8)),
  },
  password_confirmation: {
    required: helpers.withMessage(isRtl.value ? 'التأكيد مطلوب' : 'Confirmation is required', required),
    sameAs:   helpers.withMessage(isRtl.value ? 'كلمتا المرور غير متطابقتين' : 'Passwords do not match', sameAs(computed(() => pwForm.value.password))),
  },
}))
const pwv$ = useVuelidate(pwRules, pwForm)

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}

function resetProfile() {
  profileForm.value = {
    name:  currentUser.value?.name  || '',
    phone: currentUser.value?.phone || '',
    title: currentUser.value?.title || '',
  }
  pv$.value.$reset()
}

async function saveProfile() {
  const valid = await pv$.value.$validate()
  if (!valid) return
  savingProfile.value = true
  try {
    const { data } = await api.put('/settings/profile', profileForm.value)
    store.commit('auth/SET_USER', data.data)
    toast.success(isRtl.value ? 'تم تحديث الملف الشخصي بنجاح' : 'Profile updated successfully')
    pv$.value.$reset()
  } catch (err) {
    toast.error(err.response?.data?.message || (isRtl.value ? 'حدث خطأ' : 'An error occurred'))
  } finally {
    savingProfile.value = false
  }
}

async function savePassword() {
  const valid = await pwv$.value.$validate()
  if (!valid) return
  savingPw.value = true
  pwError.value  = ''
  try {
    await api.put('/settings/password', pwForm.value)
    toast.success(isRtl.value ? 'تم تغيير كلمة المرور بنجاح' : 'Password updated successfully')
    pwForm.value = { current_password: '', password: '', password_confirmation: '' }
    pwv$.value.$reset()
  } catch (err) {
    pwError.value = err.response?.data?.message || (isRtl.value ? 'حدث خطأ' : 'An error occurred')
  } finally {
    savingPw.value = false
  }
}

onMounted(() => {
  resetProfile()
  // Fetch fresh profile from server
  api.get('/settings/profile').then(({ data }) => {
    store.commit('auth/SET_USER', data.data)
    resetProfile()
  }).catch(() => {})
})
</script>

<style scoped>
.profile-page { max-width: 1000px; }

/* ─── Avatar row ─────────────────────────────────────────── */
.profile-avatar-row {
  display: flex;
  align-items: center;
  gap: 16px;
}
.profile-avatar {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 22px;
  font-weight: 700;
  flex-shrink: 0;
  letter-spacing: -0.5px;
}

/* ─── Password strength ──────────────────────────────────── */
.pw-strength-bar {
  height: 4px;
  background: var(--bs-border-color, #E2E8F0);
  border-radius: 2px;
  overflow: hidden;
}
.pw-strength-fill {
  height: 100%;
  border-radius: 2px;
  transition: width 0.3s ease;
}
.pw-weak   { background: #EF4444; }
.pw-medium { background: #F59E0B; }
.pw-strong { background: #10B981; }

/* ─── Badges ─────────────────────────────────────────────── */
.badge-soft-primary  { background: rgba(37,99,235,.12);  color: #1D4ED8; }
.badge-soft-success  { background: rgba(16,185,129,.12); color: #047857; }
.badge-soft-secondary{ background: rgba(100,116,139,.12);color: #4B5563; }
</style>
