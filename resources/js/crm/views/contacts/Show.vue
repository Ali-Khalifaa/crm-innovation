<template>
  <CrmLayout>
    <div class="contact-show">
      <!-- Loading -->
      <div v-if="loading" class="show-loading">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <template v-else-if="contact">
        <!-- Hero -->
        <div class="contact-hero">
          <div class="hero-left">
            <button class="btn-back" @click="$router.back()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
              {{ isRtl ? 'رجوع' : 'Back' }}
            </button>
            <div class="contact-avatar-lg" :style="{ background: avatarColor(contact.full_name) }">
              {{ initials(contact.full_name) }}
            </div>
            <div class="hero-info">
              <h1 class="contact-name">{{ contact.full_name }}</h1>
              <div class="hero-meta">
                <span v-if="contact.company_record" class="meta-item">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  {{ contact.company_record.name }}
                </span>
                <span v-else-if="contact.company" class="meta-item">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  {{ contact.company }}
                </span>
                <span v-if="contact.email" class="meta-item">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                  {{ contact.email }}
                </span>
                <span v-if="contact.phone" class="meta-item">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12 19.79 19.79 0 0 1 1.93 3.47 2 2 0 0 1 3.93 1.28h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6.29 6.29l1.28-1.28a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  {{ contact.phone }}
                </span>
              </div>
              <div class="hero-badges">
                <span class="badge-status" :class="'status-' + contact.status">{{ statusLabel(contact.status) }}</span>
                <span v-if="contact.lead_source" class="badge-source" :class="'source-' + contact.lead_source">{{ sourceLabel(contact.lead_source) }}</span>
                <span class="badge-date">{{ isRtl ? 'عضو منذ' : 'Since' }} {{ formatDate(contact.created_at) }}</span>
              </div>
            </div>
          </div>
          <div class="hero-actions">
            <button class="btn btn-outline-secondary btn-sm" @click="openEdit">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              {{ isRtl ? 'تعديل' : 'Edit' }}
            </button>
            <button class="btn btn-outline-danger btn-sm" @click="confirmDelete">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              {{ isRtl ? 'حذف' : 'Delete' }}
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="show-body">

          <!-- Left Sidebar -->
          <aside class="show-sidebar">
            <!-- Contact Details -->
            <div class="info-card">
              <h3 class="card-section-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ isRtl ? 'بيانات الاتصال' : 'Contact Info' }}
              </h3>
              <div class="info-rows">
                <div v-if="contact.email" class="info-row">
                  <span class="info-label">{{ isRtl ? 'البريد' : 'Email' }}</span>
                  <a :href="'mailto:' + contact.email" class="info-value link">{{ contact.email }}</a>
                </div>
                <div v-if="contact.phone" class="info-row">
                  <span class="info-label">{{ isRtl ? 'الهاتف' : 'Phone' }}</span>
                  <a :href="'tel:' + contact.phone" class="info-value link">{{ contact.phone }}</a>
                </div>
                <div v-if="contact.address" class="info-row">
                  <span class="info-label">{{ isRtl ? 'العنوان' : 'Address' }}</span>
                  <span class="info-value">{{ contact.address }}</span>
                </div>
                <div v-if="contact.lead_source" class="info-row">
                  <span class="info-label">{{ isRtl ? 'المصدر' : 'Source' }}</span>
                  <span class="info-value"><span class="source-pill" :class="'source-' + contact.lead_source">{{ sourceLabel(contact.lead_source) }}</span></span>
                </div>
                <div v-if="contact.owner" class="info-row">
                  <span class="info-label">{{ isRtl ? 'المسؤول' : 'Owner' }}</span>
                  <span class="info-value">{{ contact.owner.name }}</span>
                </div>
                <div v-if="contact.notes" class="info-row notes-row">
                  <span class="info-label">{{ isRtl ? 'ملاحظات' : 'Notes' }}</span>
                  <p class="info-value notes-text">{{ contact.notes }}</p>
                </div>
              </div>
            </div>

            <!-- Linked Company -->
            <div v-if="contact.company_record" class="info-card mt-3">
              <h3 class="card-section-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                {{ isRtl ? 'الشركة المرتبطة' : 'Linked Company' }}
              </h3>
              <div class="company-profile">
                <div class="company-avatar" :style="{ background: avatarColor(contact.company_record.name) }">
                  {{ initials(contact.company_record.name) }}
                </div>
                <div>
                  <div class="company-name">{{ contact.company_record.name }}</div>
                  <div v-if="contact.company_record.industry" class="company-industry">{{ contact.company_record.industry }}</div>
                </div>
              </div>
              <div class="info-rows">
                <div v-if="contact.company_record.website" class="info-row">
                  <span class="info-label">{{ isRtl ? 'الموقع' : 'Website' }}</span>
                  <a :href="contact.company_record.website" target="_blank" class="info-value link truncate">{{ contact.company_record.website }}</a>
                </div>
                <div v-if="contact.company_record.phone" class="info-row">
                  <span class="info-label">{{ isRtl ? 'الهاتف' : 'Phone' }}</span>
                  <span class="info-value">{{ contact.company_record.phone }}</span>
                </div>
              </div>
            </div>
          </aside>

          <!-- Main: Deals + Tasks -->
          <main class="show-main">
            <!-- Related Deals -->
            <div class="section-card">
              <h3 class="card-section-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                {{ isRtl ? 'الصفقات المرتبطة' : 'Related Deals' }}
                <span class="count-badge">{{ (contact.deals || []).length }}</span>
              </h3>
              <div v-if="contact.deals && contact.deals.length" class="deals-list">
                <div v-for="deal in contact.deals" :key="deal.id" class="deal-row">
                  <div class="deal-stage-dot" :style="{ background: deal.stage?.color || '#6b7280' }"></div>
                  <div class="deal-info">
                    <div class="deal-title">{{ deal.title }}</div>
                    <div class="deal-meta">
                      <span v-if="deal.stage">{{ deal.stage.name }}</span>
                      <span v-if="deal.expected_close_date">{{ formatDate(deal.expected_close_date) }}</span>
                    </div>
                  </div>
                  <div class="deal-right">
                    <div class="deal-value">${{ formatNumber(deal.value) }}</div>
                    <span class="deal-status" :class="'ds-' + deal.status">{{ deal.status }}</span>
                  </div>
                </div>
              </div>
              <div v-else class="empty-section">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <p>{{ isRtl ? 'لا توجد صفقات مرتبطة' : 'No related deals' }}</p>
              </div>
            </div>

            <!-- Related Tasks -->
            <div class="section-card mt-3">
              <h3 class="card-section-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                {{ isRtl ? 'المهام المرتبطة' : 'Related Tasks' }}
                <span class="count-badge">{{ (contact.tasks || []).length }}</span>
              </h3>
              <div v-if="contact.tasks && contact.tasks.length" class="tasks-list">
                <div v-for="task in contact.tasks" :key="task.id" class="task-row">
                  <div class="task-priority-dot" :class="'p-' + task.priority"></div>
                  <div class="task-info">
                    <div class="task-title" :class="{ 'task-done': task.status === 'completed' }">{{ task.title }}</div>
                    <div class="task-meta">
                      <span v-if="task.due_date" :class="{ 'text-danger': isOverdue(task.due_date, task.status) }">{{ formatDate(task.due_date) }}</span>
                      <span v-if="task.assignee">{{ task.assignee.name }}</span>
                    </div>
                  </div>
                  <span class="task-status-badge" :class="'ts-' + task.status">{{ taskStatusLabel(task.status) }}</span>
                </div>
              </div>
              <div v-else class="empty-section">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                <p>{{ isRtl ? 'لا توجد مهام مرتبطة' : 'No related tasks' }}</p>
              </div>
            </div>
          </main>

          <!-- Right: Activity -->
          <aside class="show-activity">
            <div class="info-card">
              <h3 class="card-section-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                {{ isRtl ? 'سجل النشاط' : 'Activity' }}
              </h3>
              <div v-if="activitiesLoading" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary"></div>
              </div>
              <div v-else-if="activities.length" class="timeline">
                <div v-for="(item, idx) in activities" :key="idx" class="timeline-item">
                  <div class="timeline-dot"></div>
                  <div class="timeline-content">
                    <div class="timeline-action">{{ item.description }}</div>
                    <div class="timeline-meta">
                      <span v-if="item.user" class="timeline-user">{{ item.user }}</span>
                      <span class="timeline-time">{{ timeAgo(item.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="empty-section small">
                <p>{{ isRtl ? 'لا يوجد نشاط بعد' : 'No activity yet' }}</p>
              </div>
            </div>
          </aside>

        </div>
      </template>

      <div v-else class="show-loading">
        <p class="text-muted">{{ isRtl ? 'جهة الاتصال غير موجودة' : 'Contact not found' }}</p>
      </div>

      <!-- Delete Modal -->
      <div class="modal fade" id="deleteContactModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body text-center py-4">
              <div class="delete-icon-wrap">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              </div>
              <h6 class="mt-3 mb-1">{{ isRtl ? 'حذف جهة الاتصال' : 'Delete Contact' }}</h6>
              <p class="text-muted small mb-3">{{ isRtl ? 'هل أنت متأكد؟ لا يمكن التراجع عن هذا.' : 'Are you sure? This cannot be undone.' }}</p>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ isRtl ? 'إلغاء' : 'Cancel' }}</button>
                <button class="btn btn-sm btn-danger" :disabled="deleting" @click="doDelete">
                  <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
                  {{ isRtl ? 'حذف' : 'Delete' }}
                </button>
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
import { useRouter, useRoute } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const store  = useStore()
const router = useRouter()
const route  = useRoute()

const contact           = ref(null)
const activities        = ref([])
const loading           = ref(true)
const activitiesLoading = ref(false)
const deleting          = ref(false)

const isRtl = computed(() => store.state.ui?.locale === 'ar')

const COLORS = ['#2563EB','#7C3AED','#DB2777','#D97706','#059669','#DC2626','#0891B2']
function avatarColor(name) {
  if (!name) return '#2563EB'
  let h = 0
  for (let i = 0; i < name.length; i++) h += name.charCodeAt(i)
  return COLORS[h % COLORS.length]
}
function initials(name) {
  if (!name) return '?'
  const p = name.trim().split(' ')
  return (p.length >= 2 ? p[0][0] + p[1][0] : p[0].slice(0, 2)).toUpperCase()
}
function formatDate(val) {
  if (!val) return ''
  return new Date(val).toLocaleDateString(isRtl.value ? 'ar-SA' : 'en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}
function formatNumber(n) {
  return n ? Number(n).toLocaleString() : '0'
}
function timeAgo(val) {
  if (!val) return ''
  const m = Math.floor((Date.now() - new Date(val).getTime()) / 60000)
  if (m < 1) return isRtl.value ? 'الآن' : 'just now'
  if (m < 60) return isRtl.value ? `منذ ${m} د` : `${m}m ago`
  const h = Math.floor(m / 60)
  if (h < 24) return isRtl.value ? `منذ ${h} س` : `${h}h ago`
  return formatDate(val)
}
function isOverdue(date, status) {
  return status !== 'completed' && date && new Date(date) < new Date()
}
function statusLabel(s) {
  const map = { lead: isRtl.value ? 'محتمل' : 'Lead', customer: isRtl.value ? 'عميل' : 'Customer', inactive: isRtl.value ? 'غير نشط' : 'Inactive' }
  return map[s] || s
}
function sourceLabel(s) {
  const map = { website: isRtl.value ? 'الموقع' : 'Website', referral: isRtl.value ? 'إحالة' : 'Referral', social: isRtl.value ? 'سوشيال' : 'Social', cold: isRtl.value ? 'مباشر' : 'Cold', event: isRtl.value ? 'فعالية' : 'Event', other: isRtl.value ? 'أخرى' : 'Other' }
  return map[s] || s
}
function taskStatusLabel(s) {
  const map = { pending: isRtl.value ? 'معلقة' : 'Pending', in_progress: isRtl.value ? 'جارية' : 'In Progress', completed: isRtl.value ? 'مكتملة' : 'Done' }
  return map[s] || s
}

async function fetchContact() {
  loading.value = true
  try {
    const { data } = await api.get(`/contacts/${route.params.id}`)
    contact.value = data.data
  } catch {
    contact.value = null
  } finally {
    loading.value = false
  }
}
async function fetchActivities() {
  activitiesLoading.value = true
  try {
    const { data } = await api.get(`/contacts/${route.params.id}/activities`)
    activities.value = data.data || []
  } catch {
    activities.value = []
  } finally {
    activitiesLoading.value = false
  }
}

function openEdit() {
  router.push({ name: 'crm.contacts.edit', params: { id: contact.value.id } })
}
function confirmDelete() {
  window.bootstrap?.Modal.getOrCreateInstance(document.getElementById('deleteContactModal'))?.show()
}
async function doDelete() {
  deleting.value = true
  try {
    await api.delete(`/contacts/${contact.value.id}`)
    window.bootstrap?.Modal.getInstance(document.getElementById('deleteContactModal'))?.hide()
    router.push({ name: 'crm.contacts' })
  } catch {
    deleting.value = false
  }
}

onMounted(() => {
  fetchContact()
  fetchActivities()
})
</script>

<style scoped>
.contact-show { padding: 0; }
.show-loading { display: flex; align-items: center; justify-content: center; min-height: 300px; }

/* ─── Hero ──────────────────────────────────────────────────────────────── */
.contact-hero {
  background: var(--bs-body-bg, #fff);
  border-bottom: 1px solid var(--crm-border, #E2E8F0);
  padding: 20px 24px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}
.hero-left { display: flex; align-items: flex-start; gap: 14px; flex: 1; min-width: 0; }
.btn-back {
  display: flex; align-items: center; gap: 4px;
  background: none; border: none; color: var(--crm-text-muted, #64748B);
  font-size: 13px; cursor: pointer; padding: 4px 0; white-space: nowrap; margin-top: 4px; flex-shrink: 0;
}
.btn-back:hover { color: var(--crm-primary, #2563EB); }
.contact-avatar-lg {
  width: 60px; height: 60px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 19px; font-weight: 700; flex-shrink: 0;
}
.hero-info { flex: 1; min-width: 0; }
.contact-name { font-size: 20px; font-weight: 700; margin: 0 0 6px; }
.hero-meta {
  display: flex; flex-wrap: wrap; gap: 10px; align-items: center;
  font-size: 12.5px; color: var(--crm-text-muted, #64748B); margin-bottom: 8px;
}
.meta-item { display: flex; align-items: center; gap: 4px; }
.hero-badges { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
.hero-actions { display: flex; gap: 8px; flex-shrink: 0; padding-top: 2px; }

.badge-status { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.status-lead     { background: #EFF6FF; color: #2563EB; }
.status-customer { background: #ECFDF5; color: #059669; }
.status-inactive { background: #F9FAFB; color: #9CA3AF; }

.badge-source { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.source-website  { background: #EDE9FE; color: #7C3AED; }
.source-referral { background: #FEF3C7; color: #D97706; }
.source-social   { background: #FCE7F3; color: #DB2777; }
.source-cold     { background: #F0FDF4; color: #16A34A; }
.source-event    { background: #FFF7ED; color: #EA580C; }
.source-other    { background: #F1F5F9; color: #475569; }
.badge-date { font-size: 11px; color: var(--crm-text-muted, #64748B); }

/* ─── Body grid ─────────────────────────────────────────────────────────── */
.show-body {
  display: grid;
  grid-template-columns: 270px 1fr 250px;
  gap: 18px;
  padding: 20px 24px;
  align-items: start;
}
@media (max-width: 1200px) { .show-body { grid-template-columns: 260px 1fr; } .show-activity { grid-column: 1 / -1; } }
@media (max-width: 768px)  { .show-body { grid-template-columns: 1fr; } .contact-hero { flex-direction: column; } }

/* ─── Cards ─────────────────────────────────────────────────────────────── */
.info-card, .section-card {
  background: var(--bs-body-bg, #fff);
  border: 1px solid var(--crm-border, #E2E8F0);
  border-radius: 12px;
  padding: 16px 18px;
}
.mt-3 { margin-top: 14px; }

.card-section-title {
  font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px;
  color: var(--crm-text-muted, #64748B); margin: 0 0 12px;
  display: flex; align-items: center; gap: 6px;
}
.count-badge {
  background: var(--crm-primary, #2563EB); color: #fff;
  border-radius: 10px; font-size: 10px; padding: 1px 6px;
  text-transform: none; letter-spacing: 0; font-weight: 700;
}

/* ─── Info rows ─────────────────────────────────────────────────────────── */
.info-rows { display: flex; flex-direction: column; }
.info-row {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 10px; padding: 7px 0;
  border-bottom: 1px solid var(--crm-border, #E2E8F0); font-size: 13px;
}
.info-row:last-child { border-bottom: none; }
.info-label { color: var(--crm-text-muted, #64748B); flex-shrink: 0; font-size: 12px; }
.info-value { color: var(--crm-text, #1E293B); text-align: end; word-break: break-word; }
.info-value.link { color: var(--crm-primary, #2563EB); text-decoration: none; }
.info-value.link:hover { text-decoration: underline; }
.info-value.truncate { max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.notes-row { align-items: flex-start; }
.notes-text { margin: 0; font-size: 12px; line-height: 1.5; white-space: pre-wrap; }

.source-pill { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }

/* ─── Company ────────────────────────────────────────────────────────────── */
.company-profile { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.company-avatar {
  width: 36px; height: 36px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.company-name { font-size: 13px; font-weight: 600; }
.company-industry { font-size: 11px; color: var(--crm-text-muted, #64748B); }

/* ─── Deals ──────────────────────────────────────────────────────────────── */
.deals-list { display: flex; flex-direction: column; gap: 1px; }
.deal-row { display: flex; align-items: center; gap: 10px; padding: 9px 6px; border-radius: 8px; }
.deal-row:hover { background: var(--crm-bg, #F8FAFC); }
.deal-stage-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.deal-info { flex: 1; min-width: 0; }
.deal-title { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.deal-meta { display: flex; gap: 8px; font-size: 11px; color: var(--crm-text-muted, #64748B); margin-top: 2px; }
.deal-right { display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
.deal-value { font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums; }
.deal-status { font-size: 10px; font-weight: 600; padding: 1px 7px; border-radius: 10px; text-transform: uppercase; }
.ds-open { background: #EFF6FF; color: #2563EB; }
.ds-won  { background: #ECFDF5; color: #059669; }
.ds-lost { background: #FEF2F2; color: #EF4444; }

/* ─── Tasks ──────────────────────────────────────────────────────────────── */
.tasks-list { display: flex; flex-direction: column; gap: 1px; }
.task-row { display: flex; align-items: center; gap: 10px; padding: 9px 6px; border-radius: 8px; }
.task-priority-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.p-high { background: #EF4444; } .p-medium { background: #F59E0B; } .p-low { background: #10B981; }
.task-info { flex: 1; min-width: 0; }
.task-title { font-size: 13px; font-weight: 500; }
.task-done { text-decoration: line-through; opacity: 0.5; }
.task-meta { display: flex; gap: 8px; font-size: 11px; color: var(--crm-text-muted, #64748B); margin-top: 2px; }
.task-status-badge { font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 10px; white-space: nowrap; }
.ts-pending { background: #F1F5F9; color: #475569; }
.ts-in_progress { background: #EFF6FF; color: #2563EB; }
.ts-completed { background: #ECFDF5; color: #059669; }

/* ─── Timeline ───────────────────────────────────────────────────────────── */
.timeline { position: relative; padding-inline-start: 18px; }
.timeline::before { content: ''; position: absolute; inset-inline-start: 7px; top: 0; bottom: 0; width: 2px; background: var(--crm-border, #E2E8F0); }
.timeline-item { position: relative; padding: 0 0 12px 12px; display: flex; gap: 8px; }
.timeline-dot {
  position: absolute; inset-inline-start: -18px; top: 4px;
  width: 10px; height: 10px; border-radius: 50%;
  background: var(--crm-primary, #2563EB); border: 2px solid white;
  box-shadow: 0 0 0 2px var(--crm-primary, #2563EB); flex-shrink: 0;
}
.timeline-content { flex: 1; }
.timeline-action { font-size: 12px; font-weight: 500; line-height: 1.4; }
.timeline-meta { display: flex; gap: 6px; font-size: 11px; color: var(--crm-text-muted, #64748B); margin-top: 2px; }
.timeline-user { font-weight: 500; }

/* ─── Empty ──────────────────────────────────────────────────────────────── */
.empty-section { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 28px 16px; color: var(--crm-text-muted, #64748B); text-align: center; }
.empty-section p { margin: 6px 0 0; font-size: 13px; }
.empty-section.small { padding: 16px; }

/* ─── Delete modal ───────────────────────────────────────────────────────── */
.delete-icon-wrap { width: 52px; height: 52px; border-radius: 50%; background: #FEF2F2; display: flex; align-items: center; justify-content: center; margin: 0 auto; }

/* ─── Dark mode ──────────────────────────────────────────────────────────── */
@media (prefers-color-scheme: dark) {
  .contact-hero, .info-card, .section-card { background: #1e2433; border-color: #2d3748; }
  .deal-row:hover { background: #252d3e; }
}
</style>
