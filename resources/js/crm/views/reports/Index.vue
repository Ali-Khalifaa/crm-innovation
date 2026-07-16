<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale==='ar' ? 'التقارير والإحصائيات' : 'Reports & Analytics' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale==='ar' ? 'نظرة شاملة على أداء الأعمال' : 'A complete overview of your business performance' }}</p>
      </div>
      <button class="btn btn-sm btn-outline-secondary flex-shrink-0" @click="loadAll" :disabled="loading">
        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
        <i v-else class="bi bi-arrow-clockwise me-1"></i>
        {{ locale==='ar' ? 'تحديث' : 'Refresh' }}
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:400px">
      <div class="text-center">
        <div class="spinner-border text-primary mb-2" role="status"></div>
        <div class="text-muted small">{{ locale==='ar' ? 'جاري تحميل التقارير...' : 'Loading reports...' }}</div>
      </div>
    </div>

    <template v-else>

      <!-- ═══ KPI Cards ═══ -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4" v-for="kpi in kpiCards" :key="kpi.key">
          <div class="card card-border kpi-card h-100">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between gap-2">
                <div>
                  <div class="kpi-label">{{ locale==='ar' ? kpi.labelAr : kpi.labelEn }}</div>
                  <div class="kpi-value">{{ kpi.value }}</div>
                  <div v-if="kpi.sub" class="kpi-sub">{{ kpi.sub }}</div>
                </div>
                <div class="kpi-icon" :style="{ background: kpi.bg, color: kpi.color }">
                  <i :class="kpi.icon"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══ Charts Row ═══ -->
      <div class="row g-4 mb-4">

        <!-- Monthly Revenue -->
        <div class="col-lg-7">
          <div class="card card-border h-100">
            <div class="card-header card-header-action border-0 pb-0">
              <div>
                <h6 class="mb-0 fw-bold">{{ locale==='ar' ? 'الإيرادات الشهرية' : 'Monthly Revenue' }}</h6>
                <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'آخر 12 شهراً' : 'Last 12 months' }}</p>
              </div>
              <div class="chart-legend d-flex align-items-center gap-2">
                <span class="legend-dot" style="background:#2563EB"></span>
                <span class="text-muted" style="font-size:11px">{{ locale==='ar'?'الإيرادات':'Revenue' }}</span>
              </div>
            </div>
            <div class="card-body pt-2">
              <div class="chart-wrap">
                <Bar v-if="revenueChartData.labels.length" :data="revenueChartData" :options="barOptions" />
                <div v-else class="chart-empty">
                  <i class="bi bi-bar-chart"></i>
                  <span>{{ locale==='ar' ? 'لا توجد بيانات إيرادات بعد' : 'No revenue data yet' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Deals by Stage (Doughnut) -->
        <div class="col-lg-5">
          <div class="card card-border h-100">
            <div class="card-header border-0 pb-0">
              <h6 class="mb-0 fw-bold">{{ locale==='ar' ? 'الصفقات حسب المرحلة' : 'Deals by Stage' }}</h6>
              <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'توزيع الصفقات الحالية' : 'Current deals distribution' }}</p>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
              <div class="doughnut-wrap">
                <Doughnut v-if="dealStageChartData.labels.length" :data="dealStageChartData" :options="doughnutOptions" />
                <div v-else class="chart-empty">
                  <i class="bi bi-pie-chart"></i>
                  <span>{{ locale==='ar' ? 'لا توجد صفقات بعد' : 'No deals yet' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══ Contacts by Source ═══ -->
      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card card-border">
            <div class="card-header border-0 pb-0">
              <h6 class="mb-0 fw-bold">{{ locale==='ar' ? 'جهات الاتصال حسب المصدر' : 'Contacts by Source' }}</h6>
              <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'توزيع المصادر لجهات الاتصال' : 'How contacts find you' }}</p>
            </div>
            <div class="card-body">
              <div v-if="contactsBySource.length" class="source-bars">
                <div v-for="(s, i) in contactsBySource" :key="s.lead_source" class="source-row">
                  <div class="source-label">{{ sourceLabel(s.lead_source) }}</div>
                  <div class="source-bar-wrap">
                    <div class="source-bar" :style="{ width: pctSource(s.count)+'%', background: SOURCE_COLORS[i % SOURCE_COLORS.length] }"></div>
                  </div>
                  <div class="source-count">{{ s.count }}</div>
                </div>
              </div>
              <div v-else class="chart-empty" style="height:100px">
                <i class="bi bi-people"></i>
                <span>{{ locale==='ar' ? 'لا توجد بيانات بعد' : 'No source data yet' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══ Bottom Row ═══ -->
      <div class="row g-4">

        <!-- Pipeline by Stage -->
        <div class="col-lg-6">
          <div class="card card-border h-100">
            <div class="card-header border-0 pb-0">
              <h6 class="mb-0 fw-bold">{{ locale==='ar' ? 'خط الأنابيب حسب المرحلة' : 'Pipeline by Stage' }}</h6>
              <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'قيمة وعدد الصفقات في كل مرحلة' : 'Value and count per stage' }}</p>
            </div>
            <div class="card-body">
              <div v-if="deals.by_stage?.length">
                <div v-for="(s, i) in deals.by_stage" :key="s.stage" class="stage-row">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center gap-2">
                      <span class="stage-dot" :style="{ background: STAGE_COLORS[i % STAGE_COLORS.length] }"></span>
                      <span class="stage-name">{{ s.stage }}</span>
                    </div>
                    <div class="stage-meta">
                      <span class="stage-count">{{ s.count }}</span>
                      <span class="stage-sep">·</span>
                      <span class="stage-value">${{ Number(s.total_value||0).toLocaleString() }}</span>
                    </div>
                  </div>
                  <div class="stage-bar-bg">
                    <div class="stage-bar-fill" :style="{ width: pct(s.count)+'%', background: STAGE_COLORS[i % STAGE_COLORS.length] }"></div>
                  </div>
                </div>
              </div>
              <div v-else class="chart-empty" style="height:120px">
                <i class="bi bi-funnel"></i>
                <span>{{ locale==='ar' ? 'لا توجد صفقات بعد' : 'No deals yet' }}</span>
              </div>

              <!-- Pipeline Summary -->
              <div v-if="deals.by_stage?.length" class="pipeline-summary mt-3">
                <div class="summary-item">
                  <span class="text-muted">{{ locale==='ar'?'إجمالي الصفقات':'Total Deals' }}</span>
                  <strong>{{ deals.by_stage.reduce((a,s)=>a+s.count,0) }}</strong>
                </div>
                <div class="summary-item">
                  <span class="text-muted">{{ locale==='ar'?'قيمة الخط':'Pipeline Value' }}</span>
                  <strong class="text-primary">${{ Number(dash.open_deals_value||0).toLocaleString() }}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Invoice Status -->
        <div class="col-lg-6">
          <div class="card card-border h-100">
            <div class="card-header border-0 pb-0">
              <h6 class="mb-0 fw-bold">{{ locale==='ar' ? 'حالة الفواتير' : 'Invoice Status' }}</h6>
              <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'توزيع الفواتير حسب الحالة' : 'Invoices breakdown by status' }}</p>
            </div>
            <div class="card-body">
              <div v-if="rev.by_status?.length">
                <div v-for="r in rev.by_status" :key="r.status" class="inv-status-row">
                  <div class="d-flex align-items-center gap-2">
                    <span class="inv-s-badge" :class="`inv-${r.status}`">{{ statusLabel(r.status) }}</span>
                    <span class="text-muted" style="font-size:12px">× {{ r.count }}</span>
                  </div>
                  <span class="inv-s-amount">${{ Number(r.total_amount||0).toLocaleString() }}</span>
                </div>

                <!-- Collected vs Outstanding -->
                <div class="inv-totals-box mt-3">
                  <div class="inv-total-row">
                    <div class="d-flex align-items-center gap-2">
                      <span class="tot-dot" style="background:#10B981"></span>
                      <span class="text-muted" style="font-size:12px">{{ locale==='ar'?'تم التحصيل':'Collected' }}</span>
                    </div>
                    <strong class="text-success">${{ Number(rev.total_paid||0).toLocaleString() }}</strong>
                  </div>
                  <div class="inv-total-row">
                    <div class="d-flex align-items-center gap-2">
                      <span class="tot-dot" style="background:#F59E0B"></span>
                      <span class="text-muted" style="font-size:12px">{{ locale==='ar'?'معلّق':'Outstanding' }}</span>
                    </div>
                    <strong class="text-warning">${{ Number(rev.total_pending||0).toLocaleString() }}</strong>
                  </div>
                  <!-- Collection rate bar -->
                  <div class="mt-3" v-if="(rev.total_paid||0) + (rev.total_pending||0) > 0">
                    <div class="d-flex justify-content-between mb-1" style="font-size:11px">
                      <span class="text-muted">{{ locale==='ar'?'نسبة التحصيل':'Collection Rate' }}</span>
                      <span class="fw-bold">{{ collectionRate }}%</span>
                    </div>
                    <div class="collection-bar">
                      <div class="collection-fill" :style="{ width: collectionRate+'%' }"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="chart-empty" style="height:120px">
                <i class="bi bi-receipt"></i>
                <span>{{ locale==='ar' ? 'لا توجد فواتير بعد' : 'No invoices yet' }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </template>

  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { Bar, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale, LinearScale, BarElement,
  ArcElement, Tooltip, Legend, Title,
} from 'chart.js'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, ArcElement, Tooltip, Legend, Title)

