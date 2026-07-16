<template>
  <CrmLayout>
    <div class="stages-page">

      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">
            {{ isRtl ? 'مراحل خط الصفقات' : 'Deal Pipeline Stages' }}
          </h4>
          <p class="text-muted small mt-1 mb-0">
            {{ isRtl ? 'خصّص مراحل خط الصفقات الخاص بك وأعد ترتيبها' : 'Customize and reorder your deal pipeline stages' }}
          </p>
        </div>
        <button class="btn btn-primary btn-sm" @click="openAdd">
          <i class="bi bi-plus-lg me-1"></i>
          {{ isRtl ? 'مرحلة جديدة' : 'New Stage' }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:300px">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <template v-else>

        <!-- Drag hint -->
        <div class="drag-hint mb-3">
          <i class="bi bi-info-circle me-1"></i>
          {{ isRtl ? 'اسحب الصفوف لإعادة الترتيب. الترتيب يؤثر على لوحة الـ Kanban.' : 'Drag rows to reorder. Order reflects in the Kanban board.' }}
        </div>

        <!-- Stages list -->
        <div class="card card-border">
          <div class="card-header card-header-action">
            <h6 class="mb-0">
              {{ isRtl ? 'مراحل الصفقات' : 'Pipeline Stages' }}
              <span class="badge badge-soft-primary ms-2">{{ stages.length }}</span>
            </h6>
          </div>

          <!-- Empty -->
          <div v-if="!stages.length" class="text-center py-5">
            <div style="font-size:2.5rem;opacity:.25">🏷️</div>
            <p class="text-muted mt-2 mb-3 small">{{ isRtl ? 'لا توجد مراحل بعد' : 'No stages yet' }}</p>
            <button class="btn btn-sm btn-primary" @click="openAdd">
              <i class="bi bi-plus-lg me-1"></i>{{ isRtl ? 'إضافة أول مرحلة' : 'Add first stage' }}
            </button>
          </div>

          <div v-else>
            <draggable
              v-model="stages"
              item-key="id"
              handle=".drag-handle"
              animation="200"
              ghost-class="drag-ghost"
              @end="onReorder"
            >
              <template #item="{ element: stage, index }">
                <div class="stage-row" :class="{ 'stage-row-first': index === 0, 'stage-row-last': index === stages.length - 1 }">
                  <!-- Drag handle -->
                  <div class="drag-handle" :title="isRtl ? 'اسحب لإعادة الترتيب' : 'Drag to reorder'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="8" y1="6"  x2="16" y2="6"/>
                      <line x1="8" y1="12" x2="16" y2="12"/>
                      <line x1="8" y1="18" x2="16" y2="18"/>
                    </svg>
                  </div>

                  <!-- Order badge -->
                  <div class="stage-order">{{ index + 1 }}</div>

                  <!-- Color dot -->
                  <div class="stage-color-dot" :style="{ background: stage.color || '#6b7280' }"></div>

                  <!-- Name + deals count -->
                  <div class="stage-info">
                    <div class="stage-name fw-semibold">{{ stage.name }}</div>
                    <div class="stage-sub text-muted small">
                      {{ stage.deals_count ?? 0 }} {{ isRtl ? 'صفقة' : 'deals' }}
                    </div>
                  </div>

                  <!-- Color preview pill -->
                  <div class="stage-pill" :style="{ background: (stage.color||'#6b7280') + '22', color: stage.color||'#6b7280', border: '1px solid ' + (stage.color||'#6b7280') + '44' }">
                    {{ stage.color || '#6b7280' }}
                  </div>

                  <!-- Actions -->
                  <div class="stage-actions">
                    <button class="btn btn-xs btn-outline-light" @click="openEdit(stage)" :title="isRtl?'تعديل':'Edit'">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button
                      class="btn btn-xs btn-soft-danger"
                      @click="confirmDelete(stage)"
                      :disabled="(stage.deals_count ?? 0) > 0"
                      :title="(stage.deals_count??0) > 0 ? (isRtl?'يوجد صفقات في هذه المرحلة':'Stage has deals') : (isRtl?'حذف':'Delete')"
                    >
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                </div>
              </template>
            </draggable>
          </div>
        </div>

        <!-- Info card -->
        <div class="card card-border mt-4">
          <div class="card-body">
            <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
              <i class="bi bi-lightbulb text-warning"></i>
              {{ isRtl ? 'نصائح مفيدة' : 'Tips' }}
            </h6>
            <ul class="tip-list mb-0">
              <li>{{ isRtl ? 'الترتيب من اليسار إلى اليمين كما يظهر في لوحة Kanban' : 'Order flows left-to-right on the Kanban board' }}</li>
              <li>{{ isRtl ? 'لا يمكن حذف مرحلة بها صفقات — انقل الصفقات أولاً' : "Can't delete a stage with deals — move deals first" }}</li>
              <li>{{ isRtl ? 'المراحل الافتراضية: Lead → Qualified → Proposal → Negotiation → Closed Won → Closed Lost' : 'Default stages: Lead → Qualified → Proposal → Negotiation → Closed Won → Closed Lost' }}</li>
            </ul>
          </div>
        </div>

      </template>
    </div>

    <!-- ═══ ADD / EDIT MODAL ═══ -->
    <div class="modal fade" id="stageFormModal" tabindex="-1" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <div class="d-flex align-items-center gap-3">
              <div class="modal-icon" :style="formMode==='create'?'background:rgba(37,99,235,.1);color:#2563EB':'background:rgba(124,58,237,.1);color:#7C3AED'">
                <i class="bi" :class="formMode==='create'?'bi-plus-circle':'bi-pencil-square'"></i>
              </div>
              <div>
                <h5 class="modal-title mb-0">{{ formMode==='create'?(isRtl?'إضافة مرحلة جديدة':'New Stage'):(isRtl?'تعديل المرحلة':'Edit Stage') }}</h5>
                <p class="text-muted small mb-0 mt-1">{{ isRtl?'اختر اسماً ولوناً مميزاً':'Choose a name and a distinct color' }}</p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body pt-4">
            <form @submit.prevent="saveStage" id="stageForm">
              <!-- Name -->
              <div class="mb-4">
                <label class="form-label fw-medium">{{ isRtl?'اسم المرحلة':'Stage Name' }} <span class="text-danger">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': sv$.name.$error }"
                  :placeholder="isRtl?'مثال: معروض للعميل':'e.g. Proposal Sent'"
                  @blur="sv$.name.$touch()"
                  autofocus
                />
                <div class="invalid-feedback" v-if="sv$.name.$error">{{ sv$.name.$errors[0]?.$message }}</div>
              </div>

              <!-- Color picker -->
              <div class="mb-3">
                <label class="form-label fw-medium">{{ isRtl?'لون المرحلة':'Stage Color' }}</label>
                <!-- Preset swatches -->
                <div class="color-swatches mb-3">
                  <button
                    v-for="c in COLOR_PRESETS"
                    :key="c"
                    type="button"
                    class="color-swatch"
                    :style="{ background: c }"
                    :class="{ 'selected': form.color === c }"
                    @click="form.color = c"
                    :title="c"
                  >
                    <i v-if="form.color === c" class="bi bi-check text-white fw-bold"></i>
                  </button>
                </div>
                <!-- Custom hex input -->
                <div class="d-flex align-items-center gap-2">
                  <input
                    v-model="form.color"
                    type="color"
                    class="form-control form-control-color"
                    style="width:48px;height:38px;padding:2px;cursor:pointer"
                  />
                  <input
                    v-model="form.color"
                    type="text"
                    class="form-control font-monospace"
                    placeholder="#2563EB"
                    maxlength="7"
                    style="width:110px"
                  />
                  <!-- Preview pill -->
                  <div class="stage-preview-pill ms-auto" :style="{ background: form.color + '22', color: form.color, border: '1.5px solid ' + form.color + '55' }">
                    {{ form.name || (isRtl?'معاينة':'Preview') }}
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ isRtl?'إلغاء':'Cancel' }}</button>
            <button type="submit" form="stageForm" class="btn btn-primary px-4" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
              {{ saving?(isRtl?'جاري الحفظ...':'Saving...'):(formMode==='create'?(isRtl?'إضافة المرحلة':'Add Stage'):(isRtl?'حفظ التغييرات':'Save Changes')) }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ DELETE CONFIRM MODAL ═══ -->
    <div class="modal fade" id="stageDeleteModal" tabindex="-1">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body text-center py-4 px-4">
            <div class="del-icon-wrap mb-3">
              <i class="bi bi-trash3 fs-3 text-danger"></i>
            </div>
            <h6 class="mb-1">{{ isRtl?'حذف المرحلة':'Delete Stage' }}</h6>
            <p class="text-muted small mb-3">
              {{ isRtl
                ? `هل أنت متأكد من حذف مرحلة "${deleteTarget?.name}"؟`
                : `Delete stage "${deleteTarget?.name}"?` }}
            </p>
            <div class="d-flex gap-2 justify-content-center">
              <button class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ isRtl?'إلغاء':'Cancel' }}</button>
              <button class="btn btn-sm btn-danger px-3" :disabled="deleting" @click="doDelete">
                <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
                {{ isRtl?'حذف':'Delete' }}
              </button>
            </div>
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
import { required, minLength, helpers } from '@vuelidate/validators'
import draggable from 'vuedraggable'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store  = useStore()
const toast  = useToast()
const isRtl  = computed(() => store.state.ui?.locale === 'ar')

