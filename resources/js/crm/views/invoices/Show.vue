<template>
  <CrmLayout>

    <!-- Loading -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:400px">
      <div class="text-center">
        <div class="spinner-border text-primary mb-2" role="status"></div>
        <div class="text-muted small">{{ locale==='ar'?'جاري تحميل الفاتورة...':'Loading invoice...' }}</div>
      </div>
    </div>

    <template v-else-if="invoice">

      <!-- Page Header -->
      <div class="d-flex align-items-start justify-content-between mb-4 gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-3">
          <div class="inv-header-icon">
            <i class="bi bi-receipt-cutoff"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h4 class="mb-0 fw-bold" style="font-size:1.3rem">{{ invoice.invoice_number }}</h4>
              <span class="inv-status-badge" :class="`inv-${invoice.status}`">
                <i class="bi" :class="statusIcon(invoice.status)" style="font-size:8px"></i>
                {{ statusLabel(invoice.status) }}
              </span>
            </div>
            <p class="text-muted mb-0 small mt-1">
              {{ locale==='ar'?'صادرة في:':'Issued:' }} {{ formatDate(invoice.issue_date) }}
              <span class="mx-2">·</span>
              <span :class="{ 'text-danger fw-semibold': isOverdue }">
                {{ locale==='ar'?'الاستحقاق:':'Due:' }} {{ formatDate(invoice.due_date) }}
              </span>
            </p>
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap flex-shrink-0">
          <!-- Quick action buttons -->
          <button v-if="invoice.status==='draft'" @click="updateStatus('sent')" class="btn btn-sm btn-outline-secondary" :disabled="updating">
            <i class="bi bi-send me-1"></i>{{ locale==='ar'?'إرسال':'Mark Sent' }}
          </button>
          <button v-if="['sent','overdue'].includes(invoice.status)" @click="updateStatus('paid')" class="btn btn-sm btn-success" :disabled="updating">
            <i class="bi bi-check-circle me-1"></i>{{ locale==='ar'?'تم الدفع':'Mark Paid' }}
          </button>
          <button v-if="!['cancelled','paid'].includes(invoice.status)" @click="confirmCancel=true" class="btn btn-sm btn-outline-danger" :disabled="updating">
            <i class="bi bi-x-circle me-1"></i>{{ locale==='ar'?'إلغاء':'Cancel' }}
          </button>
          <button v-if="invoice.contact?.email" class="btn btn-sm btn-outline-primary" :disabled="sendingEmail" @click="sendEmail">
            <span v-if="sendingEmail" class="spinner-border spinner-border-sm me-1"></span>
            <i v-else class="bi bi-envelope me-1"></i>{{ locale==='ar'?'إرسال بالإيميل':'Send Email' }}
          </button>
          <a :href="`/api/crm/invoices/${invoice.id}/pdf?token=${token}`" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-file-pdf me-1" style="color:#EF4444"></i>PDF
          </a>
          <router-link to="/crm/invoices" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left me-1"></i>{{ locale==='ar'?'العودة':'Back' }}
          </router-link>
        </div>
      </div>

      <!-- Body -->
      <div class="row g-4 align-items-start">

        <!-- Left: Invoice Content -->
        <div class="col-lg-8">
          <div class="card card-border">
            <div class="card-body">

              <!-- Client + Dates -->
              <div class="row g-3 mb-4 pb-3" style="border-bottom:1px solid var(--bs-border-color)">
                <div class="col-6 col-md-3" v-if="invoice.contact">
                  <div class="info-label">{{ locale==='ar'?'العميل':'Client' }}</div>
                  <div class="info-value">{{ invoice.contact.first_name }} {{ invoice.contact.last_name }}</div>
                  <div class="info-sub" v-if="invoice.contact.company">{{ invoice.contact.company }}</div>
                  <div class="info-sub" v-if="invoice.contact.email">{{ invoice.contact.email }}</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="info-label">{{ locale==='ar'?'رقم الفاتورة':'Invoice #' }}</div>
                  <div class="info-value fw-bold text-primary">{{ invoice.invoice_number }}</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="info-label">{{ locale==='ar'?'تاريخ الإصدار':'Issue Date' }}</div>
                  <div class="info-value">{{ formatDate(invoice.issue_date) }}</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="info-label">{{ locale==='ar'?'تاريخ الاستحقاق':'Due Date' }}</div>
                  <div class="info-value" :class="{'text-danger fw-semibold': isOverdue}">{{ formatDate(invoice.due_date) }}</div>
                </div>
              </div>

              <!-- Items Table -->
              <div class="table-responsive mb-4">
                <table class="table items-table mb-0">
                  <thead>
                    <tr>
                      <th>{{ locale==='ar'?'الوصف':'Description' }}</th>
                      <th class="text-center" style="width:80px">{{ locale==='ar'?'الكمية':'Qty' }}</th>
                      <th class="text-end" style="width:110px">{{ locale==='ar'?'سعر الوحدة':'Unit Price' }}</th>
                      <th class="text-end" style="width:110px">{{ locale==='ar'?'الإجمالي':'Total' }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in invoice.items" :key="item.id">
                      <td class="item-desc">{{ item.description }}</td>
                      <td class="text-center" style="font-size:13px">{{ item.quantity }}</td>
                      <td class="text-end" style="font-size:13px">${{ Number(item.unit_price).toFixed(2) }}</td>
                      <td class="text-end fw-bold" style="font-size:13px">${{ Number(item.total).toFixed(2) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Totals -->
              <div class="d-flex justify-content-end">
                <div class="totals-box">
                  <div class="tot-row">
                    <span class="text-muted">{{ locale==='ar'?'المجموع الفرعي':'Subtotal' }}</span>
                    <span>${{ Number(invoice.subtotal).toFixed(2) }}</span>
                  </div>
                  <div class="tot-row" v-if="invoice.tax_rate">
                    <span class="text-muted">{{ locale==='ar'?'الضريبة':'Tax' }} ({{ invoice.tax_rate }}%)</span>
                    <span>+${{ Number(invoice.tax_amount).toFixed(2) }}</span>
                  </div>
                  <div class="tot-row" v-if="invoice.discount">
                    <span class="text-muted">{{ locale==='ar'?'الخصم':'Discount' }}</span>
                    <span class="text-success">-${{ Number(invoice.discount).toFixed(2) }}</span>
                  </div>
                  <div class="tot-row tot-final">
                    <span>{{ locale==='ar'?'الإجمالي الكلي':'Total Amount' }}</span>
                    <span>${{ Number(invoice.total).toFixed(2) }}</span>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div v-if="invoice.notes" class="notes-box mt-4">
                <div class="info-label mb-1">{{ locale==='ar'?'ملاحظات':'Notes' }}</div>
                <p class="mb-0" style="font-size:13px;white-space:pre-wrap">{{ invoice.notes }}</p>
              </div>

            </div>
          </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="col-lg-4">

          <!-- Status Card -->
          <div class="card card-border mb-3">
            <div class="card-body">
              <div class="fsect-title"><i class="bi bi-info-circle me-2 text-primary"></i>{{ locale==='ar'?'معلومات الفاتورة':'Invoice Info' }}</div>
              <div class="sidebar-row">
                <span class="text-muted">{{ locale==='ar'?'الحالة':'Status' }}</span>
                <span class="inv-status-badge" :class="`inv-${invoice.status}`">
                  <i class="bi" :class="statusIcon(invoice.status)" style="font-size:8px"></i>
                  {{ statusLabel(invoice.status) }}
                </span>
              </div>
              <div class="sidebar-row">
                <span class="text-muted">{{ locale==='ar'?'المبلغ الإجمالي':'Total Amount' }}</span>
                <span class="fw-bold text-primary" style="font-size:1rem">${{ Number(invoice.total).toFixed(2) }}</span>
              </div>
              <div class="sidebar-row">
                <span class="text-muted">{{ locale==='ar'?'تاريخ الإنشاء':'Created' }}</span>
                <span style="font-size:12px">{{ formatDate(invoice.created_at) }}</span>
              </div>
              <div class="sidebar-row" v-if="invoice.contact">
                <span class="text-muted">{{ locale==='ar'?'العميل':'Client' }}</span>
                <span style="font-size:12px">{{ invoice.contact.first_name }} {{ invoice.contact.last_name }}</span>
              </div>
            </div>
          </div>

          <!-- Change Status -->
          <div class="card card-border mb-3" v-if="!['paid','cancelled'].includes(invoice.status)">
            <div class="card-body">
              <div class="fsect-title"><i class="bi bi-arrow-repeat me-2 text-primary"></i>{{ locale==='ar'?'تغيير الحالة':'Change Status' }}</div>
              <div class="d-grid gap-2">
                <button v-if="invoice.status==='draft'" @click="updateStatus('sent')" class="btn btn-outline-secondary btn-sm" :disabled="updating">
                  <span v-if="updating" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-send me-1"></i>{{ locale==='ar'?'تعيين كـ "مرسلة"':'Mark as Sent' }}
                </button>
                <button v-if="['draft','sent','overdue'].includes(invoice.status)" @click="updateStatus('paid')" class="btn btn-success btn-sm" :disabled="updating">
                  <span v-if="updating" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-check-circle me-1"></i>{{ locale==='ar'?'تعيين كـ "مدفوعة"':'Mark as Paid' }}
                </button>
                <button v-if="['draft','sent'].includes(invoice.status)" @click="updateStatus('overdue')" class="btn btn-outline-danger btn-sm" :disabled="updating">
                  <i class="bi bi-exclamation-circle me-1"></i>{{ locale==='ar'?'تعيين كـ "متأخرة"':'Mark as Overdue' }}
                </button>
                <button @click="confirmCancel=true" class="btn btn-light btn-sm" :disabled="updating">
                  <i class="bi bi-x-circle me-1"></i>{{ locale==='ar'?'إلغاء الفاتورة':'Cancel Invoice' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Payments Card -->
          <div class="card card-border">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fsect-title mb-0">
                  <i class="bi bi-credit-card me-2 text-success"></i>{{ locale==='ar'?'المدفوعات':'Payments' }}
                </div>
                <button v-if="!['paid','cancelled'].includes(invoice.status)" class="btn btn-sm btn-outline-success" style="font-size:.75rem;padding:2px 8px" @click="showPaymentModal=true">
                  <i class="bi bi-plus"></i> {{ locale==='ar'?'تسجيل':'Record' }}
                </button>
              </div>
              <!-- Payment progress -->
              <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                  <span class="text-muted">{{ locale==='ar'?'المدفوع':'Paid' }}</span>
                  <span class="fw-bold text-success">${{ paidAmount.toFixed(2) }}</span>
                </div>
                <div class="progress" style="height:6px;border-radius:3px">
                  <div class="progress-bar bg-success" :style="{ width: Math.min(100, paidPercent) + '%' }"></div>
                </div>
                <div class="d-flex justify-content-between small mt-1">
                  <span class="text-muted">{{ locale==='ar'?'المتبقي':'Balance' }}</span>
                  <span class="fw-bold" :class="balanceDue > 0 ? 'text-danger' : 'text-success'">${{ balanceDue.toFixed(2) }}</span>
                </div>
              </div>
              <!-- Payment list -->
              <div v-if="!payments.length" class="text-muted small text-center py-2">
                {{ locale==='ar'?'لا توجد مدفوعات بعد':'No payments recorded' }}
              </div>
              <div v-for="pay in payments" :key="pay.id" class="payment-row">
                <div>
                  <div class="fw-semibold small">${{ Number(pay.amount).toFixed(2) }}</div>
                  <div class="text-muted" style="font-size:.72rem">{{ pay.payment_date }} · {{ pay.method }}</div>
                </div>
                <button class="btn btn-sm text-danger" style="padding:2px 6px;font-size:.75rem" @click="removePayment(pay.id)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Record Payment Modal -->
      <Teleport to="body">
        <div v-if="showPaymentModal" class="modal-backdrop-inv" @click.self="showPaymentModal=false">
          <div class="modal-card-inv">
            <div class="modal-card-header-inv">
              <h6 class="mb-0 fw-bold">{{ locale==='ar'?'تسجيل دفعة':'Record Payment' }}</h6>
              <button class="btn-close" @click="showPaymentModal=false"></button>
            </div>
            <div class="modal-card-body-inv">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ locale==='ar'?'المبلغ':'Amount' }} *</label>
                  <input type="number" step="0.01" min="0.01" v-model="payForm.amount" class="form-control form-control-sm" :placeholder="balanceDue.toFixed(2)" />
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">{{ locale==='ar'?'التاريخ':'Date' }} *</label>
                  <input type="date" v-model="payForm.payment_date" class="form-control form-control-sm" />
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">{{ locale==='ar'?'طريقة الدفع':'Method' }}</label>
                  <select v-model="payForm.method" class="form-select form-select-sm">
                    <option value="cash">{{ locale==='ar'?'نقدي':'Cash' }}</option>
                    <option value="bank_transfer">{{ locale==='ar'?'تحويل بنكي':'Bank Transfer' }}</option>
                    <option value="card">{{ locale==='ar'?'بطاقة':'Card' }}</option>
                    <option value="check">{{ locale==='ar'?'شيك':'Check' }}</option>
                    <option value="other">{{ locale==='ar'?'أخرى':'Other' }}</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ locale==='ar'?'المرجع':'Reference' }}</label>
                  <input v-model="payForm.reference" class="form-control form-control-sm" :placeholder="locale==='ar'?'رقم التحويل / الشيك...':'Transfer no. / check no...'" />
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ locale==='ar'?'ملاحظات':'Notes' }}</label>
                  <textarea v-model="payForm.notes" class="form-control form-control-sm" rows="2"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-card-footer-inv">
              <button class="btn btn-sm btn-secondary" @click="showPaymentModal=false">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
              <button class="btn btn-sm btn-success" :disabled="savingPayment" @click="savePayment">
                <span v-if="savingPayment" class="spinner-border spinner-border-sm me-1"></span>
                {{ locale==='ar'?'تسجيل الدفعة':'Record Payment' }}
              </button>
            </div>
          </div>
        </div>
      </Teleport>

      <!-- Cancel Confirm Modal -->
      <div class="modal fade" id="cancelInvModal" tabindex="-1" aria-hidden="true" ref="cancelModalEl">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px">
          <div class="modal-content">
            <div class="modal-body text-center py-4 px-4">
              <div class="del-icon mb-3"><i class="bi bi-x-circle-fill"></i></div>
              <h5 class="fw-bold mb-2">{{ locale==='ar'?'إلغاء الفاتورة':'Cancel Invoice' }}</h5>
              <p class="text-muted mb-0" style="font-size:14px">
                {{ locale==='ar'?'هل أنت متأكد من إلغاء الفاتورة':'Are you sure you want to cancel invoice' }}
                <strong class="text-danger"> {{ invoice.invoice_number }}</strong>?
              </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
              <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
              <button type="button" class="btn btn-danger px-4" @click="updateStatus('cancelled')" :disabled="updating">
                <span v-if="updating" class="spinner-border spinner-border-sm me-1"></span>
                {{ locale==='ar'?'نعم، إلغاء':'Yes, Cancel' }}
              </button>
            </div>
          </div>
        </div>
      </div>

    </template>

  </CrmLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const route   = useRoute()
