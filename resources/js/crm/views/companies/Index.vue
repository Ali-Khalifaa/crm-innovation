<template>
  <CrmLayout>
    <UpgradeModal :show="showUpgrade" @close="showUpgrade = false" />

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale === 'ar' ? 'الشركات' : 'Companies' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale === 'ar' ? 'إدارة شركاتك وعملائك المؤسسيين' : 'Manage your companies and business clients' }}</p>
      </div>
      <div class="d-flex gap-2 flex-shrink-0">
        <button class="btn btn-sm btn-primary" @click="openAddModal">
          <i class="bi bi-plus-lg me-1"></i>{{ locale === 'ar' ? 'شركة جديدة' : 'New Company' }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" @click="clearFilter">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-building fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.total }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'إجمالي الشركات' : 'Total Companies' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': statusFilter==='prospect'}" @click="filterByStatus('prospect')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#D97706"><i class="bi bi-binoculars fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.prospect }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'عملاء محتملون' : 'Prospects' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': statusFilter==='customer'}" @click="filterByStatus('customer')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#059669"><i class="bi bi-building-check fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.customer }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'عملاء' : 'Customers' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': statusFilter==='churned'}" @click="filterByStatus('churned')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(239,68,68,.1);color:#DC2626"><i class="bi bi-building-x fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.churned }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'مغادرون' : 'Churned' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search + Table -->
    <div class="card card-border">
      <div class="card-header d-flex gap-2 flex-wrap">
        <div class="input-group input-group-sm" style="max-width:280px">
          <span class="input-group-text bg-transparent"><i class="bi bi-search text-muted"></i></span>
          <input
            type="text"
            class="form-control"
            :placeholder="locale === 'ar' ? 'بحث في الشركات...' : 'Search companies...'"
            v-model="search"
            @input="debouncedFetch"
          />
        </div>
        <button v-if="statusFilter" class="btn btn-sm btn-outline-secondary" @click="clearFilter">
          <i class="bi bi-x me-1"></i>{{ locale === 'ar' ? 'إلغاء الفلتر' : 'Clear filter' }}
        </button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-4">{{ locale === 'ar' ? 'الشركة' : 'Company' }}</th>
              <th>{{ locale === 'ar' ? 'القطاع' : 'Industry' }}</th>
              <th>{{ locale === 'ar' ? 'جهات الاتصال' : 'Contacts' }}</th>
              <th>{{ locale === 'ar' ? 'الصفقات' : 'Deals' }}</th>
              <th>{{ locale === 'ar' ? 'الحالة' : 'Status' }}</th>
              <th class="text-end pe-4">{{ locale === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm me-2"></div>
                {{ locale === 'ar' ? 'جاري التحميل...' : 'Loading...' }}
              </td>
            </tr>
            <tr v-else-if="!companies.length">
              <td colspan="6" class="text-center py-5">
                <div class="text-muted">
                  <i class="bi bi-building fs-1 d-block mb-2 opacity-25"></i>
                  <p class="mb-1 fw-medium">{{ locale === 'ar' ? 'لا توجد شركات بعد' : 'No companies yet' }}</p>
                  <p class="small opacity-75">{{ locale === 'ar' ? 'أضف شركتك الأولى للبدء' : 'Add your first company to get started' }}</p>
                </div>
              </td>
            </tr>
            <tr v-for="company in companies" :key="company.id">
              <td class="ps-4">
                <div class="d-flex align-items-center gap-3">
                  <div
                    class="company-avatar flex-shrink-0"
                    :style="`background:${avatarColor(company.name)}`"
                  >
                    {{ (company.name || '?').charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="fw-medium">
                      <router-link :to="`/crm/companies/${company.id}`" class="text-reset text-decoration-none" style="transition:color .1s" @mouseenter="e=>e.target.style.color='#2563EB'" @mouseleave="e=>e.target.style.color=''">
                        {{ company.name }}
                      </router-link>
                    </div>
                    <div class="text-muted small" v-if="company.website">
                      <a :href="company.website" target="_blank" class="text-muted text-decoration-none">
                        <i class="bi bi-link-45deg me-1"></i>{{ company.website.replace(/^https?:\/\//, '') }}
                      </a>
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <span class="text-muted small">{{ company.industry || '—' }}</span>
              </td>
              <td>
                <span class="badge bg-light text-dark border">
                  <i class="bi bi-person me-1"></i>{{ company.contacts_count ?? 0 }}
                </span>
              </td>
              <td>
                <span class="badge bg-light text-dark border">
                  <i class="bi bi-graph-up me-1"></i>{{ company.deals_count ?? 0 }}
                </span>
              </td>
              <td>
                <span class="badge" :class="statusClass(company.status)">
                  {{ statusLabel(company.status) }}
                </span>
              </td>
              <td class="text-end pe-4">
                <div class="d-flex gap-1 justify-content-end">
                  <button
                    class="btn btn-icon btn-flush-dark btn-rounded btn-sm"
                    @click="openEditModal(company)"
                    :title="locale === 'ar' ? 'تعديل' : 'Edit'"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button
                    class="btn btn-icon btn-flush-danger btn-rounded btn-sm"
                    @click="confirmDelete(company)"
                    :title="locale === 'ar' ? 'حذف' : 'Delete'"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="card-footer d-flex align-items-center justify-content-between py-2">
        <span class="text-muted small">
          {{ locale === 'ar'
            ? `${pagination.from}–${pagination.to} من ${pagination.total}`
            : `${pagination.from}–${pagination.to} of ${pagination.total}` }}
        </span>
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="changePage(pagination.current_page - 1)">
            <i class="bi bi-chevron-start"></i>
          </button>
          <button class="btn btn-sm btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="changePage(pagination.current_page + 1)">
            <i class="bi bi-chevron-end"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="companyModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editingCompany ? (locale === 'ar' ? 'تعديل الشركة' : 'Edit Company') : (locale === 'ar' ? 'شركة جديدة' : 'New Company') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveCompany">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-medium">
                    {{ locale === 'ar' ? 'اسم الشركة' : 'Company Name' }} <span class="text-danger">*</span>
                  </label>
                  <input v-model="form.name" type="text" class="form-control" :class="{'is-invalid': errors.name}" required />
                  <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'القطاع' : 'Industry' }}</label>
                  <input v-model="form.industry" type="text" class="form-control" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                  <input v-model="form.email" type="email" class="form-control" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'الهاتف' : 'Phone' }}</label>
                  <input v-model="form.phone" type="text" class="form-control" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'الموقع الإلكتروني' : 'Website' }}</label>
                  <input v-model="form.website" type="url" class="form-control" placeholder="https://" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'الحالة' : 'Status' }}</label>
                  <select v-model="form.status" class="form-select">
                    <option value="prospect">{{ locale === 'ar' ? 'عميل محتمل' : 'Prospect' }}</option>
                    <option value="customer">{{ locale === 'ar' ? 'عميل' : 'Customer' }}</option>
                    <option value="churned">{{ locale === 'ar' ? 'مغادر' : 'Churned' }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'عدد الموظفين' : 'Employees' }}</label>
                  <input v-model.number="form.employees_count" type="number" class="form-control" min="0" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'الإيرادات السنوية' : 'Annual Revenue' }}</label>
                  <input v-model.number="form.annual_revenue" type="number" class="form-control" min="0" step="0.01" />
                </div>
                <div class="col-12">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'العنوان' : 'Address' }}</label>
                  <textarea v-model="form.address" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-medium">{{ locale === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                  <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ locale === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                {{ saving ? (locale === 'ar' ? 'جاري الحفظ...' : 'Saving...') : (locale === 'ar' ? 'حفظ' : 'Save') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteCompanyModal" tabindex="-1">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <i class="bi bi-exclamation-triangle text-danger fs-2 d-block mb-3"></i>
            <h6 class="mb-1">{{ locale === 'ar' ? 'حذف الشركة؟' : 'Delete Company?' }}</h6>
            <p class="text-muted small mb-3">{{ deletingCompany?.name }}</p>
            <div class="d-flex gap-2 justify-content-center">
              <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">{{ locale === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
              <button class="btn btn-sm btn-danger" @click="deleteCompany" :disabled="deleting">
                <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
                {{ locale === 'ar' ? 'حذف' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </CrmLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import UpgradeModal from '../../components/UpgradeModal.vue'
import api from '../../composables/useApi.js'

const store  = useStore()
const route  = useRoute()
const locale = computed(() => store.state.ui.locale)

// ── State ──────────────────────────────────────────────────
const companies      = ref([])
const loading        = ref(false)
const saving         = ref(false)
const deleting       = ref(false)
const search         = ref('')
const statusFilter   = ref('')
const pagination     = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })
const errors         = ref({})
const showUpgrade    = ref(false)
const editingCompany = ref(null)
const deletingCompany = ref(null)

