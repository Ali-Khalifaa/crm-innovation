<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale==='ar' ? 'المهام' : 'Tasks' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale==='ar' ? 'تتبع وإدارة جميع المهام' : 'Track and manage all your tasks' }}</p>
      </div>
      <router-link to="/crm/tasks/calendar" class="btn btn-sm btn-outline-secondary flex-shrink-0">
        <i class="bi bi-calendar3 me-1"></i>{{ locale==='ar' ? 'التقويم' : 'Calendar' }}
      </router-link>
      <button class="btn btn-sm btn-primary flex-shrink-0" @click="openAddModal">
        <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar' ? 'مهمة جديدة' : 'New Task' }}
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" @click="clearFilter">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-list-check fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.total }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'إجمالي المهام' : 'Total Tasks' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': filterStatus==='pending'}" @click="filterByStatus('pending')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(100,116,139,.1);color:#64748B"><i class="bi bi-clock fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.pending }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'قيد الانتظار' : 'Pending' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': filterStatus==='in_progress'}" @click="filterByStatus('in_progress')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#D97706"><i class="bi bi-arrow-repeat fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.in_progress }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'جارية' : 'In Progress' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': filterStatus==='completed'}" @click="filterByStatus('completed')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#059669"><i class="bi bi-check-circle fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.completed }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'مكتملة' : 'Completed' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="card card-border tasks-card">

      <!-- Card Header -->
      <div class="card-header card-header-action flex-wrap gap-2">
        <h6 class="mb-0 d-flex align-items-center gap-2 flex-wrap">
          <span>{{ locale==='ar' ? 'قائمة المهام' : 'All Tasks' }}</span>
          <span class="badge badge-soft-primary">{{ meta.total ?? tasks.length }}</span>
          <button v-if="filterStatus || filterPriority || dueFrom || dueTo || showOverdue" class="btn btn-xs badge-soft-warning d-flex align-items-center gap-1 border-0" @click="clearFilter">
            {{ isRtl ? 'مسح الفلاتر' : 'Clear Filters' }}
            <i class="bi bi-x"></i>
          </button>
        </h6>
        <div class="card-action-wrap d-flex gap-2 flex-wrap">
          <div class="input-group" style="width:220px">
            <span class="input-group-text bg-transparent border-end-0 ps-2">
              <i class="bi bi-search text-muted" style="font-size:13px"></i>
            </span>
            <input v-model="search" @input="debouncedFetch" type="text"
              class="form-control border-start-0 ps-1"
              :placeholder="locale==='ar' ? 'بحث...' : 'Search tasks...'" />
          </div>
          <select v-model="filterPriority" @change="page=1;fetchTasks()" class="form-select" style="width:auto">
            <option value="">{{ locale==='ar' ? 'كل الأولويات' : 'All Priorities' }}</option>
            <option value="high">{{ locale==='ar' ? 'عالية' : 'High' }}</option>
            <option value="medium">{{ locale==='ar' ? 'متوسطة' : 'Medium' }}</option>
            <option value="low">{{ locale==='ar' ? 'منخفضة' : 'Low' }}</option>
          </select>
          <!-- Due date range -->
          <input v-model="dueFrom" @change="page=1;fetchTasks()" type="date" class="form-control" style="width:150px" :title="locale==='ar'?'من تاريخ':'From date'" />
          <input v-model="dueTo"   @change="page=1;fetchTasks()" type="date" class="form-control" style="width:150px" :title="locale==='ar'?'إلى تاريخ':'To date'" />
          <!-- Overdue toggle -->
          <button @click="showOverdue=!showOverdue;page=1;fetchTasks()" class="btn btn-sm flex-shrink-0"
            :class="showOverdue ? 'btn-danger' : 'btn-outline-danger'">
            <i class="bi bi-exclamation-circle me-1"></i>{{ locale==='ar' ? 'المتأخرة' : 'Overdue' }}
          </button>
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
      <div v-else-if="!tasks.length" class="text-center py-5 px-4">
        <div class="empty-icon mb-3"><i class="bi bi-list-check"></i></div>
        <h6 class="mb-1">{{ hasFilter ? (locale==='ar'?'لا توجد نتائج':'No results found') : (locale==='ar'?'لا توجد مهام بعد':'No tasks yet') }}</h6>
        <p class="text-muted small mb-3">
          {{ hasFilter
            ? (locale==='ar'?'جرّب تغيير معايير البحث':'Try changing your filters')
            : (locale==='ar'?'أضف أول مهمة لتبدأ':'Add your first task to get started') }}
        </p>
        <button v-if="hasFilter" class="btn btn-sm btn-outline-secondary me-2" @click="clearFilter">
          <i class="bi bi-x-lg me-1"></i>{{ locale==='ar'?'مسح الفلتر':'Clear Filter' }}
        </button>
        <button v-else class="btn btn-sm btn-primary" @click="openAddModal">
          <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar'?'إضافة مهمة':'Add Task' }}
        </button>
      </div>

      <!-- Table -->
      <div v-else>
        <div class="table-responsive">
          <table class="table tasks-table mb-0">
            <thead>
              <tr>
                <th class="ps-4" style="width:40px"></th>
                <th>{{ locale==='ar' ? 'المهمة' : 'Task' }}</th>
                <th>{{ locale==='ar' ? 'الأولوية' : 'Priority' }}</th>
                <th>{{ locale==='ar' ? 'الحالة' : 'Status' }}</th>
                <th class="d-none d-lg-table-cell">{{ locale==='ar' ? 'تاريخ الاستحقاق' : 'Due Date' }}</th>
                <th class="d-none d-md-table-cell">{{ locale==='ar' ? 'مرتبط بـ' : 'Related To' }}</th>
                <th style="width:100px"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="task in tasks" :key="task.id" class="task-row" :class="{'task-completed': task.status==='completed'}">

                <!-- Quick complete checkbox -->
                <td class="ps-4">
                  <div
                    class="task-check"
                    :class="{'task-check-done': task.status==='completed'}"
                    @click="toggleComplete(task)"
                    :title="task.status==='completed' ? (locale==='ar'?'إعادة فتح':'Reopen') : (locale==='ar'?'إتمام':'Complete')"
                  >
                    <i v-if="task.status==='completed'" class="bi bi-check-lg"></i>
                  </div>
                </td>

                <td>
                  <div class="task-title" :class="{'text-decoration-line-through text-muted': task.status==='completed'}">
                    {{ task.title }}
                  </div>
                  <div v-if="task.description" class="task-desc">{{ task.description }}</div>
                </td>

                <td>
                  <span class="priority-badge" :class="`priority-${task.priority}`">
                    <i class="bi bi-circle-fill" style="font-size:6px"></i>
                    {{ priorityLabel(task.priority) }}
                  </span>
                </td>

                <td>
                  <span class="task-status-badge" :class="`ts-${task.status}`">
                    {{ statusLabel(task.status) }}
                  </span>
                </td>

                <td class="d-none d-lg-table-cell">
                  <span v-if="formatDate(task.due_date)" :class="{ 'text-danger fw-semibold': isOverdue(task) }" style="font-size:12px">
                    <i v-if="isOverdue(task)" class="bi bi-exclamation-circle me-1"></i>
                    <i v-else class="bi bi-calendar3 me-1 text-muted" style="font-size:11px"></i>
                    {{ formatDate(task.due_date) }}
                  </span>
                  <span v-else class="no-date-badge">
                    <i class="bi bi-calendar-x"></i>
                    {{ locale==='ar' ? 'غير محدد' : 'No date' }}
                  </span>
                </td>

                <td class="d-none d-md-table-cell" style="font-size:12px;color:var(--bs-secondary-color)">
                  <span v-if="task.taskable">
                    <i class="bi me-1" :class="task.taskable_type === 'contact' ? 'bi-person' : 'bi-briefcase'"></i>
                    {{ task.taskable.label }}
                  </span>
                  <span v-else class="text-muted">—</span>
                </td>

                <td>
                  <div class="d-flex gap-1 justify-content-end">
                    <button class="btn btn-xs btn-outline-secondary" @click="openEditModal(task)" :title="locale==='ar'?'تعديل':'Edit'">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" @click="confirmDelete(task)" :title="locale==='ar'?'حذف':'Delete'">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="d-flex justify-content-center align-items-center gap-1 py-3 flex-wrap">
          <button class="btn btn-xs btn-outline-secondary" @click="page=1;fetchTasks()" :disabled="page===1">«</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page--;fetchTasks()" :disabled="page===1">‹</button>
          <button v-for="p in pages" :key="p" class="btn btn-xs"
            :class="p===page ? 'btn-primary' : 'btn-outline-secondary'"
            @click="page=p;fetchTasks()">{{ p }}</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page++;fetchTasks()" :disabled="page===meta.last_page">›</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page=meta.last_page;fetchTasks()" :disabled="page===meta.last_page">»</button>
        </div>
      </div>
    </div>


    <!-- ═══ ADD / EDIT MODAL ═══ -->
    <div class="modal fade" id="taskFormModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

          <div class="modal-header pb-0 border-0">
            <div class="d-flex align-items-center gap-3">
              <div class="modal-icon" :style="{ background: formMode==='create' ? 'rgba(37,99,235,.1)' : 'rgba(124,58,237,.1)', color: formMode==='create' ? '#2563EB' : '#7C3AED' }">
                <i class="bi" :class="formMode==='create' ? 'bi-plus-circle-fill' : 'bi-pencil-fill'"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold">{{ formMode==='create' ? (locale==='ar'?'إضافة مهمة جديدة':'New Task') : (locale==='ar'?'تعديل المهمة':'Edit Task') }}</h5>
                <p class="text-muted small mb-0 mt-1">{{ formMode==='create' ? (locale==='ar'?'أدخل تفاصيل المهمة الجديدة':'Enter the new task details') : (locale==='ar'?'قم بتعديل بيانات المهمة':'Update the task information') }}</p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body pt-3">
            <form id="taskForm" @submit.prevent="saveTask">

              <!-- Basic Info -->
              <div class="fsect-title"><i class="bi bi-info-circle me-2 text-primary"></i>{{ locale==='ar'?'المعلومات الأساسية':'Basic Information' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-12">
                  <label class="form-label">{{ locale==='ar'?'عنوان المهمة':'Task Title' }} <span class="text-danger">*</span></label>
                  <input
                    v-model="form.title"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': v$.title.$error, 'is-valid': v$.title.$dirty && !v$.title.$error }"
                    :placeholder="locale==='ar'?'مثال: مراجعة العقد مع العميل':'e.g. Review contract with client'"
                    @blur="v$.title.$touch()"
                  />
                  <div class="invalid-feedback" v-if="v$.title.$error">{{ v$.title.$errors[0]?.$message }}</div>
                </div>
                <div class="col-12">
                  <label class="form-label">{{ locale==='ar'?'الوصف':'Description' }}</label>
                  <textarea
                    v-model="form.description"
                    class="form-control"
                    :placeholder="locale==='ar'?'تفاصيل إضافية عن المهمة...':'Additional details about the task...'"
                    rows="2"
                    style="resize:none"
                  ></textarea>
                </div>
              </div>

              <!-- Task Details -->
              <div class="fsect-title"><i class="bi bi-sliders me-2 text-primary"></i>{{ locale==='ar'?'إعدادات المهمة':'Task Settings' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'الأولوية':'Priority' }}</label>
                  <Select
                    v-model="form.priority"
                    :options="priorityOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'اختر الأولوية':'Select priority'"
                    class="w-100"
                    append-to="body"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'الحالة':'Status' }}</label>
                  <Select
                    v-model="form.status"
                    :options="statusOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'اختر الحالة':'Select status'"
                    class="w-100"
                    append-to="body"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'تاريخ الاستحقاق':'Due Date' }}</label>
                  <DatePicker
                    v-model="dueDate"
                    date-format="dd/mm/yy"
                    :placeholder="locale==='ar'?'يوم/شهر/سنة':'DD/MM/YYYY'"
                    show-icon
                    icon-display="input"
                    class="w-100"
                    append-to="body"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'مرتبط بجهة اتصال':'Related Contact' }}</label>
                  <Select
                    v-model="form.contact_id"
                    :options="contactOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'— اختياري —':'— Optional —'"
                    :show-clear="true"
                    class="w-100"
                    append-to="body"
                  />
                </div>
              </div>

            </form>
          </div>

          <div class="modal-footer border-0 pt-0">
            <button v-if="formMode==='edit'" type="button" class="btn btn-outline-danger me-auto" @click="switchToDelete">
              <i class="bi bi-trash me-1"></i>{{ locale==='ar'?'حذف':'Delete' }}
            </button>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="submit" form="taskForm" class="btn btn-primary px-4" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
              {{ saving ? (locale==='ar'?'جاري الحفظ...':'Saving...') : (formMode==='create' ? (locale==='ar'?'إضافة المهمة':'Add Task') : (locale==='ar'?'حفظ التعديلات':'Save Changes')) }}
            </button>
          </div>

        </div>
      </div>
    </div>


    <!-- ═══ DELETE CONFIRM MODAL ═══ -->
    <div class="modal fade" id="taskDeleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
          <div class="modal-body text-center py-4 px-4">
            <div class="del-icon mb-3"><i class="bi bi-trash3-fill"></i></div>
            <h5 class="fw-bold mb-2">{{ locale==='ar' ? 'حذف المهمة' : 'Delete Task' }}</h5>
            <p class="text-muted mb-0" style="font-size:14px">
              {{ locale==='ar' ? 'هل أنت متأكد من حذف' : 'Are you sure you want to delete' }}
              <strong class="text-danger"> "{{ deletingTask?.title }}"</strong>?
              <br><span class="small">{{ locale==='ar' ? 'لا يمكن التراجع عن هذه العملية' : 'This action cannot be undone.' }}</span>
            </p>
          </div>
          <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="button" class="btn btn-danger px-4" @click="deleteTask" :disabled="deleting">
              <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
              {{ locale==='ar' ? 'نعم، احذف' : 'Yes, Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>

  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useStore } from 'vuex'
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, maxLength, helpers } from '@vuelidate/validators'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store  = useStore()
const toast  = useToast()
const locale = computed(() => store.state.ui.locale)