const store   = useStore()
const locale  = computed(() => store.state.ui.locale)

const dash              = ref({})
const deals             = ref({ by_stage:[], by_status:[] })
const rev               = ref({ by_status:[], monthly_revenue:[], total_paid:0, total_pending:0 })
const contactsBySource  = ref([])
const loading           = ref(true)

const MONTHS_EN = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
const MONTHS_AR = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر']
const STAGE_COLORS  = ['#2563EB','#7C3AED','#0891B2','#059669','#D97706','#DC2626','#DB2777','#6366F1']
const SOURCE_COLORS = ['#2563EB','#7C3AED','#059669','#D97706','#0891B2','#DB2777','#6366F1','#64748B']

// ── KPI cards ─────────────────────────────────────────────
function fmt(n) {
  if (!n) return '0'
  if (n >= 1000000) return (n/1000000).toFixed(1)+'M'
  if (n >= 1000)    return (n/1000).toFixed(1)+'K'
  return Number(n).toFixed(0)
}

const kpiCards = computed(() => [
  { key:'contacts',  labelAr:'إجمالي جهات الاتصال', labelEn:'Total Contacts',  value: dash.value.total_contacts??0,          icon:'bi bi-people-fill',      bg:'rgba(37,99,235,.1)',   color:'#2563EB' },
  { key:'companies', labelAr:'إجمالي الشركات',       labelEn:'Total Companies', value: dash.value.total_companies??0,         icon:'bi bi-building-fill',    bg:'rgba(124,58,237,.1)', color:'#7C3AED' },
  { key:'won',       labelAr:'صفقات رابحة',           labelEn:'Won Deals',       value: dash.value.won_deals??0,               icon:'bi bi-trophy-fill',      bg:'rgba(16,185,129,.1)', color:'#059669' },
  { key:'lost',      labelAr:'صفقات خاسرة',           labelEn:'Lost Deals',      value: dash.value.lost_deals??0,              icon:'bi bi-x-circle-fill',    bg:'rgba(239,68,68,.1)',  color:'#EF4444' },
  { key:'revenue',   labelAr:'إجمالي الإيرادات',     labelEn:'Total Revenue',   value: '$'+fmt(dash.value.total_revenue),     icon:'bi bi-currency-dollar',  bg:'rgba(16,185,129,.1)', color:'#059669' },
  { key:'pipeline',  labelAr:'قيمة خط الأنابيب',     labelEn:'Pipeline Value',  value: '$'+fmt(dash.value.open_deals_value),  icon:'bi bi-graph-up-arrow',   bg:'rgba(37,99,235,.1)',  color:'#2563EB' },
])