const store   = useStore()
const toast   = useToast()
const locale  = computed(() => store.state.ui.locale)
const token   = computed(() => store.state.auth.token)

const invoice      = ref(null)
const loading      = ref(true)
const updating     = ref(false)
const confirmCancel = ref(false)
const cancelModalEl = ref(null)

const isOverdue = computed(() => {
  if (!invoice.value?.due_date || ['paid','cancelled'].includes(invoice.value.status)) return false
  return new Date(invoice.value.due_date.substring(0,10) + 'T00:00:00') < new Date()
})

watch(confirmCancel, val => {
  if (val) window.bootstrap.Modal.getOrCreateInstance(document.getElementById('cancelInvModal')).show()
})

function formatDate(d) {
  if (!d) return '—'
  const dt = new Date((d.length > 10 ? d : d + 'T00:00:00'))
  if (isNaN(dt)) return '—'
  return `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()}`
}
function statusLabel(s) {
  const map = { draft:locale.value==='ar'?'مسودة':'Draft', sent:locale.value==='ar'?'مرسلة':'Sent', paid:locale.value==='ar'?'مدفوعة':'Paid', overdue:locale.value==='ar'?'متأخرة':'Overdue', cancelled:locale.value==='ar'?'ملغاة':'Cancelled' }
  return map[s] || s
}
function statusIcon(s) {
  return { draft:'bi-circle', sent:'bi-send-fill', paid:'bi-check-circle-fill', overdue:'bi-exclamation-circle-fill', cancelled:'bi-x-circle-fill' }[s] || 'bi-circle'
}

