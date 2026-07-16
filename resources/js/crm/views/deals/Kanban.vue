<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">
          {{ locale === 'ar' ? 'لوحة المهام' : 'Deals Board' }}
        </h4>
        <p class="text-muted mb-0 small mt-1">
          {{ totalDeals }} {{ locale === 'ar' ? 'صفقة' : 'deals' }} &middot;
          ${{ totalValue.toLocaleString() }} {{ locale === 'ar' ? 'إجمالي القيمة' : 'total value' }}
        </p>
      </div>
      <div class="d-flex gap-2 flex-shrink-0">
        <router-link to="/crm/deals/list" class="btn btn-sm btn-outline-light">
          <i class="bi bi-list-ul me-1"></i>{{ locale === 'ar' ? 'عرض القائمة' : 'List View' }}
        </router-link>
        <button class="btn btn-sm btn-primary" @click="openAddModal()">
          <i class="bi bi-plus-lg me-1"></i>{{ locale === 'ar' ? 'صفقة جديدة' : 'New Deal' }}
        </button>
      </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4 g-3">
      <div class="col-6 col-xl-3">
        <div class="card card-border h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="k-stat-icon" style="background:rgba(37,99,235,.1);color:#2563EB">
              <i class="bi bi-kanban fs-5"></i>
            </div>
            <div>
              <div class="k-stat-num">{{ totalDeals }}</div>
              <div class="k-stat-lbl">{{ locale === 'ar' ? 'إجمالي الصفقات' : 'Total Deals' }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="k-stat-icon" style="background:rgba(16,185,129,.1);color:#059669">
              <i class="bi bi-currency-dollar fs-5"></i>
            </div>
            <div>
              <div class="k-stat-num">${{ totalValue.toLocaleString() }}</div>
              <div class="k-stat-lbl">{{ locale === 'ar' ? 'إجمالي القيمة' : 'Total Value' }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="k-stat-icon" style="background:rgba(245,158,11,.1);color:#D97706">
              <i class="bi bi-hourglass-split fs-5"></i>
            </div>
            <div>
              <div class="k-stat-num">{{ openDeals }}</div>
              <div class="k-stat-lbl">{{ locale === 'ar' ? 'صفقات مفتوحة' : 'Open Deals' }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="k-stat-icon" style="background:rgba(16,185,129,.1);color:#059669">
              <i class="bi bi-trophy fs-5"></i>
            </div>
            <div>
              <div class="k-stat-num">{{ wonDeals }}</div>
              <div class="k-stat-lbl">{{ locale === 'ar' ? 'صفقات رابحة' : 'Won Deals' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:300px">
      <div class="text-center">
        <div class="spinner-border text-primary mb-2" role="status"></div>
        <div class="text-muted small">{{ locale === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</div>
      </div>
    </div>

    <!-- Kanban Board -->
    <div v-else class="kanban-board">
      <div v-for="stage in stages" :key="stage.id" class="kanban-col">

        <!-- Column Header -->
        <div class="kanban-col-header" :style="{ borderTopColor: stage.color }">
          <div class="d-flex align-items-center gap-2">
            <span class="kanban-stage-dot" :style="{ background: stage.color }"></span>
            <span class="fw-semibold" style="font-size:13px">{{ stage.name }}</span>
          </div>
          <span class="kanban-badge" :style="{ background: stage.color + '22', color: stage.color }">
            {{ (dealsByStage[stage.id] || []).length }}
          </span>
        </div>

        <!-- Column Value -->
        <div class="kanban-col-value" v-if="(dealsByStage[stage.id]||[]).length">
          ${{ (dealsByStage[stage.id]||[]).reduce((s,d)=>s+Number(d.value||0),0).toLocaleString() }}
        </div>

        <!-- Draggable Cards -->
        <draggable
          v-model="dealsByStage[stage.id]"
          group="deals"
          item-key="id"
          class="kanban-cards"
          ghost-class="kanban-ghost"
          drag-class="kanban-drag"
          @change="onCardChange($event, stage.id)"
        >
          <template #item="{ element: deal }">
            <div class="kanban-card" @click="openEditModal(deal)">
              <!-- Status badge (won/lost) -->
              <div v-if="deal.status !== 'open'" class="deal-status-badge mb-2"
                :class="deal.status === 'won' ? 'status-won' : 'status-lost'">
                <i class="bi me-1" :class="deal.status === 'won' ? 'bi-trophy' : 'bi-x-circle'"></i>
                {{ deal.status === 'won' ? (locale === 'ar' ? 'رابح' : 'Won') : (locale === 'ar' ? 'خاسر' : 'Lost') }}
              </div>

              <div class="deal-title">{{ deal.title }}</div>

              <div v-if="deal.contact" class="deal-contact">
                <i class="bi bi-person me-1"></i>{{ deal.contact.first_name }} {{ deal.contact.last_name }}
              </div>

              <div class="deal-footer mt-2">
                <span class="deal-value">${{ Number(deal.value || 0).toLocaleString() }}</span>
                <div class="deal-prob">
                  <div class="prob-bar">
                    <div class="prob-fill" :style="{ width:(deal.probability||0)+'%', background:stage.color }"></div>
                  </div>
                  <span class="prob-text">{{ deal.probability || 0 }}%</span>
                </div>
              </div>

              <div v-if="deal.expected_close_date" class="deal-date" :class="{ overdue: isOverdue(deal) }">
                <i class="bi bi-calendar3 me-1"></i>{{ formatDate(deal.expected_close_date) }}
              </div>

              <!-- Quick Win/Lost buttons (only for open deals) -->
              <div v-if="deal.status === 'open'" class="deal-quick-actions" @click.stop>
                <button class="quick-btn quick-won" @click.stop="openWonLostModal(deal, 'won')" :title="locale==='ar'?'علّم رابح':'Mark Won'">
                  <i class="bi bi-trophy-fill"></i>
                </button>
                <button class="quick-btn quick-lost" @click.stop="openWonLostModal(deal, 'lost')" :title="locale==='ar'?'علّم خاسر':'Mark Lost'">
                  <i class="bi bi-x-circle-fill"></i>
                </button>
              </div>
            </div>
          </template>
          <template #footer>
            <div class="kanban-add-btn" @click="openAddModal(stage)">
              <i class="bi bi-plus"></i> {{ locale === 'ar' ? 'إضافة صفقة' : 'Add Deal' }}
            </div>
          </template>
        </draggable>
      </div>
    </div><!-- /kanban-board -->


    <!-- ═══ ADD / EDIT MODAL ═══ -->
    <div class="modal fade" id="dealFormModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header pb-0 border-0">
            <div class="d-flex align-items-center gap-3">
              <div class="modal-icon" :style="formMode==='create'?'background:rgba(37,99,235,.1);color:#2563EB':'background:rgba(124,58,237,.1);color:#7C3AED'">
                <i class="bi" :class="formMode==='create'?'bi-plus-circle':'bi-pencil-square'"></i>
              </div>
              <div>
                <h5 class="modal-title mb-0">
                  {{ formMode==='create' ? (locale==='ar'?'إضافة صفقة جديدة':'New Deal') : (locale==='ar'?'تعديل الصفقة':'Edit Deal') }}
                </h5>
                <p class="text-muted small mb-0 mt-1">
                  {{ formMode==='create' ? (locale==='ar'?'أدخل بيانات الصفقة':'Fill in the deal details') : (locale==='ar'?'تحديث بيانات الصفقة':'Update the deal information') }}
                </p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body pt-4">
            <form @submit.prevent="saveDeal" id="dealForm">

              <!-- Deal Info -->
              <div class="fsect-title"><i class="bi bi-briefcase me-2 text-primary"></i>{{ locale==='ar'?'معلومات الصفقة':'Deal Information' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-12">
                  <label class="form-label">{{ locale==='ar'?'عنوان الصفقة':'Deal Title' }} <span class="text-danger">*</span></label>
                  <input v-model="form.title" type="text" class="form-control"
                    :class="{ 'is-invalid': v$.title.$error, 'is-valid': v$.title.$dirty && !v$.title.$error && form.title }"
                    :placeholder="locale==='ar'?'مثال: عقد توريد مستلزمات':'e.g. Software License Deal'"
                    @blur="v$.title.$touch()" />
                  <div class="invalid-feedback" v-if="v$.title.$error">{{ v$.title.$errors[0]?.$message }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'المرحلة':'Stage' }} <span class="text-danger">*</span></label>
                  <Select
                    v-model="form.stage_id"
                    :options="stageOptions"
                    option-label="name"
                    option-value="id"
                    :placeholder="locale==='ar'?'اختر المرحلة':'Select stage'"
                    :invalid="v$.stage_id.$error"
                    class="w-100"
                    append-to="body"
                    @blur="v$.stage_id.$touch()"
                  />
                  <small class="text-danger d-block mt-1" v-if="v$.stage_id.$error">{{ v$.stage_id.$errors[0]?.$message }}</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'حالة الصفقة':'Deal Status' }}</label>
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
              </div>

              <!-- Deal Details -->
              <div class="fsect-title"><i class="bi bi-bar-chart me-2 text-primary"></i>{{ locale==='ar'?'تفاصيل الصفقة':'Deal Details' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'القيمة ($)':'Value ($)' }}</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input v-model.number="form.value" type="number" min="0" step="0.01" class="form-control"
                      :class="{ 'is-invalid': v$.value.$error }"
                      placeholder="0.00" @blur="v$.value.$touch()" />
                    <div class="invalid-feedback" v-if="v$.value.$error">{{ v$.value.$errors[0]?.$message }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'احتمالية الإغلاق (%)':'Probability (%)' }}</label>
                  <div class="input-group">
                    <input v-model.number="form.probability" type="number" min="0" max="100" class="form-control"
                      :class="{ 'is-invalid': v$.probability.$error }"
                      placeholder="50" @blur="v$.probability.$touch()" />
                    <span class="input-group-text">%</span>
                    <div class="invalid-feedback" v-if="v$.probability.$error">{{ v$.probability.$errors[0]?.$message }}</div>
                  </div>
                  <!-- Probability bar preview -->
                  <div class="mt-2" v-if="form.probability >= 0">
                    <div class="prob-preview-bar">
                      <div class="prob-preview-fill"
                        :style="{ width: Math.min(form.probability||0,100)+'%', background: probColor(form.probability) }"></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'تاريخ الإغلاق المتوقع':'Expected Close Date' }}</label>
                  <DatePicker
                    v-model="closeDate"
                    date-format="dd/mm/yy"
                    :placeholder="locale==='ar'?'يوم/شهر/سنة':'DD/MM/YYYY'"
                    show-icon
                    icon-display="input"
                    class="w-100"
                    append-to="body"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'جهة الاتصال':'Contact' }}</label>
                  <Select
                    v-model="form.contact_id"
                    :options="contactOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'— بدون جهة اتصال —':'— No contact —'"
                    :show-clear="true"
                    class="w-100"
                    append-to="body"
                  />
                </div>
              </div>

              <!-- Lost Reason (shown only when status = lost) -->
              <div v-if="form.status === 'lost'" class="mb-4">
                <div class="fsect-title"><i class="bi bi-x-circle me-2 text-danger"></i>{{ locale==='ar'?'سبب الخسارة':'Lost Reason' }}</div>
                <input
                  v-model="form.lost_reason"
                  type="text"
                  class="form-control"
                  :placeholder="locale==='ar'?'مثال: السعر مرتفع، ذهب للمنافس...' :'e.g. Price too high, went with competitor...'"
                />
              </div>

              <!-- Notes -->
              <div class="fsect-title"><i class="bi bi-card-text me-2 text-primary"></i>{{ locale==='ar'?'ملاحظات':'Notes' }}</div>
              <textarea v-model="form.notes" class="form-control" rows="3"
                :placeholder="locale==='ar'?'أضف أي ملاحظات مهمة...':'Add any important notes...'"></textarea>
            </form>
          </div>
          <div class="modal-footer">
            <button v-if="formMode==='edit'" type="button" class="btn btn-outline-danger me-auto" @click="openDeleteModal">
              <i class="bi bi-trash3 me-1"></i>{{ locale==='ar'?'حذف الصفقة':'Delete Deal' }}
            </button>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="submit" form="dealForm" class="btn btn-primary px-4" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
              {{ saving ? (locale==='ar'?'جاري الحفظ...':'Saving...') : (formMode==='create'?(locale==='ar'?'إضافة الصفقة':'Add Deal'):(locale==='ar'?'حفظ التغييرات':'Save Changes')) }}
            </button>
          </div>
        </div>
      </div>
    </div><!-- /dealFormModal -->


    <!-- ═══ DELETE CONFIRM MODAL ═══ -->
    <div class="modal fade" id="dealDeleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
          <div class="modal-body text-center px-5 pt-5 pb-3">
            <div class="del-icon mb-4"><i class="bi bi-trash3"></i></div>
            <h5 class="mb-2">{{ locale==='ar'?'تأكيد الحذف':'Confirm Delete' }}</h5>
            <p class="text-muted small mb-0">
              {{ locale==='ar'
                ? `هل أنت متأكد من حذف "${editingDeal?.title}"؟ لا يمكن التراجع.`
                : `Delete "${editingDeal?.title}"? This cannot be undone.` }}
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 pb-4 gap-2">
            <button type="button" class="btn btn-light px-4" @click="closeDeleteModal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="button" class="btn btn-danger px-4" @click="executeDel" :disabled="deleting">
              <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
              {{ locale==='ar'?'نعم، احذف':'Yes, Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div><!-- /dealDeleteModal -->

    <!-- ═══ WON / LOST MODAL ═══ -->
    <div class="modal fade" id="dealOutcomeModal" tabindex="-1" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <div class="d-flex align-items-center gap-3">
              <div class="outcome-modal-icon" :class="outcomeType==='won' ? 'icon-won' : 'icon-lost'">
                <i class="bi" :class="outcomeType==='won' ? 'bi-trophy-fill' : 'bi-x-circle-fill'"></i>
              </div>
              <div>
                <h5 class="modal-title mb-0">
                  {{ outcomeType==='won'
                    ? (locale==='ar' ? 'تسجيل صفقة رابحة' : 'Mark as Won')
                    : (locale==='ar' ? 'تسجيل صفقة خاسرة' : 'Mark as Lost') }}
                </h5>
                <p class="text-muted small mb-0 mt-1">{{ outcomeTarget?.title }}</p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body pt-4">
            <!-- Close date -->
            <div class="mb-3">
              <label class="form-label fw-medium">{{ locale==='ar' ? 'تاريخ الإغلاق' : 'Close Date' }}</label>
              <input v-model="outcomeForm.closed_at" type="date" class="form-control" />
            </div>

            <!-- Lost reason (lost only) -->
            <div v-if="outcomeType==='lost'" class="mb-3">
              <label class="form-label fw-medium">{{ locale==='ar' ? 'سبب الخسارة' : 'Lost Reason' }}</label>
              <select v-model="outcomeForm.lost_reason" class="form-select">
                <option value="">{{ locale==='ar' ? 'اختر السبب' : 'Select reason' }}</option>
                <option value="price">{{ locale==='ar' ? 'السعر' : 'Price' }}</option>
                <option value="competitor">{{ locale==='ar' ? 'منافس' : 'Competitor' }}</option>
                <option value="no_budget">{{ locale==='ar' ? 'لا ميزانية' : 'No Budget' }}</option>
                <option value="no_need">{{ locale==='ar' ? 'لا حاجة' : 'No Need' }}</option>
                <option value="timing">{{ locale==='ar' ? 'التوقيت' : 'Timing' }}</option>
                <option value="other">{{ locale==='ar' ? 'أخرى' : 'Other' }}</option>
              </select>
            </div>

            <!-- Notes -->
            <div class="mb-1">
              <label class="form-label fw-medium">{{ locale==='ar' ? 'ملاحظات (اختياري)' : 'Notes (optional)' }}</label>
              <textarea v-model="outcomeForm.notes" class="form-control" rows="2" :placeholder="locale==='ar'?'إضافة ملاحظات...':'Add notes...'"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ locale==='ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="button" class="btn px-4" :class="outcomeType==='won'?'btn-success':'btn-danger'" :disabled="savingOutcome" @click="saveOutcome">
              <span v-if="savingOutcome" class="spinner-border spinner-border-sm me-2"></span>
              {{ outcomeType==='won'
                ? (locale==='ar' ? 'تسجيل كرابحة' : 'Mark as Won')
                : (locale==='ar' ? 'تسجيل كخاسرة' : 'Mark as Lost') }}
            </button>
          </div>
        </div>
      </div>
    </div><!-- /dealOutcomeModal -->

  </CrmLayout>
</template>

<script setup>
import { ref, computed, reactive, onMounted, nextTick } from 'vue'
import { useStore } from 'vuex'
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, maxLength, minValue, maxValue, helpers } from '@vuelidate/validators'
import draggable from 'vuedraggable'
import DatePicker from 'primevue/datepicker'
import Select from 'primevue/select'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store  = useStore()
const toast  = useToast()
const locale = computed(() => store.state.ui.locale)

// ── Board data ─────────────────────────────────────────
const stages       = ref([])
const dealsByStage = reactive({})
const loading      = ref(true)
const contactsList = ref([])

// ── Stats ──────────────────────────────────────────────
const allDeals    = computed(() => Object.values(dealsByStage).flat())
const totalDeals  = computed(() => allDeals.value.length)
const totalValue  = computed(() => allDeals.value.reduce((s, d) => s + Number(d.value || 0), 0))
const openDeals   = computed(() => allDeals.value.filter(d => d.status === 'open').length)
const wonDeals    = computed(() => allDeals.value.filter(d => d.status === 'won').length)

// ── Form ───────────────────────────────────────────────
const formMode    = ref('create')
const editingDeal = ref(null)
const saving      = ref(false)
const form        = ref(emptyForm())

function emptyForm() {
  return { title: '', stage_id: null, status: 'open', value: 0, probability: 50, expected_close_date: '', contact_id: null, notes: '', lost_reason: '' }
}

// ── Vuelidate ──────────────────────────────────────────
const validationRules = computed(() => {
  const ar = locale.value === 'ar'
  return {
    title: {
      required:  helpers.withMessage(ar ? 'عنوان الصفقة مطلوب'          : 'Deal title is required',    required),
      minLength: helpers.withMessage(ar ? 'يجب أن يكون حرفين على الأقل' : 'At least 2 characters',     minLength(2)),
      maxLength: helpers.withMessage(ar ? 'الحد الأقصى 100 حرف'          : 'Max 100 characters',         maxLength(100)),
    },
    stage_id: {
      required: helpers.withMessage(ar ? 'المرحلة مطلوبة' : 'Stage is required', required),
    },
    value: {
      minValue: helpers.withMessage(ar ? 'القيمة يجب أن تكون 0 أو أكثر' : 'Value must be 0 or more', minValue(0)),
    },
    probability: {
      minValue: helpers.withMessage(ar ? 'الاحتمالية من 0 إلى 100' : 'Must be between 0 and 100', minValue(0)),
      maxValue: helpers.withMessage(ar ? 'الاحتمالية من 0 إلى 100' : 'Must be between 0 and 100', maxValue(100)),
    },
    status:              {},
    contact_id:          {},
    expected_close_date: {},
    notes:               {},
    lost_reason:         {},
  }
})
const v$ = useVuelidate(validationRules, form)

// ── PrimeVue date ↔ string bridge ─────────────────────
const closeDate = computed({
  get: () => form.value.expected_close_date ? new Date(form.value.expected_close_date + 'T00:00:00') : null,
  set: (d) => {
    form.value.expected_close_date = d
      ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
      : ''
  }
})

// ── Select options ─────────────────────────────────────
const stageOptions   = computed(() => stages.value)
const statusOptions  = computed(() => [
  { label: locale.value==='ar' ? 'مفتوحة' : 'Open',  value: 'open'  },
  { label: locale.value==='ar' ? 'رابحة'  : 'Won',   value: 'won'   },
  { label: locale.value==='ar' ? 'خاسرة'  : 'Lost',  value: 'lost'  },
])
const contactOptions = computed(() => contactsList.value.map(c => ({
  label: `${c.first_name} ${c.last_name}${c.company ? ' — ' + c.company : ''}`,
  value: c.id,
})))

// ── Delete ─────────────────────────────────────────────
const deleting = ref(false)

// ── Bootstrap instances ────────────────────────────────
function getFormModal()   { return bootstrap.Modal.getOrCreateInstance(document.getElementById('dealFormModal')) }
function getDeleteModal() { return bootstrap.Modal.getOrCreateInstance(document.getElementById('dealDeleteModal')) }

// ── Visual helpers ─────────────────────────────────────
function probColor(p) {
  if (p >= 75) return '#10B981'
  if (p >= 50) return '#2563EB'
  if (p >= 25) return '#F59E0B'
  return '#EF4444'
}
function isOverdue(deal) {
  return deal.expected_close_date && new Date(deal.expected_close_date) < new Date() && deal.status === 'open'
}
function formatDate(d) {
  try {
    return new Date(d).toLocaleDateString(locale.value === 'ar' ? 'ar-EG' : 'en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  } catch { return d }
}

// ── Fetch ──────────────────────────────────────────────
async function fetchData() {
  loading.value = true
  try {
    const [sr, dr] = await Promise.all([
      api.get('/deal-stages'),
      api.get('/deals', { params: { kanban: 1 } }),
    ])
    stages.value = sr.data.data
    stages.value.forEach(s => { dealsByStage[s.id] = [] })
    dr.data.data.forEach(d => {
      if (d.stage_id != null && dealsByStage[d.stage_id] !== undefined) dealsByStage[d.stage_id].push(d)
    })
  } catch {
    toast.error(locale.value === 'ar' ? 'فشل تحميل البيانات' : 'Failed to load data')
  } finally {
    loading.value = false
  }
}

async function fetchContacts() {
  if (contactsList.value.length) return
  try {
    const { data } = await api.get('/contacts', { params: { per_page: 200 } })
    contactsList.value = data.data
  } catch {}
}

// ── Drag ───────────────────────────────────────────────
async function onCardChange(evt, stageId) {
  // @change fires on the destination column — evt.added means an item arrived here
  if (!evt.added) return
  const deal = evt.added.element
  if (deal.stage_id === stageId) return
  try {
    await api.put(`/deals/${deal.id}`, { stage_id: stageId })
    deal.stage_id = stageId
    toast.success(locale.value === 'ar' ? `تم نقل "${deal.title}"` : `"${deal.title}" moved`)
  } catch {
    toast.error(locale.value === 'ar' ? 'فشل نقل الصفقة' : 'Failed to move deal')
    fetchData()
  }
}

// ── Add / Edit ─────────────────────────────────────────
function openAddModal(stage = null) {
  formMode.value    = 'create'
  editingDeal.value = null
  form.value        = emptyForm()
  if (stage) form.value.stage_id = stage.id
  else if (stages.value.length) form.value.stage_id = stages.value[0].id
  v$.value.$reset()
  fetchContacts()
  nextTick(() => getFormModal().show())
}

function openEditModal(deal) {
  formMode.value    = 'edit'
  editingDeal.value = deal
  form.value = {
    title:               deal.title || '',
    stage_id:            deal.stage_id,
    status:              deal.status || 'open',
    value:               Number(deal.value || 0),
    probability:         Number(deal.probability || 0),
    expected_close_date: deal.expected_close_date ? deal.expected_close_date.substring(0, 10) : '',
    contact_id:          deal.contact_id || null,
    notes:               deal.notes || '',
    lost_reason:         deal.lost_reason || '',
  }
  v$.value.$reset()
  fetchContacts()
  nextTick(() => getFormModal().show())
}

async function saveDeal() {
  const valid = await v$.value.$validate()
  if (!valid) return
  saving.value = true
  try {
    if (formMode.value === 'create') {
      await api.post('/deals', form.value)
      toast.success(locale.value === 'ar' ? 'تم إضافة الصفقة بنجاح' : 'Deal added successfully')
    } else {
      await api.put(`/deals/${editingDeal.value.id}`, form.value)
      toast.success(locale.value === 'ar' ? 'تم تحديث الصفقة بنجاح' : 'Deal updated successfully')
    }
    getFormModal().hide()
    await fetchData()
  } catch (err) {
    toast.error(err.response?.data?.message || (locale.value === 'ar' ? 'حدث خطأ' : 'Something went wrong'))
  } finally {
    saving.value = false
  }
}

// ── Win / Lost ─────────────────────────────────────────
const outcomeTarget  = ref(null)
const outcomeType    = ref('won')
const savingOutcome  = ref(false)
const outcomeForm    = ref({ closed_at: new Date().toISOString().slice(0,10), lost_reason: '', notes: '' })

function getOutcomeModal() { return window.bootstrap?.Modal.getOrCreateInstance(document.getElementById('dealOutcomeModal')) }

function openWonLostModal(deal, type) {
  outcomeTarget.value = deal
  outcomeType.value   = type
  outcomeForm.value   = { closed_at: new Date().toISOString().slice(0,10), lost_reason: '', notes: '' }
  nextTick(() => getOutcomeModal()?.show())
}

async function saveOutcome() {
  if (!outcomeTarget.value) return
  savingOutcome.value = true
  try {
    await api.put(`/deals/${outcomeTarget.value.id}`, {
      title:       outcomeTarget.value.title,
      stage_id:    outcomeTarget.value.stage_id,
      status:      outcomeType.value,
      value:       outcomeTarget.value.value,
      probability: outcomeType.value === 'won' ? 100 : 0,
      closed_at:   outcomeForm.value.closed_at,
      lost_reason: outcomeForm.value.lost_reason,
      notes:       outcomeForm.value.notes || outcomeTarget.value.notes,
    })
    toast.success(locale.value === 'ar'
      ? (outcomeType.value === 'won' ? 'تم تسجيل الصفقة كرابحة!' : 'تم تسجيل الصفقة كخاسرة')
      : (outcomeType.value === 'won' ? 'Deal marked as Won!' : 'Deal marked as Lost'))
    getOutcomeModal()?.hide()
    await fetchData()
  } catch (err) {
    toast.error(err.response?.data?.message || (locale.value === 'ar' ? 'حدث خطأ' : 'Something went wrong'))
  } finally {
    savingOutcome.value = false
  }
}

// ── Delete ─────────────────────────────────────────────
function openDeleteModal() {
  getFormModal().hide()
  setTimeout(() => nextTick(() => getDeleteModal().show()), 300)
}
function closeDeleteModal() { getDeleteModal().hide() }

async function executeDel() {
  deleting.value = true
  try {
    await api.delete(`/deals/${editingDeal.value.id}`)
    toast.success(locale.value === 'ar' ? 'تم حذف الصفقة' : 'Deal deleted')
    getDeleteModal().hide()
    await fetchData()
  } catch {
    toast.error(locale.value === 'ar' ? 'فشل الحذف' : 'Delete failed')
  } finally {
    deleting.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
/* ─── Stats ─────────────────────── */
.k-stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.k-stat-num  { font-size:1.6rem;font-weight:700;line-height:1;color:var(--bs-body-color); }
.k-stat-lbl  { font-size:11px;color:var(--bs-secondary-color);margin-top:3px; }

/* ─── Board ─────────────────────── */
.kanban-board { display:flex;gap:14px;overflow-x:auto;padding-bottom:24px;align-items:flex-start;min-height:55vh; }

/* ─── Column ─────────────────────── */
.kanban-col { min-width:270px;max-width:285px;border-radius:12px;background:var(--bs-tertiary-bg,#F8FAFC);border:1px solid var(--bs-border-color);flex-shrink:0;display:flex;flex-direction:column; }
.kanban-col-header { padding:12px 14px 10px;border-top:3px solid #6366F1;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;background:var(--bs-body-bg,#fff); }
.kanban-stage-dot  { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
.kanban-badge { font-size:11px;font-weight:700;border-radius:20px;padding:2px 8px; }
.kanban-col-value  { padding:0 14px 8px;font-size:11px;color:var(--bs-secondary-color);font-weight:600;background:var(--bs-body-bg,#fff);border-bottom:1px solid var(--bs-border-color); }

/* ─── Cards ─────────────────────── */
.kanban-cards { padding:10px;display:flex;flex-direction:column;gap:8px;min-height:60px;flex:1; }
.kanban-card  { background:var(--bs-body-bg,#fff);border-radius:10px;border:1px solid var(--bs-border-color);padding:12px;cursor:pointer;transition:box-shadow .15s,transform .1s; }
.kanban-card:hover { box-shadow:0 4px 14px rgba(0,0,0,.09);transform:translateY(-1px); }
.kanban-ghost { opacity:.35;background:#EFF6FF !important;border:2px dashed #2563EB !important; }
.kanban-drag  { box-shadow:0 8px 24px rgba(0,0,0,.15);transform:rotate(1.5deg); }

.deal-status-badge { display:inline-flex;align-items:center;font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px; }
.status-won  { background:rgba(16,185,129,.12);color:#059669; }
.status-lost { background:rgba(239,68,68,.12);color:#DC2626; }

.deal-title   { font-weight:600;font-size:13px;color:var(--bs-body-color);line-height:1.4;margin-bottom:4px; }
.deal-contact { font-size:11px;color:var(--bs-secondary-color);margin-bottom:6px; }
.deal-footer  { display:flex;justify-content:space-between;align-items:center;gap:8px; }
.deal-value   { font-weight:800;color:#2563EB;font-size:13px;white-space:nowrap; }
.deal-prob    { display:flex;align-items:center;gap:5px;flex:1; }
.prob-bar     { flex:1;height:4px;background:var(--bs-border-color,#E2E8F0);border-radius:2px;overflow:hidden; }
.prob-fill    { height:100%;border-radius:2px;transition:width .3s; }
.prob-text    { font-size:10px;color:var(--bs-secondary-color);white-space:nowrap; }
.deal-date    { font-size:11px;color:var(--bs-secondary-color);margin-top:8px;display:flex;align-items:center; }

/* ─── Quick Win/Lost ─── */
.deal-quick-actions { display:none;gap:4px;margin-top:8px;border-top:1px solid var(--bs-border-color-translucent,rgba(0,0,0,.06));padding-top:8px; }
.kanban-card:hover .deal-quick-actions { display:flex; }
.quick-btn { flex:1;border:none;border-radius:6px;padding:4px 0;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:4px;transition:opacity .1s; }
.quick-btn:hover { opacity:.85; }
.quick-won  { background:rgba(16,185,129,.15);color:#059669; }
.quick-lost { background:rgba(239,68,68,.15);color:#DC2626; }

/* ─── Outcome modal icon ─── */
.outcome-modal-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.icon-won  { background:rgba(16,185,129,.15);color:#059669; }
.icon-lost { background:rgba(239,68,68,.15);color:#DC2626; }
.deal-date.overdue { color:#EF4444; }

.kanban-add-btn { text-align:center;padding:9px;color:var(--bs-secondary-color);font-size:12px;cursor:pointer;border-radius:8px;border:1.5px dashed var(--bs-border-color);margin:0 2px 8px;transition:all .15s; }
.kanban-add-btn:hover { color:#2563EB;border-color:#2563EB;background:#EFF6FF; }

/* ─── Probability preview ─────── */
.prob-preview-bar  { height:6px;border-radius:3px;background:var(--bs-border-color,#E2E8F0);overflow:hidden; }
.prob-preview-fill { height:100%;border-radius:3px;transition:width .3s,background .3s; }

/* ─── Modal ─────────────────────── */
.modal-icon { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0; }
.fsect-title { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-secondary-color);padding-bottom:.5rem;margin-bottom:.875rem;border-bottom:1px solid var(--bs-border-color);margin-top:.25rem; }

/* ─── Delete modal ──────────────── */
.del-icon { width:72px;height:72px;border-radius:50%;background:rgba(239,68,68,.1);color:#DC2626;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto; }

/* ─── Dark mode ─────────────────── */
[data-bs-theme="dark"] .kanban-col     { background:var(--bs-dark-bg-subtle); }
[data-bs-theme="dark"] .kanban-card    { background:var(--bs-body-bg); }
[data-bs-theme="dark"] .kanban-col-header { background:var(--bs-body-bg); }
[data-bs-theme="dark"] .kanban-add-btn:hover { background:rgba(37,99,235,.15); }

/* --- PrimeVue inside Bootstrap modal --- */
:deep(.p-datepicker)       { width:100%; }
:deep(.p-datepicker-input) { width:100%;border-radius:var(--bs-border-radius);border:1px solid var(--bs-border-color);padding:.375rem .75rem;font-size:.875rem;background:var(--bs-body-bg);color:var(--bs-body-color); }
:deep(.p-datepicker-input:focus) { border-color:#2563EB;outline:none;box-shadow:0 0 0 .2rem rgba(37,99,235,.2); }
:deep(.p-select)           { width:100%; }
:deep(.p-select-label)     { font-size:.875rem;padding:.375rem .75rem; }
:deep(.p-datepicker-panel),
:deep(.p-select-overlay)   { z-index:9999 !important; }
:deep(.p-invalid .p-select-label) { border-color:#EF4444; }
</style>