const stats = reactive({ total: 0, prospect: 0, customer: 0, churned: 0 })

const emptyForm = () => ({
  name: '', industry: '', email: '', phone: '',
  website: '', status: 'prospect', employees_count: null,
  annual_revenue: null, address: '', notes: '',
})
const form = reactive(emptyForm())

// ── Debounce ────────────────────────────────────────────────
let debounceTimer = null
function debouncedFetch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchCompanies(1), 350)
}

// ── API ─────────────────────────────────────────────────────
async function fetchCompanies(page = 1) {
  loading.value = true
  try {
    const params = { page }
    if (search.value)      params.search = search.value
    if (statusFilter.value) params.status = statusFilter.value
    const res = await api.get('/companies', { params })
    const d = res.data.data
    companies.value = d.data ?? d
    const meta = res.data.meta
    if (meta) {
      pagination.value = {
        current_page: meta.current_page,
        last_page:    meta.last_page,
        from:         meta.from ?? 0,
        to:           meta.to ?? 0,
        total:        meta.total,
      }
    }
    computeStats()
  } catch {
    companies.value = []
  } finally {
    loading.value = false
  }
}

function computeStats() {
  stats.total    = pagination.value.total || companies.value.length
  stats.prospect = companies.value.filter(c => c.status === 'prospect').length
  stats.customer = companies.value.filter(c => c.status === 'customer').length
  stats.churned  = companies.value.filter(c => c.status === 'churned').length
}

