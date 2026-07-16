<template>
  <CrmLayout>
    <div>
      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between mb-4 gap-3 flex-wrap">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">{{ t('calls_title') }}</h4>
          <p class="text-muted small mt-1 mb-0">{{ t('calls_sub') }}</p>
        </div>
        <button class="btn btn-primary btn-sm" @click="openAdd">
          <i class="bi bi-plus-lg me-1"></i>{{ t('log_call') }}
        </button>
      </div>

      <!-- Filters -->
      <div class="d-flex gap-2 mb-3 flex-wrap">
        <select class="form-select form-select-sm" style="max-width:150px" v-model="filters.outcome" @change="fetchCalls">
          <option value="">{{ t('all_outcomes') }}</option>
          <option value="answered">{{ t('outcome_answered') }}</option>
          <option value="no_answer">{{ t('outcome_no_answer') }}</option>
          <option value="voicemail">{{ t('outcome_voicemail') }}</option>
          <option value="busy">{{ t('outcome_busy') }}</option>
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
                <th>{{ t('direction') }}</th>
                <th>{{ t('called_at') }}</th>
                <th>{{ t('duration') }}</th>
                <th>{{ t('outcome') }}</th>
                <th>{{ t('linked_to') }}</th>
                <th>{{ t('logged_by') }}</th>
                <th class="text-end">{{ t('actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!calls.length">
                <td colspan="7" class="text-center text-muted py-5">
                  <i class="bi bi-telephone-x fs-1 d-block mb-2 opacity-25"></i>
                  {{ t('no_calls') }}
                </td>
              </tr>
              <tr v-for="c in calls" :key="c.id">
                <td>
                  <span class="badge" :class="c.direction === 'inbound' ? 'bg-info text-dark' : 'bg-primary text-white'">
                    <i class="bi" :class="c.direction === 'inbound' ? 'bi-telephone-inbound' : 'bi-telephone-outbound'"></i>
                    {{ t('dir_' + c.direction) }}
                  </span>
                </td>
                <td class="small">{{ formatDate(c.called_at) }}</td>
                <td class="small">{{ formatDuration(c.duration_seconds) }}</td>
                <td>
                  <span class="badge" :class="outcomeBadge(c.outcome)">{{ t('outcome_' + c.outcome) }}</span>
                </td>
                <td>
                  <span v-if="c.callable" class="small text-muted">{{ c.callable.name }}</span>
                </td>
                <td class="small">{{ c.user?.name ?? '—' }}</td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(c)" :title="t('delete')">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="meta.last_page > 1" class="card-footer d-flex align-items-center justify-content-between py-2">
          <span class="small text-muted">{{ t('total') }}: {{ meta.total }}</span>
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-outline-secondary" :disabled="meta.current_page <= 1" @click="page--; fetchCalls()">‹</button>
            <span class="btn btn-sm btn-outline-secondary disabled">{{ meta.current_page }} / {{ meta.last_page }}</span>
            <button class="btn btn-sm btn-outline-secondary" :disabled="meta.current_page >= meta.last_page" @click="page++; fetchCalls()">›</button>
          </div>
        </div>
      </div>

      <!-- Log Call Modal -->
      <Teleport to="body">
        <div v-if="showModal" class="modal-backdrop-custom" @click.self="closeModal">
          <div class="modal-card" style="max-width:480px">
            <div class="modal-card-header">
              <h6 class="mb-0 fw-bold">{{ t('log_call') }}</h6>
              <button class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-card-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('direction') }} *</label>
                  <select v-model="form.direction" class="form-select form-select-sm">
                    <option value="outbound">{{ t('dir_outbound') }}</option>
                    <option value="inbound">{{ t('dir_inbound') }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('outcome') }} *</label>
                  <select v-model="form.outcome" class="form-select form-select-sm" :class="{ 'is-invalid': errors.outcome }">
                    <option value="answered">{{ t('outcome_answered') }}</option>
                    <option value="no_answer">{{ t('outcome_no_answer') }}</option>
                    <option value="voicemail">{{ t('outcome_voicemail') }}</option>
                    <option value="busy">{{ t('outcome_busy') }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('called_at') }} *</label>
                  <input type="datetime-local" v-model="form.called_at" class="form-control form-control-sm" :class="{ 'is-invalid': errors.called_at }" />
                  <div class="invalid-feedback">{{ errors.called_at }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('duration_sec') }}</label>
                  <input type="number" v-model="form.duration_seconds" class="form-control form-control-sm" min="0" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('linked_to') }} *</label>
                  <select v-model="form.callable_type" class="form-select form-select-sm" @change="form.callable_id = ''">
                    <option value="contact">{{ t('contact') }}</option>
                    <option value="deal">{{ t('deal') }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('select') }} *</label>
                  <select v-model="form.callable_id" class="form-select form-select-sm" :class="{ 'is-invalid': errors.callable_id }">
                    <option value="">— {{ t('select') }} —</option>
                    <option v-for="item in callableOptions" :key="item.id" :value="item.id">{{ item.name }}</option>
                  </select>
                  <div class="invalid-feedback">{{ errors.callable_id }}</div>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ t('notes') }}</label>
                  <textarea v-model="form.notes" class="form-control form-control-sm" rows="2"></textarea>
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
          <div class="modal-card" style="max-width:360px">
            <div class="modal-card-header">
              <h6 class="mb-0 fw-bold text-danger">{{ t('confirm_delete') }}</h6>
              <button class="btn-close" @click="deleteTarget = null"></button>
            </div>
            <div class="modal-card-body"><p class="mb-0">{{ t('delete_call_confirm') }}</p></div>
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
  calls_title:'المكالمات', calls_sub:'تسجيل وتتبع المكالمات', log_call:'تسجيل مكالمة',
  direction:'الاتجاه', called_at:'وقت المكالمة', duration:'المدة', duration_sec:'المدة (ثانية)',
  outcome:'النتيجة', linked_to:'مرتبط بـ', logged_by:'سجّلها', actions:'الإجراءات',
  no_calls:'لا توجد مكالمات', all_outcomes:'كل النتائج',
  dir_inbound:'واردة', dir_outbound:'صادرة',
  outcome_answered:'أُجيب', outcome_no_answer:'لم يُجَب', outcome_voicemail:'بريد صوتي', outcome_busy:'مشغول',
  contact:'جهة اتصال', deal:'صفقة', select:'اختر', notes:'ملاحظات',
  save:'حفظ', cancel:'إلغاء', delete:'حذف', total:'الإجمالي',
  confirm_delete:'تأكيد الحذف', delete_call_confirm:'هل تريد حذف هذه المكالمة؟',
}
const EN = {
  calls_title:'Calls', calls_sub:'Log and track your calls', log_call:'Log Call',
  direction:'Direction', called_at:'Called At', duration:'Duration', duration_sec:'Duration (sec)',
  outcome:'Outcome', linked_to:'Linked To', logged_by:'Logged By', actions:'Actions',
  no_calls:'No calls logged yet', all_outcomes:'All Outcomes',
  dir_inbound:'Inbound', dir_outbound:'Outbound',
  outcome_answered:'Answered', outcome_no_answer:'No Answer', outcome_voicemail:'Voicemail', outcome_busy:'Busy',
  contact:'Contact', deal:'Deal', select:'Select', notes:'Notes',
  save:'Save', cancel:'Cancel', delete:'Delete', total:'Total',
  confirm_delete:'Confirm Delete', delete_call_confirm:'Delete this call log?',
}
const t = (key) => (isRtl.value ? AR : EN)[key] ?? key

