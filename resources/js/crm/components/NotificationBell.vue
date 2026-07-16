<template>
  <div class="notif-bell-wrap" ref="wrapRef">

    <!-- Bell button -->
    <button class="notif-bell-btn" @click="toggle" :aria-label="isRtl ? 'الإشعارات' : 'Notifications'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
      </svg>
      <span v-if="unreadCount > 0" class="notif-badge">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
    </button>

    <!-- Dropdown -->
    <Transition name="notif-drop">
      <div v-if="open" class="notif-dropdown" :class="{ rtl: isRtl }">

        <!-- Header -->
        <div class="notif-header">
          <span class="notif-title">{{ isRtl ? 'الإشعارات' : 'Notifications' }}</span>
          <button v-if="unreadCount > 0" class="notif-read-all" @click="readAll">
            {{ isRtl ? 'تعليم الكل مقروء' : 'Mark all read' }}
          </button>
        </div>

        <!-- Filter tabs -->
        <div class="notif-tabs">
          <button :class="['notif-tab', { active: filter === 'all' }]" @click="filter = 'all'">
            {{ isRtl ? 'الكل' : 'All' }}
          </button>
          <button :class="['notif-tab', { active: filter === 'unread' }]" @click="filter = 'unread'">
            {{ isRtl ? 'غير المقروءة' : 'Unread' }}
            <span v-if="unreadCount > 0" class="notif-tab-badge">{{ unreadCount }}</span>
          </button>
        </div>

        <!-- List -->
        <div class="notif-list" v-if="filteredItems.length > 0">
          <div
            v-for="n in filteredItems"
            :key="n.id"
            class="notif-item"
            :class="{ unread: !n.is_read }"
            @click="handleClick(n)"
          >
            <div class="notif-icon-wrap" :class="iconClass(n.type)">
              <span v-html="iconSvg(n.type)"></span>
            </div>
            <div class="notif-content">
              <div class="notif-item-title">{{ n.title }}</div>
              <div v-if="n.body" class="notif-item-body">{{ n.body }}</div>
              <div class="notif-item-time">{{ n.time_ago }}</div>
            </div>
            <button class="notif-dismiss" @click.stop="dismissItem(n.id)" :title="isRtl ? 'حذف' : 'Dismiss'">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Empty -->
        <div v-else class="notif-empty">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".3">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          <p>{{ isRtl ? 'لا توجد إشعارات' : 'No notifications' }}</p>
        </div>

        <!-- Footer -->
        <div class="notif-footer">
          <router-link to="/crm/notifications" @click="open = false">
            {{ isRtl ? 'عرض جميع الإشعارات' : 'View all notifications' }}
          </router-link>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import api from '../composables/useApi.js'

const store  = useStore()
const router = useRouter()
const isRtl  = computed(() => store.state.ui?.locale === 'ar')
const open   = ref(false)
const filter = ref('all')
const wrapRef = ref(null)

const unreadCount = computed(() => store.getters['notifications/unreadCount'])
const allItems    = computed(() => store.state.notifications.notifications)
const filteredItems = computed(() =>
  filter.value === 'unread' ? allItems.value.filter(n => !n.is_read) : allItems.value
)

async function toggle() {
  open.value = !open.value
  if (open.value) {
    await store.dispatch('notifications/fetchRecent')
  }
}

function readAll() {
  store.dispatch('notifications/markAllRead')
}

function dismissItem(id) {
  store.dispatch('notifications/remove', id)
}

function handleClick(n) {
  if (!n.is_read) store.dispatch('notifications/markRead', n.id)
  if (n.data?.url) {
    router.push(n.data.url)
    open.value = false
  }
}

function iconClass(type) {
  if (type === 'deal_won') return 'icon-won'
  if (type === 'deal_lost') return 'icon-lost'
  if (type === 'task_assigned' || type === 'task_due') return 'icon-task'
  if (type === 'deal_assigned') return 'icon-deal'
  return 'icon-default'
}

function iconSvg(type) {
  if (type === 'deal_won') return '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>'
  if (type === 'deal_lost') return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
  if (type === 'task_assigned' || type === 'task_due') return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>'
  if (type === 'deal_assigned') return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>'
  return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
}

// Close on outside click
function onOutsideClick(e) {
  if (wrapRef.value && !wrapRef.value.contains(e.target)) {
    open.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', onOutsideClick)
  store.dispatch('notifications/startPolling')
})
onUnmounted(() => {
  document.removeEventListener('click', onOutsideClick)
})
</script>

<style scoped>
.notif-bell-wrap { position: relative; }

