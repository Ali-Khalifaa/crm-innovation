<template>
  <CrmLayout>
    <div>
      <!-- Header -->
      <div class="d-flex align-items-center justify-content-between mb-4 gap-3 flex-wrap">
        <div>
          <h4 class="mb-0 fw-bold" style="font-size:1.2rem">{{ t('products_title') }}</h4>
          <p class="text-muted small mt-1 mb-0">{{ t('products_sub') }}</p>
        </div>
        <button class="btn btn-primary btn-sm" @click="openAdd">
          <i class="bi bi-plus-lg me-1"></i>{{ t('add_product') }}
        </button>
      </div>

      <!-- Filters -->
      <div class="d-flex gap-2 mb-3 flex-wrap">
        <input v-model="search" class="form-control form-control-sm" style="max-width:220px" :placeholder="t('search_products')" @input="debouncedSearch" />
        <select class="form-select form-select-sm" style="max-width:140px" v-model="filters.type" @change="fetchProducts">
          <option value="">{{ t('all_types') }}</option>
          <option value="product">{{ t('type_product') }}</option>
          <option value="service">{{ t('type_service') }}</option>
          <option value="subscription">{{ t('type_subscription') }}</option>
        </select>
        <select class="form-select form-select-sm" style="max-width:120px" v-model="filters.is_active" @change="fetchProducts">
          <option value="">{{ t('all') }}</option>
          <option value="true">{{ t('active') }}</option>
          <option value="false">{{ t('inactive') }}</option>
        </select>
      </div>

      <!-- Stats -->
      <div class="row g-3 mb-4">
        <div class="col-sm-4">
          <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4">{{ stats.total }}</div>
            <div class="small text-muted">{{ t('total_products') }}</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4 text-success">{{ stats.active }}</div>
            <div class="small text-muted">{{ t('active_products') }}</div>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="card border-0 shadow-sm text-center py-3">
            <div class="fw-bold fs-4 text-primary">{{ stats.services }}</div>
            <div class="small text-muted">{{ t('services') }}</div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <!-- Product grid -->
      <div v-else class="row g-3">
        <div v-if="!products.length" class="col-12 text-center text-muted py-5">
          <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
          {{ t('no_products') }}
        </div>
        <div v-for="p in products" :key="p.id" class="col-sm-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100" style="border-radius:12px">
            <div class="card-body d-flex flex-column gap-2">
              <div class="d-flex align-items-start justify-content-between">
                <div>
                  <div class="fw-bold" style="font-size:.95rem">{{ p.name }}</div>
                  <div v-if="p.sku" class="text-muted small">SKU: {{ p.sku }}</div>
                </div>
                <span class="badge" :class="typeBadge(p.type)">{{ t('type_' + p.type) }}</span>
              </div>
              <div v-if="p.description" class="text-muted small" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ p.description }}</div>
              <div class="mt-auto d-flex align-items-center justify-content-between">
                <div class="fw-bold text-primary fs-5">${{ Number(p.unit_price).toLocaleString() }}</div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge" :class="p.is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'">
                    {{ p.is_active ? t('active') : t('inactive') }}
                  </span>
                  <button class="btn btn-sm btn-outline-secondary" @click="openEdit(p)"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(p)"><i class="bi bi-trash"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="d-flex justify-content-center mt-4 gap-2">
        <button class="btn btn-sm btn-outline-secondary" :disabled="meta.current_page <= 1" @click="page--; fetchProducts()">‹</button>
        <span class="btn btn-sm btn-outline-secondary disabled">{{ meta.current_page }} / {{ meta.last_page }}</span>
        <button class="btn btn-sm btn-outline-secondary" :disabled="meta.current_page >= meta.last_page" @click="page++; fetchProducts()">›</button>
      </div>

      <!-- Add/Edit Modal -->
      <Teleport to="body">
        <div v-if="showModal" class="modal-backdrop-custom" @click.self="closeModal">
          <div class="modal-card" style="max-width:480px">
            <div class="modal-card-header">
              <h6 class="mb-0 fw-bold">{{ editing ? t('edit_product') : t('add_product') }}</h6>
              <button class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-card-body">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ t('name') }} *</label>
                  <input v-model="form.name" class="form-control form-control-sm" :class="{ 'is-invalid': errors.name }" />
                  <div class="invalid-feedback">{{ errors.name }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('type') }}</label>
                  <select v-model="form.type" class="form-select form-select-sm">
                    <option value="product">{{ t('type_product') }}</option>
                    <option value="service">{{ t('type_service') }}</option>
                    <option value="subscription">{{ t('type_subscription') }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('sku') }}</label>
                  <input v-model="form.sku" class="form-control form-control-sm" />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('unit_price') }} *</label>
                  <input type="number" step="0.01" min="0" v-model="form.unit_price" class="form-control form-control-sm" :class="{ 'is-invalid': errors.unit_price }" />
                  <div class="invalid-feedback">{{ errors.unit_price }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">{{ t('currency') }}</label>
                  <select v-model="form.currency" class="form-select form-select-sm">
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="SAR">SAR</option>
                    <option value="AED">AED</option>
                    <option value="EGP">EGP</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">{{ t('description') }}</label>
                  <textarea v-model="form.description" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="col-12">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" v-model="form.is_active" id="isActiveCheck" />
                    <label class="form-check-label small" for="isActiveCheck">{{ t('active') }}</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-card-footer">
              <button class="btn btn-sm btn-secondary" @click="closeModal">{{ t('cancel') }}</button>
              <button class="btn btn-sm btn-primary" :disabled="saving" @click="save">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                {{ t('save') }}
              </button>
            </div>
          </div>
        </div>
      </Teleport>

      <!-- Delete confirm -->
      <Teleport to="body">
        <div v-if="deleteTarget" class="modal-backdrop-custom" @click.self="deleteTarget = null">
          <div class="modal-card" style="max-width:360px">
            <div class="modal-card-header">
              <h6 class="mb-0 fw-bold text-danger">{{ t('confirm_delete') }}</h6>
              <button class="btn-close" @click="deleteTarget = null"></button>
            </div>
            <div class="modal-card-body"><p class="mb-0">{{ t('delete_product_confirm', deleteTarget.name) }}</p></div>
            <div class="modal-card-footer">
              <button class="btn btn-sm btn-secondary" @click="deleteTarget = null">{{ t('cancel') }}</button>
              <button class="btn btn-sm btn-danger" :disabled="deleting" @click="doDelete">
                <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
                {{ t('delete') }}
              </button>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </CrmLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'

const store = useStore()
const isRtl = computed(() => store.state.ui?.locale === 'ar')

const AR = {
  products_title:'المنتجات والخدمات', products_sub:'كتالوج المنتجات والخدمات', add_product:'إضافة منتج',
  edit_product:'تعديل المنتج', search_products:'بحث في المنتجات...', all_types:'كل الأنواع', all:'الكل',
  total_products:'إجمالي المنتجات', active_products:'المنتجات النشطة', services:'الخدمات',
  no_products:'لا توجد منتجات بعد', name:'الاسم', type:'النوع', sku:'رمز المنتج',
  unit_price:'السعر', currency:'العملة', description:'الوصف', active:'نشط', inactive:'غير نشط',
  type_product:'منتج', type_service:'خدمة', type_subscription:'اشتراك',
  save:'حفظ', cancel:'إلغاء', delete:'حذف', total:'الإجمالي',
  confirm_delete:'تأكيد الحذف', delete_product_confirm:'حذف "{0}"؟',
}
const EN = {
  products_title:'Products & Services', products_sub:'Product and service catalog', add_product:'Add Product',
  edit_product:'Edit Product', search_products:'Search products...', all_types:'All Types', all:'All',
  total_products:'Total Products', active_products:'Active Products', services:'Services',
  no_products:'No products yet', name:'Name', type:'Type', sku:'SKU',
  unit_price:'Unit Price', currency:'Currency', description:'Description', active:'Active', inactive:'Inactive',
  type_product:'Product', type_service:'Service', type_subscription:'Subscription',
  save:'Save', cancel:'Cancel', delete:'Delete', total:'Total',
  confirm_delete:'Confirm Delete', delete_product_confirm:'Delete "{0}"?',
}
function t(key, arg = '') {
  const dict = isRtl.value ? AR : EN
  return (dict[key] ?? key).replace('{0}', arg)
}

const products    = ref([])
const loading     = ref(true)
const page        = ref(1)
const search      = ref('')
const meta        = ref({ current_page: 1, last_page: 1, total: 0 })
const filters     = reactive({ type: '', is_active: '' })
const stats       = reactive({ total: 0, active: 0, services: 0 })

const showModal    = ref(false)
const editing      = ref(null)
const saving       = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)
const errors       = reactive({})

