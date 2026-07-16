<template>
  <CrmLayout>
    <!-- Loading -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:400px">
      <div class="text-center">
        <div class="spinner-border text-primary mb-2" role="status"></div>
        <div class="text-muted small">{{ isRtl ? 'جاري التحميل...' : 'Loading...' }}</div>
      </div>
    </div>

    <template v-else-if="company">

      <!-- ─── Hero ──────────────────────────────────────────── -->
      <div class="company-hero card card-border mb-4">
        <div class="card-body">
          <div class="d-flex align-items-start gap-4 flex-wrap">

            <!-- Avatar -->
            <div class="company-avatar" :style="{ background: avatarBg }">
              {{ initials }}
            </div>

            <!-- Info -->
            <div class="flex-grow-1 min-w-0">
              <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h3 class="mb-0 fw-bold" style="font-size:1.4rem">{{ company.name }}</h3>
                <span class="company-status-badge" :class="`st-${company.status}`">{{ statusLabel(company.status) }}</span>
              </div>
              <div class="d-flex align-items-center gap-3 flex-wrap text-muted" style="font-size:13px">
                <span v-if="company.industry"><i class="bi bi-building me-1"></i>{{ company.industry }}</span>
                <a v-if="company.website" :href="company.website" target="_blank" class="text-primary text-decoration-none">
                  <i class="bi bi-globe2 me-1"></i>{{ cleanUrl(company.website) }}
                </a>
                <span v-if="company.email"><i class="bi bi-envelope me-1"></i>{{ company.email }}</span>
                <span v-if="company.phone"><i class="bi bi-telephone me-1"></i>{{ company.phone }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-2 flex-shrink-0">
              <button class="btn btn-sm btn-outline-primary" @click="openEdit">
                <i class="bi bi-pencil me-1"></i>{{ isRtl ? 'تعديل' : 'Edit' }}
              </button>
              <button class="btn btn-sm btn-outline-danger" @click="openDeleteModal">
                <i class="bi bi-trash3 me-1"></i>{{ isRtl ? 'حذف' : 'Delete' }}
              </button>
            </div>
          </div>

          <!-- Stats row -->
          <div class="hero-stats mt-4">
            <div class="hero-stat">
              <div class="hero-stat-val">{{ company.contacts_count ?? 0 }}</div>
              <div class="hero-stat-lbl">{{ isRtl ? 'جهات اتصال' : 'Contacts' }}</div>
            </div>
            <div class="hero-stat-sep"></div>
            <div class="hero-stat">
              <div class="hero-stat-val">{{ company.deals_count ?? 0 }}</div>
              <div class="hero-stat-lbl">{{ isRtl ? 'صفقات' : 'Deals' }}</div>
            </div>
            <div class="hero-stat-sep"></div>
            <div class="hero-stat">
              <div class="hero-stat-val">{{ company.employees_count ?? '—' }}</div>
              <div class="hero-stat-lbl">{{ isRtl ? 'موظفون' : 'Employees' }}</div>
            </div>
            <div class="hero-stat-sep"></div>
            <div class="hero-stat">
              <div class="hero-stat-val">${{ company.annual_revenue ? Number(company.annual_revenue).toLocaleString() : '—' }}</div>
              <div class="hero-stat-lbl">{{ isRtl ? 'الإيراد السنوي' : 'Annual Revenue' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── Body ──────────────────────────────────────────── -->
      <div class="row g-4">

        <!-- Left: Contacts + Deals -->
        <div class="col-lg-8">

          <!-- Contacts tab card -->
          <div class="card card-border mb-4">
            <div class="card-header card-header-action border-0 pb-0">
              <h6 class="mb-0 fw-bold">
                <i class="bi bi-people me-2 text-primary"></i>{{ isRtl ? 'جهات الاتصال' : 'Contacts' }}
                <span class="badge badge-soft-primary ms-1">{{ company.contacts?.length ?? 0 }}</span>
              </h6>
              <router-link :to="`/crm/contacts?company_id=${company.id}`" class="btn btn-xs btn-light">
                {{ isRtl ? 'عرض الكل' : 'View All' }}
              </router-link>
            </div>
            <div class="card-body pt-3">
              <div v-if="company.contacts?.length">
                <div v-for="c in company.contacts" :key="c.id" class="contact-row">
                  <div class="contact-avatar-sm" :style="{ background: strToColor(c.first_name) }">
                    {{ (c.first_name[0] || '') + (c.last_name[0] || '') }}
                  </div>
                  <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold" style="font-size:13px">
                      <router-link :to="`/crm/contacts/${c.id}`" class="text-reset text-decoration-none">
                        {{ c.first_name }} {{ c.last_name }}
                      </router-link>
                    </div>
                    <div class="text-muted" style="font-size:12px">{{ c.email }}</div>
                  </div>
                  <span class="contact-s-badge" :class="`cs-${c.status}`">{{ c.status }}</span>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <div style="font-size:2rem;opacity:.2">👤</div>
                <p class="text-muted small mb-0 mt-2">{{ isRtl ? 'لا توجد جهات اتصال مرتبطة' : 'No contacts linked yet' }}</p>
              </div>
            </div>
          </div>

          <!-- Deals card -->
          <div class="card card-border">
            <div class="card-header card-header-action border-0 pb-0">
              <h6 class="mb-0 fw-bold">
                <i class="bi bi-briefcase me-2 text-success"></i>{{ isRtl ? 'الصفقات' : 'Deals' }}
                <span class="badge badge-soft-success ms-1">{{ company.deals?.length ?? 0 }}</span>
              </h6>
              <router-link to="/crm/deals" class="btn btn-xs btn-light">
                {{ isRtl ? 'لوحة الصفقات' : 'Kanban Board' }}
              </router-link>
            </div>
            <div class="card-body pt-3">
              <div v-if="company.deals?.length">
                <div v-for="d in company.deals" :key="d.id" class="deal-row">
                  <div class="deal-stage-dot" v-if="d.stage" :style="{ background: d.stage.color || '#6b7280' }"></div>
                  <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold" style="font-size:13px">{{ d.title }}</div>
                    <div class="text-muted" style="font-size:12px">{{ d.stage?.name }}</div>
                  </div>
                  <div class="text-end">
                    <div class="fw-bold" style="font-size:13px">${{ Number(d.value||0).toLocaleString() }}</div>
                    <span class="deal-s-badge" :class="`ds-${d.status}`">{{ dealStatusLabel(d.status) }}</span>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <div style="font-size:2rem;opacity:.2">💼</div>
                <p class="text-muted small mb-0 mt-2">{{ isRtl ? 'لا توجد صفقات مرتبطة' : 'No deals linked yet' }}</p>
              </div>
            </div>
          </div>

        </div>

        <!-- Right: Details card -->
        <div class="col-lg-4">
          <div class="card card-border">
            <div class="card-header border-0 pb-0">
              <h6 class="mb-0 fw-bold">
                <i class="bi bi-info-circle me-2 text-muted"></i>{{ isRtl ? 'تفاصيل الشركة' : 'Company Details' }}
              </h6>
            </div>
            <div class="card-body pt-3">
              <div class="detail-list">
                <div class="detail-row" v-if="company.address">
                  <span class="detail-key"><i class="bi bi-geo-alt me-2"></i>{{ isRtl ? 'العنوان' : 'Address' }}</span>
                  <span class="detail-val">{{ company.address }}</span>
                </div>
                <div class="detail-row" v-if="company.employees_count">
                  <span class="detail-key"><i class="bi bi-people me-2"></i>{{ isRtl ? 'الموظفون' : 'Employees' }}</span>
                  <span class="detail-val">{{ company.employees_count }}</span>
                </div>
                <div class="detail-row" v-if="company.annual_revenue">
                  <span class="detail-key"><i class="bi bi-currency-dollar me-2"></i>{{ isRtl ? 'الإيراد السنوي' : 'Annual Revenue' }}</span>
                  <span class="detail-val">${{ Number(company.annual_revenue).toLocaleString() }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-key"><i class="bi bi-calendar3 me-2"></i>{{ isRtl ? 'تاريخ الإضافة' : 'Added On' }}</span>
                  <span class="detail-val">{{ formatDate(company.created_at) }}</span>
                </div>
              </div>

              <!-- Notes -->
              <div v-if="company.notes" class="mt-3 pt-3 border-top">
                <div class="text-muted" style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">
                  {{ isRtl ? 'ملاحظات' : 'Notes' }}
                </div>
                <p class="mb-0" style="font-size:13px;line-height:1.6">{{ company.notes }}</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </template>

    <!-- Not found -->
    <div v-else class="text-center py-5">
      <div style="font-size:3rem;opacity:.2">🏢</div>
      <h5 class="text-muted mt-3">{{ isRtl ? 'الشركة غير موجودة' : 'Company not found' }}</h5>
      <router-link to="/crm/companies" class="btn btn-sm btn-primary mt-2">
        {{ isRtl ? 'العودة للقائمة' : 'Back to Companies' }}
      </router-link>
    </div>

    <!-- ═══ DELETE MODAL ═══ -->
    <div class="modal fade" id="companyDeleteModal" tabindex="-1">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body text-center py-4 px-4">
            <div class="del-icon-wrap mb-3">
              <i class="bi bi-building fs-3 text-danger"></i>
            </div>
            <h6 class="mb-1">{{ isRtl ? 'حذف الشركة' : 'Delete Company' }}</h6>
            <p class="text-muted small mb-3">
              {{ isRtl
                ? `سيتم حذف "${company?.name}" وقطع ارتباطها بجهات الاتصال والصفقات.`
                : `Delete "${company?.name}"? Contacts and deals will be unlinked.` }}
            </p>
            <div class="d-flex gap-2 justify-content-center">
              <button class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ isRtl ? 'إلغاء' : 'Cancel' }}</button>
              <button class="btn btn-sm btn-danger px-3" :disabled="deleting" @click="doDelete">
                <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
                {{ isRtl ? 'حذف' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store   = useStore()
const route   = useRoute()
const router  = useRouter()
const toast   = useToast()
const isRtl   = computed(() => store.state.ui?.locale === 'ar')

const company = ref(null)
const loading = ref(true)
const deleting = ref(false)

// ── Avatar ────────────────────────────────────────────────
const COLORS = ['#2563EB','#7C3AED','#059669','#D97706','#0891B2','#DB2777','#6366F1']
const avatarBg = computed(() => {
  if (!company.value) return '#2563EB'
  const sum = company.value.name.split('').reduce((a, c) => a + c.charCodeAt(0), 0)
  return COLORS[sum % COLORS.length]
})
const initials = computed(() => {
  if (!company.value) return ''
  return company.value.name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
})
function strToColor(name = '') {
  const sum = name.split('').reduce((a, c) => a + c.charCodeAt(0), 0)
  return COLORS[sum % COLORS.length]
}

// ── Helpers ───────────────────────────────────────────────
function cleanUrl(url = '') { return url.replace(/^https?:\/\//, '').replace(/\/$/, '') }
function formatDate(d) { if (!d) return '—'; return new Date(d).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-GB', { year:'numeric', month:'short', day:'numeric' }) }

function statusLabel(s) {
  const m = { active: isRtl.value?'نشطة':'Active', inactive: isRtl.value?'غير نشطة':'Inactive', prospect: isRtl.value?'محتملة':'Prospect' }
  return m[s] || s
}
function dealStatusLabel(s) {
  const m = { open: isRtl.value?'مفتوحة':'Open', won: isRtl.value?'رابحة':'Won', lost: isRtl.value?'خاسرة':'Lost' }
  return m[s] || s
}

// ── Fetch ─────────────────────────────────────────────────
async function fetchCompany() {
  loading.value = true
  try {
    const { data } = await api.get(`/companies/${route.params.id}`)
    company.value = data.data
  } catch {
    company.value = null
  } finally {
    loading.value = false
  }
}

// ── Actions ───────────────────────────────────────────────
function openEdit() {
  router.push({ path: '/crm/companies', query: { edit: route.params.id } })
}

function openDeleteModal() {
  nextTick(() => window.bootstrap?.Modal.getOrCreateInstance(document.getElementById('companyDeleteModal'))?.show())
}

async function doDelete() {
  deleting.value = true
  try {
    await api.delete(`/companies/${company.value.id}`)
    toast.success(isRtl.value ? 'تم حذف الشركة' : 'Company deleted')
    window.bootstrap?.Modal.getOrCreateInstance(document.getElementById('companyDeleteModal'))?.hide()
    router.push('/crm/companies')
  } catch (err) {
    toast.error(err.response?.data?.message || (isRtl.value ? 'حدث خطأ' : 'Something went wrong'))
  } finally {
    deleting.value = false
  }
}

onMounted(fetchCompany)
</script>

<style scoped>
/* ─── Hero ───────────────────────────────────────── */
.company-hero { overflow: hidden; }

.company-avatar {
  width: 72px; height: 72px;
  border-radius: 18px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.6rem; font-weight: 800;
  flex-shrink: 0;
  letter-spacing: -1px;
}

.company-status-badge {
  font-size: 11px; font-weight: 600;
  padding: 3px 10px; border-radius: 20px;
}
.st-active   { background: rgba(16,185,129,.12); color: #059669; }
.st-inactive { background: rgba(100,116,139,.12); color: #64748B; }
.st-prospect { background: rgba(37,99,235,.12);   color: #2563EB; }

.hero-stats {
  display: flex; align-items: center; gap: 0;
  border-top: 1px solid var(--bs-border-color-translucent, rgba(0,0,0,.06));
  padding-top: 1rem; flex-wrap: wrap;
}
.hero-stat { text-align: center; padding: 0 1.5rem; }
.hero-stat:first-child { padding-inline-start: 0; }
.hero-stat-val { font-size: 1.4rem; font-weight: 800; line-height: 1; color: var(--bs-body-color); }
.hero-stat-lbl { font-size: 11px; color: var(--bs-secondary-color); margin-top: 3px; }
.hero-stat-sep { width: 1px; height: 36px; background: var(--bs-border-color-translucent, rgba(0,0,0,.08)); flex-shrink: 0; }

/* ─── Contacts ───────────────────────────────────── */
.contact-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid var(--bs-border-color-translucent, rgba(0,0,0,.05));
}
.contact-row:last-child { border-bottom: none; }

.contact-avatar-sm {
  width: 34px; height: 34px; border-radius: 50%;
  color: #fff; font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; text-transform: uppercase;
}

.contact-s-badge {
  font-size: 10px; font-weight: 600;
  padding: 2px 8px; border-radius: 12px;
  white-space: nowrap;
}
.cs-lead     { background: rgba(124,58,237,.12); color: #7C3AED; }
.cs-customer { background: rgba(16,185,129,.12); color: #059669; }
.cs-inactive { background: rgba(100,116,139,.12); color: #64748B; }

/* ─── Deals ──────────────────────────────────────── */
.deal-row {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid var(--bs-border-color-translucent, rgba(0,0,0,.05));
}
.deal-row:last-child { border-bottom: none; }
.deal-stage-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

.deal-s-badge {
  font-size: 10px; font-weight: 600;
  padding: 2px 8px; border-radius: 12px;
}
.ds-open { background: rgba(37,99,235,.12); color: #2563EB; }
.ds-won  { background: rgba(16,185,129,.12); color: #059669; }
.ds-lost { background: rgba(239,68,68,.12);  color: #EF4444; }

/* ─── Detail list ────────────────────────────────── */
.detail-list { display: flex; flex-direction: column; gap: 10px; }
.detail-row  { display: flex; flex-direction: column; gap: 2px; }
.detail-key  { font-size: 11px; color: var(--bs-secondary-color); }
.detail-val  { font-size: 13px; font-weight: 500; color: var(--bs-body-color); }

/* ─── Delete icon ────────────────────────────────── */
.del-icon-wrap {
  width: 56px; height: 56px; border-radius: 50%;
  background: #FEF2F2;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto;
}

/* ─── Badges ─────────────────────────────────────── */
.badge-soft-primary { background: rgba(37,99,235,.12); color: #1D4ED8; }
.badge-soft-success { background: rgba(16,185,129,.12); color: #059669; }
.btn-xs { font-size: 12px; padding: 3px 10px; }
</style>
