<template>
  <!-- Backdrop -->
  <Teleport to="body">
    <Transition name="search-fade">
      <div v-if="open" class="gs-backdrop" @click.self="close">
        <div class="gs-modal" role="dialog" aria-label="Global Search">

          <!-- Input -->
          <div class="gs-input-wrap">
            <i class="bi bi-search gs-search-icon"></i>
            <input
              ref="inputRef"
              v-model="query"
              type="text"
              class="gs-input"
              :placeholder="isRtl ? 'ابحث في جهات الاتصال، الصفقات، الشركات...' : 'Search contacts, deals, companies...'"
              @input="onInput"
              @keydown.down.prevent="moveDown"
              @keydown.up.prevent="moveUp"
              @keydown.enter.prevent="selectHighlighted"
              @keydown.escape="close"
            />
            <button v-if="query" class="gs-clear-btn" @click="query = ''; results = null; focused = -1">
              <i class="bi bi-x"></i>
            </button>
            <kbd class="gs-esc-badge" @click="close">ESC</kbd>
          </div>

          <!-- Results -->
          <div class="gs-results" v-if="query.length >= 2">

            <!-- Loading -->
            <div v-if="searching" class="gs-loading">
              <span class="spinner-border spinner-border-sm text-primary me-2"></span>
              {{ isRtl ? 'جاري البحث...' : 'Searching...' }}
            </div>

            <template v-else-if="results">
              <div v-if="allItems.length === 0" class="gs-empty">
                <i class="bi bi-search gs-empty-icon"></i>
                <span>{{ isRtl ? `لا نتائج لـ "${query}"` : `No results for "${query}"` }}</span>
              </div>

              <template v-else>
                <!-- Contacts group -->
                <div v-if="results.contacts?.length" class="gs-group">
                  <div class="gs-group-title"><i class="bi bi-person-fill me-1"></i>{{ isRtl ? 'جهات الاتصال' : 'Contacts' }}</div>
                  <button
                    v-for="(item, i) in results.contacts"
                    :key="`c-${item.id}`"
                    class="gs-item"
                    :class="{ 'gs-item-focused': flatIndex(i, 'contacts') === focused }"
                    @click="navigate(item)"
                    @mouseenter="focused = flatIndex(i, 'contacts')"
                  >
                    <div class="gs-item-icon" :style="{ background: strToColor(item.label) + '22', color: strToColor(item.label) }">
                      <i class="bi bi-person"></i>
                    </div>
                    <div class="gs-item-body">
                      <div class="gs-item-label" v-html="highlight(item.label)"></div>
                      <div class="gs-item-sub">{{ item.sub }}</div>
                    </div>
                    <span class="gs-item-badge contact-badge">{{ isRtl ? 'جهة اتصال' : 'Contact' }}</span>
                  </button>
                </div>

                <!-- Deals group -->
                <div v-if="results.deals?.length" class="gs-group">
                  <div class="gs-group-title"><i class="bi bi-briefcase-fill me-1"></i>{{ isRtl ? 'الصفقات' : 'Deals' }}</div>
                  <button
                    v-for="(item, i) in results.deals"
                    :key="`d-${item.id}`"
                    class="gs-item"
                    :class="{ 'gs-item-focused': flatIndex(i, 'deals') === focused }"
                    @click="navigate(item)"
                    @mouseenter="focused = flatIndex(i, 'deals')"
                  >
                    <div class="gs-item-icon" style="background:rgba(37,99,235,.1);color:#2563EB">
                      <i class="bi bi-briefcase"></i>
                    </div>
                    <div class="gs-item-body">
                      <div class="gs-item-label" v-html="highlight(item.label)"></div>
                      <div class="gs-item-sub">{{ item.sub }}</div>
                    </div>
                    <span class="gs-item-badge deal-badge">{{ isRtl ? 'صفقة' : 'Deal' }}</span>
                  </button>
                </div>

                <!-- Companies group -->
                <div v-if="results.companies?.length" class="gs-group">
                  <div class="gs-group-title"><i class="bi bi-building-fill me-1"></i>{{ isRtl ? 'الشركات' : 'Companies' }}</div>
                  <button
                    v-for="(item, i) in results.companies"
                    :key="`co-${item.id}`"
                    class="gs-item"
                    :class="{ 'gs-item-focused': flatIndex(i, 'companies') === focused }"
                    @click="navigate(item)"
                    @mouseenter="focused = flatIndex(i, 'companies')"
                  >
                    <div class="gs-item-icon" style="background:rgba(124,58,237,.1);color:#7C3AED">
                      <i class="bi bi-building"></i>
                    </div>
                    <div class="gs-item-body">
                      <div class="gs-item-label" v-html="highlight(item.label)"></div>
                      <div class="gs-item-sub">{{ item.sub }}</div>
                    </div>
                    <span class="gs-item-badge company-badge">{{ isRtl ? 'شركة' : 'Company' }}</span>
                  </button>
                </div>
              </template>
            </template>
          </div>

          <!-- Footer hints -->
          <div class="gs-footer">
            <div class="gs-hint"><kbd>↑↓</kbd> {{ isRtl ? 'للتنقل' : 'to navigate' }}</div>
            <div class="gs-hint"><kbd>↵</kbd> {{ isRtl ? 'للفتح' : 'to open' }}</div>
            <div class="gs-hint"><kbd>ESC</kbd> {{ isRtl ? 'للإغلاق' : 'to close' }}</div>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import api from '../composables/useApi.js'

