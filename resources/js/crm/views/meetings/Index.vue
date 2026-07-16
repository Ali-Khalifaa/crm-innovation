<template>
  <CrmLayout>
    <div>
      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between mb-4 gap-3 flex-wrap">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">{{ t('meetings_title') }}</h4>
          <p class="text-muted small mt-1 mb-0">{{ t('meetings_sub') }}</p>
        </div>
        <button class="btn btn-primary btn-sm" @click="openAdd">
          <i class="bi bi-plus-lg me-1"></i>{{ t('log_meeting') }}
        </button>
      </div>

      <!-- Filter bar -->
      <div class="d-flex gap-2 mb-3 flex-wrap">
        <select class="form-select form-select-sm" style="max-width:160px" v-model="filters.status" @change="fetchMeetings">
          <option value="">{{ t('all_statuses') }}</option>
          <option value="scheduled">{{ t('status_scheduled') }}</option>
          <option value="completed">{{ t('status_completed') }}</option>
          <option value="cancelled">{{ t('status_cancelled') }}</option>
          <option value="no_show">{{ t('status_no_show') }}</option>
        </select>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <!-- Table -->
      <div v-else class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>{{ t('title') }}</th>
                <th>{{ t('scheduled_at') }}</th>
                <th>{{ t('duration') }}</th>
                <th>{{ t('status') }}</th>
                <th>{{ t('linked_to') }}</th>
                <th>{{ t('assignee') }}</th>
                <th class="text-end">{{ t('actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!meetings.length">
                <td colspan="7" class="text-center text-muted py-5">
                  <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                  {{ t('no_meetings') }}
                </td>
              </tr>
              <tr v-for="m in meetings" :key="m.id">
                <td>
                  <div class="fw-semibold" style="font-size:.9rem">{{ m.title }}</div>
                  <div v-if="m.location" class="text-muted small">
                    <i class="bi bi-geo-alt me-1"></i>{{ m.location }}
                  </div>
                </td>
                <td>
                  <div class="small">{{ formatDate(m.scheduled_at) }}</div>
                  <div class="text-muted" style="font-size:.75rem">{{ formatTime(m.scheduled_at) }}</div>
                </td>
                <td class="small">{{ m.duration_minutes }} {{ t('min') }}</td>
                <td>
                  <span class="badge" :class="statusBadge(m.status)">{{ t('status_' + m.status) }}</span>
                </td>
                <td>
                  <span v-if="m.meetable" class="small text-muted">
                    <i class="bi" :class="m.meetable_type?.includes('Contact') ? 'bi-person' : 'bi-briefcase'"></i>
                    {{ m.meetable.name }}
                  </span>
                </td>
                <td class="small">{{ m.assignee?.name ?? '—' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-secondary me-1" @click="openEdit(m)" :title="t('edit')">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(m)" :title="t('delete')">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="card-footer d-flex align-items-center justify-content-between py-2">
          <span class="small text-muted">{{ t('total') }}: {{ meta.total }}</span>
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-outline-secondary" :disabled="meta.current_page <= 1" @click="page--; fetchMeetings()">‹</button>
            <span class="btn btn-sm btn-outline-secondary disabled">{{ meta.current_page }} / {{ meta.last_page }}</span>
            <button class="btn btn-sm btn-outline-secondary" :disabled="meta.current_page >= meta.last_page" @click="page++; fetchMeetings()">›</button>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <Teleport to="body">
        <div v-if="showModal" class="modal-backdrop-custom" @click.self="closeModal">
          <div class="modal-card" style="max-width:520px">
            <div class="modal-card-header">
              <h6 class="mb-0 fw-bold">{{ editing ? t('edit_meeting') : t('log_meeting') }}</h6>
              <button class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-card-body">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ t('title') }} *</label>
                  <input v-model="form.title" class="form-control form-control-sm" :class="{ 'is-invalid': errors.title }" />
                  <div class="invalid-feedback">{{ errors.title }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('scheduled_at') }} *</label>
                  <input type="datetime-local" v-model="form.scheduled_at" class="form-control form-control-sm" :class="{ 'is-invalid': errors.scheduled_at }" />
                  <div class="invalid-feedback">{{ errors.scheduled_at }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('duration_min') }}</label>
                  <input type="number" v-model="form.duration_minutes" class="form-control form-control-sm" min="1" max="480" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('status') }}</label>
                  <select v-model="form.status" class="form-select form-select-sm">
                    <option value="scheduled">{{ t('status_scheduled') }}</option>
                    <option value="completed">{{ t('status_completed') }}</option>
                    <option value="cancelled">{{ t('status_cancelled') }}</option>
                    <option value="no_show">{{ t('status_no_show') }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('location') }}</label>
                  <input v-model="form.location" class="form-control form-control-sm" />
                </div>
                <div v-if="!editing" class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('linked_to') }} *</label>
                  <select v-model="form.meetable_type" class="form-select form-select-sm" @change="form.meetable_id = ''">
                    <option value="contact">{{ t('contact') }}</option>
                    <option value="deal">{{ t('deal') }}</option>
                  </select>
                </div>
                <div v-if="!editing" class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('select_' + form.meetable_type) }} *</label>
                  <select v-model="form.meetable_id" class="form-select form-select-sm" :class="{ 'is-invalid': errors.meetable_id }">
                    <option value="">— {{ t('select') }} —</option>
                    <option v-for="item in meetableOptions" :key="item.id" :value="item.id">{{ item.name }}</option>
                  </select>
                  <div class="invalid-feedback">{{ errors.meetable_id }}</div>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ t('description') }}</label>
                  <textarea v-model="form.description" class="form-control form-control-sm" rows="2"></textarea>
                </div>
                <div v-if="form.status === 'completed'" class="col-12">
                  <label class="form-label small fw-semibold">{{ t('outcome') }}</label>
                  <textarea v-model="form.outcome" class="form-control form-control-sm" rows="2" :placeholder="t('meeting_outcome_placeholder')"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-card-footer">
              <button class="btn btn-sm btn-secondary" @click="closeModal">{{ t('cancel') }}</button>
              <button class="btn btn-sm btn-primary" :disabled="saving" @click="save">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                {{ t('save') }}
              </button>
            </div>
          </div>
        </div>
      </Teleport>

      <!-- Delete confirm -->
      <Teleport to="body">
        <div v-if="deleteTarget" class="modal-backdrop-custom" @click.self="deleteTarget = null">
          <div class="modal-card" style="max-width:380px">
            <div class="modal-card-header">
              <h6 class="mb-0 fw-bold text-danger">{{ t('confirm_delete') }}</h6>
              <button class="btn-close" @click="deleteTarget = null"></button>
            </div>
            <div class="modal-card-body">
              <p class="mb-0">{{ t('delete_meeting_confirm', { title: deleteTarget.title }) }}</p>
            </div>
            <div class="modal-card-footer">
              <button class="btn btn-sm btn-secondary" @click="deleteTarget = null">{{ t('cancel') }}</button>
              <button class="btn btn-sm btn-danger" :disabled="deleting" @click="doDelete">
                <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
                {{ t('delete') }}
              </button>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const store = useStore()
