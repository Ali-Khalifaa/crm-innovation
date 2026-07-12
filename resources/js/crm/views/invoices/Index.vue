<template>
  <CrmLayout>
    <div class="crm-card">
      <div class="crm-card-header">
        <span class="crm-card-title">Invoices</span>
        <router-link to="/crm/invoices/create" class="btn-primary"><i class="fas fa-plus"></i> New Invoice</router-link>
      </div>
      <div style="display:flex;gap:12px;margin-bottom:16px;">
        <select v-model="filterStatus" @change="fetchInvoices" class="form-control" style="max-width:160px;">
          <option value="">All Status</option>
          <option value="draft">Draft</option>
          <option value="sent">Sent</option>
          <option value="paid">Paid</option>
          <option value="overdue">Overdue</option>
        </select>
      </div>
      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <table v-else class="crm-table">
        <thead><tr><th>Invoice #</th><th>Date</th><th>Due Date</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <tr v-for="inv in invoices" :key="inv.id">
            <td><router-link :to="`/crm/invoices/${inv.id}`" style="color:#2563EB;font-weight:700;">{{ inv.invoice_number }}</router-link></td>
            <td>{{ new Date(inv.issue_date).toLocaleDateString() }}</td>
            <td>{{ new Date(inv.due_date).toLocaleDateString() }}</td>
            <td style="font-weight:700;">${{ Number(inv.total).toLocaleString() }}</td>
            <td><span class="badge" :class="invBadge(inv.status)">{{ inv.status }}</span></td>
            <td>
              <a :href="`/api/crm/invoices/${inv.id}/pdf`" target="_blank" class="btn-secondary btn-sm btn-icon" title="PDF"><i class="fas fa-file-pdf"></i></a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </CrmLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
const invoices = ref([])
const loading = ref(true)
const filterStatus = ref('')
async function fetchInvoices() {
  loading.value = true
  const { data } = await api.get('/invoices', { params: { status: filterStatus.value } })
  invoices.value = data.data
  loading.value = false
}
function invBadge(s) {
  return { paid: 'badge-success', sent: 'badge-info', draft: 'badge-gray', overdue: 'badge-danger', cancelled: 'badge-gray' }[s] || 'badge-gray'
}
onMounted(fetchInvoices)
</script>
