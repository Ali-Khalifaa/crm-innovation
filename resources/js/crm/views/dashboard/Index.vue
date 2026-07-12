<template>
  <CrmLayout>

    <!-- KPI Stats Row -->
    <div class="row mb-md-4 mb-3">
      <div class="col-md-12">
        <div class="card card-border mb-0">
          <div class="card-header card-header-action">
            <h6>{{ t('dashboard.crm_overview') }}</h6>
            <div class="card-action-wrap">
              <span class="text-muted fs-7">{{ today }}</span>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="d-flex justify-content-center py-4">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
            <template v-else>
              <div class="row">
                <div class="col-xxl-3 col-sm-6 mb-xxl-0 mb-3">
                  <span class="d-block fw-medium fs-7">{{ t('dashboard.total_contacts') }}</span>
                  <div class="d-flex align-items-center gap-2">
                    <span class="d-block fs-4 fw-medium text-dark mb-0">{{ stats.total_contacts ?? 0 }}</span>
                    <span class="badge badge-sm badge-soft-success"><i class="bi bi-people"></i></span>
                  </div>
                </div>
                <div class="col-xxl-3 col-sm-6 mb-xxl-0 mb-3">
                  <span class="d-block fw-medium fs-7">{{ t('dashboard.total_deals') }}</span>
                  <div class="d-flex align-items-center gap-2">
                    <span class="d-block fs-4 fw-medium text-dark mb-0">{{ stats.total_deals ?? 0 }}</span>
                    <span class="badge badge-sm badge-soft-primary">{{ t('dashboard.open') }}</span>
                  </div>
                </div>
                <div class="col-xxl-3 col-sm-6 mb-xxl-0 mb-3">
                  <span class="d-block fw-medium fs-7">{{ t('dashboard.pipeline_value') }}</span>
                  <div class="d-flex align-items-center gap-2">
                    <span class="d-block fs-4 fw-medium text-dark mb-0">${{ formatNum(stats.open_deals_value) }}</span>
                    <span class="badge badge-sm badge-soft-warning"><i class="bi bi-arrow-up"></i></span>
                  </div>
                </div>
                <div class="col-xxl-3 col-sm-6">
                  <span class="d-block fw-medium fs-7">{{ t('dashboard.win_rate') }}</span>
                  <div class="d-flex align-items-center gap-2">
                    <span class="d-block fs-4 fw-medium text-dark mb-0">{{ stats.win_rate ?? 0 }}%</span>
                    <span class="badge badge-sm badge-soft-success"><i class="bi bi-arrow-up"></i> {{ t('dashboard.won') }}</span>
                  </div>
                </div>
              </div>
            </template>
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
