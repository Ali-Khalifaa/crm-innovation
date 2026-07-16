<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale==='ar' ? 'إعدادات الشركة' : 'Company Settings' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale==='ar' ? 'إدارة بيانات شركتك والاشتراك الحالي' : 'Manage your company profile and subscription' }}</p>
      </div>
    </div>

    <div class="row g-4 align-items-start">

      <!-- ═══ Left: Company Info ═══ -->
      <div class="col-lg-6">
        <div class="card card-border">
          <div class="card-header card-header-action border-0 pb-0">
            <div>
              <h6 class="mb-0 fw-bold">{{ locale==='ar' ? 'بيانات الشركة' : 'Company Profile' }}</h6>
              <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'معلومات الشركة الأساسية' : 'Basic company information' }}</p>
            </div>
            <button v-if="!editing" @click="startEdit" class="btn btn-sm btn-outline-primary flex-shrink-0">
              <i class="bi bi-pencil me-1"></i>{{ locale==='ar' ? 'تعديل' : 'Edit' }}
            </button>
          </div>

          <div class="card-body pt-3">

            <!-- View Mode -->
            <div v-if="!editing">
              <!-- Company Avatar -->
              <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:1px solid var(--bs-border-color)">
                <div class="company-avatar">{{ tenant?.name?.charAt(0)?.toUpperCase() || '?' }}</div>
                <div>
                  <div class="fw-bold fs-6">{{ tenant?.name }}</div>
                  <div class="text-muted small">{{ locale==='ar' ? 'مالك الحساب' : 'Account Owner' }}</div>
                </div>
              </div>

              <div class="info-list">
                <div class="info-item">
                  <div class="info-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-building"></i></div>
                  <div>
                    <div class="info-key">{{ locale==='ar' ? 'اسم الشركة' : 'Company Name' }}</div>
                    <div class="info-val">{{ tenant?.name || '—' }}</div>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon" style="background:rgba(16,185,129,.1);color:#059669"><i class="bi bi-envelope"></i></div>
                  <div>
                    <div class="info-key">{{ locale==='ar' ? 'البريد الإلكتروني' : 'Email' }}</div>
                    <div class="info-val">{{ tenant?.email || '—' }}</div>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon" style="background:rgba(124,58,237,.1);color:#7C3AED"><i class="bi bi-telephone"></i></div>
                  <div>
                    <div class="info-key">{{ locale==='ar' ? 'رقم الهاتف' : 'Phone' }}</div>
                    <div class="info-val">{{ tenant?.phone || '—' }}</div>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon" style="background:rgba(245,158,11,.1);color:#D97706"><i class="bi bi-calendar3"></i></div>
                  <div>
                    <div class="info-key">{{ locale==='ar' ? 'دورة الفوترة' : 'Billing Cycle' }}</div>
                    <div class="info-val">{{ billingLabel(tenant?.billing_cycle) }}</div>
                  </div>
                </div>
                <div class="info-item" v-if="tenant?.trial_ends_at">
                  <div class="info-icon" style="background:rgba(239,68,68,.1);color:#DC2626"><i class="bi bi-hourglass-split"></i></div>
                  <div>
                    <div class="info-key">{{ locale==='ar' ? 'انتهاء التجربة' : 'Trial Ends' }}</div>
                    <div class="info-val">{{ formatDate(tenant.trial_ends_at) }}</div>
                  </div>
                </div>
                <div class="info-item">
                  <div class="info-icon" style="background:rgba(16,185,129,.1);color:#059669"><i class="bi bi-shield-check"></i></div>
                  <div>
                    <div class="info-key">{{ locale==='ar' ? 'حالة الحساب' : 'Account Status' }}</div>
                    <span class="status-pill" :class="`s-${tenant?.status}`">{{ statusLabel(tenant?.status) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Edit Mode -->
            <div v-else>
              <form @submit.prevent="saveChanges">
                <div class="mb-3">
                  <label class="form-label fw-semibold">{{ locale==='ar' ? 'اسم الشركة' : 'Company Name' }} <span class="text-danger">*</span></label>
                  <input v-model="form.name" class="form-control" required :placeholder="locale==='ar'?'اسم شركتك':'Your company name'" />
                </div>
                <div class="mb-4">
                  <label class="form-label fw-semibold">{{ locale==='ar' ? 'رقم الهاتف' : 'Phone' }}</label>
                  <input v-model="form.phone" class="form-control" placeholder="+1 555 000 0000" />
                </div>
                <div v-if="saveError" class="alert alert-danger py-2 px-3 small mb-3">{{ saveError }}</div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary" :disabled="saving">
                    <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                    {{ saving ? (locale==='ar'?'جاري الحفظ...':'Saving...') : (locale==='ar'?'حفظ التغييرات':'Save Changes') }}
                  </button>
                  <button type="button" class="btn btn-light" @click="editing=false">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══ Right: Plan & Subscription ═══ -->
      <div class="col-lg-6">
        <div class="card card-border">
          <div class="card-header card-header-action border-0 pb-0">
            <div>
              <h6 class="mb-0 fw-bold">{{ locale==='ar' ? 'الاشتراك الحالي' : 'Current Plan' }}</h6>
              <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'تفاصيل خطتك وميزاتها' : 'Your plan details and features' }}</p>
            </div>
            <button @click="showUpgrade=true" class="btn btn-sm btn-primary flex-shrink-0">
              <i class="bi bi-arrow-up-circle me-1"></i>{{ locale==='ar' ? 'ترقية' : 'Upgrade' }}
            </button>
          </div>

          <div class="card-body pt-3">
            <!-- Plan Hero -->
            <div class="plan-hero mb-4">
              <div class="plan-badge">
                <i class="bi bi-lightning-charge-fill me-1"></i>
                {{ plan?.name || '—' }}
              </div>
              <div class="plan-price">
                <span class="plan-price-val">${{ plan?.monthly_price ?? '—' }}</span>
                <span class="plan-price-per">{{ locale==='ar'?'/ شهر':'/ month' }}</span>
              </div>
              <span class="status-pill" :class="`s-${tenant?.status}`">{{ statusLabel(tenant?.status) }}</span>
            </div>

            <!-- Limits -->
            <div class="limits-row mb-3">
              <div class="limit-box">
                <div class="limit-icon"><i class="bi bi-people"></i></div>
                <div class="limit-val">{{ plan?.max_users === 0 ? '∞' : plan?.max_users }}</div>
                <div class="limit-label">{{ locale==='ar'?'مستخدم':'Users' }}</div>
              </div>
              <div class="limit-divider"></div>
              <div class="limit-box">
                <div class="limit-icon"><i class="bi bi-person-lines-fill"></i></div>
                <div class="limit-val">{{ plan?.max_contacts === 0 ? '∞' : (plan?.max_contacts ? Number(plan.max_contacts).toLocaleString() : '—') }}</div>
                <div class="limit-label">{{ locale==='ar'?'جهة اتصال':'Contacts' }}</div>
              </div>
              <div class="limit-divider"></div>
              <div class="limit-box">
                <div class="limit-icon"><i class="bi bi-star"></i></div>
                <div class="limit-val">{{ plan?.features?.length || 0 }}</div>
                <div class="limit-label">{{ locale==='ar'?'ميزة':'Features' }}</div>
              </div>
            </div>

            <!-- Features -->
            <div class="fsect-mini mb-2">{{ locale==='ar'?'الميزات المتضمنة':'Included Features' }}</div>
            <div class="features-list">
              <div v-for="f in (plan?.features || [])" :key="f" class="feature-item">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <span>{{ featureLabel(f) }}</span>
              </div>
              <div v-if="!plan?.features?.length" class="text-muted small">{{ locale==='ar'?'لا توجد ميزات':'No features listed' }}</div>
            </div>

            <!-- Plan dates -->
            <div class="plan-dates" v-if="tenant?.plan_ends_at">
              <i class="bi bi-calendar-event text-muted me-2"></i>
              <span class="text-muted small">{{ locale==='ar'?'ينتهي في':'Expires' }} {{ formatDate(tenant.plan_ends_at) }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <UpgradeModal :show="showUpgrade" @close="showUpgrade=false" />
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import UpgradeModal from '../../components/UpgradeModal.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store   = useStore()
const toast   = useToast()
const locale  = computed(() => store.state.ui.locale)

const editing   = ref(false)
const saving    = ref(false)
const saveError = ref('')
const showUpgrade = ref(false)

const tenant = computed(() => store.getters['auth/tenant'])
const plan   = computed(() => store.getters['auth/plan'])
const form   = ref({ name:'', phone:'' })

function startEdit() {
  form.value = { name: tenant.value?.name || '', phone: tenant.value?.phone || '' }
  editing.value = true
  saveError.value = ''
}

async function saveChanges() {
  saving.value = true; saveError.value = ''
  try {
    await api.put('/settings/company', form.value)
    store.commit('auth/SET_TENANT', { ...tenant.value, name: form.value.name, phone: form.value.phone })
    editing.value = false
    toast.success(locale.value==='ar' ? 'تم حفظ إعدادات الشركة' : 'Company settings saved')
  } catch (err) {
    saveError.value = err.response?.data?.message || (locale.value==='ar' ? 'فشل الحفظ' : 'Failed to save')
    toast.error(saveError.value)
  } finally { saving.value = false }
}

function formatDate(d) {
  if (!d) return '—'
  const dt = new Date(d)
  if (isNaN(dt)) return '—'
  return `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()}`
}

function statusLabel(s) {
  const map = { active: locale.value==='ar'?'نشط':'Active', trial: locale.value==='ar'?'تجريبي':'Trial', suspended: locale.value==='ar'?'معلّق':'Suspended', cancelled: locale.value==='ar'?'ملغى':'Cancelled' }
  return map[s] || s
}
function billingLabel(b) {
  const map = { monthly: locale.value==='ar'?'شهري':'Monthly', yearly: locale.value==='ar'?'سنوي':'Yearly', custom: locale.value==='ar'?'مخصص':'Custom' }
  return map[b] || b || '—'
}
function featureLabel(f) {
  const map = { contacts: locale.value==='ar'?'جهات الاتصال':'Contacts', deals: locale.value==='ar'?'الصفقات':'Deals', tasks: locale.value==='ar'?'المهام':'Tasks', invoices: locale.value==='ar'?'الفواتير':'Invoices', reports: locale.value==='ar'?'التقارير':'Reports', priority_support: locale.value==='ar'?'دعم أولوية':'Priority Support' }
  return map[f] || f.replace(/_/g,' ')
}

onMounted(() => store.dispatch('auth/fetchMe').catch(() => {}))
</script>

<style scoped>
/* ─── Company Avatar ─────────────────────────── */
.company-avatar { width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;flex-shrink:0; }

/* ─── Info list ──────────────────────────────── */
.info-list { display:flex;flex-direction:column;gap:.5rem; }
.info-item { display:flex;align-items:center;gap:.875rem;padding:.6rem .75rem;border-radius:10px;background:var(--bs-tertiary-bg,#F8FAFC);transition:background .15s; }
.info-item:hover { background:var(--bs-border-color); }
.info-icon { width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px; }
.info-key  { font-size:11px;font-weight:600;color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.3px; }
.info-val  { font-size:13px;font-weight:600;color:var(--bs-body-color);margin-top:1px; }

/* ─── Status pill ────────────────────────────── */
.status-pill { display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.3px; }
.s-active    { background:rgba(16,185,129,.12);color:#059669; }
.s-trial     { background:rgba(245,158,11,.12);color:#D97706; }
.s-suspended { background:rgba(239,68,68,.12);color:#DC2626; }
.s-cancelled { background:rgba(100,116,139,.12);color:#64748B; }

/* ─── Plan Hero ──────────────────────────────── */
.plan-hero { text-align:center;padding:1.25rem;background:var(--bs-tertiary-bg,#F8FAFC);border-radius:12px; }
.plan-badge { display:inline-flex;align-items:center;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;background:rgba(37,99,235,.1);color:#2563EB;padding:4px 14px;border-radius:20px;margin-bottom:.75rem; }
.plan-price { margin-bottom:.5rem; }
.plan-price-val { font-size:2.5rem;font-weight:900;color:var(--bs-body-color);line-height:1; }
.plan-price-per { font-size:13px;color:var(--bs-secondary-color);margin-inline-start:.25rem; }

/* ─── Limits row ─────────────────────────────── */
.limits-row { display:flex;align-items:center;background:var(--bs-tertiary-bg,#F8FAFC);border-radius:12px;overflow:hidden; }
.limit-box  { flex:1;text-align:center;padding:.875rem .5rem; }
.limit-icon { font-size:18px;color:#2563EB;margin-bottom:.25rem; }
.limit-val  { font-size:1.4rem;font-weight:800;color:var(--bs-body-color);line-height:1; }
.limit-label{ font-size:11px;color:var(--bs-secondary-color);margin-top:2px; }
.limit-divider { width:1px;height:50px;background:var(--bs-border-color);flex-shrink:0; }

/* ─── Features ───────────────────────────────── */
.fsect-mini  { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-secondary-color); }
.features-list { display:flex;flex-direction:column;gap:.3rem;margin-bottom:.875rem; }
.feature-item  { display:flex;align-items:center;font-size:13px;color:var(--bs-body-color);padding:.25rem 0; }

/* ─── Plan dates ─────────────────────────────── */
.plan-dates { display:flex;align-items:center;padding:.5rem .75rem;background:var(--bs-tertiary-bg,#F8FAFC);border-radius:8px; }
</style>
