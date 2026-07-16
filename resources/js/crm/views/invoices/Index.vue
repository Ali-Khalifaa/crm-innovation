<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale==='ar' ? 'الفواتير' : 'Invoices' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale==='ar' ? 'إدارة وتتبع جميع الفواتير' : 'Manage and track all your invoices' }}</p>
      </div>
      <router-link to="/crm/invoices/create" class="btn btn-sm btn-primary flex-shrink-0">
        <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar' ? 'فاتورة جديدة' : 'New Invoice' }}
      </router-link>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
      <div class="col-6 col-xl" v-for="s in statCards" :key="s.key">
        <div class="card card-border stat-card" :class="{'stat-active': filterStatus===s.key && s.key!==''}" @click="filterByStatus(s.key)">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" :style="{ background: s.bg, color: s.color }"><i :class="s.icon + ' fs-5'"></i></div>
              <div>
                <div class="stat-number">{{ stats[s.key] ?? 0 }}</div>
                <div class="stat-label">{{ locale==='ar' ? s.labelAr : s.labelEn }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="card card-border">

      <!-- Card Header -->
      <div class="card-header card-header-action flex-wrap gap-2">
        <h6 class="mb-0 d-flex align-items-center gap-2 flex-wrap">
          <span>{{ locale==='ar' ? 'قائمة الفواتير' : 'All Invoices' }}</span>
          <span class="badge badge-soft-primary">{{ meta.total ?? 0 }}</span>
          <button v-if="filterStatus" class="btn btn-xs badge-soft-warning d-flex align-items-center gap-1 border-0" @click="clearFilter">
            {{ statusLabel(filterStatus) }} <i class="bi bi-x"></i>
          </button>
        </h6>
        <div class="card-action-wrap d-flex gap-2 flex-wrap">
          <div class="input-group" style="width:220px">
            <span class="input-group-text bg-transparent border-end-0 ps-2">
              <i class="bi bi-search text-muted" style="font-size:13px"></i>
            </span>
            <input v-model="search" @input="debouncedFetch" type="text"
              class="form-control border-start-0 ps-1"
              :placeholder="locale==='ar' ? 'رقم الفاتورة...' : 'Invoice number...'" />
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:260px">
        <div class="text-center">
          <div class="spinner-border text-primary mb-2" role="status"></div>
          <div class="text-muted small">{{ locale==='ar' ? 'جاري التحميل...' : 'Loading...' }}</div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="!invoices.length" class="text-center py-5 px-4">
        <div class="empty-icon mb-3"><i class="bi bi-receipt"></i></div>
        <h6 class="mb-1">{{ search || filterStatus ? (locale==='ar'?'لا توجد نتائج':'No results found') : (locale==='ar'?'لا توجد فواتير بعد':'No invoices yet') }}</h6>
        <p class="text-muted small mb-3">{{ search || filterStatus ? (locale==='ar'?'جرّب تغيير الفلتر':'Try changing your filter') : (locale==='ar'?'أنشئ أول فاتورة لتبدأ':'Create your first invoice to get started') }}</p>
        <button v-if="search || filterStatus" class="btn btn-sm btn-outline-secondary me-2" @click="clearFilter">
          <i class="bi bi-x-lg me-1"></i>{{ locale==='ar'?'مسح الفلتر':'Clear Filter' }}
        </button>
        <router-link v-else to="/crm/invoices/create" class="btn btn-sm btn-primary">
          <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar'?'إضافة فاتورة':'Add Invoice' }}
        </router-link>
      </div>

      <!-- Table -->
      <div v-else>
        <div class="table-responsive">
          <table class="table inv-table mb-0">
            <thead>
              <tr>
                <th class="ps-4">{{ locale==='ar'?'رقم الفاتورة':'Invoice #' }}</th>
                <th class="d-none d-md-table-cell">{{ locale==='ar'?'العميل':'Client' }}</th>
                <th class="d-none d-lg-table-cell">{{ locale==='ar'?'تاريخ الإصدار':'Issue Date' }}</th>
                <th>{{ locale==='ar'?'الاستحقاق':'Due Date' }}</th>
                <th>{{ locale==='ar'?'المبلغ':'Total' }}</th>
                <th>{{ locale==='ar'?'الحالة':'Status' }}</th>
                <th style="width:90px"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="inv in invoices" :key="inv.id" class="inv-row">
                <td class="ps-4">
                  <router-link :to="`/crm/invoices/${inv.id}`" class="inv-number">{{ inv.invoice_number }}</router-link>
                </td>
                <td class="d-none d-md-table-cell" style="font-size:13px">
                  <span v-if="inv.contact">{{ inv.contact.first_name }} {{ inv.contact.last_name }}</span>
                  <span v-else class="text-muted">—</span>
                </td>
                <td class="d-none d-lg-table-cell" style="font-size:12px;color:var(--bs-secondary-color)">
                  {{ formatDate(inv.issue_date) }}
                </td>
                <td style="font-size:12px" :class="{ 'text-danger fw-semibold': isDueSoon(inv) }">
                  <i v-if="isDueSoon(inv)" class="bi bi-exclamation-circle me-1"></i>
                  {{ formatDate(inv.due_date) }}
                </td>
                <td class="inv-total">${{ Number(inv.total).toLocaleString('en', {minimumFractionDigits:2, maximumFractionDigits:2}) }}</td>
                <td>
                  <span class="inv-status-badge" :class="`inv-${inv.status}`">
                    <i class="bi" :class="statusIcon(inv.status)" style="font-size:8px"></i>
                    {{ statusLabel(inv.status) }}
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-1 justify-content-end">
                    <router-link :to="`/crm/invoices/${inv.id}`" class="btn btn-xs btn-outline-secondary" :title="locale==='ar'?'عرض':'View'">
                      <i class="bi bi-eye"></i>
                    </router-link>
                    <a :href="`/api/crm/invoices/${inv.id}/pdf?token=${token}`" target="_blank" class="btn btn-xs btn-outline-danger" :title="locale==='ar'?'تحميل PDF':'Download PDF'">
                      <i class="bi bi-file-pdf"></i>
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="d-flex justify-content-center align-items-center gap-1 py-3 flex-wrap">
          <button class="btn btn-xs btn-outline-secondary" @click="page=1;fetchInvoices()" :disabled="page===1">«</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page--;fetchInvoices()" :disabled="page===1">‹</button>
          <button v-for="p in pages" :key="p" class="btn btn-xs"
            :class="p===page ? 'btn-primary':'btn-outline-secondary'"
            @click="page=p;fetchInvoices()">{{ p }}</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page++;fetchInvoices()" :disabled="page===meta.last_page">›</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page=meta.last_page;fetchInvoices()" :disabled="page===meta.last_page">»</button>
        </div>
      </div>
    </div>

  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store  = useStore()
const toast  = useToast()
const locale = computed(() => store.state.ui.locale)
const token  = computed(() => store.state.auth.token)

const invoices     = ref([])
const meta         = ref({})
const loading      = ref(true)
const page         = ref(1)
const search       = ref('')
const filterStatus = ref('')
let searchTimer    = null

const stats = ref({ total:0, draft:0, sent:0, paid:0, overdue:0, cancelled:0 })

const statCards = [
  { key:'total',     labelAr:'الإجمالي',     labelEn:'Total',     icon:'bi bi-receipt',        bg:'rgba(37,99,235,.1)',   color:'#2563EB' },
  { key:'draft',     labelAr:'مسودة',         labelEn:'Draft',     icon:'bi bi-pencil-square',  bg:'rgba(100,116,139,.1)',color:'#64748B' },
  { key:'sent',      labelAr:'مرسلة',         labelEn:'Sent',      icon:'bi bi-send',           bg:'rgba(124,58,237,.1)', color:'#7C3AED' },
  { key:'paid',      labelAr:'مدفوعة',        labelEn:'Paid',      icon:'bi bi-check-circle',   bg:'rgba(16,185,129,.1)', color:'#059669' },
  { key:'overdue',   labelAr:'متأخرة',        labelEn:'Overdue',   icon:'bi bi-exclamation-circle', bg:'rgba(239,68,68,.1)',  color:'#DC2626' },
]

const pages = computed(() => {
  const t = meta.value.last_page || 1, c = page.value, arr = []
  for (let p = Math.max(1, c-2); p <= Math.min(t, c+2); p++) arr.push(p)
  return arr
})

function formatDate(d) {
  if (!d) return '—'
  const dt = new Date(d.substring(0,10) + 'T00:00:00')
  if (isNaN(dt)) return '—'
  return `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()}`
}
function isDueSoon(inv) {
  if (!inv.due_date || inv.status === 'paid' || inv.status === 'cancelled') return false
  const d = new Date(inv.due_date.substring(0,10) + 'T00:00:00')
  return !isNaN(d) && d < new Date()
}
function statusLabel(s) {
  const map = { draft: locale.value==='ar'?'مسودة':'Draft', sent: locale.value==='ar'?'مرسلة':'Sent', paid: locale.value==='ar'?'مدفوعة':'Paid', overdue: locale.value==='ar'?'متأخرة':'Overdue', cancelled: locale.value==='ar'?'ملغاة':'Cancelled' }
  return map[s] || s
}
function statusIcon(s) {
  return { draft:'bi-circle', sent:'bi-send-fill', paid:'bi-check-circle-fill', overdue:'bi-exclamation-circle-fill', cancelled:'bi-x-circle-fill' }[s] || 'bi-circle'
}
function filterByStatus(key) {
  filterStatus.value = filterStatus.value === key ? '' : key
  page.value = 1
  fetchInvoices()
}
function clearFilter() { filterStatus.value=''; search.value=''; page.value=1; fetchInvoices() }
function debouncedFetch() { clearTimeout(searchTimer); searchTimer = setTimeout(() => { page.value=1; fetchInvoices() }, 400) }

async function fetchInvoices() {
  loading.value = true
  try {
    const { data } = await api.get('/invoices', { params: { search: search.value, status: filterStatus.value, page: page.value, per_page: 15 } })
    invoices.value = data.data
    meta.value     = data.meta || {}
  } catch { toast.error(locale.value==='ar'?'فشل تحميل الفواتير':'Failed to load invoices') }
  loading.value = false
}

async function fetchStats() {
  try {
    const keys = ['total','draft','sent','paid','overdue']
    const params = { per_page:1 }
    const results = await Promise.all([
      api.get('/invoices', { params }),
      api.get('/invoices', { params: { ...params, status:'draft' } }),
      api.get('/invoices', { params: { ...params, status:'sent' } }),
      api.get('/invoices', { params: { ...params, status:'paid' } }),
      api.get('/invoices', { params: { ...params, status:'overdue' } }),
    ])
    keys.forEach((k,i) => { stats.value[k] = results[i].data.meta?.total ?? 0 })
  } catch {}
}

onMounted(() => { fetchInvoices(); fetchStats() })
</script>

<style scoped>
.stat-card   { cursor:pointer;transition:all .15s; }
.stat-card:hover { transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08); }
.stat-active { border-color:#2563EB !important;box-shadow:0 0 0 3px rgba(37,99,235,.12) !important; }
.stat-icon   { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.stat-number { font-size:1.5rem;font-weight:800;line-height:1.2;color:var(--bs-body-color); }
.stat-label  { font-size:11px;color:var(--bs-secondary-color);font-weight:500;margin-top:2px; }

.inv-table thead th { background:var(--bs-tertiary-bg,#F8FAFC);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-secondary-color);padding:.75rem 1rem;border-bottom:1px solid var(--bs-border-color);white-space:nowrap; }
.inv-row { transition:background .1s; }
.inv-row:hover { background:rgba(37,99,235,.03); }
.inv-table td { padding:.85rem 1rem;vertical-align:middle;border-bottom:1px solid var(--bs-border-color); }
.inv-table tbody tr:last-child td { border-bottom:none; }

.inv-number { font-weight:700;color:#2563EB;text-decoration:none;font-size:13px; }
.inv-number:hover { text-decoration:underline; }
.inv-total  { font-weight:800;color:var(--bs-body-color);font-size:13px;white-space:nowrap; }

.inv-status-badge { display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }
.inv-draft     { background:rgba(100,116,139,.1);color:#64748B; }
.inv-sent      { background:rgba(124,58,237,.1);color:#7C3AED; }
.inv-paid      { background:rgba(16,185,129,.1);color:#059669; }
.inv-overdue   { background:rgba(239,68,68,.1);color:#DC2626; }
.inv-cancelled { background:rgba(100,116,139,.1);color:#94A3B8; }

.empty-icon { width:80px;height:80px;border-radius:20px;background:var(--bs-tertiary-bg,#f8f9fa);display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--bs-secondary-color);margin:0 auto; }
[data-bs-theme="dark"] .inv-table thead th { background:var(--bs-dark-bg-subtle); }
</style>