async function saveCompany() {
  saving.value = true
  errors.value = {}
  try {
    if (editingCompany.value) {
      await api.put(`/companies/${editingCompany.value.id}`, form)
    } else {
      await api.post('/companies', form)
    }
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('companyModal')).hide()
    await fetchCompanies(pagination.value.current_page)
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors ?? {}
  } finally {
    saving.value = false
  }
}

async function deleteCompany() {
  deleting.value = true
  try {
    await api.delete(`/companies/${deletingCompany.value.id}`)
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteCompanyModal')).hide()
    await fetchCompanies(pagination.value.current_page)
  } catch {
    // silence
  } finally {
    deleting.value = false
  }
}

// ── Modal helpers ────────────────────────────────────────────
function openAddModal() {
  editingCompany.value = null
  Object.assign(form, emptyForm())
  errors.value = {}
  window.bootstrap.Modal.getOrCreateInstance(document.getElementById('companyModal')).show()
}

function openEditModal(company) {
  editingCompany.value = company
  Object.assign(form, {
    name: company.name || '',
    industry: company.industry || '',
    email: company.email || '',
    phone: company.phone || '',
    website: company.website || '',
    status: company.status || 'prospect',
    employees_count: company.employees_count ?? null,
    annual_revenue: company.annual_revenue ?? null,
    address: company.address || '',
    notes: company.notes || '',
  })
  errors.value = {}
  window.bootstrap.Modal.getOrCreateInstance(document.getElementById('companyModal')).show()
}

function confirmDelete(company) {
  deletingCompany.value = company
  window.bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteCompanyModal')).show()
}

// ── Filters ──────────────────────────────────────────────────
function filterByStatus(s) {
  statusFilter.value = statusFilter.value === s ? '' : s
  fetchCompanies(1)
}
function clearFilter() {
  statusFilter.value = ''
  fetchCompanies(1)
}
function changePage(p) { fetchCompanies(p) }

// ── Display helpers ──────────────────────────────────────────
const avatarColors = [
  '#2563EB', '#7C3AED', '#059669', '#D97706',
  '#DC2626', '#0891B2', '#9333EA', '#16A34A',
]
function avatarColor(name) {
  let sum = 0
  for (const c of (name || 'A')) sum += c.charCodeAt(0)
  return avatarColors[sum % avatarColors.length]
}

function statusClass(s) {
  return { prospect: 'badge-soft-warning', customer: 'badge-soft-success', churned: 'badge-soft-danger' }[s] || 'badge-soft-secondary'
}
function statusLabel(s) {
  const map = {
    prospect: locale.value === 'ar' ? 'محتمل' : 'Prospect',
    customer: locale.value === 'ar' ? 'عميل'  : 'Customer',
    churned:  locale.value === 'ar' ? 'مغادر' : 'Churned',
  }
  return map[s] || s
}

onMounted(async () => {
  await fetchCompanies()
  // Open edit modal if redirected from Show page with ?edit=ID
  if (route.query.edit) {
    const id = Number(route.query.edit)
    const found = companies.value.find(c => c.id === id)
    if (found) {
      nextTick(() => openEditModal(found))
    } else {
      try {
        const { data } = await api.get(`/companies/${id}`)
        if (data.data) nextTick(() => openEditModal(data.data))
      } catch {}
    }
  }
})
</script>

<style scoped>
.stat-card { cursor: pointer; transition: box-shadow .15s, border-color .15s; }
.stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.stat-active { border-color: #2563EB !important; }
.stat-icon {
  width: 40px; height: 40px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.stat-number { font-size: 1.4rem; font-weight: 700; line-height: 1.2; }
.stat-label  { font-size: .75rem; color: #64748B; }
.company-avatar {
  width: 36px; height: 36px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: .875rem;
}
</style>
