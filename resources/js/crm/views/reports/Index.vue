<template>
  <CrmLayout>
    <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
    <div v-else>

      <!-- KPI Row -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:#EFF6FF;color:#2563EB;"><i class="fas fa-users"></i></div>
          <div class="kpi-value">{{ dash.total_contacts ?? 0 }}</div>
          <div class="kpi-label">Total Contacts</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:#F0FDF4;color:#16A34A;"><i class="fas fa-handshake"></i></div>
          <div class="kpi-value">{{ dash.won_deals ?? 0 }}</div>
          <div class="kpi-label">Won Deals</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:#FFF7ED;color:#EA580C;"><i class="fas fa-percent"></i></div>
          <div class="kpi-value">{{ dash.win_rate ?? 0 }}%</div>
          <div class="kpi-label">Win Rate</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:#F0FDF4;color:#16A34A;"><i class="fas fa-dollar-sign"></i></div>
          <div class="kpi-value">${{ fmt(dash.total_revenue) }}</div>
          <div class="kpi-label">Total Revenue</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:#EFF6FF;color:#2563EB;"><i class="fas fa-funnel-dollar"></i></div>
          <div class="kpi-value">${{ fmt(dash.open_deals_value) }}</div>
          <div class="kpi-label">Pipeline Value</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:#FFFBEB;color:#D97706;"><i class="fas fa-file-invoice-dollar"></i></div>
          <div class="kpi-value">${{ fmt(rev.total_pending) }}</div>
          <div class="kpi-label">Pending Revenue</div>
        </div>
      </div>

      <!-- Charts Row -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

        <div class="crm-card">
          <div class="crm-card-header">
            <span class="crm-card-title">Monthly Revenue</span>
            <span style="font-size:12px;color:#64748B;">Last 12 months</span>
          </div>
          <div style="height:220px;position:relative;">
            <Bar v-if="revenueChartData.labels.length" :data="revenueChartData" :options="barOptions" />
            <div v-else class="empty-chart">No revenue data yet</div>
          </div>
        </div>

        <div class="crm-card">
          <div class="crm-card-header">
            <span class="crm-card-title">Deals by Stage</span>
          </div>
          <div style="height:220px;display:flex;align-items:center;justify-content:center;position:relative;">
            <Doughnut v-if="dealStageChartData.labels.length" :data="dealStageChartData" :options="doughnutOptions" />
            <div v-else class="empty-chart">No deals yet</div>
          </div>
        </div>
      </div>

      <!-- Bottom Row -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div class="crm-card">
          <div class="crm-card-header">
            <span class="crm-card-title">Pipeline by Stage</span>
          </div>
          <div v-for="s in (deals.by_stage || [])" :key="s.stage" style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
              <span style="font-weight:500;">{{ s.stage }}</span>
              <span style="font-weight:700;">{{ s.count }} · ${{ Number(s.total_value || 0).toLocaleString() }}</span>
            </div>
            <div style="height:6px;background:var(--border,#E2E8F0);border-radius:3px;overflow:hidden;">
              <div style="height:100%;background:#2563EB;border-radius:3px;" :style="{ width: pct(s.count) + '%' }"></div>
            </div>
          </div>
          <div v-if="!deals.by_stage?.length" class="empty-chart" style="height:80px;">No deals yet</div>
        </div>

        <div class="crm-card">
          <div class="crm-card-header">
            <span class="crm-card-title">Invoice Status</span>
          </div>
          <div v-for="r in (rev.by_status || [])" :key="r.status" style="margin-bottom:10px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
              <span class="badge" :class="invBadge(r.status)">{{ r.status }}</span>
              <span style="font-weight:700;font-size:13px;">{{ r.count }} · ${{ Number(r.total_amount || 0).toLocaleString() }}</span>
            </div>
          </div>
          <div v-if="!rev.by_status?.length" class="empty-chart" style="height:80px;">No invoices yet</div>
          <div v-if="rev.by_status?.length" style="border-top:1px solid var(--border,#E2E8F0);padding-top:12px;margin-top:8px;">
            <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
              <span style="color:#64748B;">Collected</span>
              <strong style="color:#16A34A;">${{ Number(rev.total_paid || 0).toLocaleString() }}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:14px;">
              <span style="color:#64748B;">Outstanding</span>
              <strong style="color:#F59E0B;">${{ Number(rev.total_pending || 0).toLocaleString() }}</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Bar, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale, LinearScale, BarElement,
  ArcElement, Tooltip, Legend,
} from 'chart.js'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, ArcElement, Tooltip, Legend)

const dash    = ref({})
const deals   = ref({ by_stage: [], by_status: [] })
const rev     = ref({ by_status: [], monthly_revenue: [] })
const loading = ref(true)

const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
const COLORS = ['#6366F1','#3B82F6','#F59E0B','#8B5CF6','#10B981','#EF4444','#06B6D4']

const revenueChartData = computed(() => {
  const m = rev.value.monthly_revenue || []
  return {
    labels: m.map(r => `${MONTHS[r.month - 1]} ${r.year}`),
    datasets: [{
      label: 'Revenue ($)',
      data: m.map(r => Number(r.revenue)),
      backgroundColor: 'rgba(37,99,235,0.8)',
      borderRadius: 6,
      borderSkipped: false,
    }],
  }
})

const dealStageChartData = computed(() => {
  const s = (deals.value.by_stage || []).filter(s => s.count > 0)
  return {
    labels: s.map(x => x.stage),
    datasets: [{
      data: s.map(x => x.count),
      backgroundColor: COLORS.slice(0, s.length),
      borderWidth: 2,
      borderColor: '#fff',
      hoverOffset: 6,
    }],
  }
})

const barOptions = {
  responsive: true, maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { callback: v => v >= 1000 ? `$${(v/1000).toFixed(0)}k` : `$${v}` },
      grid: { color: 'rgba(0,0,0,0.04)' },
    },
    x: { grid: { display: false } },
  },
}
const doughnutOptions = {
  responsive: true, maintainAspectRatio: false,
  plugins: { legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 } } } },
  cutout: '60%',
}

const maxCount = computed(() => Math.max(...(deals.value.by_stage?.map(s => s.count) || [1]), 1))
function pct(count) { return Math.round((count / maxCount.value) * 100) }
function invBadge(s) {
  return { paid:'badge-success', sent:'badge-info', draft:'badge-gray', overdue:'badge-danger', cancelled:'badge-gray' }[s] || 'badge-gray'
}
function fmt(n) {
  if (!n) return '0'
  if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M'
  if (n >= 1000) return (n / 1000).toFixed(1) + 'K'
  return Number(n).toFixed(0)
}

onMounted(async () => {
  const [d, dr, rr] = await Promise.all([
    api.get('/reports/dashboard'),
    api.get('/reports/deals'),
    api.get('/reports/revenue'),
  ])
  dash.value  = d.data.data
  deals.value = dr.data.data
  rev.value   = rr.data.data
  loading.value = false
})
</script>

<style scoped>
.empty-chart { display:flex;align-items:center;justify-content:center;color:#CBD5E1;font-size:13px;width:100%; }
</style>