const emptyForm = () => ({ name: '', type: 'product', sku: '', unit_price: 0, currency: 'USD', description: '', is_active: true })
const form = reactive(emptyForm())

let searchTimer = null
function debouncedSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; fetchProducts() }, 400)
}

async function fetchProducts() {
  loading.value = true
  try {
    const params = { page: page.value }
    if (search.value)       params.search    = search.value
    if (filters.type)       params.type      = filters.type
    if (filters.is_active !== '') params.is_active = filters.is_active
    const { data } = await api.get('/products', { params })
    products.value = data.data ?? []
    meta.value     = data.meta ?? {}
    // Compute stats from meta (approximate)
    if (!search.value && !filters.type && filters.is_active === '') {
      stats.total = data.meta?.total ?? 0
    }
  } finally {
    loading.value = false
  }
}

async function fetchStats() {
  try {
    const [all, active, services] = await Promise.all([
      api.get('/products', { params: { per_page: 1 } }),
      api.get('/products', { params: { per_page: 1, is_active: 'true' } }),
      api.get('/products', { params: { per_page: 1, type: 'service' } }),
    ])
    stats.total    = all.data.meta?.total ?? 0
    stats.active   = active.data.meta?.total ?? 0
    stats.services = services.data.meta?.total ?? 0
  } catch {}
}