const stages      = ref([])
const loading     = ref(true)
const saving      = ref(false)
const deleting    = ref(false)
const formMode    = ref('create')
const editingId   = ref(null)
const deleteTarget= ref(null)

const COLOR_PRESETS = [
  '#2563EB','#7C3AED','#DB2777','#D97706','#059669',
  '#EF4444','#0891B2','#F59E0B','#10B981','#6366F1',
  '#64748B','#1E293B',
]

const form = ref({ name: '', color: '#2563EB' })

// ── Validation ────────────────────────────────────────────
const stageRules = computed(() => ({
  name: {
    required: helpers.withMessage(isRtl.value ? 'اسم المرحلة مطلوب' : 'Stage name is required', required),
    minLength: helpers.withMessage(isRtl.value ? 'حرفان على الأقل' : 'At least 2 characters', minLength(2)),
  },
}))
const sv$ = useVuelidate(stageRules, form)

// ── Bootstrap modals ──────────────────────────────────────
function getFormModal()   { return window.bootstrap?.Modal.getOrCreateInstance(document.getElementById('stageFormModal')) }
function getDeleteModal() { return window.bootstrap?.Modal.getOrCreateInstance(document.getElementById('stageDeleteModal')) }

// ── Fetch stages ──────────────────────────────────────────
async function fetchStages() {
  loading.value = true
  try {
    const { data } = await api.get('/deal-stages')
    stages.value = data.data || []
  } catch {
    toast.error(isRtl.value ? 'فشل جلب المراحل' : 'Failed to load stages')
  } finally {
    loading.value = false
  }
}