const store  = useStore()
const router = useRouter()
const isRtl  = computed(() => store.state.ui?.locale === 'ar')

const open      = ref(false)
const query     = ref('')
const results   = ref(null)
const searching = ref(false)
const focused   = ref(-1)
const inputRef  = ref(null)
let debounceTimer = null

// ── Open / Close ──────────────────────────────────────────
function openSearch() {
  open.value   = true
  query.value  = ''
  results.value = null
  focused.value = -1
  nextTick(() => inputRef.value?.focus())
}
function close() {
  open.value = false
}

// ── Keyboard shortcut (Ctrl+K / Cmd+K) ───────────────────
function onKeydown(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault()
    open.value ? close() : openSearch()
  }
}
onMounted(()  => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))

// Expose open method for external triggering
defineExpose({ openSearch })

// ── Search ────────────────────────────────────────────────
function onInput() {
  focused.value = -1
  clearTimeout(debounceTimer)
  if (query.value.length < 2) { results.value = null; return }
  searching.value = true
  debounceTimer = setTimeout(doSearch, 300)
}

async function doSearch() {
  try {
    const { data } = await api.get('/search', { params: { q: query.value } })
    results.value = data.data
  } catch {
    results.value = { contacts: [], deals: [], companies: [] }
  } finally {
    searching.value = false
  }
}

// ── Flat list for keyboard nav ────────────────────────────
const allItems = computed(() => {
  if (!results.value) return []
  return [
    ...(results.value.contacts  || []),
    ...(results.value.deals     || []),
    ...(results.value.companies || []),
  ]
})

function flatIndex(i, type) {
  if (!results.value) return -1
  const offsets = { contacts: 0, deals: (results.value.contacts?.length||0), companies: (results.value.contacts?.length||0)+(results.value.deals?.length||0) }
  return offsets[type] + i
}

function moveDown() {
  if (allItems.value.length === 0) return
  focused.value = (focused.value + 1) % allItems.value.length
}
function moveUp() {
  if (allItems.value.length === 0) return
  focused.value = (focused.value - 1 + allItems.value.length) % allItems.value.length
}
function selectHighlighted() {
  if (focused.value >= 0 && allItems.value[focused.value]) {
    navigate(allItems.value[focused.value])
  }
}

// ── Navigate ──────────────────────────────────────────────
function navigate(item) {
  close()
  router.push(item.url)
}

// ── Highlight matching text ───────────────────────────────
function highlight(text) {
  if (!query.value || !text) return text
  const escaped = query.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  return String(text).replace(new RegExp(`(${escaped})`, 'gi'), '<mark class="gs-mark">$1</mark>')
}

// ── Avatar color ──────────────────────────────────────────
const COLORS = ['#2563EB','#7C3AED','#059669','#D97706','#0891B2','#DB2777']
function strToColor(name = '') {
  const sum = String(name).split('').reduce((a, c) => a + c.charCodeAt(0), 0)
  return COLORS[sum % COLORS.length]
}
</script>

<style scoped>
/* ─── Backdrop ──────────────────────────────────── */
.gs-backdrop {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0, 0, 0, .45);
  backdrop-filter: blur(4px);
  display: flex; align-items: flex-start; justify-content: center;
  padding-top: 80px;
  padding-inline: 16px;
}