// ── Data ──────────────────────────────────────────────────
const tasks    = ref([])
const contacts = ref([])
const meta     = ref({})
const loading  = ref(true)
const saving   = ref(false)
const deleting = ref(false)
const page     = ref(1)

const search         = ref('')
const filterStatus   = ref('')
const filterPriority = ref('')
const dueFrom        = ref('')
const dueTo          = ref('')
const showOverdue    = ref(false)
const isRtl      = computed(() => locale.value === 'ar')
const hasFilter  = computed(() => !!(search.value || filterStatus.value || filterPriority.value || dueFrom.value || dueTo.value || showOverdue.value))

const stats = ref({ total: 0, pending: 0, in_progress: 0, completed: 0 })

const formMode     = ref('create')
const editingTask  = ref(null)
const deletingTask = ref(null)

const form = ref(emptyForm())
let searchTimer = null

function emptyForm() {
  return { title: '', description: '', priority: 'medium', status: 'pending', due_date: '', contact_id: null }
}

// ── Vuelidate ─────────────────────────────────────────────
const ar = computed(() => locale.value === 'ar')
const rules = computed(() => ({
  title: {
    required: helpers.withMessage(ar.value ? 'العنوان مطلوب' : 'Title is required', required),
    minLength: helpers.withMessage(ar.value ? 'حرفين على الأقل' : 'At least 2 characters', minLength(2)),
    maxLength: helpers.withMessage(ar.value ? 'الحد 100 حرف' : 'Max 100 characters', maxLength(100)),
  },
}))
const v$ = useVuelidate(rules, form)

