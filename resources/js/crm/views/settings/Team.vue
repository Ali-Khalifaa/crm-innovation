<template>
  <CrmLayout>
    <div class="crm-card">
      <div class="crm-card-header">
        <span class="crm-card-title">Team Members <span class="badge badge-info">{{ users.length }}</span></span>
        <button @click="showInvite = true" class="btn-primary" v-if="isOwner || isAdmin">
          <i class="fas fa-user-plus"></i> Invite Member
        </button>
      </div>

      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <table v-else class="crm-table">
        <thead>
          <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>
        </thead>
        <tbody>
          <tr v-for="u in users" :key="u.id">
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:50%;background:#2563EB;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;">
                  {{ u.name?.charAt(0).toUpperCase() }}
                </div>
                <span style="font-weight:600;">{{ u.name }}</span>
              </div>
            </td>
            <td>{{ u.email }}</td>
            <td>
              <span class="badge" :class="roleClass(u.role)">{{ u.role }}</span>
            </td>
            <td>
              <span class="badge" :class="u.status === 'active' ? 'badge-success' : 'badge-gray'">{{ u.status }}</span>
            </td>
          </tr>
          <tr v-if="!users.length">
            <td colspan="4" style="text-align:center;color:#94A3B8;padding:32px;">No team members yet</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Invite Modal -->
    <div v-if="showInvite" class="modal-overlay" @click.self="showInvite = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Invite Team Member</h3>
          <button @click="showInvite = false" style="background:none;border:none;font-size:22px;cursor:pointer;">×</button>
        </div>

        <div v-if="inviteError" class="alert-error">{{ inviteError }}</div>

        <form @submit.prevent="invite">
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input v-model="form.name" class="form-control" required placeholder="John Smith" />
          </div>
          <div class="form-group">
            <label class="form-label">Email *</label>
            <input v-model="form.email" type="email" class="form-control" required placeholder="john@company.com" />
          </div>
          <div class="form-group">
            <label class="form-label">Password *</label>
            <input v-model="form.password" type="password" class="form-control" required minlength="8" placeholder="Min 8 characters" />
          </div>
          <div class="form-group">
            <label class="form-label">Role *</label>
            <select v-model="form.role" class="form-control">
              <option value="admin">Admin — can manage everything</option>
              <option value="member">Member — view and create only</option>
            </select>
          </div>
          <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
            <button type="button" @click="showInvite = false" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary" :disabled="inviting">
              <span v-if="inviting" class="spinner"></span>
              {{ inviting ? 'Inviting...' : 'Send Invite' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store  = useStore()
const toast  = useToast()
const users  = ref([])
const loading = ref(true)
const showInvite = ref(false)
const inviting   = ref(false)
const inviteError = ref('')
const form = ref({ name: '', email: '', password: '', role: 'member' })

const currentUser = computed(() => store.getters['auth/user'])
const isOwner = computed(() => currentUser.value?.role === 'owner')
const isAdmin = computed(() => currentUser.value?.role === 'admin')

async function fetchUsers() {
  loading.value = true
  try {
    const { data } = await api.get('/settings/users')
    users.value = data.data
  } catch { toast.error('Failed to load team') }
  loading.value = false
}

async function invite() {
  inviting.value = true
  inviteError.value = ''
  try {
    await api.post('/settings/users', form.value)
    toast.success(`${form.value.name} invited successfully`)
    showInvite.value = false
    form.value = { name: '', email: '', password: '', role: 'member' }
    fetchUsers()
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to invite user'
    inviteError.value = msg
    toast.error(msg)
  } finally { inviting.value = false }
}

function roleClass(role) {
  return { owner: 'badge-danger', admin: 'badge-info', member: 'badge-gray' }[role] || 'badge-gray'
}

onMounted(fetchUsers)
</script>

<style scoped>
.alert-error { background:#FEE2E2;color:#991B1B;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:14px; }
.modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:3000;display:flex;align-items:center;justify-content:center;padding:20px; }
.modal-box { background:#fff;border-radius:16px;padding:28px;width:100%;max-width:440px; }
.modal-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px; }
.modal-header h3 { font-size:18px;font-weight:700;margin:0; }
</style>
