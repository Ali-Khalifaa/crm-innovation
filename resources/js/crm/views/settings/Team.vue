<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale==='ar' ? 'إعدادات الفريق' : 'Team Settings' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale==='ar' ? 'إدارة أعضاء فريق العمل وصلاحياتهم' : 'Manage team members and their permissions' }}</p>
      </div>
      <button v-if="isOwner || isAdmin" class="btn btn-sm btn-primary flex-shrink-0" @click="openInvite">
        <i class="bi bi-person-plus me-1"></i>{{ locale==='ar' ? 'دعوة عضو' : 'Invite Member' }}
      </button>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3" v-for="s in teamStats" :key="s.key">
        <div class="card card-border team-stat">
          <div class="card-body py-3">
            <div class="d-flex align-items-center gap-3">
              <div class="ts-icon" :style="{background:s.bg,color:s.color}"><i :class="s.icon"></i></div>
              <div>
                <div class="ts-num">{{ s.value }}</div>
                <div class="ts-label">{{ locale==='ar' ? s.ar : s.en }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Members Card -->
    <div class="card card-border">
      <div class="card-header card-header-action flex-wrap gap-2">
        <h6 class="mb-0 d-flex align-items-center gap-2">
          <span>{{ locale==='ar' ? 'أعضاء الفريق' : 'Team Members' }}</span>
          <span class="badge badge-soft-primary">{{ users.length }}</span>
        </h6>
        <div class="input-group" style="width:220px">
          <span class="input-group-text bg-transparent border-end-0 ps-2">
            <i class="bi bi-search text-muted" style="font-size:13px"></i>
          </span>
          <input v-model="search" type="text" class="form-control border-start-0 ps-1"
            :placeholder="locale==='ar' ? 'بحث...' : 'Search...'" />
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:220px">
        <div class="text-center">
          <div class="spinner-border text-primary mb-2" role="status"></div>
          <div class="text-muted small">{{ locale==='ar' ? 'جاري التحميل...' : 'Loading...' }}</div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="!filteredUsers.length" class="text-center py-5 px-4">
        <div class="empty-icon mb-3"><i class="bi bi-people"></i></div>
        <h6 class="mb-1">{{ search ? (locale==='ar'?'لا توجد نتائج':'No results found') : (locale==='ar'?'لا يوجد أعضاء بعد':'No team members yet') }}</h6>
        <p class="text-muted small mb-3">{{ locale==='ar'?'ادعُ أعضاء فريقك للبدء':'Invite your team members to get started' }}</p>
        <button v-if="isOwner || isAdmin" class="btn btn-sm btn-primary" @click="openInvite">
          <i class="bi bi-person-plus me-1"></i>{{ locale==='ar'?'دعوة عضو':'Invite Member' }}
        </button>
      </div>

      <!-- Table -->
      <div v-else>
        <div class="table-responsive">
          <table class="table team-table mb-0">
            <thead>
              <tr>
                <th class="ps-4">{{ locale==='ar'?'العضو':'Member' }}</th>
                <th class="d-none d-md-table-cell">{{ locale==='ar'?'البريد الإلكتروني':'Email' }}</th>
                <th>{{ locale==='ar'?'الدور':'Role' }}</th>
                <th>{{ locale==='ar'?'الحالة':'Status' }}</th>
                <th v-if="isOwner" style="width:50px"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in filteredUsers" :key="u.id" class="team-row">
                <td class="ps-4">
                  <div class="d-flex align-items-center gap-3">
                    <div class="member-av" :style="{ background: avatarColor(u.name) }">
                      {{ u.name?.charAt(0)?.toUpperCase() || '?' }}
                    </div>
                    <div>
                      <div class="member-name">{{ u.name }}</div>
                      <div class="member-sub d-md-none">{{ u.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="d-none d-md-table-cell">
                  <span class="text-muted" style="font-size:13px">{{ u.email }}</span>
                </td>
                <td>
                  <span class="role-badge" :class="`role-${u.role}`">
                    <i class="bi" :class="roleIcon(u.role)" style="font-size:9px"></i>
                    {{ roleLabel(u.role) }}
                  </span>
                </td>
                <td>
                  <span class="status-dot-wrap">
                    <span class="status-dot" :class="u.status==='active'?'dot-active':'dot-inactive'"></span>
                    <span style="font-size:12px">{{ u.status==='active' ? (locale==='ar'?'نشط':'Active') : (locale==='ar'?'غير نشط':'Inactive') }}</span>
                  </span>
                </td>
                <td v-if="isOwner" class="text-end pe-3">
                  <button v-if="u.id !== currentUser?.id" class="btn btn-xs btn-outline-danger" @click="confirmRemove(u)" :title="locale==='ar'?'إزالة':'Remove'">
                    <i class="bi bi-trash3"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ═══ Invite Modal ═══ -->
    <div class="modal fade" id="inviteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title fw-bold">{{ locale==='ar' ? 'دعوة عضو جديد' : 'Invite Team Member' }}</h5>
              <p class="text-muted small mb-0 mt-1">{{ locale==='ar' ? 'أضف عضواً جديداً لفريق العمل' : 'Add a new member to your team' }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="inviteError" class="alert alert-danger py-2 px-3 small mb-3">{{ inviteError }}</div>
            <form @submit.prevent="invite" id="inviteForm">
              <div class="mb-3">
                <label class="form-label fw-semibold">{{ locale==='ar'?'الاسم الكامل':'Full Name' }} <span class="text-danger">*</span></label>
                <input v-model="form.name" class="form-control" required :placeholder="locale==='ar'?'محمد أحمد':'John Smith'" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">{{ locale==='ar'?'البريد الإلكتروني':'Email' }} <span class="text-danger">*</span></label>
                <input v-model="form.email" type="email" class="form-control" required :placeholder="locale==='ar'?'john@company.com':'john@company.com'" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">{{ locale==='ar'?'كلمة المرور':'Password' }} <span class="text-danger">*</span></label>
                <input v-model="form.password" type="password" class="form-control" required minlength="8" :placeholder="locale==='ar'?'8 أحرف على الأقل':'Min 8 characters'" />
              </div>
              <div class="mb-1">
                <label class="form-label fw-semibold">{{ locale==='ar'?'الدور':'Role' }}</label>
                <div class="role-select-wrap">
                  <div class="role-option" :class="{active: form.role==='admin'}" @click="form.role='admin'">
                    <div class="role-opt-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-shield-half"></i></div>
                    <div>
                      <div class="role-opt-title">{{ locale==='ar'?'مدير':'Admin' }}</div>
                      <div class="role-opt-desc">{{ locale==='ar'?'إدارة كاملة للبيانات':'Full data management' }}</div>
                    </div>
                    <i v-if="form.role==='admin'" class="bi bi-check-circle-fill ms-auto text-primary"></i>
                  </div>
                  <div class="role-option" :class="{active: form.role==='member'}" @click="form.role='member'">
                    <div class="role-opt-icon" style="background:rgba(100,116,139,.1);color:#64748B"><i class="bi bi-person"></i></div>
                    <div>
                      <div class="role-opt-title">{{ locale==='ar'?'عضو':'Member' }}</div>
                      <div class="role-opt-desc">{{ locale==='ar'?'عرض وإنشاء فقط':'View and create only' }}</div>
                    </div>
                    <i v-if="form.role==='member'" class="bi bi-check-circle-fill ms-auto text-primary"></i>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="submit" form="inviteForm" class="btn btn-primary" :disabled="inviting">
              <span v-if="inviting" class="spinner-border spinner-border-sm me-2"></span>
              {{ inviting ? (locale==='ar'?'جاري الإضافة...':'Adding...') : (locale==='ar'?'إضافة العضو':'Add Member') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Remove Confirm Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <div class="remove-icon mb-3"><i class="bi bi-person-x-fill"></i></div>
            <h6 class="fw-bold mb-1">{{ locale==='ar'?'إزالة العضو؟':'Remove Member?' }}</h6>
            <p class="text-muted small mb-4">
              {{ locale==='ar' ? `سيتم إزالة ${removeTarget?.name} من الفريق` : `${removeTarget?.name} will be removed from your team` }}
            </p>
            <div class="d-flex gap-2 justify-content-center">
              <button class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
              <button class="btn btn-danger btn-sm" @click="removeUser" :disabled="removing">
                <span v-if="removing" class="spinner-border spinner-border-sm me-1"></span>
                {{ locale==='ar'?'إزالة':'Remove' }}
              </button>
            </div>
          </div>
        </div>
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
const locale = computed(() => store.state.ui.locale)

const users        = ref([])
const loading      = ref(true)
const inviting     = ref(false)
const removing     = ref(false)
const search       = ref('')
const inviteError  = ref('')
const removeTarget = ref(null)

const form = ref({ name:'', email:'', password:'', role:'member' })

const currentUser = computed(() => store.getters['auth/user'])
const isOwner     = computed(() => currentUser.value?.role === 'owner')
const isAdmin     = computed(() => currentUser.value?.role === 'admin')

const filteredUsers = computed(() => {
  if (!search.value) return users.value
  const q = search.value.toLowerCase()
  return users.value.filter(u => u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q))
})

const teamStats = computed(() => [
  { key:'total',  ar:'إجمالي الأعضاء', en:'Total Members',  value: users.value.length,                              icon:'bi bi-people-fill',    bg:'rgba(37,99,235,.1)',   color:'#2563EB' },
  { key:'active', ar:'نشط',             en:'Active',          value: users.value.filter(u=>u.status==='active').length, icon:'bi bi-check-circle-fill', bg:'rgba(16,185,129,.1)', color:'#059669' },
  { key:'admins', ar:'مديرون',          en:'Admins',          value: users.value.filter(u=>u.role==='admin').length,  icon:'bi bi-shield-half',    bg:'rgba(124,58,237,.1)', color:'#7C3AED' },
  { key:'owners', ar:'مالكون',          en:'Owners',          value: users.value.filter(u=>u.role==='owner').length,  icon:'bi bi-star-fill',      bg:'rgba(245,158,11,.1)', color:'#D97706' },
])

function openInvite() {
  form.value = { name:'', email:'', password:'', role:'member' }
  inviteError.value = ''
  window.bootstrap.Modal.getOrCreateInstance(document.getElementById('inviteModal')).show()
}

function confirmRemove(user) {
  removeTarget.value = user
  window.bootstrap.Modal.getOrCreateInstance(document.getElementById('removeModal')).show()
}

async function invite() {
  inviting.value = true; inviteError.value = ''
  try {
    await api.post('/settings/users', form.value)
    toast.success(locale.value==='ar' ? `تمت إضافة ${form.value.name} للفريق` : `${form.value.name} added to your team`)
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('inviteModal')).hide()
    fetchUsers()
  } catch (err) {
    inviteError.value = err.response?.data?.message || (locale.value==='ar' ? 'فشل الإضافة' : 'Failed to add member')
    toast.error(inviteError.value)
  } finally { inviting.value = false }
}

async function removeUser() {
  if (!removeTarget.value) return
  removing.value = true
  try {
    await api.delete(`/settings/users/${removeTarget.value.id}`)
    users.value = users.value.filter(u => u.id !== removeTarget.value.id)
    toast.success(locale.value==='ar' ? 'تم إزالة العضو' : 'Member removed')
    window.bootstrap.Modal.getOrCreateInstance(document.getElementById('removeModal')).hide()
  } catch (err) {
    toast.error(err.response?.data?.message || (locale.value==='ar'?'فشل الحذف':'Failed to remove'))
  } finally { removing.value = false; removeTarget.value = null }
}

async function fetchUsers() {
  loading.value = true
  try {
    const { data } = await api.get('/settings/users')
    users.value = data.data
  } catch { toast.error(locale.value==='ar' ? 'فشل تحميل الفريق' : 'Failed to load team') }
  loading.value = false
}

function roleLabel(r) {
  return { owner: locale.value==='ar'?'مالك':'Owner', admin: locale.value==='ar'?'مدير':'Admin', member: locale.value==='ar'?'عضو':'Member' }[r] || r
}
function roleIcon(r) {
  return { owner:'bi-star-fill', admin:'bi-shield-half', member:'bi-person-fill' }[r] || 'bi-person-fill'
}

const AVATAR_COLORS = ['#2563EB','#7C3AED','#059669','#D97706','#DC2626','#0891B2','#DB2777']
function avatarColor(name) { return AVATAR_COLORS[(name?.charCodeAt(0) || 0) % AVATAR_COLORS.length] }

onMounted(fetchUsers)
</script>

<style scoped>
/* ─── Team Stats ─────────────────────────────── */
.team-stat { transition:all .15s; }
.team-stat:hover { transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08); }
.ts-icon  { width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0; }
.ts-num   { font-size:1.4rem;font-weight:800;line-height:1.2;color:var(--bs-body-color); }
.ts-label { font-size:11px;color:var(--bs-secondary-color);font-weight:500;margin-top:2px; }

/* ─── Table ──────────────────────────────────── */
.team-table thead th { background:var(--bs-tertiary-bg,#F8FAFC);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-secondary-color);padding:.75rem 1rem;border-bottom:1px solid var(--bs-border-color);white-space:nowrap; }
.team-row { transition:background .1s; }
.team-row:hover { background:rgba(37,99,235,.03); }
.team-table td { padding:.85rem 1rem;vertical-align:middle;border-bottom:1px solid var(--bs-border-color); }
.team-table tbody tr:last-child td { border-bottom:none; }

/* ─── Member ─────────────────────────────────── */
.member-av   { width:36px;height:36px;border-radius:10px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;flex-shrink:0; }
.member-name { font-size:13px;font-weight:600;color:var(--bs-body-color); }
.member-sub  { font-size:11px;color:var(--bs-secondary-color);margin-top:1px; }

/* ─── Role badge ─────────────────────────────── */
.role-badge { display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.2px; }
.role-owner  { background:rgba(245,158,11,.12);color:#D97706; }
.role-admin  { background:rgba(37,99,235,.1);color:#2563EB; }
.role-member { background:rgba(100,116,139,.1);color:#64748B; }

/* ─── Status dot ─────────────────────────────── */
.status-dot-wrap { display:flex;align-items:center;gap:6px; }
.status-dot      { width:7px;height:7px;border-radius:50%; }
.dot-active   { background:#10B981;box-shadow:0 0 0 3px rgba(16,185,129,.2); }
.dot-inactive { background:#94A3B8; }

/* ─── Empty ──────────────────────────────────── */
.empty-icon { width:80px;height:80px;border-radius:20px;background:var(--bs-tertiary-bg,#f8f9fa);display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--bs-secondary-color);margin:0 auto; }

/* ─── Remove icon ────────────────────────────── */
.remove-icon { width:64px;height:64px;border-radius:16px;background:rgba(239,68,68,.1);color:#DC2626;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto; }

/* ─── Role select (invite modal) ─────────────── */
.role-select-wrap { display:flex;flex-direction:column;gap:.5rem; }
.role-option { display:flex;align-items:center;gap:.75rem;padding:.75rem;border-radius:10px;border:1.5px solid var(--bs-border-color);cursor:pointer;transition:all .15s; }
.role-option:hover { border-color:#2563EB; }
.role-option.active { border-color:#2563EB;background:rgba(37,99,235,.04); }
.role-opt-icon  { width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0; }
.role-opt-title { font-size:13px;font-weight:600;color:var(--bs-body-color); }
.role-opt-desc  { font-size:11px;color:var(--bs-secondary-color);margin-top:1px; }

[data-bs-theme="dark"] .team-table thead th { background:var(--bs-dark-bg-subtle); }
[data-bs-theme="dark"] .role-option { border-color:var(--bs-border-color); }
</style>
