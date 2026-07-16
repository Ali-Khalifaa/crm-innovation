<template>
  <CrmLayout>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale==='ar' ? 'قائمة الصفقات' : 'Deals List' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale==='ar' ? 'إدارة وتتبع جميع الصفقات' : 'Manage and track all your deals' }}</p>
      </div>
      <div class="d-flex gap-2 flex-shrink-0">
        <router-link to="/crm/deals" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-kanban me-1"></i>{{ locale==='ar' ? 'لوحة كانبان' : 'Kanban Board' }}
        </router-link>
        <button class="btn btn-sm btn-primary" @click="openAddModal">
          <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar' ? 'صفقة جديدة' : 'New Deal' }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" @click="clearFilter">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-briefcase fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.total }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'إجمالي الصفقات' : 'Total Deals' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': filterStatus==='open'}" @click="filterByStatus('open')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-arrow-repeat fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.open }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'مفتوحة' : 'Open' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': filterStatus==='won'}" @click="filterByStatus('won')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#059669"><i class="bi bi-trophy fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.won }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'رابحة' : 'Won' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': filterStatus==='lost'}" @click="filterByStatus('lost')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(239,68,68,.1);color:#DC2626"><i class="bi bi-x-circle fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.lost }}</div>
                <div class="stat-label">{{ locale==='ar' ? 'خاسرة' : 'Lost' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="card card-border deals-card">

      <!-- Card Header -->
      <div class="card-header card-header-action flex-wrap gap-2">
        <h6 class="mb-0 d-flex align-items-center gap-2 flex-wrap">
          <span>{{ locale==='ar' ? 'قائمة الصفقات' : 'All Deals' }}</span>
          <span class="badge badge-soft-primary">{{ meta.total ?? 0 }}</span>
          <button v-if="filterStatus" class="btn btn-xs badge-soft-warning d-flex align-items-center gap-1 border-0" @click="clearFilter">
            {{ statusLabel(filterStatus) }} <i class="bi bi-x"></i>
          </button>
        </h6>
        <div class="card-action-wrap d-flex gap-2 flex-wrap">
          <div class="input-group" style="width:230px">
            <span class="input-group-text bg-transparent border-end-0 ps-2">
              <i class="bi bi-search text-muted" style="font-size:13px"></i>
            </span>
            <input v-model="search" @input="debouncedFetch" type="text"
              class="form-control border-start-0 ps-1"
              :placeholder="locale==='ar' ? 'بحث...' : 'Search deals...'" />
          </div>
          <select v-model="filterStage" @change="page=1;fetchDeals()" class="form-select" style="width:auto">
            <option value="">{{ locale==='ar' ? 'كل المراحل' : 'All Stages' }}</option>
            <option v-for="s in stages" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:260px">
        <div class="text-center">
          <div class="spinner-border text-primary mb-2" role="status"></div>
          <div class="text-muted small">{{ locale==='ar' ? 'جاري التحميل...' : 'Loading...' }}</div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="!deals.length" class="text-center py-5 px-4">
        <div class="empty-icon mb-3"><i class="bi bi-briefcase"></i></div>
        <h6 class="mb-1">{{ search || filterStatus || filterStage ? (locale==='ar' ? 'لا توجد نتائج' : 'No results found') : (locale==='ar' ? 'لا توجد صفقات بعد' : 'No deals yet') }}</h6>
        <p class="text-muted small mb-3">
          {{ search || filterStatus || filterStage
            ? (locale==='ar' ? 'جرّب تغيير معايير البحث' : 'Try changing your filters')
            : (locale==='ar' ? 'أضف أول صفقة لتبدأ' : 'Add your first deal to get started') }}
        </p>
        <button v-if="search || filterStatus || filterStage" class="btn btn-sm btn-outline-secondary me-2" @click="clearFilter">
          <i class="bi bi-x-lg me-1"></i>{{ locale==='ar' ? 'مسح الفلتر' : 'Clear Filter' }}
        </button>
        <button v-else class="btn btn-sm btn-primary" @click="openAddModal">
          <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar' ? 'إضافة صفقة' : 'Add Deal' }}
        </button>
      </div>

      <!-- Table -->
      <div v-else>
        <div class="table-responsive">
          <table class="table deals-table mb-0">
            <thead>
              <tr>
                <th class="ps-4">{{ locale==='ar' ? 'الصفقة' : 'Deal' }}</th>
                <th class="d-none d-md-table-cell">{{ locale==='ar' ? 'المرحلة' : 'Stage' }}</th>
                <th>{{ locale==='ar' ? 'القيمة' : 'Value' }}</th>
                <th class="d-none d-lg-table-cell">{{ locale==='ar' ? 'الاحتمالية' : 'Probability' }}</th>
                <th>{{ locale==='ar' ? 'الحالة' : 'Status' }}</th>
                <th class="d-none d-xl-table-cell">{{ locale==='ar' ? 'تاريخ الإغلاق' : 'Close Date' }}</th>
                <th class="d-none d-lg-table-cell">{{ locale==='ar' ? 'جهة الاتصال' : 'Contact' }}</th>
                <th style="width:90px"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="deal in deals" :key="deal.id" class="deal-row">
                <td class="ps-4">
                  <div class="d-flex align-items-center gap-3">
                    <div class="deal-avatar" :style="{ background: dealColor(deal.title) }">
                      {{ (deal.title || '?')[0].toUpperCase() }}
                    </div>
                    <div>
                      <div class="deal-name">{{ deal.title }}</div>
                      <div class="text-muted" style="font-size:11px" v-if="deal.contact">
                        {{ deal.contact.first_name }} {{ deal.contact.last_name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="d-none d-md-table-cell">
                  <span v-if="deal.stage" class="stage-badge" :style="{ background: (deal.stage.color || '#2563EB') + '22', color: deal.stage.color || '#2563EB' }">
                    {{ deal.stage.name }}
                  </span>
                  <span v-else class="text-muted">—</span>
                </td>
                <td class="deal-value">${{ Number(deal.value || 0).toLocaleString() }}</td>
                <td class="d-none d-lg-table-cell">
                  <div class="d-flex align-items-center gap-2">
                    <div class="prob-bar-wrap">
                      <div class="prob-bar-fill" :style="{ width: (deal.probability||0)+'%', background: probColor(deal.probability) }"></div>
                    </div>
                    <span class="prob-text">{{ deal.probability||0 }}%</span>
                  </div>
                </td>
                <td>
                  <span class="deal-status-badge" :class="`status-${deal.status}`">
                    <i class="bi" :class="deal.status==='won' ? 'bi-check-circle-fill' : deal.status==='lost' ? 'bi-x-circle-fill' : 'bi-circle-fill'" style="font-size:8px"></i>
                    {{ statusLabel(deal.status) }}
                  </span>
                </td>
                <td class="d-none d-xl-table-cell">
                  <span v-if="formatDate(deal.expected_close_date)" :class="{ 'text-danger': isOverdue(deal) }" style="font-size:12px">
                    <i v-if="isOverdue(deal)" class="bi bi-exclamation-circle me-1"></i>
                    <i v-else class="bi bi-calendar3 me-1 text-muted" style="font-size:11px"></i>
                    {{ formatDate(deal.expected_close_date) }}
                  </span>
                  <span v-else class="no-date-badge">
                    <i class="bi bi-calendar-x"></i>
                    {{ locale==='ar' ? 'غير محدد' : 'No date' }}
                  </span>
                </td>
                <td class="d-none d-lg-table-cell" style="font-size:12px;color:var(--bs-secondary-color)">
                  <span v-if="deal.contact">{{ deal.contact.first_name }} {{ deal.contact.last_name }}</span>
                  <span v-else>—</span>
                </td>
                <td>
                  <div class="d-flex gap-1 justify-content-end">
                    <!-- Win/Lost quick buttons (only for open deals) -->
                    <template v-if="deal.status === 'open'">
                      <button class="btn btn-xs btn-soft-success" @click="openWonLostModal(deal,'won')" :title="locale==='ar'?'علّم رابح':'Mark Won'">
                        <i class="bi bi-trophy-fill"></i>
                      </button>
                      <button class="btn btn-xs btn-soft-danger" @click="openWonLostModal(deal,'lost')" :title="locale==='ar'?'علّم خاسر':'Mark Lost'">
                        <i class="bi bi-x-circle-fill"></i>
                      </button>
                    </template>
                    <button class="btn btn-xs btn-outline-secondary" @click="openEditModal(deal)" :title="locale==='ar'?'تعديل':'Edit'">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" @click="confirmDelete(deal)" :title="locale==='ar'?'حذف':'Delete'">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="d-flex justify-content-center align-items-center gap-1 py-3 flex-wrap">
          <button class="btn btn-xs btn-outline-secondary" @click="page=1;fetchDeals()" :disabled="page===1">«</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page--;fetchDeals()" :disabled="page===1">‹</button>
          <button v-for="p in pages" :key="p"
            class="btn btn-xs"
            :class="p===page ? 'btn-primary' : 'btn-outline-secondary'"
            @click="page=p;fetchDeals()">{{ p }}</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page++;fetchDeals()" :disabled="page===meta.last_page">›</button>
          <button class="btn btn-xs btn-outline-secondary" @click="page=meta.last_page;fetchDeals()" :disabled="page===meta.last_page">»</button>
        </div>
      </div>
    </div>


    <!-- ═══ ADD / EDIT MODAL ═══ -->
    <div class="modal fade" id="dealListFormModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

          <div class="modal-header pb-0 border-0">
            <div class="d-flex align-items-center gap-3">
              <div class="modal-icon" :style="{ background: formMode==='create' ? 'rgba(37,99,235,.1)' : 'rgba(124,58,237,.1)', color: formMode==='create' ? '#2563EB' : '#7C3AED' }">
                <i class="bi" :class="formMode==='create' ? 'bi-plus-circle-fill' : 'bi-pencil-fill'"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold">{{ formMode==='create' ? (locale==='ar'?'إضافة صفقة جديدة':'New Deal') : (locale==='ar'?'تعديل الصفقة':'Edit Deal') }}</h5>
                <p class="text-muted small mb-0 mt-1">{{ formMode==='create' ? (locale==='ar'?'أدخل بيانات الصفقة الجديدة':'Enter the new deal details') : (locale==='ar'?'قم بتعديل بيانات الصفقة':'Update the deal information') }}</p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body pt-3">
            <form id="dealListForm" @submit.prevent="saveDeal">

              <!-- Basic Info -->
              <div class="fsect-title"><i class="bi bi-info-circle me-2 text-primary"></i>{{ locale==='ar'?'المعلومات الأساسية':'Basic Information' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-12">
                  <label class="form-label">{{ locale==='ar'?'عنوان الصفقة':'Deal Title' }} <span class="text-danger">*</span></label>
                  <input
                    v-model="form.title"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': v$.title.$error, 'is-valid': v$.title.$dirty && !v$.title.$error }"
                    :placeholder="locale==='ar'?'مثال: عقد توريد أجهزة':'e.g. Hardware supply contract'"
                    @blur="v$.title.$touch()"
                  />
                  <div class="invalid-feedback" v-if="v$.title.$error">{{ v$.title.$errors[0]?.$message }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'المرحلة':'Stage' }} <span class="text-danger">*</span></label>
                  <Select
                    v-model="form.stage_id"
                    :options="stageOptions"
                    option-label="name"
                    option-value="id"
                    :placeholder="locale==='ar'?'اختر المرحلة':'Select stage'"
                    :invalid="v$.stage_id.$error"
                    class="w-100"
                    append-to="body"
                    @blur="v$.stage_id.$touch()"
                  />
                  <small class="text-danger d-block mt-1" v-if="v$.stage_id.$error">{{ v$.stage_id.$errors[0]?.$message }}</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'حالة الصفقة':'Deal Status' }}</label>
                  <Select
                    v-model="form.status"
                    :options="statusOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'اختر الحالة':'Select status'"
                    class="w-100"
                    append-to="body"
                  />
                </div>
              </div>

              <!-- Deal Details -->
              <div class="fsect-title"><i class="bi bi-bar-chart me-2 text-primary"></i>{{ locale==='ar'?'تفاصيل الصفقة':'Deal Details' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'القيمة ($)':'Value ($)' }}</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input v-model.number="form.value" type="number" min="0" step="0.01"
                      class="form-control"
                      :class="{ 'is-invalid': v$.value.$error }"
                      placeholder="0.00"
                      @blur="v$.value.$touch()" />
                    <div class="invalid-feedback" v-if="v$.value.$error">{{ v$.value.$errors[0]?.$message }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'احتمالية الإغلاق (%)':'Probability (%)' }}</label>
                  <div class="input-group">
                    <input v-model.number="form.probability" type="number" min="0" max="100"
                      class="form-control"
                      :class="{ 'is-invalid': v$.probability.$error }"
                      placeholder="50"
                      @blur="v$.probability.$touch()" />
                    <span class="input-group-text">%</span>
                    <div class="invalid-feedback" v-if="v$.probability.$error">{{ v$.probability.$errors[0]?.$message }}</div>
                  </div>
                  <div class="mt-2" v-if="form.probability >= 0">
                    <div class="prob-preview-bar">
                      <div class="prob-preview-fill" :style="{ width: Math.min(form.probability||0,100)+'%', background: probColor(form.probability) }"></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'تاريخ الإغلاق المتوقع':'Expected Close Date' }}</label>
                  <DatePicker
                    v-model="closeDate"
                    date-format="dd/mm/yy"
                    :placeholder="locale==='ar'?'يوم/شهر/سنة':'DD/MM/YYYY'"
                    show-icon
                    icon-display="input"
                    class="w-100"
                    append-to="body"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'جهة الاتصال':'Contact' }}</label>
                  <Select
                    v-model="form.contact_id"
                    :options="contactOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'— بدون جهة اتصال —':'— No contact —'"
                    :show-clear="true"
                    class="w-100"
                    append-to="body"
                  />
                </div>
              </div>

              <!-- Lost Reason -->
              <div v-if="form.status === 'lost'" class="mb-4">
                <div class="fsect-title"><i class="bi bi-x-circle me-2 text-danger"></i>{{ locale==='ar'?'سبب الخسارة':'Lost Reason' }}</div>
                <input
                  v-model="form.lost_reason"
                  type="text"
                  class="form-control"
                  :placeholder="locale==='ar'?'مثال: السعر مرتفع، ذهب للمنافس...':'e.g. Price too high, went with competitor...'"
                />
              </div>

              <!-- Notes -->
              <div class="fsect-title"><i class="bi bi-chat-left-text me-2 text-primary"></i>{{ locale==='ar'?'ملاحظات':'Notes' }}</div>
              <textarea
                v-model="form.notes"
                class="form-control"
                :placeholder="locale==='ar'?'أي ملاحظات إضافية...':'Any additional notes...'"
                rows="3"
                style="resize:none"
              ></textarea>

            </form>
          </div>

          <div class="modal-footer border-0 pt-0">
            <button v-if="formMode==='edit'" type="button" class="btn btn-outline-danger me-auto" @click="switchToDelete">
              <i class="bi bi-trash me-1"></i>{{ locale==='ar'?'حذف':'Delete' }}
            </button>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="submit" form="dealListForm" class="btn btn-primary px-4" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
              {{ saving ? (locale==='ar'?'جاري الحفظ...':'Saving...') : (formMode==='create' ? (locale==='ar'?'إضافة الصفقة':'Add Deal') : (locale==='ar'?'حفظ التعديلات':'Save Changes')) }}
            </button>
          </div>

        </div>
      </div>
    </div>


    <!-- ═══ DELETE CONFIRM MODAL ═══ -->
    <div class="modal fade" id="dealListDeleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
          <div class="modal-body text-center py-4 px-4">
            <div class="del-icon mb-3"><i class="bi bi-trash3-fill"></i></div>
            <h5 class="fw-bold mb-2">{{ locale==='ar' ? 'حذف الصفقة' : 'Delete Deal' }}</h5>
            <p class="text-muted mb-0" style="font-size:14px">
              {{ locale==='ar' ? 'هل أنت متأكد من حذف' : 'Are you sure you want to delete' }}
              <strong class="text-danger"> "{{ deletingDeal?.title }}"</strong>?
              <br><span class="small">{{ locale==='ar' ? 'لا يمكن التراجع عن هذه العملية' : 'This action cannot be undone.' }}</span>
            </p>
          </div>
          <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
            <button type="button" class="btn btn-light px-4" @click="cancelDelete">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="button" class="btn btn-danger px-4" @click="deleteDeal" :disabled="deleting">
              <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
              {{ locale==='ar' ? 'نعم، احذف' : 'Yes, Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ WIN / LOST MODAL ═══ -->
    <div class="modal fade" id="dealListOutcomeModal" tabindex="-1" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <div class="d-flex align-items-center gap-3">
              <div class="outcome-icon" :class="outcomeType==='won' ? 'icon-won' : 'icon-lost'">
                <i class="bi" :class="outcomeType==='won' ? 'bi-trophy-fill' : 'bi-x-circle-fill'"></i>
              </div>
              <div>
                <h5 class="modal-title mb-0">
                  {{ outcomeType==='won'
                    ? (locale==='ar' ? 'تسجيل صفقة رابحة' : 'Mark as Won')
                    : (locale==='ar' ? 'تسجيل صفقة خاسرة' : 'Mark as Lost') }}
                </h5>
                <p class="text-muted small mb-0 mt-1">{{ outcomeTarget?.title }}</p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body pt-4">
            <div class="mb-3">
              <label class="form-label fw-medium">{{ locale==='ar' ? 'تاريخ الإغلاق' : 'Close Date' }}</label>
              <input v-model="outcomeForm.closed_at" type="date" class="form-control" />
            </div>
            <div v-if="outcomeType==='lost'" class="mb-3">
              <label class="form-label fw-medium">{{ locale==='ar' ? 'سبب الخسارة' : 'Lost Reason' }}</label>
              <select v-model="outcomeForm.lost_reason" class="form-select">
                <option value="">{{ locale==='ar' ? 'اختر السبب' : 'Select reason' }}</option>
                <option value="price">{{ locale==='ar' ? 'السعر' : 'Price' }}</option>
                <option value="competitor">{{ locale==='ar' ? 'منافس' : 'Competitor' }}</option>
                <option value="no_budget">{{ locale==='ar' ? 'لا ميزانية' : 'No Budget' }}</option>
                <option value="no_need">{{ locale==='ar' ? 'لا حاجة' : 'No Need' }}</option>
                <option value="timing">{{ locale==='ar' ? 'التوقيت' : 'Timing' }}</option>
                <option value="other">{{ locale==='ar' ? 'أخرى' : 'Other' }}</option>
              </select>
            </div>
            <div>
              <label class="form-label fw-medium">{{ locale==='ar' ? 'ملاحظات (اختياري)' : 'Notes (optional)' }}</label>
              <textarea v-model="outcomeForm.notes" class="form-control" rows="2" :placeholder="locale==='ar'?'إضافة ملاحظات...':'Add notes...'"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ locale==='ar' ? 'إلغاء' : 'Cancel' }}</button>
            <button type="button" class="btn px-4" :class="outcomeType==='won'?'btn-success':'btn-danger'" :disabled="savingOutcome" @click="saveOutcome">
              <span v-if="savingOutcome" class="spinner-border spinner-border-sm me-2"></span>
              {{ outcomeType==='won'
                ? (locale==='ar' ? 'تسجيل كرابحة' : 'Mark as Won')
                : (locale==='ar' ? 'تسجيل كخاسرة' : 'Mark as Lost') }}
            </button>
          </div>
        </div>
      </div>
    </div><!-- /dealListOutcomeModal -->

  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useStore } from 'vuex'
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, maxLength, minValue, maxValue, helpers } from '@vuelidate/validators'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import CrmLayout from '../../layouts/CrmLayout.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store  = useStore()
const toast  = useToast()
const locale = computed(() => store.state.ui.locale)

