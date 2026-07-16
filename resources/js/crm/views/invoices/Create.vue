<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale==='ar' ? 'فاتورة جديدة' : 'New Invoice' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale==='ar' ? 'أنشئ فاتورة جديدة وأرسلها للعميل' : 'Create a new invoice and send it to your client' }}</p>
      </div>
      <router-link to="/crm/invoices" class="btn btn-sm btn-outline-secondary flex-shrink-0">
        <i class="bi bi-arrow-left me-1"></i>{{ locale==='ar' ? 'العودة' : 'Back' }}
      </router-link>
    </div>

    <form @submit.prevent="save">
      <div class="row g-4 align-items-start">

        <!-- Left: Invoice Form -->
        <div class="col-lg-8">

          <!-- Client & Dates -->
          <div class="card card-border mb-4">
            <div class="card-body">
              <div class="fsect-title"><i class="bi bi-person me-2 text-primary"></i>{{ locale==='ar'?'معلومات الفاتورة':'Invoice Details' }}</div>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">{{ locale==='ar'?'العميل':'Client' }}</label>
                  <Select
                    v-model="form.contact_id"
                    :options="contactOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'اختر عميلاً (اختياري)':'Select a client (optional)'"
                    :show-clear="true"
                    class="w-100"
                    append-to="body"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'تاريخ الإصدار':'Issue Date' }} <span class="text-danger">*</span></label>
                  <DatePicker
                    v-model="issueDate"
                    date-format="dd/mm/yy"
                    :placeholder="locale==='ar'?'يوم/شهر/سنة':'DD/MM/YYYY'"
                    show-icon icon-display="input"
                    class="w-100" append-to="body"
                  />
                  <small class="text-danger" v-if="errors.issue_date">{{ errors.issue_date }}</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'تاريخ الاستحقاق':'Due Date' }} <span class="text-danger">*</span></label>
                  <DatePicker
                    v-model="dueDate"
                    date-format="dd/mm/yy"
                    :placeholder="locale==='ar'?'يوم/شهر/سنة':'DD/MM/YYYY'"
                    show-icon icon-display="input"
                    class="w-100" append-to="body"
                  />
                  <small class="text-danger" v-if="errors.due_date">{{ errors.due_date }}</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Invoice Items -->
          <div class="card card-border mb-4">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fsect-title mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>{{ locale==='ar'?'بنود الفاتورة':'Invoice Items' }}</div>
                <button type="button" class="btn btn-xs btn-outline-primary" @click="addItem">
                  <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar'?'إضافة بند':'Add Item' }}
                </button>
              </div>
              <small class="text-danger d-block mb-2" v-if="errors.items">{{ errors.items }}</small>

              <div class="table-responsive">
                <table class="table items-table mb-0">
                  <thead>
                    <tr>
                      <th>{{ locale==='ar'?'الوصف':'Description' }}</th>
                      <th style="width:90px">{{ locale==='ar'?'الكمية':'Qty' }}</th>
                      <th style="width:120px">{{ locale==='ar'?'سعر الوحدة':'Unit Price' }}</th>
                      <th style="width:110px" class="text-end">{{ locale==='ar'?'الإجمالي':'Total' }}</th>
                      <th style="width:44px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, i) in form.items" :key="i">
                      <td>
                        <input v-model="item.description" type="text" class="form-control form-control-sm"
                          :placeholder="locale==='ar'?'وصف الخدمة أو المنتج...':'Service or product description...'"
                          :class="{'is-invalid': errors[`item_${i}`]}" />
                      </td>
                      <td>
                        <input v-model.number="item.quantity" type="number" min="0.01" step="0.01"
                          class="form-control form-control-sm text-center"
                          @input="calcItem(item)" />
                      </td>
                      <td>
                        <div class="input-group input-group-sm">
                          <span class="input-group-text">$</span>
                          <input v-model.number="item.unit_price" type="number" min="0" step="0.01"
                            class="form-control"
                            @input="calcItem(item)" />
                        </div>
                      </td>
                      <td class="text-end">
                        <span class="fw-bold" style="color:#2563EB;font-size:13px">${{ (item.total||0).toFixed(2) }}</span>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-xs btn-outline-danger" @click="removeItem(i)" :disabled="form.items.length===1">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="card card-border">
            <div class="card-body">
              <div class="fsect-title"><i class="bi bi-chat-left-text me-2 text-primary"></i>{{ locale==='ar'?'ملاحظات':'Notes' }}</div>
              <textarea v-model="form.notes" class="form-control"
                :placeholder="locale==='ar'?'شروط الدفع، ملاحظات للعميل...':'Payment terms, notes for client...'"
                rows="3" style="resize:none"></textarea>
            </div>
          </div>

        </div>

        <!-- Right: Summary -->
        <div class="col-lg-4">
          <div class="card card-border sticky-summary">
            <div class="card-body">
              <div class="fsect-title"><i class="bi bi-calculator me-2 text-primary"></i>{{ locale==='ar'?'ملخص الفاتورة':'Invoice Summary' }}</div>

              <!-- Tax & Discount -->
              <div class="mb-3">
                <label class="form-label small">{{ locale==='ar'?'نسبة الضريبة (%)':'Tax Rate (%)' }}</label>
                <div class="input-group input-group-sm">
                  <input v-model.number="form.tax_rate" type="number" min="0" max="100" step="0.1" class="form-control" placeholder="0" />
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="mb-4">
                <label class="form-label small">{{ locale==='ar'?'الخصم ($)':'Discount ($)' }}</label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text">$</span>
                  <input v-model.number="form.discount" type="number" min="0" step="0.01" class="form-control" placeholder="0.00" />
                </div>
              </div>

              <!-- Totals -->
              <div class="totals-box">
                <div class="tot-row">
                  <span class="text-muted">{{ locale==='ar'?'المجموع الفرعي':'Subtotal' }}</span>
                  <span>${{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="tot-row" v-if="form.tax_rate">
                  <span class="text-muted">{{ locale==='ar'?'الضريبة':'Tax' }} ({{ form.tax_rate }}%)</span>
                  <span>+${{ taxAmount.toFixed(2) }}</span>
                </div>
                <div class="tot-row" v-if="form.discount">
                  <span class="text-muted">{{ locale==='ar'?'الخصم':'Discount' }}</span>
                  <span class="text-success">-${{ Number(form.discount||0).toFixed(2) }}</span>
                </div>
                <div class="tot-row tot-final">
                  <span>{{ locale==='ar'?'الإجمالي':'Total' }}</span>
                  <span>${{ total.toFixed(2) }}</span>
                </div>
              </div>

              <div class="d-grid mt-4 gap-2">
                <button type="submit" class="btn btn-primary" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                  {{ saving ? (locale==='ar'?'جاري الإنشاء...':'Creating...') : (locale==='ar'?'إنشاء الفاتورة':'Create Invoice') }}
                </button>
                <router-link to="/crm/invoices" class="btn btn-light">{{ locale==='ar'?'إلغاء':'Cancel' }}</router-link>
              </div>
            </div>
          </div>
        </div>

      </div>
    </form>

  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const router = useRouter()
const store  = useStore()
const toast  = useToast()
const locale = computed(() => store.state.ui.locale)

const saving   = ref(false)
const contacts = ref([])
const errors   = ref({})

const today = new Date()
const form  = ref({
  contact_id: null,
  issue_date: today.toISOString().split('T')[0],
  due_date:   '',
  tax_rate:   0,
  discount:   0,
  notes:      '',
  items: [{ description:'', quantity:1, unit_price:0, total:0 }],
})

// ── Date bridges ──────────────────────────────────────────
function strToDate(s) { return s ? new Date(s + 'T00:00:00') : null }
function dateToStr(d) { return d ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}` : '' }

const issueDate = computed({ get: () => strToDate(form.value.issue_date), set: d => { form.value.issue_date = dateToStr(d) } })
const dueDate   = computed({ get: () => strToDate(form.value.due_date),   set: d => { form.value.due_date   = dateToStr(d) } })

// ── Options ───────────────────────────────────────────────
const contactOptions = computed(() => contacts.value.map(c => ({
  label: `${c.first_name} ${c.last_name}${c.company ? ' — '+c.company : ''}`,
  value: c.id,
})))

// ── Computed totals ───────────────────────────────────────
const subtotal  = computed(() => form.value.items.reduce((s,i) => s + (i.total||0), 0))
const taxAmount = computed(() => subtotal.value * (form.value.tax_rate||0) / 100)
const total     = computed(() => Math.max(0, subtotal.value + taxAmount.value - (form.value.discount||0)))

// ── Items ─────────────────────────────────────────────────
function calcItem(item) { item.total = parseFloat(((item.quantity||0)*(item.unit_price||0)).toFixed(2)) }
function addItem()     { form.value.items.push({ description:'', quantity:1, unit_price:0, total:0 }) }
function removeItem(i) { if (form.value.items.length > 1) form.value.items.splice(i,1) }

// ── Validate ──────────────────────────────────────────────
function validate() {
  errors.value = {}
  if (!form.value.issue_date) errors.value.issue_date = locale.value==='ar'?'تاريخ الإصدار مطلوب':'Issue date is required'
  if (!form.value.due_date)   errors.value.due_date   = locale.value==='ar'?'تاريخ الاستحقاق مطلوب':'Due date is required'
  const emptyItems = form.value.items.filter(i => !i.description.trim())
  if (emptyItems.length) errors.value.items = locale.value==='ar'?'يجب ملء وصف جميع البنود':'All item descriptions are required'
  form.value.items.forEach((item,idx) => { if (!item.description.trim()) errors.value[`item_${idx}`] = true })
  return Object.keys(errors.value).length === 0
}

async function save() {
  if (!validate()) return
  saving.value = true
  try {
    const { data } = await api.post('/invoices', form.value)
    toast.success(locale.value==='ar' ? `تم إنشاء الفاتورة ${data.data?.invoice_number||''}` : `Invoice ${data.data?.invoice_number||''} created successfully`)
    router.push(`/crm/invoices/${data.data.id}`)
  } catch (e) {
    const msg = e.response?.data?.message || (locale.value==='ar'?'حدث خطأ':'An error occurred')
    toast.error(msg)
  }
  saving.value = false
}

onMounted(async () => {
  try { const { data } = await api.get('/contacts', { params:{per_page:200} }); contacts.value = data.data } catch {}
})
</script>

<style scoped>
.fsect-title { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-secondary-color);padding-bottom:.5rem;margin-bottom:.875rem;border-bottom:1px solid var(--bs-border-color); }

.items-table { border-collapse:separate;border-spacing:0; }
.items-table thead th { background:var(--bs-tertiary-bg,#F8FAFC);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-secondary-color);padding:.6rem .75rem;border-bottom:1px solid var(--bs-border-color); }
.items-table td { padding:.5rem .75rem;border-bottom:1px solid var(--bs-border-color);vertical-align:middle; }
.items-table tbody tr:last-child td { border-bottom:none; }

.sticky-summary { position:sticky;top:80px; }

.totals-box { background:var(--bs-tertiary-bg,#F8FAFC);border-radius:10px;padding:1rem; }
.tot-row { display:flex;justify-content:space-between;align-items:center;padding:.35rem 0;font-size:13px;border-bottom:1px solid var(--bs-border-color); }
.tot-row:last-child { border-bottom:none; }
.tot-final { font-size:1.1rem;font-weight:800;color:#2563EB;padding-top:.6rem;margin-top:.25rem; }

:deep(.p-datepicker)       { width:100%; }
:deep(.p-datepicker-input) { width:100%;border-radius:var(--bs-border-radius);border:1px solid var(--bs-border-color);padding:.375rem .75rem;font-size:.875rem;background:var(--bs-body-bg);color:var(--bs-body-color); }
:deep(.p-datepicker-input:focus) { border-color:#2563EB;outline:none;box-shadow:0 0 0 .2rem rgba(37,99,235,.2); }
:deep(.p-select)       { width:100%; }
:deep(.p-select-label) { font-size:.875rem;padding:.375rem .75rem; }
:deep(.p-datepicker-panel),:deep(.p-select-overlay) { z-index:9999 !important; }
</style>
