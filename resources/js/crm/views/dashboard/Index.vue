<template>
  <CrmLayout>

    <!-- KPI Stats Row -->
    <div class="row g-3 mb-4">
      <!-- Contacts -->
      <div class="col-6 col-xl-2">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(37,99,235,.1);color:#2563EB">
            <i class="bi bi-people fs-5"></i>
          </div>
          <div class="kpi-body">
            <div class="kpi-value">
              <span v-if="loading" class="placeholder-glow"><span class="placeholder" style="width:40px"></span></span>
              <span v-else>{{ stats.total_contacts ?? 0 }}</span>
            </div>
            <div class="kpi-label">{{ t('dashboard.total_contacts') }}</div>
            <div class="kpi-sub">+{{ stats.new_contacts_this_month ?? 0 }} {{ locale==='ar'?'هذا الشهر':'this month' }}</div>
          </div>
        </div>
      </div>
      <!-- Companies -->
      <div class="col-6 col-xl-2">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(124,58,237,.1);color:#7C3AED">
            <i class="bi bi-building fs-5"></i>
          </div>
          <div class="kpi-body">
            <div class="kpi-value">
              <span v-if="loading" class="placeholder-glow"><span class="placeholder" style="width:40px"></span></span>
              <span v-else>{{ stats.total_companies ?? 0 }}</span>
            </div>
            <div class="kpi-label">{{ locale==='ar'?'الشركات':'Companies' }}</div>
            <div class="kpi-sub">{{ locale==='ar'?'شركة مسجّلة':'registered' }}</div>
          </div>
        </div>
      </div>
      <!-- Pipeline -->
      <div class="col-6 col-xl-2">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(245,158,11,.1);color:#D97706">
            <i class="bi bi-graph-up-arrow fs-5"></i>
          </div>
          <div class="kpi-body">
            <div class="kpi-value">
              <span v-if="loading" class="placeholder-glow"><span class="placeholder" style="width:60px"></span></span>
              <span v-else>${{ formatNum(stats.open_deals_value) }}</span>
            </div>
            <div class="kpi-label">{{ t('dashboard.pipeline_value') }}</div>
            <div class="kpi-sub">{{ stats.total_deals ?? 0 }} {{ locale==='ar'?'صفقة':'deals total' }}</div>
          </div>
        </div>
      </div>
      <!-- Won Deals -->
      <div class="col-6 col-xl-2">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(16,185,129,.1);color:#059669">
            <i class="bi bi-trophy fs-5"></i>
          </div>
          <div class="kpi-body">
            <div class="kpi-value">
              <span v-if="loading" class="placeholder-glow"><span class="placeholder" style="width:40px"></span></span>
              <span v-else>{{ stats.won_deals ?? 0 }}</span>
            </div>
            <div class="kpi-label">{{ locale==='ar'?'صفقات مكسوبة':'Won Deals' }}</div>
            <div class="kpi-sub kpi-sub-success">{{ stats.win_rate ?? 0 }}% {{ locale==='ar'?'معدل الفوز':'win rate' }}</div>
          </div>
        </div>
      </div>
      <!-- Lost Deals -->
      <div class="col-6 col-xl-2">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(239,68,68,.1);color:#EF4444">
            <i class="bi bi-x-circle fs-5"></i>
          </div>
          <div class="kpi-body">
            <div class="kpi-value">
              <span v-if="loading" class="placeholder-glow"><span class="placeholder" style="width:40px"></span></span>
              <span v-else>{{ stats.lost_deals ?? 0 }}</span>
            </div>
            <div class="kpi-label">{{ locale==='ar'?'صفقات خاسرة':'Lost Deals' }}</div>
            <div class="kpi-sub kpi-sub-danger">{{ locale==='ar'?'مغلقة بخسارة':'closed lost' }}</div>
          </div>
        </div>
      </div>
      <!-- Overdue Invoices -->
      <div class="col-6 col-xl-2">
        <div class="kpi-card" :class="{ 'kpi-card-alert': stats.overdue_invoices > 0 }">
          <div class="kpi-icon" :style="stats.overdue_invoices > 0 ? 'background:rgba(239,68,68,.1);color:#EF4444' : 'background:rgba(100,116,139,.1);color:#64748B'">
            <i class="bi bi-receipt-cutoff fs-5"></i>
          </div>
          <div class="kpi-body">
            <div class="kpi-value">
              <span v-if="loading" class="placeholder-glow"><span class="placeholder" style="width:40px"></span></span>
              <span v-else :class="{ 'text-danger': stats.overdue_invoices > 0 }">{{ stats.overdue_invoices ?? 0 }}</span>
            </div>
            <div class="kpi-label">{{ locale==='ar'?'فواتير متأخرة':'Overdue Invoices' }}</div>
            <div class="kpi-sub" :class="stats.overdue_invoices > 0 ? 'kpi-sub-danger' : ''">
              {{ stats.overdue_invoices > 0 ? (locale==='ar'?'تحتاج اهتمام':'need attention') : (locale==='ar'?'لا متأخرات':'no overdue') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions + Subscription -->
    <div class="row">
      <div class="col-xxl-9 col-lg-8 col-md-7 mb-md-4 mb-3">
        <div class="card card-border mb-0 h-100">
          <div class="card-header card-header-action">
            <h6>{{ t('dashboard.quick_actions') }}</h6>
            <div class="card-action-wrap">
              <span class="badge badge-soft-primary">{{ tenant?.name }}</span>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-sm-6">
                <router-link to="/crm/contacts/create" class="text-decoration-none">
                  <div class="card card-border">
                    <div class="card-body d-flex align-items-center gap-3">
                      <div class="avatar avatar-icon avatar-md avatar-soft-primary avatar-rounded">
                        <span class="initial-wrap"><i class="bi bi-person-plus fs-5"></i></span>
                      </div>
                      <div>
                        <span class="d-block fw-semibold text-dark">{{ t('dashboard.new_contact') }}</span>
                        <span class="d-block fs-7 text-muted">{{ t('dashboard.add_lead') }}</span>
                      </div>
                    </div>
                  </div>
                </router-link>
              </div>
              <div class="col-sm-6">
                <router-link to="/crm/deals" class="text-decoration-none">
                  <div class="card card-border">
                    <div class="card-body d-flex align-items-center gap-3">
                      <div class="avatar avatar-icon avatar-md avatar-soft-violet avatar-rounded">
                        <span class="initial-wrap"><i class="bi bi-kanban fs-5"></i></span>
                      </div>
                      <div>
                        <span class="d-block fw-semibold text-dark">{{ t('dashboard.deals_kanban') }}</span>
                        <span class="d-block fs-7 text-muted">{{ t('dashboard.manage_pipeline') }}</span>
                      </div>
                    </div>
                  </div>
                </router-link>
              </div>
              <div class="col-sm-6">
                <router-link to="/crm/invoices/create" class="text-decoration-none">
                  <div class="card card-border">
                    <div class="card-body d-flex align-items-center gap-3">
                      <div class="avatar avatar-icon avatar-md avatar-soft-success avatar-rounded">
                        <span class="initial-wrap"><i class="bi bi-receipt fs-5"></i></span>
                      </div>
                      <div>
                        <span class="d-block fw-semibold text-dark">{{ t('dashboard.new_invoice') }}</span>
                        <span class="d-block fs-7 text-muted">{{ t('dashboard.create_send') }}</span>
                      </div>
                    </div>
                  </div>
                </router-link>
              </div>
              <div class="col-sm-6">
                <router-link to="/crm/tasks" class="text-decoration-none">
                  <div class="card card-border">
                    <div class="card-body d-flex align-items-center gap-3">
                      <div class="avatar avatar-icon avatar-md avatar-soft-warning avatar-rounded">
                        <span class="initial-wrap"><i class="bi bi-check2-square fs-5"></i></span>
                      </div>
                      <div>
                        <span class="d-block fw-semibold text-dark">{{ t('dashboard.view_tasks') }}</span>
                        <span class="d-block fs-7 text-muted">{{ stats.pending_tasks ?? 0 }} {{ t('dashboard.pending') }}</span>
                      </div>
                    </div>
                  </div>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Subscription -->
      <div class="col-xxl-3 col-lg-4 col-md-5 mb-md-4 mb-3">
        <div class="card card-border mb-0 h-100">
          <div class="card-header card-header-action">
            <h6>{{ t('dashboard.subscription') }}</h6>
            <div class="card-action-wrap">
              <router-link to="/crm/settings/company" class="btn btn-sm btn-outline-light">{{ t('dashboard.manage') }}</router-link>
            </div>
          </div>
          <div class="card-body">
            <div class="media align-items-center mb-3">
              <div class="media-head me-2">
                <div class="avatar avatar-primary avatar-md avatar-rounded">
                  <span class="initial-wrap">{{ (tenant?.name || '?').charAt(0) }}</span>
                </div>
              </div>
              <div class="media-body">
                <div class="fw-bold">{{ tenant?.name }}</div>
                <div class="fs-7 text-muted">{{ tenant?.email }}</div>
              </div>
            </div>
            <div class="separator-full mb-3"></div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fs-7 text-muted fw-medium">{{ t('subscription.plan') }}</span>
              <span class="badge badge-soft-primary">{{ plan?.name }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fs-7 text-muted fw-medium">{{ t('subscription.status') }}</span>
              <span class="badge" :class="statusClass(tenant?.status)">{{ statusLabel(tenant?.status) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fs-7 text-muted fw-medium">{{ t('subscription.billing') }}</span>
              <span class="fs-7 fw-semibold">{{ billingLabel(tenant?.billing_cycle) }}</span>
            </div>
            <div v-if="tenant?.trial_ends_at" class="d-flex justify-content-between align-items-center mb-2">
              <span class="fs-7 text-muted fw-medium">{{ t('subscription.trial') }}</span>
              <span class="fs-7 fw-semibold">{{ formatDate(tenant.trial_ends_at) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fs-7 text-muted">{{ t('subscription.users') }}</span>
              <span class="fs-7">{{ plan?.max_users === 0 ? t('subscription.unlimited') : plan?.max_users }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="fs-7 text-muted">{{ t('subscription.contacts') }}</span>
              <span class="fs-7">{{ plan?.max_contacts === 0 ? t('subscription.unlimited') : plan?.max_contacts }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Revenue Summary -->
    <div class="row">
      <div class="col-md-12 mb-md-4 mb-3">
        <div class="card card-border mb-0">
          <div class="card-header card-header-action">
            <h6>{{ t('dashboard.revenue_summary') }}
              <span class="badge badge-sm badge-light ms-1">${{ formatNum(stats.total_revenue) }}</span>
            </h6>
            <div class="card-action-wrap">
              <router-link to="/crm/reports" class="btn btn-sm btn-outline-light">
                <i class="bi bi-bar-chart-line me-1"></i>{{ t('dashboard.view_reports') }}
              </router-link>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-4">
              <div class="col-sm-3 text-center">
                <div class="avatar avatar-icon avatar-xl avatar-soft-success avatar-rounded mx-auto mb-2">
                  <span class="initial-wrap"><i class="bi bi-currency-dollar fs-3"></i></span>
                </div>
                <div class="fs-4 fw-bold text-dark">${{ formatNum(stats.total_revenue) }}</div>
                <div class="fs-7 text-muted">{{ t('dashboard.total_revenue') }}</div>
              </div>
              <div class="col-sm-3 text-center">
                <div class="avatar avatar-icon avatar-xl avatar-soft-primary avatar-rounded mx-auto mb-2">
                  <span class="initial-wrap"><i class="bi bi-graph-up-arrow fs-3"></i></span>
                </div>
                <div class="fs-4 fw-bold text-dark">${{ formatNum(stats.open_deals_value) }}</div>
                <div class="fs-7 text-muted">{{ t('dashboard.pipeline_value') }}</div>
              </div>
              <div class="col-sm-3 text-center">
                <div class="avatar avatar-icon avatar-xl avatar-soft-warning avatar-rounded mx-auto mb-2">
                  <span class="initial-wrap"><i class="bi bi-award fs-3"></i></span>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ stats.win_rate ?? 0 }}%</div>
                <div class="fs-7 text-muted">{{ t('dashboard.win_rate') }}</div>
              </div>
              <div class="col-sm-3 text-center">
                <div class="avatar avatar-icon avatar-xl avatar-soft-danger avatar-rounded mx-auto mb-2">
                  <span class="initial-wrap"><i class="bi bi-clock fs-3"></i></span>
                </div>
                <div class="fs-4 fw-bold text-dark">{{ stats.pending_tasks ?? 0 }}</div>
                <div class="fs-7 text-muted">{{ t('dashboard.pending_tasks') }}</div>
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
import { trans } from 'laravel-vue-i18n'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const store   = useStore()
const stats   = ref({})
const loading = ref(true)
const locale  = computed(() => store.state.ui.locale)

const tenant  = computed(() => store.getters['auth/tenant'])
const plan    = computed(() => store.getters['auth/plan'])

// Translation helper — reactive on locale change
function t(key) { return trans(key) }

const today = computed(() => {
  return new Date().toLocaleDateString(locale.value === 'ar' ? 'ar-EG' : 'en-GB', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
  })
})

onMounted(async () => {
  try {
    const { data } = await api.get('/reports/dashboard')
    stats.value = data.data
  } catch {}
  loading.value = false
})

function formatNum(n) {
  if (!n) return '0'
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M'
  if (n >= 1_000)     return (n / 1_000).toFixed(1) + 'K'
  return Number(n).toFixed(0)
}

function formatDate(d) {
  return new Date(d).toLocaleDateString(locale.value === 'ar' ? 'ar-EG' : 'en-GB', {
    day: 'numeric', month: 'short', year: 'numeric'
  })
}

function statusClass(s) {
  return {
    'badge-soft-success':   s === 'active',
    'badge-soft-warning':   s === 'trial',
    'badge-soft-danger':    s === 'suspended',
    'badge-soft-secondary': s === 'cancelled',
  }
}

function statusLabel(s) {
  const map = { active: t('subscription.active'), trial: t('subscription.trial_s'), suspended: t('subscription.suspended'), cancelled: t('subscription.cancelled') }
  return map[s] || s
}

function billingLabel(b) {
  const map = { monthly: t('subscription.monthly'), yearly: t('subscription.yearly') }
  return map[b] || b
}
</script>

<style scoped>
/* ─── KPI Cards ──────────────────────────────────────────────────────────── */
.kpi-card {
  background: var(--bs-body-bg, #fff);
  border: 1px solid var(--bs-border-color, #E2E8F0);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  height: 100%;
  transition: box-shadow 0.2s, border-color 0.2s;
}
.kpi-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06); }
.kpi-card-alert { border-color: rgba(239,68,68,.3); }
.kpi-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.kpi-body { flex: 1; min-width: 0; }
.kpi-value { font-size: 1.5rem; font-weight: 700; line-height: 1.1; font-variant-numeric: tabular-nums; }
.kpi-label { font-size: 11.5px; font-weight: 600; color: var(--bs-secondary-color, #64748B); margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.kpi-sub { font-size: 11px; color: var(--bs-secondary-color, #9CA3AF); margin-top: 2px; }
.kpi-sub-success { color: #059669; }
.kpi-sub-danger  { color: #EF4444; }
</style>
