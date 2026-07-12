<template>
  <CrmLayout>
    <div class="crm-card">
      <div class="crm-card-header">
        <span class="crm-card-title">Deals <span class="badge badge-info">{{ meta.total ?? 0 }}</span></span>
        <div style="display:flex;gap:8px;">
          <router-link to="/crm/deals" class="btn-secondary btn-sm"><i class="fas fa-columns"></i> Kanban</router-link>
          <button @click="showModal = true" class="btn-primary"><i class="fas fa-plus"></i> New Deal</button>
        </div>
      </div>

      <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <input v-model="search" @input="debouncedFetch" type="text" class="form-control" placeholder="Search deals..." style="max-width:260px;" />
        <select v-model="filterStatus" @change="page=1;fetchDeals()" class="form-control" style="max-width:130px;">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="won">Won</option>
          <option value="lost">Lost</option>
        </select>
        <select v-model="filterStage" @change="page=1;fetchDeals()" class="form-control" style="max-width:160px;">
          <option value="">All Stages</option>
          <option v-for="s in stages" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>

      <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
      <div v-else-if="!deals.length" style="text-align:center;padding:40px;color:#94A3B8;">
        <i class="fas fa-funnel-dollar" style="font-size:40px;display:block;margin-bottom:12px;"></i>No deals found
      </div>
      <div v-else class="crm-table-wrapper">
        <table class="crm-table">
          <thead><tr><th>Title</th><th>Stage</th><th>Value</th><th>Probability</th><th>Status</th><th>Close Date</th><th></th></tr></thead>
          <tbody>
            <tr v-for="d in deals" :key="d.id">
              <td style="font-weight:600;">{{ d.title }}</td>
              <td><span v-if="d.stage" class="badge badge-info">{{ d.stage?.name }}</span><span v-else style="color:#94A3B8;">—</span></td>
              <td style="font-weight:700;color:#2563EB;">${{ Number(d.value||0).toLocaleString() }}</td>
              <td>
                <div style="display:flex;align-items:center;gap:6px;">
                  <div style="height:4px;width:48px;background:#E2E8F0;border-radius:2px;overflow:hidden;">
                    <div style="height:100%;background:#2563EB;" :style="{width:(d.probability||0)+'%'}"></div>
                  </div>
                  <span style="font-size:12px;color:#64748B;">{{ d.probability||0 }}%</span>
                </div>
              </td>
              <td><span class="badge" :class="`status-${d.status}`">{{ d.status }}</span></td>
              <td style="font-size:13px;">{{ d.expected_close_date ? new Date(d.expected_close_date).toLocaleDateString() : '—' }}</td>
              <td>
                <button @click="openEdit(d)" class="btn-secondary btn-sm btn-icon"><i class="fas fa-edit"></i></button>
                <button @click="del(d)" class="btn-danger btn-sm btn-icon" style="margin-left:4px;"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="meta.last_page > 1" style="display:flex;justify-content:center;gap:6px;margin-top:16px;flex-wrap:wrap;">
        <button @click="page=1;fetchDeals()" class="btn-secondary btn-sm" :disabled="page===1">«</button>
        <button v-for="p in pages" :key="p" @click="page=p;fetchDeals()" class="btn-secondary btn-sm" :class="{'btn-primary':p===page}">{{ p }}</button>
        <button @click="page=meta.last_page;fetchDeals()" class="btn-secondary btn-sm" :disabled="page===meta.last_page">»</button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-box">
        <div class="modal-header">
          <h3>{{ editingDeal ? 'Edit Deal' : 'New Deal' }}</h3>
          <button @click="closeModal" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94A3B8;">×</button>
        </div>
        <form @submit.prevent="save">
          <div class="form-group"><label class="form-label">Title *</label><input v-model="form.title" class="form-control" required /></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group"><label class="form-label">Value ($)</label><input v-model.number="form.value" type="number" min="0" class="form-control" /></div>
            <div class="form-group"><label class="form-label">Stage</label>
              <select v-model="form.stage_id" class="form-control"><option v-for="s in stages" :key="s.id" :value="s.id">{{ s.name }}</option></select>
            </div>
            <div class="form-group"><label class="form-label">Probability</label><input v-model.number="form.probability" type="number" min="0" max="100" class="form-control" /></div>
            <div class="form-group"><label class="form-label">Status</label>
              <select v-model="form.status" class="form-control"><option value="open">Open</option><option value="won">Won</option><option value="lost">Lost</option></select>
            </div>
          </div>
          <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">
            <button type="button" @click="closeModal" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary" :disabled="saving"><span v-if="saving" class="spinner"></span>{{ saving ? '...' : (editingDeal ? 'Update' : 'Create') }}</button>
          </div>
        </form>
      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const toast = useToast()
const deals = ref([]), stages = ref([]), meta = ref({})
const loading = ref(true), saving = ref(false)
const search = ref(''), filterStatus = ref(''), filterStage = ref('')
const page = ref(1), showModal = ref(false), editingDeal = ref(null)
const form = ref({ title:'', value:0, stage_id:null, probability:50, status:'open' })
let searchTimer = null

const pages = computed(() => {
  const t = meta.value.last_page || 1, c = page.value, arr = []
  for (let p = Math.max(1, c-2); p <= Math.min(t, c+2); p++) arr.push(p)
  return arr
})

async function fetchDeals() {
  loading.value = true
  try {
    const { data } = await api.get('/deals', { params: { search: search.value, status: filterStatus.value, stage_id: filterStage.value, page: page.value } })
    deals.value = data.data; meta.value = data.meta || {}
  } catch { toast.error('Failed to load') }
  loading.value = false
}
async function fetchStages() {
  const { data } = await api.get('/deal-stages'); stages.value = data.data
  if (stages.value.length && !form.value.stage_id) form.value.stage_id = stages.value[0].id
}
function debouncedFetch() { clearTimeout(searchTimer); searchTimer = setTimeout(() => { page.value=1; fetchDeals() }, 400) }
function openEdit(d) { editingDeal.value = d; form.value = { ...d }; showModal.value = true }
function closeModal()  { showModal.value = false; editingDeal.value = null }
async function save() {
  saving.value = true
  try {
    editingDeal.value ? await api.put(`/deals/${editingDeal.value.id}`, form.value) : await api.post('/deals', form.value)
    toast.success(editingDeal.value ? 'Updated' : 'Created')
    closeModal(); fetchDeals()
  } catch (e) { toast.error(e.response?.data?.message || 'Error') }
  finally { saving.value = false }
}
async function del(d) {
  if (!confirm(`Delete "${d.title}"?`)) return
  try { await api.delete(`/deals/${d.id}`); toast.success('Deleted'); fetchDeals() } catch { toast.error('Failed') }
}
onMounted(async () => { await fetchStages(); fetchDeals() })
</script>
<style scoped>
.modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;display:flex;align-items:center;justify-content:center;padding:20px; }
.modal-box { background:var(--surface,#fff);border-radius:16px;padding:28px;width:100%;max-width:460px; }
.modal-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:18px; }
.modal-header h3 { font-size:18px;font-weight:700;margin:0; }
</style>
