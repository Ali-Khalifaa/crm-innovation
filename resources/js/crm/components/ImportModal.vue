<template>
  <Teleport to="body">
    <div class="import-backdrop" @click.self="$emit('close')">
      <div class="import-modal" role="dialog">

        <!-- Header -->
        <div class="import-header">
          <div class="import-title-wrap">
            <div class="import-icon-box">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
            </div>
            <div>
              <h5 class="import-title">{{ isRtl ? 'استيراد جهات الاتصال' : 'Import Contacts' }}</h5>
              <p class="import-sub">{{ isRtl ? 'ارفع ملف CSV أو Excel لاستيراد جهات الاتصال دفعةً واحدة' : 'Upload a CSV or Excel file to import contacts in bulk' }}</p>
            </div>
          </div>
          <button class="import-close" @click="$emit('close')">&times;</button>
        </div>

        <!-- Body -->
        <div class="import-body">

          <!-- Download template -->
          <div class="import-template-row">
            <span class="import-template-hint">{{ isRtl ? 'لا تعرف الصيغة؟ حمّل القالب:' : "Don't know the format? Download the template:" }}</span>
            <button class="btn btn-sm btn-outline-secondary" @click="downloadTemplate">
              <i class="bi bi-download me-1"></i>
              {{ isRtl ? 'تحميل القالب' : 'Download Template' }}
            </button>
          </div>

          <!-- File drop zone -->
          <div
            class="import-dropzone"
            :class="{ 'drag-over': dragging, 'has-file': file }"
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="onDrop"
            @click="fileInputRef?.click()"
          >
            <input type="file" ref="fileInputRef" accept=".csv,.xlsx,.xls" style="display:none" @change="onFileChange" />
            <template v-if="!file">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity=".4">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
              <p class="drop-hint">{{ isRtl ? 'اسحب الملف هنا أو اضغط للتصفح' : 'Drop file here or click to browse' }}</p>
              <p class="drop-types">xlsx, xls, csv — {{ isRtl ? 'بحد أقصى 5 ميجابايت' : 'max 5 MB' }}</p>
            </template>
            <template v-else>
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
              <p class="drop-hint fw-medium">{{ file.name }}</p>
              <button class="btn btn-xs btn-link text-muted" @click.stop="removeFile">{{ isRtl ? 'إزالة' : 'Remove' }}</button>
            </template>
          </div>

          <!-- Columns hint -->
          <div class="import-columns-hint">
            <span class="columns-label">{{ isRtl ? 'الأعمدة المتوقعة:' : 'Expected columns:' }}</span>
            <span class="columns-chips">
              <span class="col-chip required">first_name *</span>
              <span class="col-chip">last_name</span>
              <span class="col-chip">email</span>
              <span class="col-chip">phone</span>
              <span class="col-chip">company</span>
              <span class="col-chip">status</span>
              <span class="col-chip">lead_source</span>
              <span class="col-chip">address</span>
              <span class="col-chip">notes</span>
            </span>
          </div>

          <!-- Result -->
          <div v-if="result" class="import-result" :class="result.errors.length ? 'has-warnings' : 'all-ok'">
            <div class="result-line">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
              <span>{{ isRtl ? `تم استيراد ${result.imported} جهة اتصال بنجاح` : `${result.imported} contacts imported successfully` }}</span>
            </div>
            <div v-if="result.errors.length" class="result-errors">
              <p class="result-errors-title">{{ isRtl ? `${result.errors.length} تحذير:` : `${result.errors.length} warning(s):` }}</p>
              <ul>
                <li v-for="(e, i) in result.errors.slice(0, 5)" :key="i">{{ e }}</li>
                <li v-if="result.errors.length > 5">... {{ result.errors.length - 5 }} {{ isRtl ? 'أخرى' : 'more' }}</li>
              </ul>
            </div>
          </div>

          <!-- Error -->
          <div v-if="importError" class="alert alert-danger py-2 small">{{ importError }}</div>

        </div>

        <!-- Footer -->
        <div class="import-footer">
          <button class="btn btn-light" @click="$emit('close')">{{ isRtl ? 'إغلاق' : 'Close' }}</button>
          <button
            class="btn btn-primary px-4"
            :disabled="!file || importing || !!result"
            @click="doImport"
          >
            <span v-if="importing" class="spinner-border spinner-border-sm me-2"></span>
            {{ importing ? (isRtl ? 'جاري الاستيراد...' : 'Importing...') : (isRtl ? 'استيراد' : 'Import') }}
          </button>
        </div>

      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useStore } from 'vuex'
import api from '../composables/useApi.js'
import { useToast } from '../composables/useToast.js'

const emit    = defineEmits(['close', 'done'])
const store   = useStore()
const toast   = useToast()
const isRtl   = computed(() => store.state.ui?.locale === 'ar')

const fileInputRef = ref(null)
const file       = ref(null)
const dragging   = ref(false)
const importing  = ref(false)
const result     = ref(null)
const importError = ref('')

function onDrop(e) {
  dragging.value = false
  const dropped = e.dataTransfer.files[0]
  if (dropped) setFile(dropped)
}