// ── Invoice computed ──────────────────────────────────────
const collectionRate = computed(() => {
  const total = (rev.value.total_paid||0) + (rev.value.total_pending||0)
  if (!total) return 0
  return Math.round((rev.value.total_paid||0) / total * 100)
})

// ── Chart data ────────────────────────────────────────────
const revenueChartData = computed(() => {
  const m = rev.value.monthly_revenue || []
  const MONTHS = locale.value==='ar' ? MONTHS_AR : MONTHS_EN
  return {
    labels: m.map(r => `${MONTHS[r.month-1]} ${r.year}`),
    datasets: [{
      label: locale.value==='ar' ? 'الإيرادات ($)' : 'Revenue ($)',
      data: m.map(r => Number(r.revenue)),
      backgroundColor: 'rgba(37,99,235,0.85)',
      hoverBackgroundColor: 'rgba(37,99,235,1)',
      borderRadius: 8,
      borderSkipped: false,
      maxBarThickness: 40,
    }],
  }
})

const dealStageChartData = computed(() => {
  const s = (deals.value.by_stage || []).filter(x => x.count > 0)
  return {
    labels: s.map(x => x.stage),
    datasets: [{
      data: s.map(x => x.count),
      backgroundColor: STAGE_COLORS.slice(0, s.length),
      borderWidth: 3,
      borderColor: 'transparent',
      hoverOffset: 8,
    }],
  }
})