// ── Data ──────────────────────────────────────────────────
const deals   = ref([])
const stages  = ref([])
const contacts = ref([])
const meta    = ref({})
const loading = ref(true)
const saving  = ref(false)
const deleting = ref(false)
const page    = ref(1)

const search      = ref('')
const filterStatus = ref('')
const filterStage  = ref('')

const stats = ref({ total: 0, open: 0, won: 0, lost: 0 })

const formMode     = ref('create')
const editingDeal  = ref(null)
const deletingDeal = ref(null)

// ── Win/Lost outcome ───────────────────────────────────────
const outcomeTarget  = ref(null)
const outcomeType    = ref('won')
const savingOutcome  = ref(false)
const outcomeForm    = ref({ closed_at: '', lost_reason: '', notes: '' })

function getOutcomeModal() { return window.bootstrap?.Modal.getOrCreateInstance(document.getElementById('dealListOutcomeModal')) }
function openWonLostModal(deal, type) {
  outcomeTarget.value = deal
  outcomeType.value   = type
  outcomeForm.value   = { closed_at: new Date().toISOString().slice(0,10), lost_reason: '', notes: '' }
  nextTick(() => getOutcomeModal()?.show())
}
async function saveOutcome() {
  if (!outcomeTarget.value) return
  savingOutcome.value = true
  try {
    await api.put(`/deals/${outcomeTarget.value.id}`, {
      title:       outcomeTarget.value.title,
      stage_id:    outcomeTarget.value.stage_id ?? outcomeTarget.value.stage?.id,
      status:      outcomeType.value,
      value:       outcomeTarget.value.value,
      probability: outcomeType.value === 'won' ? 100 : 0,
      closed_at:   outcomeForm.value.closed_at,
      lost_reason: outcomeForm.value.lost_reason,
    })
    toast.success(locale.value === 'ar'
      ? (outcomeType.value === 'won' ? 'تم تسجيل الصفقة كرابحة!' : 'تم تسجيل الصفقة كخاسرة')
      : (outcomeType.value === 'won' ? 'Deal marked as Won!' : 'Deal marked as Lost'))
    getOutcomeModal()?.hide()
    fetchDeals()
    fetchStats()
  } catch (err) {
    toast.error(err.response?.data?.message || (locale.value === 'ar' ? 'حدث خطأ' : 'Something went wrong'))
  } finally {
    savingOutcome.value = false
  }
}