// ── Add ───────────────────────────────────────────────────
function openAdd() {
  formMode.value  = 'create'
  editingId.value = null
  form.value      = { name: '', color: '#2563EB' }
  sv$.value.$reset()
  nextTick(() => getFormModal()?.show())
}

// ── Edit ──────────────────────────────────────────────────
function openEdit(stage) {
  formMode.value  = 'edit'
  editingId.value = stage.id
  form.value      = { name: stage.name, color: stage.color || '#2563EB' }
  sv$.value.$reset()
  nextTick(() => getFormModal()?.show())
}

// ── Save ──────────────────────────────────────────────────
async function saveStage() {
  const valid = await sv$.value.$validate()
  if (!valid) return
  saving.value = true
  try {
    if (formMode.value === 'create') {
      const maxOrder = stages.value.length ? Math.max(...stages.value.map(s => s.order || 0)) : 0
      await api.post('/deal-stages', { ...form.value, order: maxOrder + 1 })
      toast.success(isRtl.value ? 'تمت إضافة المرحلة' : 'Stage added')
    } else {
      await api.put(`/deal-stages/${editingId.value}`, form.value)
      toast.success(isRtl.value ? 'تم تحديث المرحلة' : 'Stage updated')
    }
    getFormModal()?.hide()
    await fetchStages()
  } catch (err) {
    toast.error(err.response?.data?.message || (isRtl.value ? 'حدث خطأ' : 'Something went wrong'))
  } finally {
    saving.value = false
  }
}

// ── Delete ────────────────────────────────────────────────
function confirmDelete(stage) {
  deleteTarget.value = stage
  nextTick(() => getDeleteModal()?.show())
}