.notif-bell-btn {
  position: relative;
  width: 38px; height: 38px;
  display: flex; align-items: center; justify-content: center;
  border: none; background: transparent;
  border-radius: 10px; cursor: pointer;
  color: var(--crm-text-muted, #64748b);
  transition: background .15s, color .15s;
}
.notif-bell-btn:hover { background: var(--crm-bg, #f8fafc); color: var(--crm-text, #1e293b); }

.notif-badge {
  position: absolute; top: 4px; right: 4px;
  min-width: 16px; height: 16px;
  background: #ef4444; color: #fff;
  font-size: 9px; font-weight: 700;
  border-radius: 99px;
  display: flex; align-items: center; justify-content: center;
  padding: 0 3px;
  border: 2px solid var(--crm-surface, #fff);
}

.notif-dropdown {
  position: absolute; top: calc(100% + 8px); right: 0;
  width: 340px; max-height: 480px;
  background: var(--crm-surface, #fff);
  border: 1px solid var(--crm-border, #e2e8f0);
  border-radius: 14px;
  box-shadow: 0 8px 30px rgba(0,0,0,.12);
  display: flex; flex-direction: column;
  z-index: 1200;
  overflow: hidden;
}
.notif-dropdown.rtl { right: auto; left: 0; }

.notif-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px 0;
}
.notif-title { font-weight: 700; font-size: .9rem; }
.notif-read-all {
  background: none; border: none; cursor: pointer;
  font-size: .75rem; color: #2563eb; font-weight: 600;
  padding: 0;
}
.notif-read-all:hover { text-decoration: underline; }

.notif-tabs {
  display: flex; gap: 4px;
  padding: 10px 16px 0;
  border-bottom: 1px solid var(--crm-border, #e2e8f0);
}
.notif-tab {
  display: flex; align-items: center; gap: 5px;
  background: none; border: none; cursor: pointer;
  font-size: .8rem; font-weight: 600;
  color: var(--crm-text-muted, #64748b);
  padding: 0 4px 10px;
  border-bottom: 2px solid transparent;
  transition: color .15s, border-color .15s;
}
.notif-tab.active { color: #2563eb; border-bottom-color: #2563eb; }
.notif-tab-badge {
  background: #ef4444; color: #fff;
  font-size: 10px; font-weight: 700;
  border-radius: 99px; padding: 1px 5px;
}

.notif-list { flex: 1; overflow-y: auto; }

.notif-item {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 11px 16px;
  cursor: pointer;
  border-bottom: 1px solid var(--crm-border, #e2e8f0);
  transition: background .12s;
  position: relative;
}
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: var(--crm-bg, #f8fafc); }
.notif-item.unread { background: #eff6ff; }
.notif-item.unread:hover { background: #dbeafe; }

.notif-icon-wrap {
  width: 32px; height: 32px; flex-shrink: 0;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
}
.icon-won    { background: #d1fae5; color: #059669; }
.icon-lost   { background: #fee2e2; color: #dc2626; }
.icon-task   { background: #ede9fe; color: #7c3aed; }
.icon-deal   { background: #dbeafe; color: #2563eb; }
.icon-default { background: #f1f5f9; color: #64748b; }

.notif-content { flex: 1; min-width: 0; }
.notif-item-title { font-size: .8rem; font-weight: 600; margin-bottom: 2px; }
.notif-item-body  { font-size: .74rem; color: var(--crm-text-muted, #64748b); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.notif-item-time  { font-size: .7rem; color: var(--crm-text-muted, #64748b); }

.notif-dismiss {
  background: none; border: none; cursor: pointer;
  width: 20px; height: 20px;
  color: var(--crm-text-muted, #64748b);
  border-radius: 4px;
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity .12s;
  flex-shrink: 0;
}
.notif-item:hover .notif-dismiss { opacity: 1; }
.notif-dismiss:hover { background: #fee2e2; color: #dc2626; }

.notif-empty {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 32px 16px; gap: 8px;
  color: var(--crm-text-muted, #64748b); font-size: .82rem;
}
.notif-empty p { margin: 0; }

.notif-footer {
  border-top: 1px solid var(--crm-border, #e2e8f0);
  padding: 10px 16px;
  text-align: center;
}
.notif-footer a {
  font-size: .78rem; color: #2563eb; font-weight: 600;
  text-decoration: none;
}
.notif-footer a:hover { text-decoration: underline; }

/* Transition */
.notif-drop-enter-active, .notif-drop-leave-active { transition: opacity .15s, transform .15s; }
.notif-drop-enter-from, .notif-drop-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
