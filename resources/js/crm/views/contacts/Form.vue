<template>
  <CrmLayout>
    <div class="crm-card" style="max-width:640px;margin:0 auto;">
      <div class="crm-card-header">
        <span class="crm-card-title">{{ isEdit ? 'Edit Contact' : 'New Contact' }}</span>
        <router-link to="/crm/contacts" class="btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</router-link>
      </div>

      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <form v-else @submit.prevent="save">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div class="form-group">
            <label class="form-label">First Name *</label>
            <input v-model="form.first_name" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Last Name</label>
            <input v-model="form.last_name" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input v-model="form.phone" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Company</label>
            <input v-model="form.company" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select v-model="form.status" class="form-control">
              <option value="lead">Lead</option>
              <option value="customer">Customer</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Address</label>
          <input v-model="form.address" class="form-control" />
        </div>
        <div class="form-group">
          <label class="form-label">Notes</label>
          <textarea v-model="form.notes" class="form-control" rows="3"></textarea>
        </div>

        <div v-if="error" class="alert-error">{{ error }}</div>

        <div style="display:flex;gap:12px;">
          <button type="submit" class="btn-primary" :disabled="saving">
            <span v-if="saving" class="spinner"></span>
            {{ saving ? 'Saving...' : (isEdit ? 'Update Contact' : 'Create Contact') }}
          </button>
          <router-link to="/crm/contacts" class="btn-secondary">Cancel</router-link>
        </div>
      </form>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'
const toast = useToast()

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)
const form = ref({ first_name: '', last_name: '', email: '', phone: '', company: '', address: '', notes: '', status: 'lead' })
const loading = ref(isEdit.value)
const saving = ref(false)
const error = ref('')

onMounted(async () => {
  if (isEdit.value) {
    const { data } = await api.get(`/contacts/${route.params.id}`)
    Object.assign(form.value, data.data)
    loading.value = false
  }
})

async function save() {
  saving.value = true; error.value = ''
  try {
    if (isEdit.value) {
      await api.put(`/contacts/${route.params.id}`, form.value)
      toast.success('Contact updated successfully')
    } else {
      await api.post('/contacts', form.value)
      toast.success('Contact created successfully')
    }
    router.push('/crm/contacts')
  } catch (err) {
    const msg = err.response?.data?.message || 'Something went wrong'
    error.value = msg
    toast.error(msg)
  } finally {
    saving.value = false
  }
}
</script>
<style scoped>
.alert-error { background:#FEE2E2;color:#991B1B;border-radius:8px;padding:10px;margin-bottom:12px;font-size:14px; }
</style>
