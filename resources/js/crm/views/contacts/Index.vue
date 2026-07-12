<template>
  <CrmLayout>
    <PlanLimitBanner
      v-if="planMaxContacts > 0"
      :current="meta.total ?? 0"
      :limit="planMaxContacts"
      label="contacts"
      @upgrade="showUpgrade = true"
    />
    <UpgradeModal :show="showUpgrade" @close="showUpgrade = false" />

    <div class="card card-border">
      <div class="card-header card-header-action">
        <h6>
          {{ t('contacts.title') }}
          <span class="badge badge-soft-primary ms-2">{{ meta.total ?? 0 }}</span>
        </h6>
        <div class="card-action-wrap d-flex gap-2">
          <a :href="`/api/crm/contacts/export?token=${token}`" class="btn btn-sm btn-outline-light">
            <i class="bi bi-file-earmark-excel me-1" style="color:#16A34A;"></i>{{ t('contacts.export') }}
          </a>
          <router-link to="/crm/contacts/create" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ t('contacts.new') }}
          </router-link>
        </div>
      </div>

      <div class="card-body pb-0">
        <div class="d-flex gap-2 mb-4 flex-wrap">
          <div class="input-group flex-grow-1" style="max-width:300px;">
            <span class="input-group-text bg-transparent border-end-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input
              v-model="search"
              @input="debouncedFetch"
              type="text"
              class="form-control border-start-0"
              :placeholder="t('contacts.search')"
            />
          </div>
          <select v-model="status" @change="fetchContacts" class="form-select" style="max-width:150px;">
            <option value="">{{ t('contacts.all_status') }}</option>
            <option value="lead">{{ t('contacts.lead') }}</option>
            <option value="customer">{{ t('contacts.customer') }}</option>
            <option value="inactive">{{ t('contacts.inactive') }}</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-content-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- Empty -->
      <div v-else-if="!contacts.length" class="text-center py-5 text-muted">
        <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
        <p class="mb-2">{{ t('contacts.no_contacts') }}</p>
        <router-link to="/crm/contacts/create" class="btn btn-sm btn-primary">
          {{ t('contacts.add_first') }}
        </router-link>
      </div>

      <!-- Table -->
      <div v-else class="card-body pt-0">
        <div class="table-responsive">
          <table class="table table-bordered table-sm nowrap w-100 mb-0">
            <thead>
              <tr>
                <th>{{ t('contacts.name') }}</th>
                <th>{{ t('contacts.email') }}</th>
                <th>{{ t('contacts.phone') }}</th>
                <th>{{ t('contacts.company') }}</th>
                <th>{{ t('contacts.status') }}</th>
                <th class="text-end">{{ t('contacts.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="c in contacts" :key="c.id">
                <td>
                  <div class="media align-items-center">
                    <div class="media-head me-2">
                      <div class="avatar avatar-xs avatar-soft-primary avatar-rounded">
                        <span class="initial-wrap">{{ (c.first_name || '?').charAt(0) }}</span>
                      </div>
                    </div>
                    <div class="media-body">
                      <router-link :to="`/crm/contacts/${c.id}`" class="fw-semibold text-primary text-decoration-none link-dark">
                        {{ c.first_name }} {{ c.last_name }}
                      </router-link>
                    </div>
                  </div>
                </td>
                <td>{{ c.email || '—' }}</td>
                <td>{{ c.phone || '—' }}</td>
                <td>{{ c.company || '—' }}</td>
                <td>
                  <span class="badge" :class="{
                    'badge-soft-primary':   c.status === 'lead',
                    'badge-soft-success':   c.status === 'customer',
                    'badge-soft-secondary': c.status === 'inactive',
                  }">{{ statusLabel(c.status) }}</span>
                </td>
                <td class="text-end">
                  <router-link :to="`/crm/contacts/${c.id}/edit`" class="btn btn-xs btn-outline-light me-1" :title="t('contacts.edit')">
                    <i class="bi bi-pencil"></i>
                  </router-link>
                  <button @click="deleteContact(c)" class="btn btn-xs btn-soft-danger" :title="t('contacts.delete')">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="d-flex justify-content-center gap-1 mt-3 pb-2">
          <button
            v-for="p in meta.last_page" :key="p"
            @click="page = p; fetchContacts()"
            class="btn btn-xs"
            :class="p === page ? 'btn-primary' : 'btn-outline-light'"
          >{{ p }}</button>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { trans } from 'laravel-vue-i18n'
import CrmLayout from '../../layouts/CrmLayout.vue'
import PlanLimitBanner from '../../components/PlanLimitBanner.vue'
import UpgradeModal from '../../components/UpgradeModal.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store = useStore()
const toast = useToast()
const locale = computed(() => store.state.ui.locale)

function t(key) { return trans(key) }

function statusLabel(s) {
  const map = { lead: t('contacts.lead'), customer: t('contacts.customer'), inactive: t('contacts.inactive') }
  return map[s] || s
}

const showUpgrade     = ref(false)
const planMaxContacts = computed(() => store.getters['auth/plan']?.max_contacts ?? 0)
const token    = computed(() => store.state.auth.token)
const contacts = ref([])
const meta     = ref({})
const loading  = ref(true)
const search   = ref('')
const status   = ref('')
const page     = ref(1)
let searchTimeout = null

async function fetchContacts() {
  loading.value = true
  try {
    const { data } = await api.get('/contacts', {
      params: { search: search.value, status: status.value, page: page.value }
    })
    contacts.value = data.data
    meta.value     = data.meta
  } catch { toast.error(t('common.error')) }
  loading.value = false
}

function debouncedFetch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(fetchContacts, 400)
}

async function deleteContact(c) {
  const name = `${c.first_name} ${c.last_name}`
  const msg  = locale.value === 'ar' ? `هل تريد حذف ${name}؟` : `Delete ${name}?`
  if (!confirm(msg)) return
  try {
    await api.delete(`/contacts/${c.id}`)
    toast.success(t('common.success'))
    fetchContacts()
  } catch { toast.error(t('common.error')) }
}

onMounted(fetchContacts)
</script>