const isRtl = computed(() => store.state.ui?.locale === 'ar')

const AR = {
  meetings_title: 'الاجتماعات', meetings_sub: 'تسجيل وإدارة الاجتماعات', log_meeting: 'تسجيل اجتماع',
  edit_meeting: 'تعديل الاجتماع', all_statuses: 'كل الحالات', title: 'العنوان',
  scheduled_at: 'الموعد', duration: 'المدة', duration_min: 'المدة (دقيقة)', status: 'الحالة',
  linked_to: 'مرتبط بـ', assignee: 'المسؤول', actions: 'الإجراءات', no_meetings: 'لا توجد اجتماعات',
  status_scheduled: 'مجدول', status_completed: 'مكتمل', status_cancelled: 'ملغى', status_no_show: 'لم يحضر',
  min: 'دقيقة', location: 'الموقع', description: 'الوصف', outcome: 'النتيجة',
  meeting_outcome_placeholder: 'ما الذي تم التوصل إليه؟', contact: 'جهة اتصال', deal: 'صفقة',
  select: 'اختر', select_contact: 'اختر جهة الاتصال', select_deal: 'اختر الصفقة',
  save: 'حفظ', cancel: 'إلغاء', edit: 'تعديل', delete: 'حذف', total: 'الإجمالي',
  confirm_delete: 'تأكيد الحذف', delete_meeting_confirm: 'هل تريد حذف "{title}"؟',
}
const EN = {
  meetings_title: 'Meetings', meetings_sub: 'Log and manage your meetings', log_meeting: 'Log Meeting',
  edit_meeting: 'Edit Meeting', all_statuses: 'All Statuses', title: 'Title',
  scheduled_at: 'Scheduled At', duration: 'Duration', duration_min: 'Duration (min)', status: 'Status',
  linked_to: 'Linked To', assignee: 'Assignee', actions: 'Actions', no_meetings: 'No meetings yet',
  status_scheduled: 'Scheduled', status_completed: 'Completed', status_cancelled: 'Cancelled', status_no_show: 'No Show',
  min: 'min', location: 'Location', description: 'Description', outcome: 'Outcome',
  meeting_outcome_placeholder: 'What was the outcome?', contact: 'Contact', deal: 'Deal',
  select: 'Select', select_contact: 'Select Contact', select_deal: 'Select Deal',
  save: 'Save', cancel: 'Cancel', edit: 'Edit', delete: 'Delete', total: 'Total',
  confirm_delete: 'Confirm Delete', delete_meeting_confirm: 'Delete "{title}"?',
}
function t(key, vars = {}) {
  const dict = isRtl.value ? AR : EN
  let str = dict[key] ?? key
  Object.entries(vars).forEach(([k, v]) => { str = str.replace(`{${k}}`, v) })
  return str
}

