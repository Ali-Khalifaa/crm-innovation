<template>
  <CrmLayout>
    <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
    <div v-else-if="contact">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
        <div style="width:52px;height:52px;background:#2563EB;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:700;">
          {{ contact.first_name?.charAt(0) }}
        </div>
        <div>
          <h2 style="margin:0;font-size:20px;font-weight:700;">{{ contact.first_name }} {{ contact.last_name }}</h2>
          <span class="badge" :class="`status-${contact.status}`">{{ contact.status }}</span>
        </div>
        <div style="margin-left:auto;display:flex;gap:8px;">
          <router-link :to="`/crm/contacts/${contact.id}/edit`" class="btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</router-link>
          <router-link to="/crm/contacts" class="btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</router-link>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="crm-card">
          <div class="crm-card-title mb-3">Contact Details</div>
          <div class="detail-row"><span>Email</span><span>{{ contact.email || '—' }}</span></div>
          <div class="detail-row"><span>Phone</span><span>{{ contact.phone || '—' }}</span></div>
          <div class="detail-row"><span>Company</span><span>{{ contact.company || '—' }}</span></div>
          <div class="detail-row"><span>Address</span><span>{{ contact.address || '—' }}</span></div>
          <div class="detail-row" v-if="contact.notes"><span>Notes</span><span>{{ contact.notes }}</span></div>
        </div>
        <div class="crm-card">
          <div class="crm-card-title mb-3">Activity</div>
          <div class="detail-row"><span>Created</span><span>{{ new Date(contact.created_at).toLocaleDateString() }}</span></div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const route = useRoute()
const contact = ref(null)
const loading = ref(true)

onMounted(async () => {
  const { data } = await api.get(`/contacts/${route.params.id}`)
  contact.value = data.data
  loading.value = false
})
</script>
<style scoped>
.detail-row { display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #F1F5F9;font-size:14px; }
.detail-row span:first-child { color:#64748B;font-weight:500; }
.mb-3 { margin-bottom:12px; }
</style>