const form = ref(emptyForm())
let searchTimer = null

function emptyForm() {
  return { title: '', stage_id: null, status: 'open', value: 0, probability: 50, expected_close_date: '', contact_id: null, notes: '', lost_reason: '' }
}

// ── Vuelidate ─────────────────────────────────────────────
const ar = computed(() => locale.value === 'ar')
const rules = computed(() => ({
  title: {
    required: helpers.withMessage(ar.value ? 'العنوان مطلوب' : 'Title is required', required),
    minLength: helpers.withMessage(ar.value ? 'حرفين على الأقل' : 'At least 2 characters', minLength(2)),
    maxLength: helpers.withMessage(ar.value ? 'الحد 100 حرف' : 'Max 100 characters', maxLength(100)),
  },
  stage_id: {
    required: helpers.withMessage(ar.value ? 'المرحلة مطلوبة' : 'Stage is required', required),
  },
  value: {
    minValue: helpers.withMessage(ar.value ? 'القيمة يجب أن تكون 0 أو أكثر' : 'Value must be 0 or more', minValue(0)),
  },
  probability: {
    minValue: helpers.withMessage(ar.value ? 'بين 0 و100' : 'Between 0 and 100', minValue(0)),
    maxValue: helpers.withMessage(ar.value ? 'بين 0 و100' : 'Between 0 and 100', maxValue(100)),
  },
}))
const v$ = useVuelidate(rules, form)