// ── Computed options ──────────────────────────────────────
const priorityOptions = computed(() => [
  { label: locale.value==='ar' ? 'عالية'    : 'High',   value: 'high'   },
  { label: locale.value==='ar' ? 'متوسطة'   : 'Medium', value: 'medium' },
  { label: locale.value==='ar' ? 'منخفضة'   : 'Low',    value: 'low'    },
])
const statusOptions = computed(() => [
  { label: locale.value==='ar' ? 'قيد الانتظار' : 'Pending',     value: 'pending'     },
  { label: locale.value==='ar' ? 'جارية'        : 'In Progress', value: 'in_progress' },
  { label: locale.value==='ar' ? 'مكتملة'       : 'Completed',   value: 'completed'   },
])
const contactOptions = computed(() => contacts.value.map(c => ({
  label: `${c.first_name} ${c.last_name}${c.company ? ' — ' + c.company : ''}`,
  value: c.id,
})))

// ── Date bridge ───────────────────────────────────────────
const dueDate = computed({
  get: () => form.value.due_date ? new Date(form.value.due_date.substring(0, 10) + 'T00:00:00') : null,
  set: (d) => {
    form.value.due_date = d
      ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
      : ''
  },
})

// ── Pagination ────────────────────────────────────────────
const pages = computed(() => {
  const t = meta.value.last_page || 1, c = page.value, arr = []
  for (let p = Math.max(1, c - 2); p <= Math.min(t, c + 2); p++) arr.push(p)
  return arr
})