function onFileChange(e) {
  const selected = e.target.files[0]
  if (selected) setFile(selected)
}

function setFile(f) {
  const allowed = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
  if (!allowed.includes(f.type) && !f.name.match(/\.(csv|xls|xlsx)$/i)) {
    importError.value = isRtl.value ? 'نوع الملف غير مدعوم. الأنواع المقبولة: xlsx, xls, csv' : 'Unsupported file type. Accepted: xlsx, xls, csv'
    return
  }
  file.value = f
  result.value = null
  importError.value = ''
}

function removeFile() {
  file.value = null
  result.value = null
  importError.value = ''
  if (fileInputRef.value) fileInputRef.value.value = ''
}

async function doImport() {
  if (!file.value) return
  importing.value = true
  importError.value = ''
  try {
    const fd = new FormData()
    fd.append('file', file.value)
    const { data } = await api.post('/contacts/import', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    result.value = data.data
    toast.success(isRtl.value ? `تم استيراد ${data.data.imported} جهة اتصال` : `Imported ${data.data.imported} contacts`)
    if (!result.value.errors.length) {
      setTimeout(() => emit('done'), 1200)
    }
  } catch (err) {
    importError.value = err.response?.data?.message || (isRtl.value ? 'فشل الاستيراد' : 'Import failed')
  } finally {
    importing.value = false
  }
}

function downloadTemplate() {
  const headers = 'first_name,last_name,email,phone,company,address,notes,status,lead_source\n'
  const example = 'محمد,أحمد,m@example.com,+966501234567,شركة مثال,الرياض,,lead,referral\n'
  const blob = new Blob(['﻿' + headers + example], { type: 'text/csv;charset=utf-8;' })
  const a = document.createElement('a')
  a.href = URL.createObjectURL(blob)
  a.download = 'contacts-template.csv'
  a.click()
  URL.revokeObjectURL(a.href)
}
</script>

<style scoped>
.import-backdrop {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 1400;
  display: flex; align-items: center; justify-content: center;
  padding: 16px;
}

.import-modal {
  background: var(--bs-body-bg, #fff);
  border-radius: 16px;
  width: 100%; max-width: 560px;
  box-shadow: 0 20px 60px rgba(0,0,0,.2);
  display: flex; flex-direction: column;
}

.import-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 20px 24px 0;
}
.import-title-wrap { display: flex; align-items: flex-start; gap: 12px; }
.import-icon-box {
  width: 40px; height: 40px; border-radius: 10px;
  background: rgba(37,99,235,.1); color: #2563eb;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.import-title { font-size: .95rem; font-weight: 700; margin: 0 0 2px; }
.import-sub   { font-size: .78rem; color: var(--bs-secondary-color); margin: 0; }
.import-close {
  background: none; border: none; cursor: pointer;
  font-size: 1.4rem; line-height: 1;
  color: var(--bs-secondary-color);
  padding: 0; margin-top: -2px;
}
.import-close:hover { color: var(--bs-body-color); }

.import-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }

.import-template-row {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px;
}
.import-template-hint { font-size: .8rem; color: var(--bs-secondary-color); }

.import-dropzone {
  border: 2px dashed var(--bs-border-color);
  border-radius: 12px;
  padding: 28px 20px;
  text-align: center;
  cursor: pointer;
  transition: border-color .15s, background .15s;
  display: flex; flex-direction: column; align-items: center; gap: 8px;
}
.import-dropzone:hover, .import-dropzone.drag-over {
  border-color: #2563eb;
  background: #eff6ff;
}
.import-dropzone.has-file {
  border-color: #16a34a;
  background: #f0fdf4;
}
.drop-hint  { margin: 0; font-size: .85rem; font-weight: 500; }
.drop-types { margin: 0; font-size: .72rem; color: var(--bs-secondary-color); }

.import-columns-hint {
  display: flex; align-items: flex-start; gap: 8px; flex-wrap: wrap;
}
.columns-label { font-size: .75rem; color: var(--bs-secondary-color); white-space: nowrap; margin-top: 3px; }
.columns-chips { display: flex; gap: 4px; flex-wrap: wrap; }
.col-chip {
  font-size: .7rem; padding: 2px 7px;
  background: #f1f5f9; border-radius: 4px;
  font-family: monospace;
}
.col-chip.required { background: #dbeafe; color: #1e40af; font-weight: 700; }

.import-result {
  border-radius: 10px; padding: 12px 16px;
}
.import-result.all-ok { background: #f0fdf4; border: 1px solid #bbf7d0; }
.import-result.has-warnings { background: #fffbeb; border: 1px solid #fde68a; }
.result-line { display: flex; align-items: center; gap: 8px; font-size: .83rem; font-weight: 600; }
.result-errors { margin-top: 8px; }
.result-errors-title { font-size: .78rem; font-weight: 600; margin-bottom: 4px; }
.result-errors ul { margin: 0; padding-inline-start: 16px; font-size: .75rem; }
.result-errors li { margin-bottom: 2px; }

.import-footer {
  padding: 16px 24px 20px;
  border-top: 1px solid var(--bs-border-color);
  display: flex; justify-content: flex-end; gap: 8px;
}
</style>