// ── Computed options ──────────────────────────────────────
const stageOptions = computed(() => stages.value)
const statusOptions = computed(() => [
  { label: locale.value==='ar' ? 'مفتوحة' : 'Open',  value: 'open'  },
  { label: locale.value==='ar' ? 'رابحة'  : 'Won',   value: 'won'   },
  { label: locale.value==='ar' ? 'خاسرة'  : 'Lost',  value: 'lost'  },
])
const contactOptions = computed(() => contacts.value.map(c => ({
  label: `${c.first_name} ${c.last_name}${c.company ? ' — ' + c.company : ''}`,
  value: c.id,
})))

// ── Date bridge ───────────────────────────────────────────
const closeDate = computed({
  get: () => form.value.expected_close_date ? new Date(form.value.expected_close_date + 'T00:00:00') : null,
  set: (d) => {
    form.value.expected_close_date = d
      ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
      : ''
  },
})

// ── Pagination ────────────────────────────────────────────
const pages = computed(() => {
  const t = meta.value.last_page || 1, c = page.value, arr = []
  for (let p = Math.max(1, c - 2); p <= Math.min(t, c + 2); p++) arr.push(p)
  return arr
})

// ── Helpers ───────────────────────────────────────────────
const COLORS = ['#2563EB','#7C3AED','#0891B2','#059669','#D97706','#DC2626','#DB2777']
function dealColor(title) {
  const code = (title || 'D').charCodeAt(0)
  return COLORS[code % COLORS.length]
}
function probColor(p) {
  if (p >= 70) return '#10B981'
  if (p >= 40) return '#F59E0B'
  return '#EF4444'
}
function formatDate(dateStr) {
  if (!dateStr) return null
  const d = new Date(dateStr.substring(0, 10) + 'T00:00:00')
  if (isNaN(d.getTime())) return null
  return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`
}
function isOverdue(deal) {
  if (!deal.expected_close_date || deal.status !== 'open') return false
  const d = new Date(deal.expected_close_date.substring(0, 10) + 'T00:00:00')
  return !isNaN(d.getTime()) && d < new Date()
}
function statusLabel(s) {
  const map = { open: locale.value==='ar'?'مفتوحة':'Open', won: locale.value==='ar'?'رابحة':'Won', lost: locale.value==='ar'?'خاسرة':'Lost' }
  return map[s] || s
}
function filterByStatus(s) {
  filterStatus.value = filterStatus.value === s ? '' : s
  page.value = 1
  fetchDeals()
}
function clearFilter() {
  filterStatus.value = ''
  filterStage.value  = ''
  search.value = ''
  page.value = 1
  fetchDeals()
}
function debouncedFetch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; fetchDeals() }, 400)
}

// ── Modal helpers ─────────────────────────────────────────
function getFormModal()   { return window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dealListFormModal')) }
function getDeleteModal() { return window.bootstrap.Modal.getOrCreateInstance(document.getElementById('dealListDeleteModal')) }

function openAddModal() {
  formMode.value    = 'create'
  editingDeal.value = null
  form.value        = emptyForm()
  if (stages.value.length) form.value.stage_id = stages.value[0].id
  v$.value.$reset()
  nextTick(() => getFormModal().show())
}
function openEditModal(deal) {
  formMode.value    = 'edit'
  editingDeal.value = deal
  form.value = {
    title:               deal.title || '',
    stage_id:            deal.stage_id,
    status:              deal.status || 'open',
    value:               Number(deal.value || 0),
    probability:         Number(deal.probability || 0),
    expected_close_date: deal.expected_close_date ? deal.expected_close_date.substring(0, 10) : '',
    contact_id:          deal.contact_id || null,
    notes:               deal.notes || '',
    lost_reason:         deal.lost_reason || '',
  }
  v$.value.$reset()
  nextTick(() => getFormModal().show())
}
function confirmDelete(deal) {
  deletingDeal.value = deal
  getDeleteModal().show()
}
function switchToDelete() {
  getFormModal().hide()
  nextTick(() => {
    deletingDeal.value = editingDeal.value
    getDeleteModal().show()
  })
}
function cancelDelete() {
  getDeleteModal().hide()
  deletingDeal.value = null
}

// ── API ───────────────────────────────────────────────────
async function fetchDeals() {
  loading.value = true
  try {
    const { data } = await api.get('/deals', {
      params: { search: search.value, status: filterStatus.value, stage_id: filterStage.value, page: page.value, per_page: 15 }
    })
    deals.value = data.data
    meta.value  = data.meta || {}
  } catch {
    toast.error(locale.value==='ar' ? 'فشل تحميل الصفقات' : 'Failed to load deals')
  }
  loading.value = false
}

async function fetchStats() {
  try {
    const [total, open, won, lost] = await Promise.all([
      api.get('/deals', { params: { per_page: 1 } }),
      api.get('/deals', { params: { status: 'open',  per_page: 1 } }),
      api.get('/deals', { params: { status: 'won',   per_page: 1 } }),
      api.get('/deals', { params: { status: 'lost',  per_page: 1 } }),
    ])
    stats.value = {
      total: total.data.meta?.total ?? 0,
      open:  open.data.meta?.total  ?? 0,
      won:   won.data.meta?.total   ?? 0,
      lost:  lost.data.meta?.total  ?? 0,
    }
  } catch {}
}

async function fetchStages() {
  try {
    const { data } = await api.get('/deal-stages')
    stages.value = data.data
  } catch {}
}

async function fetchContacts() {
  try {
    const { data } = await api.get('/contacts', { params: { per_page: 200 } })
    contacts.value = data.data
  } catch {}
}

async function saveDeal() {
  const valid = await v$.value.$validate()
  if (!valid) return
  saving.value = true
  try {
    if (formMode.value === 'create') {
      await api.post('/deals', form.value)
      toast.success(locale.value==='ar' ? 'تم إضافة الصفقة بنجاح' : 'Deal added successfully')
    } else {
      await api.put(`/deals/${editingDeal.value.id}`, form.value)
      toast.success(locale.value==='ar' ? 'تم تحديث الصفقة بنجاح' : 'Deal updated successfully')
    }
    getFormModal().hide()
    await fetchDeals()
    fetchStats()
  } catch (e) {
    toast.error(e.response?.data?.message || (locale.value==='ar' ? 'حدث خطأ' : 'An error occurred'))
  }
  saving.value = false
}

async function deleteDeal() {
  if (!deletingDeal.value) return
  deleting.value = true
  try {
    await api.delete(`/deals/${deletingDeal.value.id}`)
    toast.success(locale.value==='ar' ? 'تم حذف الصفقة بنجاح' : 'Deal deleted successfully')
    getDeleteModal().hide()
    deletingDeal.value = null
    await fetchDeals()
    fetchStats()
  } catch {
    toast.error(locale.value==='ar' ? 'فشل الحذف' : 'Delete failed')
  }
  deleting.value = false
}

onMounted(async () => {
  await Promise.all([fetchStages(), fetchContacts()])
  fetchDeals()
  fetchStats()
})
</script>

<style scoped>
/* ─── Stats ──────────────────────────────── */
.stat-card    { cursor:pointer;transition:all .15s; }
.stat-card:hover { transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08); }
.stat-active  { border-color:#2563EB !important;box-shadow:0 0 0 3px rgba(37,99,235,.12) !important; }
.stat-icon    { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.stat-number  { font-size:1.5rem;font-weight:800;line-height:1.2;color:var(--bs-body-color); }
.stat-label   { font-size:11px;color:var(--bs-secondary-color);font-weight:500;margin-top:2px; }

/* ─── Table ──────────────────────────────── */
.deals-table  { border-collapse:separate;border-spacing:0; }
.deals-table thead th {
  background:var(--bs-tertiary-bg,#F8FAFC);
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;
  color:var(--bs-secondary-color);padding:.75rem 1rem;border-bottom:1px solid var(--bs-border-color);
  white-space:nowrap;
}
.deal-row { transition:background .1s; }
.deal-row:hover { background:rgba(37,99,235,.03); }
.deals-table td { padding:.85rem 1rem;vertical-align:middle;border-bottom:1px solid var(--bs-border-color); }
.deals-table tbody tr:last-child td { border-bottom:none; }

.deal-avatar { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0; }
.deal-name   { font-weight:600;font-size:13px;color:var(--bs-body-color); }
.deal-value  { font-weight:800;color:#2563EB;font-size:13px;white-space:nowrap; }

.stage-badge { display:inline-flex;align-items:center;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }

.deal-status-badge { display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px; }
.status-open { background:rgba(37,99,235,.1);color:#2563EB; }
.status-won  { background:rgba(16,185,129,.1);color:#059669; }
.status-lost { background:rgba(239,68,68,.1);color:#DC2626; }

.prob-bar-wrap  { width:48px;height:4px;background:var(--bs-border-color,#E2E8F0);border-radius:2px;overflow:hidden; }
.prob-bar-fill  { height:100%;border-radius:2px;transition:width .3s; }
.prob-text      { font-size:11px;color:var(--bs-secondary-color);white-space:nowrap; }

/* ─── No-date badge ──────────────────────── */
.no-date-badge { display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:500;color:var(--bs-secondary-color);background:var(--bs-tertiary-bg,#F1F5F9);padding:2px 8px;border-radius:20px; }

/* ─── Empty state ────────────────────────── */
.empty-icon { width:80px;height:80px;border-radius:20px;background:var(--bs-tertiary-bg,#f8f9fa);display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--bs-secondary-color);margin:0 auto; }

/* ─── Modal ──────────────────────────────── */
.modal-icon  { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0; }
.fsect-title { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--bs-secondary-color);padding-bottom:.5rem;margin-bottom:.875rem;border-bottom:1px solid var(--bs-border-color);margin-top:.25rem; }

/* ─── Delete modal ───────────────────────── */
.del-icon { width:72px;height:72px;border-radius:50%;background:rgba(239,68,68,.1);color:#DC2626;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto; }

/* ─── Soft buttons ───────────────────────── */
.btn-soft-success { background:rgba(16,185,129,.12);color:#059669;border:none; }
.btn-soft-success:hover { background:rgba(16,185,129,.22); }
.btn-soft-danger  { background:rgba(239,68,68,.12);color:#DC2626;border:none; }
.btn-soft-danger:hover  { background:rgba(239,68,68,.22); }

/* ─── Outcome modal icon ─────────────────── */
.outcome-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.icon-won  { background:rgba(16,185,129,.15);color:#059669; }
.icon-lost { background:rgba(239,68,68,.15);color:#DC2626; }

/* ─── Probability preview ────────────────── */
.prob-preview-bar  { height:6px;border-radius:3px;background:var(--bs-border-color,#E2E8F0);overflow:hidden; }
.prob-preview-fill { height:100%;border-radius:3px;transition:width .3s,background .3s; }

/* ─── PrimeVue inside Bootstrap modal ─────── */
:deep(.p-datepicker)       { width:100%; }
:deep(.p-datepicker-input) { width:100%;border-radius:var(--bs-border-radius);border:1px solid var(--bs-border-color);padding:.375rem .75rem;font-size:.875rem;background:var(--bs-body-bg);color:var(--bs-body-color); }
:deep(.p-datepicker-input:focus) { border-color:#2563EB;outline:none;box-shadow:0 0 0 .2rem rgba(37,99,235,.2); }
:deep(.p-select)           { width:100%; }
:deep(.p-select-label)     { font-size:.875rem;padding:.375rem .75rem; }
:deep(.p-datepicker-panel),
:deep(.p-select-overlay)   { z-index:9999 !important; }

/* ─── Dark mode ──────────────────────────── */
[data-bs-theme="dark"] .deals-table thead th { background:var(--bs-dark-bg-subtle); }
[data-bs-theme="dark"] .deal-row:hover { background:rgba(255,255,255,.04); }
</style>