const calls   = ref([])
const loading = ref(true)
const page    = ref(1)
const meta    = ref({ current_page: 1, last_page: 1, total: 0 })
const filters = reactive({ outcome: '' })

const showModal    = ref(false)
const saving       = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)
const errors       = reactive({})
const callableOptions = ref([])

const emptyForm = () => ({
  direction: 'outbound', outcome: 'answered', called_at: new Date().toISOString().slice(0,16),
  duration_seconds: 0, callable_type: 'contact', callable_id: '', notes: '',
})
const form = reactive(emptyForm())

async function fetchCalls() {
  loading.value = true
  try {
    const params = { page: page.value }
    if (filters.outcome) params.outcome = filters.outcome
    const { data } = await api.get('/calls', { params })
    calls.value = data.data ?? []
    meta.value  = data.meta ?? {}
  } finally {
    loading.value = false
  }
}

async function fetchCallableOptions() {
  const type = form.callable_type
  const endpoint = type === 'contact' ? '/contacts' : '/deals'
  const { data } = await api.get(endpoint, { params: { per_page: 100 } })
  callableOptions.value = (data.data ?? []).map(item => ({
    id: item.id,
    name: type === 'contact'
      ? ((item.first_name ?? '') + ' ' + (item.last_name ?? '')).trim()
      : item.title,
  }))
}

watch(() => form.callable_type, fetchCallableOptions)

function openAdd() {
  Object.assign(form, emptyForm())
  Object.keys(errors).forEach(k => delete errors[k])
  showModal.value = true
  fetchCallableOptions()
}

function closeModal() { showModal.value = false }

async function save() {
  Object.keys(errors).forEach(k => delete errors[k])
  if (!form.called_at) { errors.called_at = 'required'; return }
  if (!form.callable_id) { errors.callable_id = 'required'; return }

  saving.value = true
  try {
    await api.post('/calls', { ...form })
    closeModal()
    fetchCalls()
  } finally {
    saving.value = false
  }
}

function confirmDelete(c) { deleteTarget.value = c }

async function doDelete() {
  deleting.value = true
  try {
    await api.delete(`/calls/${deleteTarget.value.id}`)
    deleteTarget.value = null
    fetchCalls()
  } finally {
    deleting.value = false
  }
}

function outcomeBadge(o) {
  return { answered:'bg-success text-white', no_answer:'bg-secondary text-white', voicemail:'bg-info text-dark', busy:'bg-warning text-dark' }[o] ?? 'bg-secondary text-white'
}

function formatDate(iso) {
  if (!iso) return '—'
  return new Date(iso).toLocaleString(isRtl.value ? 'ar-EG' : 'en-US', { month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' })
}

function formatDuration(sec) {
  if (!sec) return '—'
  const m = Math.floor(sec / 60)
  const s = sec % 60
  return m > 0 ? `${m}m ${s}s` : `${s}s`
}

onMounted(fetchCalls)
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
