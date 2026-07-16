<template>
  <CrmLayout>
    <div>
      <!-- Page Header -->
      <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">{{ isRtl ? 'تقويم المهام' : 'Task Calendar' }}</h4>
          <p class="text-muted small mt-1 mb-0">{{ isRtl ? 'عرض المهام حسب التاريخ' : 'Tasks organized by date' }}</p>
        </div>
        <div class="d-flex gap-2">
          <router-link to="/crm/tasks" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i>{{ isRtl ? 'عرض القائمة' : 'List View' }}
          </router-link>
          <button class="btn btn-sm btn-outline-secondary" @click="prevMonth">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button class="btn btn-sm btn-outline-secondary" @click="nextMonth">
            <i class="bi bi-chevron-right"></i>
          </button>
          <button class="btn btn-sm btn-outline-primary" @click="goToday">{{ isRtl ? 'اليوم' : 'Today' }}</button>
        </div>
      </div>

      <!-- Month Title -->
      <h5 class="text-center fw-bold mb-4">
        {{ monthTitle }}
      </h5>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <div v-else>
        <!-- Day headers -->
        <div class="cal-grid-head">
          <div v-for="d in dayNames" :key="d" class="cal-day-label">{{ d }}</div>
        </div>

        <!-- Calendar grid -->
        <div class="cal-grid">
          <div
            v-for="(cell, i) in calendarCells"
            :key="i"
            class="cal-cell"
            :class="{
              'other-month': !cell.current,
              'is-today': cell.isToday,
              'has-tasks': cell.tasks.length > 0,
            }"
          >
            <div class="cal-cell-date">{{ cell.day }}</div>
            <div class="cal-tasks">
              <div
                v-for="t in cell.tasks.slice(0, 3)"
                :key="t.id"
                class="cal-task-pill"
                :class="priorityClass(t.priority)"
                :title="t.title"
                @click="openTask(t)"
              >
                <span class="pill-dot"></span>
                <span class="pill-text">{{ t.title }}</span>
                <span v-if="t.status === 'completed'" class="pill-done">
                  <i class="bi bi-check2"></i>
                </span>
              </div>
              <div v-if="cell.tasks.length > 3" class="cal-more" @click="openDayModal(cell)">
                +{{ cell.tasks.length - 3 }} {{ isRtl ? 'أخرى' : 'more' }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Legend -->
      <div class="cal-legend mt-3">
        <span class="legend-item priority-high">{{ isRtl ? 'عالية' : 'High' }}</span>
        <span class="legend-item priority-medium">{{ isRtl ? 'متوسطة' : 'Medium' }}</span>
        <span class="legend-item priority-low">{{ isRtl ? 'منخفضة' : 'Low' }}</span>
        <span class="legend-sep">|</span>
        <span class="legend-item is-today-legend">{{ isRtl ? 'اليوم' : 'Today' }}</span>
      </div>

      <!-- Day tasks modal -->
      <div v-if="dayModal" class="day-modal-backdrop" @click.self="dayModal = null">
        <div class="day-modal">
          <div class="day-modal-header">
            <span class="fw-bold">{{ dayModal.label }}</span>
            <button @click="dayModal = null" style="background:none;border:none;font-size:1.2rem;cursor:pointer;">&times;</button>
          </div>
          <div class="day-modal-body">
            <div
              v-for="t in dayModal.tasks"
              :key="t.id"
              class="day-task-item"
              :class="{ done: t.status === 'completed' }"
              @click="openTask(t)"
            >
              <span class="dt-dot" :class="priorityClass(t.priority)"></span>
              <span class="dt-title">{{ t.title }}</span>
              <span class="dt-status">
                <span class="badge badge-soft-sm" :class="statusBadge(t.status)">{{ statusLabel(t.status) }}</span>
              </span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const store  = useStore()
const router = useRouter()
const isRtl  = computed(() => store.state.ui?.locale === 'ar')

const today    = new Date()
const curYear  = ref(today.getFullYear())
const curMonth = ref(today.getMonth()) // 0-indexed

const tasks   = ref([])
const loading = ref(true)
const dayModal = ref(null)

// Day names
const dayNamesAr = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت']
const dayNamesEn = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']
const dayNames   = computed(() => isRtl.value ? dayNamesAr : dayNamesEn)

const monthNamesAr = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر']
const monthNamesEn = ['January','February','March','April','May','June','July','August','September','October','November','December']

const monthTitle = computed(() => {
  const name = isRtl.value ? monthNamesAr[curMonth.value] : monthNamesEn[curMonth.value]
  return `${name} ${curYear.value}`
})

// Build calendar grid
const calendarCells = computed(() => {
  const year  = curYear.value
  const month = curMonth.value

  const firstDay = new Date(year, month, 1).getDay() // 0=Sun
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const daysInPrev  = new Date(year, month, 0).getDate()

  const cells = []

  // Leading days from previous month
  for (let i = firstDay - 1; i >= 0; i--) {
    cells.push({ day: daysInPrev - i, current: false, tasks: [], isToday: false })
  }

  // Current month days
  const todayStr = today.toISOString().slice(0, 10)
  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`
    const isToday = dateStr === todayStr
    const dayTasks = tasks.value.filter(t => t.due_date?.slice(0, 10) === dateStr)
    cells.push({ day: d, date: dateStr, current: true, isToday, tasks: dayTasks })
  }

  // Trailing days to fill last week
  const remaining = (7 - (cells.length % 7)) % 7
  for (let d = 1; d <= remaining; d++) {
    cells.push({ day: d, current: false, tasks: [], isToday: false })
  }

  return cells
})

async function fetchTasks() {
  loading.value = true
  try {
    // Fetch tasks for current month
    const year  = curYear.value
    const month = curMonth.value
    const from  = `${year}-${String(month + 1).padStart(2, '0')}-01`
    const lastDay = new Date(year, month + 1, 0).getDate()
    const to    = `${year}-${String(month + 1).padStart(2, '0')}-${lastDay}`
    const { data } = await api.get('/tasks', { params: { due_from: from, due_to: to, per_page: 200 } })
    tasks.value = data.data ?? []
  } finally {
    loading.value = false
  }
}

function prevMonth() {
  if (curMonth.value === 0) { curMonth.value = 11; curYear.value-- }
  else curMonth.value--
}

function nextMonth() {
  if (curMonth.value === 11) { curMonth.value = 0; curYear.value++ }
  else curMonth.value++
}

function goToday() {
  curYear.value  = today.getFullYear()
  curMonth.value = today.getMonth()
}

function openTask(t) {
  router.push({ path: '/crm/tasks', query: { edit: t.id } })
}

function openDayModal(cell) {
  if (!cell.current) return
  dayModal.value = {
    label: isRtl.value
      ? `${cell.day} ${monthNamesAr[curMonth.value]} ${curYear.value}`
      : `${monthNamesEn[curMonth.value]} ${cell.day}, ${curYear.value}`,
    tasks: cell.tasks,
  }
}

function priorityClass(p) {
  return { high: 'priority-high', medium: 'priority-medium', low: 'priority-low' }[p] || 'priority-medium'
}

function statusLabel(s) {
  if (isRtl.value) return { pending:'معلقة', in_progress:'قيد التنفيذ', completed:'مكتملة' }[s] || s
  return { pending:'Pending', in_progress:'In Progress', completed:'Completed' }[s] || s
}

function statusBadge(s) {
  return { pending:'badge-soft-warning', in_progress:'badge-soft-primary', completed:'badge-soft-success' }[s] || 'badge-soft-secondary'
}

watch([curMonth, curYear], fetchTasks)
onMounted(fetchTasks)
</script>

<style scoped>
.cal-grid-head {
  display: grid; grid-template-columns: repeat(7, 1fr);
  margin-bottom: 4px;
}
.cal-day-label {
  text-align: center;
  font-size: .72rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .5px;
  color: var(--bs-secondary-color);
  padding: 4px 0;
}

.cal-grid {
  display: grid; grid-template-columns: repeat(7, 1fr);
  border-inline-start: 1px solid var(--bs-border-color);
  border-top: 1px solid var(--bs-border-color);
}

.cal-cell {
  min-height: 100px;
  border-inline-end: 1px solid var(--bs-border-color);
  border-bottom: 1px solid var(--bs-border-color);
  padding: 6px 6px 4px;
  position: relative;
}
.cal-cell.other-month { background: var(--bs-light); opacity: .5; }
.cal-cell.is-today { background: #eff6ff; }
.cal-cell.is-today .cal-cell-date {
  background: #2563eb; color: #fff;
  border-radius: 50%; width: 22px; height: 22px;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700;
}

.cal-cell-date {
  font-size: .75rem; font-weight: 600;
  margin-bottom: 4px;
  width: 22px; height: 22px;
  display: flex; align-items: center; justify-content: center;
}

.cal-tasks { display: flex; flex-direction: column; gap: 2px; }

.cal-task-pill {
  display: flex; align-items: center; gap: 4px;
  padding: 2px 6px;
  border-radius: 4px;
  cursor: pointer;
  font-size: .7rem;
  overflow: hidden;
  transition: opacity .12s;
}
.cal-task-pill:hover { opacity: .8; }

.priority-high   { background: #fee2e2; color: #b91c1c; }
.priority-medium { background: #fef3c7; color: #92400e; }
.priority-low    { background: #d1fae5; color: #065f46; }

.pill-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: currentColor; flex-shrink: 0;
}
.pill-text { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pill-done { font-size: .65rem; }

.cal-more {
  font-size: .67rem; color: #2563eb;
  cursor: pointer; padding: 1px 4px;
  font-weight: 600;
}
.cal-more:hover { text-decoration: underline; }

/* Legend */
.cal-legend {
  display: flex; align-items: center; gap: 12px;
  flex-wrap: wrap;
}
.legend-item {
  display: flex; align-items: center; gap: 5px;
  font-size: .72rem; font-weight: 600;
  padding: 3px 8px; border-radius: 4px;
}
.legend-sep { color: var(--bs-border-color); }
.is-today-legend { background: #eff6ff; color: #2563eb; }

/* Day tasks modal */
.day-modal-backdrop {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.35);
  z-index: 1300;
  display: flex; align-items: center; justify-content: center;
  padding: 16px;
}
.day-modal {
  background: var(--bs-body-bg, #fff);
  border-radius: 14px;
  width: 100%; max-width: 420px;
  max-height: 80vh;
  display: flex; flex-direction: column;
  box-shadow: 0 12px 40px rgba(0,0,0,.18);
}
.day-modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 20px;
  border-bottom: 1px solid var(--bs-border-color);
}
.day-modal-body { flex: 1; overflow-y: auto; padding: 8px 12px; }
.day-task-item {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 8px; border-radius: 8px; cursor: pointer;
  transition: background .12s;
}
.day-task-item:hover { background: var(--bs-light); }
.day-task-item.done .dt-title { text-decoration: line-through; opacity: .6; }
.dt-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dt-title { flex: 1; font-size: .83rem; }
.dt-status { flex-shrink: 0; }
.badge-soft-sm { font-size: .7rem; padding: 2px 6px; }
</style>
