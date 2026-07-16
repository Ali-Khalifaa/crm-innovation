<template>
  <CrmLayout>
    <div class="notif-page">

      <!-- Page header -->
      <div class="d-flex align-items-center justify-content-between mb-4 gap-3 flex-wrap">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">
            {{ isRtl ? 'الإشعارات' : 'Notifications' }}
          </h4>
          <p class="text-muted small mt-1 mb-0">
            {{ isRtl ? `${meta.total ?? 0} إشعار` : `${meta.total ?? 0} notifications` }}
          </p>
        </div>
        <div class="d-flex gap-2">
          <button v-if="unreadCount > 0" class="btn btn-sm btn-outline-primary" @click="readAll">
            <i class="bi bi-check2-all me-1"></i>
            {{ isRtl ? 'تعليم الكل مقروء' : 'Mark all read' }}
          </button>
        </div>
      </div>

      <!-- Filter tabs -->
      <div class="notif-page-tabs mb-3">
        <button :class="['tab-btn', { active: filter === 'all' }]" @click="setFilter('all')">
          {{ isRtl ? 'الكل' : 'All' }}
        </button>
        <button :class="['tab-btn', { active: filter === 'unread' }]" @click="setFilter('unread')">
          {{ isRtl ? 'غير المقروءة' : 'Unread' }}
          <span v-if="unreadCount > 0" class="tab-count">{{ unreadCount }}</span>
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <!-- Empty -->
      <div v-else-if="!notifications.length" class="card card-border text-center py-5">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3" opacity=".3">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <p class="text-muted mb-0">{{ isRtl ? 'لا توجد إشعارات' : 'No notifications' }}</p>
      </div>

      <!-- List -->
      <div v-else class="notif-page-list card card-border">
        <div
          v-for="n in notifications"
          :key="n.id"
          class="notif-page-item"
          :class="{ unread: !n.is_read }"
          @click="handleClick(n)"
        >
          <div class="notif-pi-icon" :class="iconClass(n.type)">
            <span v-html="iconSvg(n.type)"></span>
          </div>
          <div class="notif-pi-body">
            <div class="notif-pi-title">{{ n.title }}</div>
            <div v-if="n.body" class="notif-pi-sub">{{ n.body }}</div>
            <div class="notif-pi-time">{{ n.time_ago }}</div>
          </div>
          <div class="notif-pi-actions">
            <button
              v-if="!n.is_read"
              class="btn btn-xs btn-outline-primary"
              @click.stop="markRead(n.id)"
              :title="isRtl ? 'تعليم مقروء' : 'Mark read'"
            >
              <i class="bi bi-check2"></i>
            </button>
            <button
              class="btn btn-xs btn-outline-danger"
              @click.stop="remove(n.id)"
              :title="isRtl ? 'حذف' : 'Delete'"
            >
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="d-flex justify-content-center mt-3 gap-2">
        <button class="btn btn-sm btn-outline-secondary" :disabled="page === 1" @click="page--; fetch()">
          <i class="bi bi-chevron-left"></i>
        </button>
        <span class="btn btn-sm btn-light disabled">{{ page }} / {{ meta.last_page }}</span>
        <button class="btn btn-sm btn-outline-secondary" :disabled="page >= meta.last_page" @click="page++; fetch()">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>

    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const store  = useStore()
const router = useRouter()
const isRtl  = computed(() => store.state.ui?.locale === 'ar')
const unreadCount = computed(() => store.getters['notifications/unreadCount'])

const notifications = ref([])
const meta    = ref({})
const loading = ref(true)
const filter  = ref('all')
const page    = ref(1)

async function fetch() {
  loading.value = true
  try {
    const { data } = await api.get('/notifications', {
      params: { filter: filter.value, page: page.value }
    })
    notifications.value = data.data ?? []
    meta.value = data.meta ?? {}
  } finally {
    loading.value = false
  }
}

function setFilter(f) {
  filter.value = f
  page.value = 1
  fetch()
}

async function readAll() {
  await store.dispatch('notifications/markAllRead')
  notifications.value.forEach(n => (n.is_read = true))
}

async function markRead(id) {
  await store.dispatch('notifications/markRead', id)
  const n = notifications.value.find(n => n.id === id)
  if (n) n.is_read = true
}

async function remove(id) {
  await store.dispatch('notifications/remove', id)
  notifications.value = notifications.value.filter(n => n.id !== id)
}

function handleClick(n) {
  if (!n.is_read) markRead(n.id)
  if (n.data?.url) router.push(n.data.url)
}

function iconClass(type) {
  if (type === 'deal_won') return 'icon-won'
  if (type === 'deal_lost') return 'icon-lost'
  if (type === 'task_assigned' || type === 'task_due') return 'icon-task'
  if (type === 'deal_assigned') return 'icon-deal'
  return 'icon-default'
}

function iconSvg(type) {
  if (type === 'deal_won') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>'
  if (type === 'deal_lost') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
  if (type === 'task_assigned' || type === 'task_due') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>'
  if (type === 'deal_assigned') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>'
  return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
}

onMounted(fetch)
</script>

<style scoped>
.notif-page-tabs { display: flex; gap: 4px; border-bottom: 1px solid var(--bs-border-color); }
.tab-btn {
  display: flex; align-items: center; gap: 6px;
  background: none; border: none; cursor: pointer;
  font-size: .85rem; font-weight: 600;
  color: var(--bs-secondary-color);
  padding: 8px 12px; border-bottom: 2px solid transparent;
  transition: color .15s, border-color .15s;
}
.tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; }
.tab-count {
  background: #ef4444; color: #fff;
  font-size: 10px; font-weight: 700;
  border-radius: 99px; padding: 1px 5px;
}

.notif-page-list { overflow: hidden; }

.notif-page-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 14px 20px;
  border-bottom: 1px solid var(--bs-border-color);
  cursor: pointer; transition: background .12s;
}
.notif-page-item:last-child { border-bottom: none; }
.notif-page-item:hover { background: var(--bs-light); }
.notif-page-item.unread { background: #eff6ff; }
.notif-page-item.unread:hover { background: #dbeafe; }

.notif-pi-icon {
  width: 38px; height: 38px; flex-shrink: 0;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
}
.icon-won    { background: #d1fae5; color: #059669; }
.icon-lost   { background: #fee2e2; color: #dc2626; }
.icon-task   { background: #ede9fe; color: #7c3aed; }
.icon-deal   { background: #dbeafe; color: #2563eb; }
.icon-default { background: #f1f5f9; color: #64748b; }

.notif-pi-body  { flex: 1; min-width: 0; }
.notif-pi-title { font-size: .85rem; font-weight: 600; margin-bottom: 2px; }
.notif-pi-sub   { font-size: .78rem; color: var(--bs-secondary-color); margin-bottom: 4px; }
.notif-pi-time  { font-size: .72rem; color: var(--bs-secondary-color); }

.notif-pi-actions { display: flex; gap: 6px; flex-shrink: 0; align-self: center; }
.btn-xs { padding: 2px 7px; font-size: .72rem; }
</style>