const barOptions = {
  responsive: true, maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: {
    callbacks: { label: ctx => ` $${Number(ctx.raw).toLocaleString()}` }
  }},
  scales: {
    y: {
      beginAtZero: true,
      ticks: { callback: v => v>=1000 ? `$${(v/1000).toFixed(0)}k` : `$${v}`, font:{ size:11 } },
      grid: { color: 'rgba(0,0,0,0.05)' },
      border: { display:false },
    },
    x: { grid: { display:false }, ticks:{ font:{ size:11 } } },
  },
}
const doughnutOptions = {
  responsive: true, maintainAspectRatio: false,
  plugins: {
    legend: { position:'bottom', labels:{ padding:14, font:{ size:11 }, boxWidth:12, borderRadius:4 } },
    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} ${locale.value==='ar'?'صفقة':'deals'}` }}
  },
  cutout: '65%',
}

// ── Helpers ───────────────────────────────────────────────
const maxCount       = computed(() => Math.max(...(deals.value.by_stage?.map(s=>s.count)||[1]),1))
const maxSourceCount = computed(() => Math.max(...(contactsBySource.value.map(s=>s.count)||[1]),1))
function pctSource(count) { return Math.round((count/maxSourceCount.value)*100) }

const SOURCE_LABELS_AR = { website:'الموقع الإلكتروني', referral:'توصية', social_media:'التواصل الاجتماعي', email_campaign:'حملة بريدية', cold_call:'اتصال بارد', event:'فعالية', other:'أخرى' }
const SOURCE_LABELS_EN = { website:'Website', referral:'Referral', social_media:'Social Media', email_campaign:'Email Campaign', cold_call:'Cold Call', event:'Event', other:'Other' }
function sourceLabel(s) { return (locale.value==='ar' ? SOURCE_LABELS_AR : SOURCE_LABELS_EN)[s] || s }
function pct(count) { return Math.round((count/maxCount.value)*100) }

function statusLabel(s) {
  const map = { draft:locale.value==='ar'?'مسودة':'Draft', sent:locale.value==='ar'?'مرسلة':'Sent', paid:locale.value==='ar'?'مدفوعة':'Paid', overdue:locale.value==='ar'?'متأخرة':'Overdue', cancelled:locale.value==='ar'?'ملغاة':'Cancelled' }
  return map[s] || s
}

// ── Load ──────────────────────────────────────────────────
async function loadAll() {
  loading.value = true
  try {
    const [d, dr, rr] = await Promise.all([
      api.get('/reports/dashboard'),
      api.get('/reports/deals'),
      api.get('/reports/revenue'),
    ])
    dash.value           = d.data.data  || {}
    deals.value          = dr.data.data || {}
    rev.value            = rr.data.data || {}
    contactsBySource.value = dash.value.contacts_by_source || []
  } catch {}
  loading.value = false
}

onMounted(loadAll)
</script>

<style scoped>
/* ─── KPI Cards ──────────────────────────────── */
.kpi-card   { transition:all .15s; }
.kpi-card:hover { transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08); }
.kpi-icon   { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.kpi-label  { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-secondary-color);margin-bottom:.4rem; }
.kpi-value  { font-size:1.75rem;font-weight:800;line-height:1;color:var(--bs-body-color);margin-bottom:.2rem; }
.kpi-sub    { font-size:11px;color:var(--bs-secondary-color); }

/* ─── Charts ─────────────────────────────────── */
.chart-wrap    { height:230px;position:relative; }
.doughnut-wrap { height:230px;width:100%;max-width:280px;position:relative; }
.chart-empty   { height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:var(--bs-secondary-color);font-size:13px; }
.chart-empty i { font-size:32px;opacity:.35; }
.legend-dot    { width:10px;height:10px;border-radius:3px;flex-shrink:0; }

/* ─── Pipeline ───────────────────────────────── */
.stage-row  { margin-bottom:1rem; }
.stage-dot  { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
.stage-name { font-size:13px;font-weight:500;color:var(--bs-body-color); }
.stage-meta { display:flex;align-items:center;gap:4px; }
.stage-count{ font-weight:700;font-size:13px;color:var(--bs-body-color); }
.stage-sep  { color:var(--bs-secondary-color);font-size:11px; }
.stage-value{ font-size:12px;color:var(--bs-secondary-color); }
.stage-bar-bg   { height:6px;background:var(--bs-border-color,#E2E8F0);border-radius:3px;overflow:hidden; }
.stage-bar-fill { height:100%;border-radius:3px;transition:width .6s cubic-bezier(.4,0,.2,1); }

.pipeline-summary { display:flex;gap:1rem;border-top:1px solid var(--bs-border-color);padding-top:.875rem;flex-wrap:wrap; }
.summary-item     { display:flex;flex-direction:column;gap:.2rem;font-size:12px; }

/* ─── Invoice Status ─────────────────────────── */
.inv-status-row  { display:flex;align-items:center;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid var(--bs-border-color); }
.inv-status-row:last-child { border-bottom:none; }
.inv-s-badge { display:inline-flex;align-items:center;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }
.inv-draft     { background:rgba(100,116,139,.1);color:#64748B; }
.inv-sent      { background:rgba(124,58,237,.1);color:#7C3AED; }
.inv-paid      { background:rgba(16,185,129,.1);color:#059669; }
.inv-overdue   { background:rgba(239,68,68,.1);color:#DC2626; }
.inv-cancelled { background:rgba(100,116,139,.1);color:#94A3B8; }
.inv-s-amount  { font-weight:700;font-size:13px;color:var(--bs-body-color); }

.inv-totals-box  { background:var(--bs-tertiary-bg,#F8FAFC);border-radius:10px;padding:1rem; }
.inv-total-row   { display:flex;align-items:center;justify-content:space-between;padding:.35rem 0; }
.tot-dot         { width:10px;height:10px;border-radius:50%;flex-shrink:0; }

.collection-bar  { height:6px;background:var(--bs-border-color,#E2E8F0);border-radius:3px;overflow:hidden; }
.collection-fill { height:100%;background:#10B981;border-radius:3px;transition:width .6s cubic-bezier(.4,0,.2,1); }

/* ─── Section title ──────────────────────────── */
.fsect-title { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-secondary-color);padding-bottom:.5rem;margin-bottom:.875rem;border-bottom:1px solid var(--bs-border-color); }

/* ─── Source bars ────────────────────────────── */
.source-bars  { display:flex;flex-direction:column;gap:12px; }
.source-row   { display:flex;align-items:center;gap:12px; }
.source-label { font-size:12px;font-weight:500;color:var(--bs-body-color);min-width:130px;flex-shrink:0; }
.source-bar-wrap { flex:1;height:10px;background:var(--bs-border-color,#E2E8F0);border-radius:5px;overflow:hidden; }
.source-bar   { height:100%;border-radius:5px;transition:width .6s cubic-bezier(.4,0,.2,1); }
.source-count { font-size:12px;font-weight:700;color:var(--bs-body-color);min-width:28px;text-align:end; }
</style>
