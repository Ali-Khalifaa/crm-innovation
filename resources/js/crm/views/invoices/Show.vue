<template>
  <CrmLayout>
    <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
    <div v-else-if="invoice">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
          <h2 style="font-size:20px;font-weight:800;color:#2563EB;margin:0;">{{ invoice.invoice_number }}</h2>
          <span class="badge" :class="invBadge(invoice.status)" style="font-size:13px;margin-top:4px;display:inline-block;">{{ invoice.status }}</span>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <button v-if="invoice.status === 'draft'" @click="updateStatus('sent')" class="btn-secondary btn-sm" :disabled="updating">
            <i class="fas fa-paper-plane"></i> Mark Sent
          </button>
          <button v-if="['sent','overdue'].includes(invoice.status)" @click="updateStatus('paid')" class="btn-primary btn-sm" :disabled="updating">
            <i class="fas fa-check-circle"></i> Mark Paid
          </button>
          <button v-if="!['cancelled','paid'].includes(invoice.status)" @click="updateStatus('cancelled')" class="btn-danger btn-sm" :disabled="updating">
            Cancel
          </button>
          <a :href="`/api/crm/invoices/${invoice.id}/pdf?token=${token}`" target="_blank" class="btn-secondary btn-sm">
            <i class="fas fa-file-pdf" style="color:#EF4444;"></i> PDF
          </a>
          <router-link to="/crm/invoices" class="btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</router-link>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start;">
        <div class="crm-card">
          <div style="display:flex;gap:32px;margin-bottom:20px;flex-wrap:wrap;">
            <div><div style="font-size:12px;color:#64748B;">Issue Date</div><strong>{{ new Date(invoice.issue_date).toLocaleDateString() }}</strong></div>
            <div>
              <div style="font-size:12px;color:#64748B;">Due Date</div>
              <strong :style="{ color: isOverdue ? '#EF4444' : 'inherit' }">{{ new Date(invoice.due_date).toLocaleDateString() }}</strong>
            </div>
            <div v-if="invoice.contact"><div style="font-size:12px;color:#64748B;">Client</div><strong>{{ invoice.contact?.email }}</strong></div>
          </div>

          <table class="crm-table" style="margin-bottom:16px;">
            <thead><tr><th>Description</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Unit Price</th><th style="text-align:right;">Total</th></tr></thead>
            <tbody>
              <tr v-for="item in invoice.items" :key="item.id">
                <td>{{ item.description }}</td>
                <td style="text-align:right;">{{ item.quantity }}</td>
                <td style="text-align:right;">${{ Number(item.unit_price).toFixed(2) }}</td>
                <td style="text-align:right;font-weight:600;">${{ Number(item.total).toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>

          <div style="display:flex;justify-content:flex-end;">
            <div style="min-width:220px;">
              <div class="tot-row"><span style="color:#64748B;">Subtotal</span><span>${{ Number(invoice.subtotal).toFixed(2) }}</span></div>
              <div v-if="invoice.tax_rate" class="tot-row"><span style="color:#64748B;">Tax ({{ invoice.tax_rate }}%)</span><span>${{ Number(invoice.tax_amount).toFixed(2) }}</span></div>
              <div v-if="invoice.discount" class="tot-row"><span style="color:#64748B;">Discount</span><span>-${{ Number(invoice.discount).toFixed(2) }}</span></div>
              <div class="tot-row" style="border-top:2px solid #E2E8F0;padding-top:10px;font-size:20px;font-weight:800;color:#2563EB;">
                <span>Total</span><span>${{ Number(invoice.total).toFixed(2) }}</span>
              </div>
            </div>
          </div>

          <div v-if="invoice.notes" style="margin-top:16px;padding:12px;background:var(--bg,#F8FAFC);border-radius:8px;font-size:13px;color:#64748B;">
            <strong>Notes:</strong> {{ invoice.notes }}
          </div>
        </div>

        <div>
          <div class="crm-card" style="margin-bottom:12px;">
            <div style="font-weight:700;font-size:14px;margin-bottom:12px;">Invoice Info</div>
            <div class="info-row"><span>Total</span><strong style="color:#2563EB;">${{ Number(invoice.total).toFixed(2) }}</strong></div>
            <div class="info-row"><span>Status</span><span class="badge" :class="invBadge(invoice.status)">{{ invoice.status }}</span></div>
            <div class="info-row"><span>Created</span><span style="font-size:12px;">{{ new Date(invoice.created_at).toLocaleDateString() }}</span></div>
          </div>
          <div class="crm-card" v-if="!['paid','cancelled'].includes(invoice.status)">
            <div style="font-weight:700;font-size:14px;margin-bottom:12px;">Change Status</div>
            <select v-model="newStatus" class="form-control" style="margin-bottom:10px;">
              <option value="draft">Draft</option>
              <option value="sent">Sent</option>
              <option value="paid">Paid</option>
              <option value="overdue">Overdue</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <button @click="updateStatus(newStatus)" class="btn-primary" style="width:100%;" :disabled="updating">
              <span v-if="updating" class="spinner"></span>
              {{ updating ? 'Updating...' : 'Update' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const route    = useRoute()
const store    = useStore()
const toast    = useToast()
const invoice  = ref(null)
const loading  = ref(true)
const updating = ref(false)
const newStatus = ref('')

const token     = computed(() => store.state.auth.token)
const isOverdue = computed(() =>
  invoice.value?.due_date && new Date(invoice.value.due_date) < new Date() && invoice.value.status !== 'paid'
)

onMounted(async () => {
  try {
    const { data } = await api.get(`/invoices/${route.params.id}`)
    invoice.value = data.data
    newStatus.value = data.data.status
  } catch { toast.error('Failed to load invoice') }
  loading.value = false
})

async function updateStatus(status) {
  updating.value = true
  try {
    const { data } = await api.patch(`/invoices/${invoice.value.id}/status`, { status })
    invoice.value  = data.data
    newStatus.value = data.data.status
    toast.success(`Marked as ${status}`)
  } catch (err) { toast.error(err.response?.data?.message || 'Failed') }
  finally { updating.value = false }
}

function invBadge(s) {
  return { paid:'badge-success', sent:'badge-info', draft:'badge-gray', overdue:'badge-danger', cancelled:'badge-gray' }[s] || 'badge-gray'
}
</script>

<style scoped>
.tot-row { display:flex;justify-content:space-between;padding:5px 0;font-size:14px; }
.info-row { display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border,#F1F5F9);font-size:13px; }
.info-row:last-child { border:none; }
</style>