onMounted(async () => {
  try {
    const { data } = await api.get(`/invoices/${route.params.id}`)
    invoice.value = data.data
    await fetchPayments()
  } catch { toast.error(locale.value==='ar'?'فشل تحميل الفاتورة':'Failed to load invoice') }
  loading.value = false
})

async function updateStatus(status) {
  updating.value = true
  try {
    const { data } = await api.patch(`/invoices/${invoice.value.id}/status`, { status })
    invoice.value = data.data
    confirmCancel.value = false
    window.bootstrap?.Modal.getInstance(document.getElementById('cancelInvModal'))?.hide()
    toast.success(locale.value==='ar' ? `تم تحديث حالة الفاتورة إلى: ${statusLabel(status)}` : `Invoice marked as ${statusLabel(status)}`)
  } catch (e) {
    toast.error(e.response?.data?.message || (locale.value==='ar'?'فشل التحديث':'Update failed'))
  }
  updating.value = false
}

// ── Send Email ──────────────────────────────────────────
const sendingEmail = ref(false)
async function sendEmail() {
  sendingEmail.value = true
  try {
    await api.post(`/invoices/${invoice.value.id}/send`)
    toast.success(locale.value==='ar'?'تم إرسال الفاتورة بالإيميل':'Invoice sent by email')
    if (invoice.value.status === 'draft') invoice.value.status = 'sent'
  } catch (e) {
    toast.error(e.response?.data?.message || (locale.value==='ar'?'فشل الإرسال':'Send failed'))
  }
  sendingEmail.value = false
}

