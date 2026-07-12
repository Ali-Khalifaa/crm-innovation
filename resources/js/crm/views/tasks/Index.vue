<template>
  <CrmLayout>
    <div class="crm-card">
      <div class="crm-card-header">
        <span class="crm-card-title">Tasks</span>
        <button @click="showForm = true" class="btn-primary"><i class="fas fa-plus"></i> New Task</button>
      </div>
      <div style="display:flex;gap:12px;margin-bottom:16px;">
        <select v-model="filterStatus" @change="fetchTasks" class="form-control" style="max-width:150px;">
          <option value="">All</option>
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
        <select v-model="filterPriority" @change="fetchTasks" class="form-control" style="max-width:130px;">
          <option value="">Any Priority</option>
          <option value="high">High</option>
          <option value="medium">Medium</option>
          <option value="low">Low</option>
        </select>
      </div>
      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <table v-else class="crm-table">
        <thead><tr><th>Task</th><th>Priority</th><th>Status</th><th>Due Date</th><th>Actions</th></tr></thead>
        <tbody>
          <tr v-for="t in tasks" :key="t.id">
            <td style="font-weight:600;">{{ t.title }}</td>
            <td><span class="badge" :class="priorityClass(t.priority)">{{ t.priority }}</span></td>
            <td><span class="badge" :class="statusClass(t.status)">{{ t.status }}</span></td>
            <td>{{ t.due_date ? new Date(t.due_date).toLocaleDateString() : '—' }}</td>
            <td>
              <button @click="toggleComplete(t)" class="btn-secondary btn-sm btn-icon" :title="t.status === 'completed' ? 'Reopen' : 'Complete'">
                <i :class="t.status === 'completed' ? 'fas fa-undo' : 'fas fa-check'"></i>
              </button>
              <button @click="deleteTask(t)" class="btn-danger btn-sm btn-icon ms-1"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- New Task Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm=false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>New Task</h3>
          <button @click="showForm=false" style="background:none;border:none;font-size:20px;cursor:pointer;">×</button>
        </div>
        <form @submit.prevent="createTask">
          <div class="form-group"><label class="form-label">Title *</label><input v-model="form.title" class="form-control" required /></div>
          <div class="form-group"><label class="form-label">Description</label><textarea v-model="form.description" class="form-control" rows="2"></textarea></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group"><label class="form-label">Priority</label>
              <select v-model="form.priority" class="form-control"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
            </div>
            <div class="form-group"><label class="form-label">Due Date</label><input v-model="form.due_date" type="datetime-local" class="form-control" /></div>
          </div>
          <div style="display:flex;gap:8px;justify-content:flex-end;">
            <button type="button" @click="showForm=false" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Create Task</button>
          </div>
        </form>
      </div>
    </div>
  </CrmLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const tasks = ref([])
const loading = ref(true)
const showForm = ref(false)
const filterStatus = ref('')
const filterPriority = ref('')
const form = ref({ title: '', description: '', priority: 'medium', due_date: '' })

async function fetchTasks() {
  loading.value = true
  const { data } = await api.get('/tasks', { params: { status: filterStatus.value, priority: filterPriority.value } })
  tasks.value = data.data
  loading.value = false
}

async function createTask() {
  await api.post('/tasks', form.value)
  showForm.value = false
  form.value = { title: '', description: '', priority: 'medium', due_date: '' }
  fetchTasks()
}

async function toggleComplete(t) {
  const status = t.status === 'completed' ? 'pending' : 'completed'
  await api.put(`/tasks/${t.id}`, { status })
  t.status = status
}

async function deleteTask(t) {
  if (!confirm('Delete this task?')) return
  await api.delete(`/tasks/${t.id}`)
  fetchTasks()
}

function priorityClass(p) { return { high: 'badge-danger', medium: 'badge-warning', low: 'badge-info' }[p] || 'badge-gray' }
function statusClass(s) { return { completed: 'badge-success', in_progress: 'badge-info', pending: 'badge-gray' }[s] || 'badge-gray' }

onMounted(fetchTasks)
</script>
<style scoped>
.ms-1 { margin-left: 4px; }
.modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:2000;display:flex;align-items:center;justify-content:center; }
.modal-box { background:#fff;border-radius:16px;padding:28px;width:100%;max-width:480px; }
.modal-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px; }
.modal-header h3 { font-size:18px;font-weight:700;margin:0; }
</style>