/* ─── Modal ─────────────────────────────────────── */
.gs-modal {
  background: var(--bs-body-bg, #fff);
  border-radius: 16px;
  box-shadow: 0 24px 64px rgba(0,0,0,.22), 0 0 0 1px rgba(0,0,0,.06);
  width: 100%; max-width: 600px;
  overflow: hidden;
  display: flex; flex-direction: column;
  max-height: calc(100vh - 120px);
}

/* ─── Input ─────────────────────────────────────── */
.gs-input-wrap {
  display: flex; align-items: center; gap: 10px;
  padding: 14px 18px;
  border-bottom: 1px solid var(--bs-border-color, #E2E8F0);
  flex-shrink: 0;
}
.gs-search-icon { font-size: 1.1rem; color: var(--bs-secondary-color, #64748B); flex-shrink: 0; }
.gs-input {
  flex: 1; border: none; outline: none;
  background: transparent;
  font-size: 15px; font-weight: 500;
  color: var(--bs-body-color);
  min-width: 0;
}
.gs-input::placeholder { color: var(--bs-secondary-color, #94A3B8); }
.gs-clear-btn {
  background: none; border: none; cursor: pointer; padding: 2px 6px;
  color: var(--bs-secondary-color); border-radius: 6px; font-size: 1rem;
  line-height: 1; flex-shrink: 0;
}
.gs-clear-btn:hover { background: var(--bs-tertiary-bg); }
.gs-esc-badge {
  font-size: 11px; font-family: inherit;
  background: var(--bs-tertiary-bg, #F1F5F9);
  color: var(--bs-secondary-color);
  border: 1px solid var(--bs-border-color);
  border-radius: 6px; padding: 2px 7px;
  cursor: pointer; flex-shrink: 0;
}

/* ─── Results ───────────────────────────────────── */
.gs-results {
  overflow-y: auto; flex: 1;
  padding: 8px 0;
}
.gs-loading {
  display: flex; align-items: center; justify-content: center;
  padding: 24px; color: var(--bs-secondary-color); font-size: 13px;
}
.gs-empty {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 32px; gap: 10px; color: var(--bs-secondary-color); font-size: 13px;
}
.gs-empty-icon { font-size: 2rem; opacity: .25; }

/* ─── Group ─────────────────────────────────────── */
.gs-group { padding: 0 8px 4px; }
.gs-group-title {
  font-size: 10.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .6px;
  color: var(--bs-secondary-color);
  padding: 8px 10px 4px;
}

/* ─── Item ──────────────────────────────────────── */
.gs-item {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 10px;
  border-radius: 10px; border: none; width: 100%;
  background: transparent; cursor: pointer; text-align: start;
  transition: background .1s;
}
.gs-item:hover, .gs-item-focused {
  background: var(--bs-tertiary-bg, rgba(37,99,235,.05));
}
.gs-item-icon {
  width: 34px; height: 34px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; flex-shrink: 0;
}
.gs-item-body { flex: 1; min-width: 0; text-align: start; }
.gs-item-label { font-size: 13px; font-weight: 600; color: var(--bs-body-color); }
.gs-item-sub   { font-size: 11.5px; color: var(--bs-secondary-color); margin-top: 1px; }

.gs-item-badge {
  font-size: 10.5px; font-weight: 600;
  padding: 2px 8px; border-radius: 20px; flex-shrink: 0;
}
.contact-badge { background: rgba(16,185,129,.12);  color: #059669; }
.deal-badge    { background: rgba(37,99,235,.12);   color: #2563EB; }
.company-badge { background: rgba(124,58,237,.12);  color: #7C3AED; }

/* ─── Footer ─────────────────────────────────────── */
.gs-footer {
  display: flex; align-items: center; gap: 16px;
  padding: 10px 18px;
  border-top: 1px solid var(--bs-border-color, #E2E8F0);
  flex-shrink: 0;
}
.gs-hint { font-size: 11px; color: var(--bs-secondary-color); display: flex; align-items: center; gap: 4px; }
.gs-hint kbd {
  font-size: 10px; background: var(--bs-tertiary-bg, #F1F5F9);
  border: 1px solid var(--bs-border-color); border-radius: 4px;
  padding: 1px 5px; font-family: inherit;
}

/* ─── Mark (highlight) ───────────────────────────── */
:deep(.gs-mark) {
  background: rgba(37,99,235,.18);
  color: #1D4ED8;
  border-radius: 2px;
  padding: 0 1px;
}

/* ─── Transition ─────────────────────────────────── */
.search-fade-enter-active { transition: opacity .18s ease, transform .18s ease; }
.search-fade-leave-active { transition: opacity .15s ease, transform .15s ease; }
.search-fade-enter-from   { opacity: 0; }
.search-fade-leave-to     { opacity: 0; }
.search-fade-enter-from .gs-modal { transform: translateY(-12px) scale(.97); }
.search-fade-leave-to   .gs-modal { transform: translateY(-8px)  scale(.98); }
</style>
