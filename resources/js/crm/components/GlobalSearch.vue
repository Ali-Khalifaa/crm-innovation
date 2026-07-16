<template>
  <Teleport to="body">
    <Transition name="gs-fade">
      <div v-if="open" class="gs-backdrop" @click.self="close">
        <div class="gs-shell" role="dialog" aria-label="Global Search">

          <!-- ── Search Input ── -->
          <div class="gs-head">
            <div class="gs-input-row">
              <svg class="gs-lens" viewBox="0 0 20 20" fill="none">
                <circle cx="8.5" cy="8.5" r="5.75" stroke="currentColor" stroke-width="1.6"/>
                <path d="M13 13l3.5 3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              </svg>
              <input
                ref="inputRef"
                v-model="query"
                class="gs-input"
                type="text"
                autocomplete="off"
                spellcheck="false"
                :placeholder="isRtl ? 'ابحث في جهات الاتصال، الصفقات، الشركات...' : 'Search contacts, deals, companies...'"
                @input="onInput"
                @keydown.down.prevent="moveDown"
                @keydown.up.prevent="moveUp"
                @keydown.enter.prevent="selectHighlighted"
                @keydown.escape="close"
              />
              <button v-if="query" class="gs-clear" @click="query=''; results=null; focused=-1" aria-label="Clear">
                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M4.47 4.47a.75.75 0 0 1 1.06 0L8 6.94l2.47-2.47a.75.75 0 0 1 1.06 1.06L9.06 8l2.47 2.47a.75.75 0 1 1-1.06 1.06L8 9.06 5.53 11.53a.75.75 0 0 1-1.06-1.06L6.94 8 4.47 5.53a.75.75 0 0 1 0-1.06z"/></svg>
              </button>
              <kbd class="gs-esc" @click="close">esc</kbd>
            </div>
          </div>

          <!-- ── Body ── -->
          <div class="gs-body">

            <!-- Empty query: quick links -->
            <div v-if="query.length < 2 && !results" class="gs-quicklinks">
              <p class="gs-ql-label">{{ isRtl ? 'وصول سريع' : 'Quick Access' }}</p>
              <div class="gs-ql-grid">
                <button class="gs-ql-btn" @click="go('/crm/contacts')">
                  <span class="gs-ql-icon" style="background:rgba(16,185,129,.12);color:#059669"><i class="bi bi-people-fill"></i></span>
                  <span>{{ isRtl ? 'جهات الاتصال' : 'Contacts' }}</span>
                </button>
                <button class="gs-ql-btn" @click="go('/crm/deals')">
                  <span class="gs-ql-icon" style="background:rgba(37,99,235,.12);color:#2563EB"><i class="bi bi-briefcase-fill"></i></span>
                  <span>{{ isRtl ? 'الصفقات' : 'Deals' }}</span>
                </button>
                <button class="gs-ql-btn" @click="go('/crm/companies')">
                  <span class="gs-ql-icon" style="background:rgba(124,58,237,.12);color:#7C3AED"><i class="bi bi-building-fill"></i></span>
                  <span>{{ isRtl ? 'الشركات' : 'Companies' }}</span>
                </button>
                <button class="gs-ql-btn" @click="go('/crm/invoices')">
                  <span class="gs-ql-icon" style="background:rgba(245,158,11,.12);color:#D97706"><i class="bi bi-receipt"></i></span>
                  <span>{{ isRtl ? 'الفواتير' : 'Invoices' }}</span>
                </button>
                <button class="gs-ql-btn" @click="go('/crm/tasks')">
                  <span class="gs-ql-icon" style="background:rgba(239,68,68,.12);color:#DC2626"><i class="bi bi-check2-square"></i></span>
                  <span>{{ isRtl ? 'المهام' : 'Tasks' }}</span>
                </button>
                <button class="gs-ql-btn" @click="go('/crm/reports')">
                  <span class="gs-ql-icon" style="background:rgba(8,145,178,.12);color:#0891B2"><i class="bi bi-bar-chart-fill"></i></span>
                  <span>{{ isRtl ? 'التقارير' : 'Reports' }}</span>
                </button>
              </div>
            </div>

            <!-- Searching spinner -->
            <div v-else-if="searching" class="gs-state">
              <div class="gs-spinner"></div>
              <span>{{ isRtl ? 'جاري البحث...' : 'Searching…' }}</span>
            </div>

            <!-- No results -->
            <div v-else-if="results && allItems.length === 0" class="gs-state gs-empty">
              <svg viewBox="0 0 48 48" fill="none" class="gs-empty-svg">
                <circle cx="21" cy="21" r="14" stroke="currentColor" stroke-width="2.5" stroke-dasharray="4 3"/>
                <path d="M31 31l9 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
              </svg>
              <p class="gs-empty-title">{{ isRtl ? 'لا توجد نتائج' : 'No results found' }}</p>
              <p class="gs-empty-sub">{{ isRtl ? `لا يوجد شيء مطابق لـ "${query}"` : `Nothing matches "${query}"` }}</p>
            </div>

            <!-- Results -->
            <div v-else-if="results" class="gs-results">

              <!-- Contacts -->
              <div v-if="results.contacts?.length" class="gs-group">
                <div class="gs-group-label">
                  <i class="bi bi-people"></i>
                  {{ isRtl ? 'جهات الاتصال' : 'Contacts' }}
                  <span class="gs-count">{{ results.contacts.length }}</span>
                </div>
                <button
                  v-for="(item, i) in results.contacts" :key="`c-${item.id}`"
                  class="gs-item"
                  :class="{ 'is-active': flatIndex(i,'contacts') === focused }"
                  @click="navigate(item)"
                  @mouseenter="focused = flatIndex(i,'contacts')"
                >
                  <span class="gs-avatar" :style="{ background: strToColor(item.label)+'18', color: strToColor(item.label) }">
                    {{ initials(item.label) }}
                  </span>
                  <span class="gs-item-main">
                    <span class="gs-item-name" v-html="highlight(item.label)"></span>
                    <span class="gs-item-meta">{{ item.sub }}</span>
                  </span>
                  <span class="gs-tag gs-tag-green">{{ isRtl ? 'جهة اتصال' : 'Contact' }}</span>
                  <i class="bi bi-arrow-return-left gs-enter-hint"></i>
                </button>
              </div>

              <!-- Deals -->
              <div v-if="results.deals?.length" class="gs-group">
                <div class="gs-group-label">
                  <i class="bi bi-briefcase"></i>
                  {{ isRtl ? 'الصفقات' : 'Deals' }}
                  <span class="gs-count">{{ results.deals.length }}</span>
                </div>
                <button
                  v-for="(item, i) in results.deals" :key="`d-${item.id}`"
                  class="gs-item"
                  :class="{ 'is-active': flatIndex(i,'deals') === focused }"
                  @click="navigate(item)"
                  @mouseenter="focused = flatIndex(i,'deals')"
                >
                  <span class="gs-icon-box" style="background:rgba(37,99,235,.1);color:#2563EB">
                    <i class="bi bi-briefcase"></i>
                  </span>
                  <span class="gs-item-main">
                    <span class="gs-item-name" v-html="highlight(item.label)"></span>
                    <span class="gs-item-meta">{{ item.sub }}</span>
                  </span>
                  <span class="gs-tag gs-tag-blue">{{ isRtl ? 'صفقة' : 'Deal' }}</span>
                  <i class="bi bi-arrow-return-left gs-enter-hint"></i>
                </button>
              </div>

              <!-- Companies -->
              <div v-if="results.companies?.length" class="gs-group">
                <div class="gs-group-label">
                  <i class="bi bi-building"></i>
                  {{ isRtl ? 'الشركات' : 'Companies' }}
                  <span class="gs-count">{{ results.companies.length }}</span>
                </div>
                <button
                  v-for="(item, i) in results.companies" :key="`co-${item.id}`"
                  class="gs-item"
                  :class="{ 'is-active': flatIndex(i,'companies') === focused }"
                  @click="navigate(item)"
                  @mouseenter="focused = flatIndex(i,'companies')"
                >
                  <span class="gs-icon-box" style="background:rgba(124,58,237,.1);color:#7C3AED">
                    <i class="bi bi-building"></i>
                  </span>
                  <span class="gs-item-main">
                    <span class="gs-item-name" v-html="highlight(item.label)"></span>
                    <span class="gs-item-meta">{{ item.sub }}</span>
                  </span>
                  <span class="gs-tag gs-tag-purple">{{ isRtl ? 'شركة' : 'Company' }}</span>
                  <i class="bi bi-arrow-return-left gs-enter-hint"></i>
                </button>
              </div>

            </div>
          </div>

          <!-- ── Footer ── -->
          <div class="gs-foot">
            <div class="gs-shortcuts">
              <span class="gs-shortcut"><kbd>↑</kbd><kbd>↓</kbd> {{ isRtl ? 'تنقل' : 'navigate' }}</span>
              <span class="gs-shortcut"><kbd>↵</kbd> {{ isRtl ? 'فتح' : 'open' }}</span>
              <span class="gs-shortcut"><kbd>esc</kbd> {{ isRtl ? 'إغلاق' : 'close' }}</span>
            </div>
            <span class="gs-brand">CRM<span>Innovation</span></span>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
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