// ── Payments ─────────────────────────────────────────────
const payments        = ref([])
const showPaymentModal = ref(false)
const savingPayment   = ref(false)

const payForm = reactive({
  amount: '',
  payment_date: new Date().toISOString().slice(0,10),
  method: 'cash',
  reference: '',
  notes: '',
})

const paidAmount = computed(() => payments.value.reduce((s, p) => s + Number(p.amount), 0))
const balanceDue = computed(() => invoice.value ? Math.max(0, Number(invoice.value.total) - paidAmount.value) : 0)
const paidPercent = computed(() => invoice.value && Number(invoice.value.total) > 0
  ? (paidAmount.value / Number(invoice.value.total)) * 100 : 0)

async function fetchPayments() {
  if (!invoice.value) return
  try {
    const { data } = await api.get(`/invoices/${invoice.value.id}/payments`)
    payments.value = data.data ?? []
  } catch {}
}

async function savePayment() {
  if (!payForm.amount || Number(payForm.amount) <= 0) {
    toast.error(locale.value==='ar'?'أدخل مبلغاً صحيحاً':'Enter a valid amount')
    return
  }
  savingPayment.value = true
  try {
    await api.post(`/invoices/${invoice.value.id}/payments`, { ...payForm })
    toast.success(locale.value==='ar'?'تم تسجيل الدفعة':'Payment recorded')
    showPaymentModal.value = false
    payForm.amount = ''
    payForm.reference = ''
    payForm.notes = ''
    await fetchPayments()
    // refresh invoice (may have been marked paid)
    const { data } = await api.get(`/invoices/${invoice.value.id}`)
    invoice.value = data.data
  } catch (e) {
    toast.error(e.response?.data?.message || (locale.value==='ar'?'فشل التسجيل':'Failed to record'))
  }
  savingPayment.value = false
}