const meetings = ref([])
const loading  = ref(true)
const page     = ref(1)
const meta     = ref({ current_page: 1, last_page: 1, total: 0 })
const filters  = reactive({ status: '' })

const showModal    = ref(false)
const editing      = ref(null)
const saving       = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)
const errors       = reactive({})
const meetableOptions = ref([])

const emptyForm = () => ({
  title: '', scheduled_at: '', duration_minutes: 30, status: 'scheduled',
  location: '', description: '', outcome: '', meetable_type: 'contact', meetable_id: '',
})
const form = reactive(emptyForm())

async function fetchMeetings() {
  loading.value = true
  try {
    const params = { page: page.value }
    if (filters.status) params.status = filters.status
    const { data } = await api.get('/meetings', { params })
    meetings.value = data.data ?? []
    meta.value     = data.meta ?? {}
  } finally {
    loading.value = false
  }
}

async function fetchMeetableOptions() {
  const type = form.meetable_type
  const endpoint = type === 'contact' ? '/contacts' : '/deals'
  const { data } = await api.get(endpoint, { params: { per_page: 100 } })
  meetableOptions.value = (data.data ?? []).map(item => ({
    id: item.id,
    name: type === 'contact'
      ? ((item.first_name ?? '') + ' ' + (item.last_name ?? '')).trim()
      : item.title,
  }))
}

watch(() => form.meetable_type, fetchMeetableOptions)

function openAdd() {
  Object.assign(form, emptyForm())
  Object.keys(errors).forEach(k => delete errors[k])
  editing.value = null
  showModal.value = true
  fetchMeetableOptions()
}

function openEdit(m) {
  editing.value = m
  form.title            = m.title
  form.scheduled_at     = m.scheduled_at ? m.scheduled_at.slice(0, 16) : ''
  form.duration_minutes = m.duration_minutes
  form.status           = m.status
  form.location         = m.location ?? ''
  form.description      = m.description ?? ''
  form.outcome          = m.outcome ?? ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editing.value = null
}

async function save() {
  Object.keys(errors).forEach(k => delete errors[k])
  if (!form.title.trim()) { errors.title = t('required'); return }
  if (!form.scheduled_at) { errors.scheduled_at = t('required'); return }
  if (!editing.value && !form.meetable_id) { errors.meetable_id = t('required'); return }

  saving.value = true
  try {
    if (editing.value) {
      await api.put(`/meetings/${editing.value.id}`, {
        title: form.title, scheduled_at: form.scheduled_at,
        duration_minutes: form.duration_minutes, status: form.status,
        location: form.location, description: form.description, outcome: form.outcome,
      })
    } else {
      await api.post('/meetings', {
        title: form.title, scheduled_at: form.scheduled_at,
        duration_minutes: form.duration_minutes, status: form.status,
        location: form.location, description: form.description,
        meetable_type: form.meetable_type, meetable_id: form.meetable_id,
      })
    }
    closeModal()
    fetchMeetings()
  } finally {
    saving.value = false
  }
}

function confirmDelete(m) { deleteTarget.value = m }

async function doDelete() {
  deleting.value = true
  try {
    await api.delete(`/meetings/${deleteTarget.value.id}`)
    deleteTarget.value = null
    fetchMeetings()
  } finally {
    deleting.value = false
  }
}

function statusBadge(s) {
  return {
    scheduled: 'bg-primary text-white',
    completed: 'bg-success text-white',
    cancelled: 'bg-secondary text-white',
    no_show:   'bg-warning text-dark',
  }[s] ?? 'bg-secondary text-white'
}

function formatDate(iso) {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
function formatTime(iso) {
  if (!iso) return ''
  return new Date(iso).toLocaleTimeString(isRtl.value ? 'ar-EG' : 'en-US', { hour: '2-digit', minute: '2-digit' })
}

onMounted(fetchMeetings)
</script>

<style scoped>
.modal-backdrop-custom {
  position: fixed; inset: 0; background: rgba(0,0,0,.4);
  z-index: 1055; display: flex; align-items: center; justify-content: center; padding: 16px;
}
.modal-card { background: var(--bs-body-bg, #fff); border-radius: 14px; width: 100%; box-shadow: 0 12px 40px rgba(0,0,0,.18); display: flex; flex-direction: column; max-height: 90vh; }
.modal-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--bs-border-color); }
.modal-card-body { flex: 1; overflow-y: auto; padding: 20px; }
.modal-card-footer { display: flex; gap: 8px; justify-content: flex-end; padding: 14px 20px; border-top: 1px solid var(--bs-border-color); }
</style>