function openSearch() {
  open.value    = true
  query.value   = ''
  results.value = null
  focused.value = -1
  nextTick(() => inputRef.value?.focus())
}
function close() { open.value = false }

function go(path) { close(); router.push(path) }

function onKeydown(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault()
    open.value ? close() : openSearch()
  }
}
onMounted(()  => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))

defineExpose({ openSearch })

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
  const c = results.value.contacts?.length || 0
  const d = results.value.deals?.length    || 0
  return { contacts: 0, deals: c, companies: c + d }[type] + i
}

function moveDown() {
  if (!allItems.value.length) return
  focused.value = (focused.value + 1) % allItems.value.length
}
function moveUp() {
  if (!allItems.value.length) return
  focused.value = (focused.value - 1 + allItems.value.length) % allItems.value.length
}
function selectHighlighted() {
  if (focused.value >= 0 && allItems.value[focused.value]) navigate(allItems.value[focused.value])
}
function navigate(item) { close(); router.push(item.url) }

function highlight(text) {
  if (!query.value || !text) return text
  const esc = query.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  return String(text).replace(new RegExp(`(${esc})`, 'gi'), '<mark class="gs-hl">$1</mark>')
}

const PALETTE = ['#2563EB','#7C3AED','#059669','#D97706','#0891B2','#DB2777','#EA580C']
function strToColor(name = '') {
  const s = String(name).split('').reduce((a, c) => a + c.charCodeAt(0), 0)
  return PALETTE[s % PALETTE.length]
}
function initials(name = '') {
  return String(name).split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
}
</script>