async function removePayment(id) {
  try {
    await api.delete(`/invoices/${invoice.value.id}/payments/${id}`)
    toast.success(locale.value==='ar'?'تم حذف الدفعة':'Payment deleted')
    await fetchPayments()
    const { data } = await api.get(`/invoices/${invoice.value.id}`)
    invoice.value = data.data
  } catch (e) {
    toast.error(e.response?.data?.message || (locale.value==='ar'?'فشل الحذف':'Delete failed'))
  }
}
</script>

<style scoped>
.inv-header-icon { width:52px;height:52px;border-radius:14px;background:rgba(37,99,235,.1);color:#2563EB;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0; }

.inv-status-badge { display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }
.inv-draft     { background:rgba(100,116,139,.1);color:#64748B; }
.inv-sent      { background:rgba(124,58,237,.1);color:#7C3AED; }
.inv-paid      { background:rgba(16,185,129,.1);color:#059669; }
.inv-overdue   { background:rgba(239,68,68,.1);color:#DC2626; }
.inv-cancelled { background:rgba(100,116,139,.1);color:#94A3B8; }

.info-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-secondary-color);margin-bottom:3px; }
.info-value { font-size:13px;font-weight:600;color:var(--bs-body-color); }
.info-sub   { font-size:11px;color:var(--bs-secondary-color);margin-top:1px; }

.items-table { border-collapse:separate;border-spacing:0; }
.items-table thead th { background:var(--bs-tertiary-bg,#F8FAFC);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:var(--bs-secondary-color);padding:.65rem .75rem;border-bottom:1px solid var(--bs-border-color); }
.items-table td { padding:.75rem;border-bottom:1px solid var(--bs-border-color);vertical-align:middle; }
.items-table tbody tr:last-child td { border-bottom:none; }
.item-desc { font-weight:500;font-size:13px; }

.totals-box { min-width:260px;background:var(--bs-tertiary-bg,#F8FAFC);border-radius:10px;padding:1rem; }
.tot-row { display:flex;justify-content:space-between;align-items:center;padding:.35rem 0;font-size:13px;border-bottom:1px solid var(--bs-border-color); }
.tot-row:last-child { border-bottom:none; }
.tot-final { font-size:1.1rem;font-weight:800;color:#2563EB;padding-top:.6rem;margin-top:.25rem; }

.notes-box { background:var(--bs-tertiary-bg,#F8FAFC);border-radius:10px;padding:1rem; }

.fsect-title { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-secondary-color);padding-bottom:.5rem;margin-bottom:.875rem;border-bottom:1px solid var(--bs-border-color); }
.sidebar-row { display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--bs-border-color);font-size:13px; }
.sidebar-row:last-child { border-bottom:none; }

.del-icon { width:72px;height:72px;border-radius:50%;background:rgba(239,68,68,.1);color:#DC2626;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto; }

.payment-row { display:flex;justify-content:space-between;align-items:center;padding:.4rem 0;border-bottom:1px solid var(--bs-border-color); }
.payment-row:last-child { border-bottom:none; }

.modal-backdrop-inv { position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1060;display:flex;align-items:center;justify-content:center;padding:16px; }
.modal-card-inv { background:var(--bs-body-bg,#fff);border-radius:14px;width:100%;max-width:460px;box-shadow:0 12px 40px rgba(0,0,0,.2);display:flex;flex-direction:column;max-height:90vh; }
.modal-card-header-inv { display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--bs-border-color); }
.modal-card-body-inv { flex:1;overflow-y:auto;padding:20px; }
.modal-card-footer-inv { display:flex;gap:8px;justify-content:flex-end;padding:14px 20px;border-top:1px solid var(--bs-border-color); }
</style>