// ── Helpers ───────────────────────────────────────────────
function formatDate(dateStr) {
  if (!dateStr) return null
  const d = new Date(dateStr.substring(0, 10) + 'T00:00:00')
  if (isNaN(d.getTime())) return null
  return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`
}
function isOverdue(task) {
  if (!task.due_date || task.status === 'completed') return false
  const d = new Date(task.due_date.substring(0, 10) + 'T00:00:00')
  return !isNaN(d.getTime()) && d < new Date()
}
function priorityLabel(p) {
  const map = { high: locale.value==='ar'?'عالية':'High', medium: locale.value==='ar'?'متوسطة':'Medium', low: locale.value==='ar'?'منخفضة':'Low' }
  return map[p] || p
}
function statusLabel(s) {
  const map = { pending: locale.value==='ar'?'قيد الانتظار':'Pending', in_progress: locale.value==='ar'?'جارية':'In Progress', completed: locale.value==='ar'?'مكتملة':'Completed' }
  return map[s] || s
}
function filterByStatus(s) {
  filterStatus.value = filterStatus.value === s ? '' : s
  page.value = 1
  fetchTasks()
}
function clearFilter() {
  filterStatus.value   = ''
  filterPriority.value = ''
  dueFrom.value        = ''
  dueTo.value          = ''
  showOverdue.value    = false
  search.value         = ''
  page.value           = 1
  fetchTasks()
}
function debouncedFetch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; fetchTasks() }, 400)
}

// ── Modal helpers ─────────────────────────────────────────
function getFormModal()   { return window.bootstrap.Modal.getOrCreateInstance(document.getElementById('taskFormModal')) }
function getDeleteModal() { return window.bootstrap.Modal.getOrCreateInstance(document.getElementById('taskDeleteModal')) }

function openAddModal() {
  formMode.value    = 'create'
  editingTask.value = null
  form.value        = emptyForm()
  v$.value.$reset()
  nextTick(() => getFormModal().show())
}
function openEditModal(task) {
  formMode.value    = 'edit'
  editingTask.value = task
  form.value = {
    title:       task.title || '',
    description: task.description || '',
    priority:    task.priority || 'medium',
    status:      task.status || 'pending',
    due_date:    task.due_date ? task.due_date.substring(0, 10) : '',
    contact_id:  task.taskable_type === 'contact' ? task.taskable_id : null,
  }
  v$.value.$reset()
  nextTick(() => getFormModal().show())
}
function confirmDelete(task) {
  deletingTask.value = task
  getDeleteModal().show()
}
function switchToDelete() {
  getFormModal().hide()
  nextTick(() => {
    deletingTask.value = editingTask.value
    getDeleteModal().show()
  })
}

// ── API ───────────────────────────────────────────────────
async function fetchTasks() {
  loading.value = true
  try {
    const { data } = await api.get('/tasks', {
      params: {
        search:   search.value,
        status:   filterStatus.value,
        priority: filterPriority.value,
        due_from: dueFrom.value,
        due_to:   dueTo.value,
        overdue:  showOverdue.value ? 1 : undefined,
        page:     page.value,
        per_page: 15,
      }
    })
    tasks.value = data.data
    meta.value  = data.meta || {}
  } catch {
    toast.error(locale.value==='ar' ? 'فشل تحميل المهام' : 'Failed to load tasks')
  }
  loading.value = false
}

async function fetchStats() {
  try {
    const [total, pending, inProgress, completed] = await Promise.all([
      api.get('/tasks', { params: { per_page: 1 } }),
      api.get('/tasks', { params: { status: 'pending',     per_page: 1 } }),
      api.get('/tasks', { params: { status: 'in_progress', per_page: 1 } }),
      api.get('/tasks', { params: { status: 'completed',   per_page: 1 } }),
    ])
    stats.value = {
      total:       total.data.meta?.total       ?? 0,
      pending:     pending.data.meta?.total     ?? 0,
      in_progress: inProgress.data.meta?.total  ?? 0,
      completed:   completed.data.meta?.total   ?? 0,
    }
  } catch {}
}

async function fetchContacts() {
  try {
    const { data } = await api.get('/contacts', { params: { per_page: 200 } })
    contacts.value = data.data
  } catch {}
}

async function saveTask() {
  const valid = await v$.value.$validate()
  if (!valid) return
  saving.value = true
  try {
    const payload = { ...form.value }
    if (payload.contact_id) {
      payload.taskable_type = 'contact'
      payload.taskable_id   = payload.contact_id
    } else {
      payload.taskable_type = null
      payload.taskable_id   = null
    }
    delete payload.contact_id

    if (formMode.value === 'create') {
      await api.post('/tasks', payload)
      toast.success(locale.value==='ar' ? 'تم إضافة المهمة بنجاح' : 'Task added successfully')
    } else {
      await api.put(`/tasks/${editingTask.value.id}`, payload)
      toast.success(locale.value==='ar' ? 'تم تحديث المهمة بنجاح' : 'Task updated successfully')
    }
    getFormModal().hide()
    await fetchTasks()
    fetchStats()
  } catch (e) {
    toast.error(e.response?.data?.message || (locale.value==='ar' ? 'حدث خطأ' : 'An error occurred'))
  }
  saving.value = false
}

async function toggleComplete(task) {
  const newStatus = task.status === 'completed' ? 'pending' : 'completed'
  try {
    await api.put(`/tasks/${task.id}`, { status: newStatus })
    task.status = newStatus
    fetchStats()
  } catch {
    toast.error(locale.value==='ar' ? 'فشل تحديث الحالة' : 'Failed to update status')
  }
}

async function deleteTask() {
  if (!deletingTask.value) return
  deleting.value = true
  try {
    await api.delete(`/tasks/${deletingTask.value.id}`)
    toast.success(locale.value==='ar' ? 'تم حذف المهمة بنجاح' : 'Task deleted successfully')
    getDeleteModal().hide()
    deletingTask.value = null
    await fetchTasks()
    fetchStats()
  } catch {
    toast.error(locale.value==='ar' ? 'فشل الحذف' : 'Delete failed')
  }
  deleting.value = false
}

onMounted(async () => {
  await fetchContacts()
  fetchTasks()
  fetchStats()
})
</script>

<style scoped>
/* ─── Stats ──────────────────────────────── */
.stat-card    { cursor:pointer;transition:all .15s; }
.stat-card:hover { transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08); }
.stat-active  { border-color:#2563EB !important;box-shadow:0 0 0 3px rgba(37,99,235,.12) !important; }
.stat-icon    { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.stat-number  { font-size:1.5rem;font-weight:800;line-height:1.2;color:var(--bs-body-color); }
.stat-label   { font-size:11px;color:var(--bs-secondary-color);font-weight:500;margin-top:2px; }

/* ─── Table ──────────────────────────────── */
.tasks-table  { border-collapse:separate;border-spacing:0; }
.tasks-table thead th {
  background:var(--bs-tertiary-bg,#F8FAFC);
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;
  color:var(--bs-secondary-color);padding:.75rem 1rem;border-bottom:1px solid var(--bs-border-color);
  white-space:nowrap;
}
.task-row { transition:background .1s; }
.task-row:hover { background:rgba(37,99,235,.03); }
.tasks-table td { padding:.85rem 1rem;vertical-align:middle;border-bottom:1px solid var(--bs-border-color); }
.tasks-table tbody tr:last-child td { border-bottom:none; }
.task-completed { opacity:.7; }

/* ─── Checkbox ───────────────────────────── */
.task-check {
  width:20px;height:20px;border-radius:50%;border:2px solid var(--bs-border-color);
  cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:11px;flex-shrink:0;
}
.task-check:hover { border-color:#2563EB; }
.task-check-done  { background:#10B981;border-color:#10B981; }

/* ─── Task title ─────────────────────────── */
.task-title { font-weight:600;font-size:13px;color:var(--bs-body-color); }
.task-desc  { font-size:11px;color:var(--bs-secondary-color);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px; }

/* ─── Badges ─────────────────────────────── */
.priority-badge { display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }
.priority-high   { background:rgba(239,68,68,.1);color:#DC2626; }
.priority-medium { background:rgba(245,158,11,.1);color:#D97706; }
.priority-low    { background:rgba(16,185,129,.1);color:#059669; }

.task-status-badge { display:inline-flex;align-items:center;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }
.ts-pending     { background:rgba(100,116,139,.1);color:#64748B; }
.ts-in_progress { background:rgba(245,158,11,.1);color:#D97706; }
.ts-completed   { background:rgba(16,185,129,.1);color:#059669; }

/* ─── No-date badge ──────────────────────── */
.no-date-badge { display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:500;color:var(--bs-secondary-color);background:var(--bs-tertiary-bg,#F1F5F9);padding:2px 8px;border-radius:20px; }

/* ─── Empty state ────────────────────────── */
.empty-icon { width:80px;height:80px;border-radius:20px;background:var(--bs-tertiary-bg,#f8f9fa);display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--bs-secondary-color);margin:0 auto; }

/* ─── Modal ──────────────────────────────── */
.modal-icon  { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0; }
.fsect-title { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-secondary-color);padding-bottom:.5rem;margin-bottom:.875rem;border-bottom:1px solid var(--bs-border-color);margin-top:.25rem; }

/* ─── Delete modal ───────────────────────── */
.del-icon { width:72px;height:72px;border-radius:50%;background:rgba(239,68,68,.1);color:#DC2626;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto; }

/* ─── PrimeVue inside Bootstrap modal ─────── */
:deep(.p-datepicker)       { width:100%; }
:deep(.p-datepicker-input) { width:100%;border-radius:var(--bs-border-radius);border:1px solid var(--bs-border-color);padding:.375rem .75rem;font-size:.875rem;background:var(--bs-body-bg);color:var(--bs-body-color); }
:deep(.p-datepicker-input:focus) { border-color:#2563EB;outline:none;box-shadow:0 0 0 .2rem rgba(37,99,235,.2); }
:deep(.p-select)           { width:100%; }
:deep(.p-select-label)     { font-size:.875rem;padding:.375rem .75rem; }
:deep(.p-datepicker-panel),
:deep(.p-select-overlay)   { z-index:9999 !important; }

/* ─── Dark mode ──────────────────────────── */
[data-bs-theme="dark"] .tasks-table thead th { background:var(--bs-dark-bg-subtle); }
[data-bs-theme="dark"] .task-row:hover { background:rgba(255,255,255,.04); }
</style>