async function doDelete() {
  deleting.value = true
  try {
    await api.delete(`/deal-stages/${deleteTarget.value.id}`)
    toast.success(isRtl.value ? 'تم حذف المرحلة' : 'Stage deleted')
    getDeleteModal()?.hide()
    await fetchStages()
  } catch (err) {
    toast.error(err.response?.data?.message || (isRtl.value ? 'حدث خطأ' : 'Something went wrong'))
  } finally {
    deleting.value = false
  }
}

// ── Reorder ───────────────────────────────────────────────
async function onReorder() {
  const ordered = stages.value.map((s, i) => ({ id: s.id, order: i + 1 }))
  try {
    await api.put('/deal-stages/reorder', { stages: ordered })
  } catch {
    // silently reload on error
    await fetchStages()
  }
}

onMounted(fetchStages)
</script>

<style scoped>
.stages-page { max-width: 780px; }

/* ─── Drag hint ──────────────────────────────────────────── */
.drag-hint {
  font-size: 12.5px;
  color: var(--bs-secondary-color, #64748B);
  display: flex;
  align-items: center;
}

/* ─── Stage row ──────────────────────────────────────────── */
.stage-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  border-bottom: 1px solid var(--bs-border-color-translucent, rgba(0,0,0,.06));
  transition: background 0.15s;
}
.stage-row:hover { background: var(--bs-tertiary-bg, rgba(37,99,235,.02)); }
.stage-row-last  { border-bottom: none; }

.drag-handle {
  cursor: grab;
  color: var(--bs-secondary-color, #9CA3AF);
  padding: 4px;
  border-radius: 4px;
  flex-shrink: 0;
  line-height: 1;
}
.drag-handle:hover { color: var(--bs-body-color); background: var(--bs-tertiary-bg); }
.drag-handle:active { cursor: grabbing; }

.stage-order {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: var(--bs-tertiary-bg, #F1F5F9);
  color: var(--bs-secondary-color, #64748B);
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stage-color-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  flex-shrink: 0;
  box-shadow: 0 0 0 2px white, 0 0 0 3px currentColor;
}

.stage-info { flex: 1; min-width: 0; }
.stage-name { font-size: 14px; }
.stage-sub  { font-size: 12px; margin-top: 1px; }

.stage-pill {
  font-size: 11px;
  font-weight: 600;
  font-family: monospace;
  padding: 3px 10px;
  border-radius: 20px;
  flex-shrink: 0;
}

.stage-actions { display: flex; gap: 6px; flex-shrink: 0; }

/* ─── Drag ghost ─────────────────────────────────────────── */
.drag-ghost {
  opacity: 0.4;
  background: rgba(37,99,235,.06) !important;
}

/* ─── Color swatches ─────────────────────────────────────── */
.color-swatches {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.color-swatch {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  border: 2px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  transition: transform 0.1s, border-color 0.1s;
  flex-shrink: 0;
}
.color-swatch:hover   { transform: scale(1.15); }
.color-swatch.selected { border-color: #fff; box-shadow: 0 0 0 2px #1e293b; }

/* ─── Stage preview pill ─────────────────────────────────── */
.stage-preview-pill {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  max-width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ─── Modal icon ─────────────────────────────────────────── */
.modal-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

/* ─── Delete icon ────────────────────────────────────────── */
.del-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

/* ─── Tips list ──────────────────────────────────────────── */
.tip-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.tip-list li {
  font-size: 13px;
  color: var(--bs-secondary-color, #64748B);
  padding-inline-start: 16px;
  position: relative;
}
.tip-list li::before {
  content: '•';
  position: absolute;
  inset-inline-start: 0;
  color: var(--crm-primary, #2563EB);
  font-weight: 700;
}

/* ─── Soft danger btn ────────────────────────────────────── */
.btn-soft-danger {
  background: rgba(239,68,68,.1);
  color: #EF4444;
  border: none;
}
.btn-soft-danger:hover:not(:disabled) { background: rgba(239,68,68,.2); }
.btn-soft-danger:disabled { opacity: 0.4; cursor: not-allowed; }

.badge-soft-primary { background: rgba(37,99,235,.12); color: #1D4ED8; }
</style>
