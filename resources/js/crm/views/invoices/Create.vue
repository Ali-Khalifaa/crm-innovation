<template>
  <CrmLayout>
    <div class="crm-card" style="max-width:760px;margin:0 auto;">
      <div class="crm-card-header">
        <span class="crm-card-title">New Invoice</span>
        <router-link to="/crm/invoices" class="btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</router-link>
      </div>

      <form @submit.prevent="save">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div class="form-group">
            <label class="form-label">Issue Date *</label>
            <input v-model="form.issue_date" type="date" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Due Date *</label>
            <input v-model="form.due_date" type="date" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Tax Rate (%)</label>
            <input v-model.number="form.tax_rate" type="number" min="0" max="100" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Discount ($)</label>
            <input v-model.number="form.discount" type="number" min="0" class="form-control" />
          </div>
        </div>

        <!-- Items -->
        <div class="mb-4">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <label class="form-label" style="margin:0;">Invoice Items *</label>
            <button type="button" @click="addItem" class="btn-secondary btn-sm"><i class="fas fa-plus"></i> Add Item</button>
          </div>
          <table class="crm-table">
            <thead><tr><th>Description</th><th style="width:100px;">Qty</th><th style="width:120px;">Unit Price</th><th style="width:100px;">Total</th><th style="width:40px;"></th></tr></thead>
            <tbody>
              <tr v-for="(item, i) in form.items" :key="i">
                <td><input v-model="item.description" class="form-control" style="min-width:200px;" required /></td>
                <td><input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="form-control" @input="calcTotal(item)" /></td>
                <td><input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="form-control" @input="calcTotal(item)" /></td>
                <td style="font-weight:700;">${{ (item.total || 0).toFixed(2) }}</td>
                <td><button type="button" @click="removeItem(i)" class="btn-danger btn-sm btn-icon"><i class="fas fa-times"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals -->
        <div style="text-align:right;margin-bottom:20px;">
          <div style="font-size:14px;color:#64748B;">Subtotal: <strong>${{ subtotal.toFixed(2) }}</strong></div>
          <div v-if="form.tax_rate" style="font-size:14px;color:#64748B;">Tax ({{ form.tax_rate }}%): <strong>${{ taxAmount.toFixed(2) }}</strong></div>
          <div v-if="form.discount" style="font-size:14px;color:#64748B;">Discount: <strong>-${{ form.discount.toFixed(2) }}</strong></div>
          <div style="font-size:20px;font-weight:800;color:#2563EB;">Total: ${{ total.toFixed(2) }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Notes</label>
          <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
        </div>

        <div v-if="error" style="color:#EF4444;margin-bottom:12px;">{{ error }}</div>
        <button type="submit" class="btn-primary" :disabled="saving">
          <span v-if="saving" class="spinner"></span>
          {{ saving ? 'Creating...' : 'Create Invoice' }}
        </button>
      </form>
    </div>
  </CrmLayout>
</template>
<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const router = useRouter()
const toast = useToast()
const today = new Date().toISOString().split('T')[0]
const form = ref({
  issue_date: today, due_date: '', tax_rate: 0, discount: 0, notes: '',
  items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
})
const saving = ref(false)
const error = ref('')

const subtotal = computed(() => form.value.items.reduce((s, i) => s + (i.total || 0), 0))
const taxAmount = computed(() => subtotal.value * (form.value.tax_rate || 0) / 100)
const total = computed(() => subtotal.value + taxAmount.value - (form.value.discount || 0))

function calcTotal(item) { item.total = parseFloat(((item.quantity || 0) * (item.unit_price || 0)).toFixed(2)) }
function addItem() { form.value.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 }) }
function removeItem(i) { if (form.value.items.length > 1) form.value.items.splice(i, 1) }

async function save() {
  saving.value = true; error.value = ''
  try {
    const { data } = await api.post('/invoices', form.value)
    toast.success(`Invoice ${data.data?.invoice_number || ''} created successfully`)
    router.push('/crm/invoices')
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to create invoice'
    error.value = msg
    toast.error(msg)
  } finally { saving.value = false }
}
</script>
<style scoped>
.mb-4 { margin-bottom: 16px; }
</style>