<style scoped>
/* ── Backdrop ───────────────────────────────────── */
.gs-backdrop {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(6px) saturate(150%);
  display: flex; align-items: flex-start; justify-content: center;
  padding-top: clamp(60px, 10vh, 120px);
  padding-inline: 16px;
}

/* ── Shell ──────────────────────────────────────── */
.gs-shell {
  background: var(--bs-body-bg, #fff);
  border: 1px solid var(--bs-border-color, #E2E8F0);
  border-radius: 18px;
  box-shadow: 0 0 0 1px rgba(0,0,0,.04), 0 8px 24px rgba(0,0,0,.08), 0 32px 72px rgba(0,0,0,.16);
  width: 100%; max-width: 640px;
  display: flex; flex-direction: column;
  max-height: calc(100vh - 140px);
  overflow: hidden;
}

/* ── Head ───────────────────────────────────────── */
.gs-head {
  flex-shrink: 0;
  border-bottom: 1px solid var(--bs-border-color, #E2E8F0);
}
.gs-input-row {
  display: flex; align-items: center; gap: 12px;
  padding: 16px 18px;
}
.gs-lens {
  width: 18px; height: 18px; flex-shrink: 0;
  color: #94A3B8;
}
.gs-input {
  flex: 1; min-width: 0;
  border: none; outline: none; background: transparent;
  font-size: 15px; font-weight: 500; line-height: 1.4;
  color: var(--bs-body-color);
}
.gs-input::placeholder { color: #94A3B8; font-weight: 400; }
.gs-clear {
  display: flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 6px; flex-shrink: 0;
  border: none; cursor: pointer; padding: 0;
  background: var(--bs-tertiary-bg, #F1F5F9);
  color: var(--bs-secondary-color);
  transition: background .15s, color .15s;
}
.gs-clear svg { width: 14px; height: 14px; }
.gs-clear:hover { background: #E2E8F0; color: #475569; }
.gs-esc {
  font-size: 10.5px; font-family: inherit; font-weight: 600;
  background: var(--bs-tertiary-bg, #F8FAFC);
  color: var(--bs-secondary-color, #64748B);
  border: 1px solid var(--bs-border-color, #E2E8F0);
  border-radius: 6px; padding: 3px 8px;
  cursor: pointer; flex-shrink: 0; letter-spacing: .3px;
  transition: background .15s;
}
.gs-esc:hover { background: #E2E8F0; }

/* ── Body ───────────────────────────────────────── */
.gs-body {
  flex: 1; overflow-y: auto; min-height: 0;
}
.gs-body::-webkit-scrollbar { width: 4px; }
.gs-body::-webkit-scrollbar-thumb { background: var(--bs-border-color); border-radius: 2px; }

/* ── Quick Links ─────────────────────────────────── */
.gs-quicklinks { padding: 16px 18px 12px; }
.gs-ql-label {
  font-size: 10.5px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .7px; color: var(--bs-secondary-color);
  margin-bottom: 10px;
}
.gs-ql-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px;
}
.gs-ql-btn {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px; border-radius: 10px;
  border: 1px solid var(--bs-border-color, #E2E8F0);
  background: var(--bs-tertiary-bg, #F8FAFC);
  cursor: pointer; font-size: 12.5px; font-weight: 500;
  color: var(--bs-body-color); text-align: start;
  transition: all .15s ease;
}
.gs-ql-btn:hover {
  background: var(--bs-body-bg, #fff);
  border-color: #2563EB33;
  box-shadow: 0 2px 8px rgba(37,99,235,.08);
  color: #2563EB;
}
.gs-ql-icon {
  width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px;
}

/* ── State (loading / empty) ─────────────────────── */
.gs-state {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; padding: 40px 20px; gap: 10px;
  color: var(--bs-secondary-color); font-size: 13px;
}
.gs-spinner {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2.5px solid var(--bs-border-color);
  border-top-color: #2563EB;
  animation: gs-spin .7s linear infinite;
}
@keyframes gs-spin { to { transform: rotate(360deg); } }

.gs-empty-svg {
  width: 48px; height: 48px;
  color: var(--bs-border-color, #CBD5E1);
  margin-bottom: 4px;
}
.gs-empty-title { font-size: 14px; font-weight: 600; color: var(--bs-body-color); margin: 0; }
.gs-empty-sub   { font-size: 12.5px; color: var(--bs-secondary-color); margin: 0; text-align: center; }

/* ── Results ─────────────────────────────────────── */
.gs-results { padding: 8px 10px 10px; }

.gs-group { margin-bottom: 4px; }
.gs-group-label {
  display: flex; align-items: center; gap: 5px;
  font-size: 10.5px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .6px; color: var(--bs-secondary-color);
  padding: 10px 8px 5px;
}
.gs-count {
  margin-inline-start: auto;
  background: var(--bs-tertiary-bg, #F1F5F9);
  color: var(--bs-secondary-color);
  border-radius: 20px; font-size: 10px; padding: 1px 7px;
  font-weight: 600;
}

/* ── Item ────────────────────────────────────────── */
.gs-item {
  display: flex; align-items: center; gap: 10px;
  width: 100%; padding: 9px 8px;
  border: none; background: transparent; cursor: pointer;
  border-radius: 10px; text-align: start;
  transition: background .1s ease;
  position: relative;
}
.gs-item:hover, .gs-item.is-active {
  background: rgba(37,99,235,.05);
}
.gs-item.is-active .gs-enter-hint { opacity: 1; }

.gs-avatar {
  width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; line-height: 1;
}
.gs-icon-box {
  width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px;
}
.gs-item-main {
  flex: 1; min-width: 0;
  display: flex; flex-direction: column; gap: 1px;
}
.gs-item-name {
  font-size: 13px; font-weight: 600;
  color: var(--bs-body-color);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  display: block;
}
.gs-item-meta {
  font-size: 11.5px; color: var(--bs-secondary-color);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  display: block;
}

/* ── Tag pills ───────────────────────────────────── */
.gs-tag {
  font-size: 10px; font-weight: 700;
  padding: 2px 8px; border-radius: 20px; flex-shrink: 0;
  letter-spacing: .3px;
}
.gs-tag-green  { background: rgba(16,185,129,.12);  color: #059669; }
.gs-tag-blue   { background: rgba(37,99,235,.12);   color: #2563EB; }
.gs-tag-purple { background: rgba(124,58,237,.12);  color: #7C3AED; }

/* ── Enter hint icon ─────────────────────────────── */
.gs-enter-hint {
  font-size: 11px; color: var(--bs-secondary-color);
  flex-shrink: 0; opacity: 0;
  transition: opacity .1s;
}

/* ── Highlight mark ──────────────────────────────── */
:deep(.gs-hl) {
  background: rgba(37,99,235,.15);
  color: #1D4ED8;
  border-radius: 3px;
  padding: 0 2px;
  font-weight: 700;
}

/* ── Foot ────────────────────────────────────────── */
.gs-foot {
  flex-shrink: 0;
  display: flex; align-items: center; justify-content: space-between;
  padding: 9px 18px;
  border-top: 1px solid var(--bs-border-color, #E2E8F0);
}
.gs-shortcuts { display: flex; align-items: center; gap: 14px; }
.gs-shortcut {
  display: flex; align-items: center; gap: 4px;
  font-size: 11px; color: var(--bs-secondary-color);
}
.gs-shortcut kbd {
  font-family: inherit; font-size: 10px; font-weight: 600;
  background: var(--bs-tertiary-bg, #F1F5F9);
  border: 1px solid var(--bs-border-color);
  border-bottom-width: 2px;
  border-radius: 5px; padding: 1px 6px; line-height: 1.5;
}
.gs-brand {
  font-size: 10.5px; font-weight: 700; color: var(--bs-secondary-color);
  letter-spacing: .2px; opacity: .6;
}
.gs-brand span { color: #2563EB; opacity: 1; margin-inline-start: 2px; }

/* ── Transition ──────────────────────────────────── */
.gs-fade-enter-active { transition: opacity .2s ease; }
.gs-fade-leave-active { transition: opacity .15s ease; }
.gs-fade-enter-from,
.gs-fade-leave-to     { opacity: 0; }

.gs-fade-enter-active .gs-shell {
  animation: gs-pop .2s cubic-bezier(.22,.68,0,1.2) both;
}
.gs-fade-leave-active .gs-shell {
  animation: gs-pop-out .15s ease both;
}
@keyframes gs-pop {
  from { opacity: 0; transform: scale(.95) translateY(-10px); }
  to   { opacity: 1; transform: scale(1)   translateY(0); }
}
@keyframes gs-pop-out {
  to { opacity: 0; transform: scale(.97) translateY(-6px); }
}

/* ── RTL flip ────────────────────────────────────── */
[dir="rtl"] .gs-count { margin-inline-start: auto; margin-inline-end: 0; }

/* ── Dark mode ───────────────────────────────────── */
@media (prefers-color-scheme: dark) {
  .gs-shell { box-shadow: 0 0 0 1px rgba(255,255,255,.06), 0 8px 24px rgba(0,0,0,.3), 0 32px 72px rgba(0,0,0,.4); }
  .gs-item:hover, .gs-item.is-active { background: rgba(255,255,255,.05); }
  :deep(.gs-hl) { background: rgba(96,165,250,.18); color: #93C5FD; }
}
:root[data-theme="dark"] .gs-shell { box-shadow: 0 0 0 1px rgba(255,255,255,.06), 0 8px 24px rgba(0,0,0,.3), 0 32px 72px rgba(0,0,0,.4); }
:root[data-theme="dark"] .gs-item:hover,
:root[data-theme="dark"] .gs-item.is-active { background: rgba(255,255,255,.05); }
</style>