function openAdd() {
  Object.assign(form, emptyForm())
  Object.keys(errors).forEach(k => delete errors[k])
  editing.value = null
  showModal.value = true
}

function openEdit(p) {
  editing.value = p
  form.name        = p.name
  form.type        = p.type
  form.sku         = p.sku ?? ''
  form.unit_price  = p.unit_price
  form.currency    = p.currency
  form.description = p.description ?? ''
  form.is_active   = p.is_active
  showModal.value  = true
}

function closeModal() { showModal.value = false; editing.value = null }

async function save() {
  Object.keys(errors).forEach(k => delete errors[k])
  if (!form.name.trim()) { errors.name = 'required'; return }
  if (form.unit_price < 0) { errors.unit_price = 'invalid'; return }

  saving.value = true
  try {
    if (editing.value) {
      await api.put(`/products/${editing.value.id}`, { ...form })
    } else {
      await api.post('/products', { ...form })
    }
    closeModal()
    fetchProducts()
    fetchStats()
  } finally {
    saving.value = false
  }
}

function confirmDelete(p) { deleteTarget.value = p }

async function doDelete() {
  deleting.value = true
  try {
    await api.delete(`/products/${deleteTarget.value.id}`)
    deleteTarget.value = null
    fetchProducts()
    fetchStats()
  } finally {
    deleting.value = false
  }
}

function typeBadge(type) {
  return { product:'bg-primary-subtle text-primary', service:'bg-success-subtle text-success', subscription:'bg-warning-subtle text-warning' }[type] ?? 'bg-secondary-subtle text-secondary'
}

onMounted(() => { fetchProducts(); fetchStats() })
</script>

<style scoped>
.modal-backdrop-custom {
  position: fixed; inset: 0; background: rgba(0,0,0,.4);
  z-index: 1055; display: flex; align-items: center; justify-content: center; padding: 16px;
}
.modal-card { background: var(--bs-body-bg, #fff); border-radius: 14px; width: 100%; box-shadow: 0 12px 40px rgba(0,0,0,.18); display: flex; flex-direction: column; max-height: 90vh; }
.modal-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--bs-border-color); }
.modal-card-body { flex: 1; overflow-y: auto; padding: 20px; }
.modal-card-footer { display: flex; gap: 8px; justify-content: flex-end; padding: 14px 20px; border-top: 1px solid var(--bs-border-color); }
</style>
