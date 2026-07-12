<template>
  <CrmLayout>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
      <div>
        <h2 style="font-size:20px;font-weight:700;margin:0;">Deals Pipeline</h2>
        <p style="color:#64748B;font-size:13px;margin:4px 0 0;">{{ totalDeals }} deals · ${{ totalValue.toLocaleString() }}</p>
      </div>
      <div style="display:flex;gap:8px;">
        <router-link to="/crm/deals/list" class="btn-secondary btn-sm"><i class="fas fa-list"></i> List</router-link>
        <button @click="openNewDeal()" class="btn-primary"><i class="fas fa-plus"></i> New Deal</button>
      </div>
    </div>

    <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
    <div v-else class="kanban-board">
      <div v-for="stage in stages" :key="stage.id" class="kanban-column">
        <div class="kanban-header" :style="{ borderTopColor: stage.color }">
          <span class="kanban-title">{{ stage.name }}</span>
          <span class="kanban-count" :style="{ background: stage.color + '22', color: stage.color }">
            {{ (dealsByStage[stage.id] || []).length }}
          </span>
        </div>

        <draggable
          v-model="dealsByStage[stage.id]"
          group="deals"
          item-key="id"
          class="kanban-cards"
          ghost-class="kanban-ghost"
          drag-class="kanban-drag"
          @end="onDragEnd($event, stage.id)"
        >
          <template #item="{ element: deal }">
            <div class="kanban-card" @click="openEditDeal(deal)">
              <div class="deal-title">{{ deal.title }}</div>
              <div class="deal-footer">
                <span class="deal-value">${{ Number(deal.value || 0).toLocaleString() }}</span>
                <div class="deal-prob-wrap">
                  <div class="prob-bar">
                    <div class="prob-fill" :style="{ width: (deal.probability || 0) + '%', background: stage.color }"></div>
                  </div>
                  <span class="prob-text">{{ deal.probability || 0 }}%</span>
                </div>
              </div>
              <div v-if="deal.expected_close_date" class="deal-date" :class="{ overdue: isOverdue(deal) }">
                <i class="fas fa-calendar-alt"></i> {{ formatDate(deal.expected_close_date) }}
              </div>
            </div>
          </template>

          <template #footer>
            <div class="kanban-add" @click="openNewDeal(stage)">
              <i class="fas fa-plus"></i> Add Deal
            </div>
          </template>
        </draggable>
      </div>
    </div>

    <!-- Deal Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-box">
        <div class="modal-header">
          <h3>{{ editingDeal ? 'Edit Deal' : 'New Deal' }}</h3>
          <button @click="closeModal" class="modal-close">×</button>
        </div>
        <form @submit.prevent="saveDeal">
          <div class="form-group">
            <label class="form-label">Title *</label>
            <input v-model="form.title" class="form-control" required autofocus />
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
              <label class="form-label">Value ($)</label>
              <input v-model.number="form.value" type="number" min="0" class="form-control" />
            </div>
            <div class="form-group">
              <label class="form-label">Stage</label>
              <select v-model="form.stage_id" class="form-control">
                <option v-for="s in stages" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Probability (%)</label>
              <input v-model.number="form.probability" type="number" min="0" max="100" class="form-control" />
            </div>
            <div class="form-group">
              <label class="form-label">Expected Close</label>
              <input v-model="form.expected_close_date" type="date" class="form-control" />
            </div>
            <div class="form-group">
              <label class="form-label">Status</label>
              <select v-model="form.status" class="form-control">
                <option value="open">Open</option>
                <option value="won">Won</option>
                <option value="lost">Lost</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea v-model="form.notes" class="form-control" rows="2"></textarea>
          </div>
          <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:4px;">
            <button v-if="editingDeal" type="button" class="btn-danger btn-sm" @click="deleteDeal">
              <i class="fas fa-trash"></i> Delete
            </button>
            <button type="button" @click="closeModal" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner"></span>
              {{ saving ? 'Saving...' : (editingDeal ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import draggable from 'vuedraggable'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const toast = useToast()
const stages = ref([])
const dealsByStage = reactive({})
const loading = ref(true)
const showModal = ref(false)
const saving = ref(false)
const editingDeal = ref(null)

const mkForm = () => ({ title: '', value: 0, stage_id: null, probability: 50, expected_close_date: '', status: 'open', notes: '' })
const form = ref(mkForm())

const totalDeals = computed(() => Object.values(dealsByStage).flat().length)
const totalValue  = computed(() => Object.values(dealsByStage).flat().reduce((s, d) => s + Number(d.value || 0), 0))

async function fetchData() {
  loading.value = true
  const [sr, dr] = await Promise.all([
    api.get('/deal-stages'),
    api.get('/deals', { params: { kanban: 1 } }),
  ])
  stages.value = sr.data.data
  stages.value.forEach(s => { dealsByStage[s.id] = [] })
  dr.data.data.forEach(d => {
    if (d.stage_id && dealsByStage[d.stage_id] !== undefined) dealsByStage[d.stage_id].push(d)
  })
  loading.value = false
}

async function onDragEnd(evt, toStageId) {
  const list = dealsByStage[toStageId] || []
  const deal = list[evt.newIndex]
  if (!deal || deal.stage_id === toStageId) return
  try {
    await api.put(`/deals/${deal.id}`, { stage_id: toStageId })
    deal.stage_id = toStageId
    toast.success(`"${deal.title}" moved`)
  } catch {
    toast.error('Failed to move deal')
    fetchData()
  }
}

function openNewDeal(stage = null) {
  editingDeal.value = null
  form.value = mkForm()
  if (stage) form.value.stage_id = stage.id
  else if (stages.value.length) form.value.stage_id = stages.value[0].id
  showModal.value = true
}

function openEditDeal(deal) {
  editingDeal.value = deal
  form.value = { ...deal }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingDeal.value = null
}

async function saveDeal() {
  saving.value = true
  try {
    if (editingDeal.value) {
      const { data } = await api.put(`/deals/${editingDeal.value.id}`, form.value)
      toast.success('Deal updated')
      await fetchData()
    } else {
      await api.post('/deals', form.value)
      toast.success('Deal created')
      await fetchData()
    }
    closeModal()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Failed to save deal')
  } finally { saving.value = false }
}

async function deleteDeal() {
  if (!confirm(`Delete "${editingDeal.value.title}"?`)) return
  try {
    await api.delete(`/deals/${editingDeal.value.id}`)
    toast.success('Deal deleted')
    closeModal()
    fetchData()
  } catch { toast.error('Failed to delete') }
}

function isOverdue(deal) {
  return deal.expected_close_date && new Date(deal.expected_close_date) < new Date() && deal.status === 'open'
}
function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

onMounted(fetchData)
</script>

<style scoped>
.kanban-board { display:flex;gap:14px;overflow-x:auto;padding-bottom:20px;align-items:flex-start;min-height:60vh; }
.kanban-column { min-width:265px;max-width:280px;background:var(--bg,#F8FAFC);border-radius:12px;border:1px solid var(--border,#E2E8F0);flex-shrink:0; }
.kanban-header { padding:12px 14px;border-top:3px solid #6366F1;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;background:var(--surface,#fff); }
.kanban-title { font-weight:700;font-size:13px; }
.kanban-count { font-size:11px;font-weight:700;border-radius:10px;padding:2px 8px; }
.kanban-cards { padding:10px;display:flex;flex-direction:column;gap:8px;min-height:60px; }
.kanban-card { background:var(--surface,#fff);border-radius:10px;border:1px solid var(--border,#E2E8F0);padding:12px;cursor:pointer;transition:box-shadow .15s,transform .1s; }
.kanban-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.1);transform:translateY(-1px); }
.kanban-ghost { opacity:.4;background:#EFF6FF !important;border:2px dashed #2563EB !important; }
.kanban-drag { box-shadow:0 8px 24px rgba(0,0,0,.15);transform:rotate(1.5deg); }
.deal-title { font-weight:600;font-size:13px;margin-bottom:8px;color:var(--text,#1E293B); }
.deal-footer { display:flex;justify-content:space-between;align-items:center;gap:8px; }
.deal-value { font-weight:800;color:#2563EB;font-size:13px;white-space:nowrap; }
.deal-prob-wrap { display:flex;align-items:center;gap:5px;flex:1; }
.prob-bar { flex:1;height:4px;background:#E2E8F0;border-radius:2px;overflow:hidden; }
.prob-fill { height:100%;border-radius:2px;transition:width .3s; }
.prob-text { font-size:10px;color:#94A3B8; }
.deal-date { font-size:11px;color:#64748B;margin-top:6px;display:flex;align-items:center;gap:5px; }
.deal-date.overdue { color:#EF4444; }
.kanban-add { text-align:center;padding:10px;color:#94A3B8;font-size:12px;cursor:pointer;border-radius:8px;border:1.5px dashed #E2E8F0;margin:0 2px 8px;transition:all .15s; }
.kanban-add:hover { color:#2563EB;border-color:#2563EB;background:#EFF6FF; }
.modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;display:flex;align-items:center;justify-content:center;padding:20px; }
.modal-box { background:var(--surface,#fff);border-radius:16px;padding:28px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto; }
.modal-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px; }
.modal-header h3 { font-size:18px;font-weight:700;margin:0; }
.modal-close { background:none;border:none;font-size:24px;cursor:pointer;color:#94A3B8;line-height:1; }
</style>
