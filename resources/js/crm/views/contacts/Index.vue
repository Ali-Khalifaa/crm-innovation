<template>
  <CrmLayout>
    <PlanLimitBanner
      v-if="planMaxContacts > 0"
      :current="stats.total"
      :limit="planMaxContacts"
      label="contacts"
      @upgrade="showUpgrade = true"
    />
    <UpgradeModal :show="showUpgrade" @close="showUpgrade = false" />

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
      <div>
        <h4 class="mb-0 fw-bold" style="font-size:1.25rem">{{ locale === 'ar' ? 'جهات الاتصال' : 'Contacts' }}</h4>
        <p class="text-muted mb-0 small mt-1">{{ locale === 'ar' ? 'إدارة جهات الاتصال والعملاء المحتملين' : 'Manage your contacts and leads' }}</p>
      </div>
      <div class="d-flex gap-2 flex-shrink-0">
        <button class="btn btn-sm btn-outline-light" @click="openImportModal">
          <i class="bi bi-upload me-1"></i>{{ locale === 'ar' ? 'استيراد' : 'Import' }}
        </button>
        <a :href="`/api/crm/contacts/export?token=${token}`" class="btn btn-sm btn-outline-light">
          <i class="bi bi-file-earmark-excel me-1" style="color:#16A34A"></i>
          {{ locale === 'ar' ? 'تصدير' : 'Export' }}
        </a>
        <button class="btn btn-sm btn-primary" @click="openAddModal">
          <i class="bi bi-plus-lg me-1"></i>{{ locale === 'ar' ? 'جهة اتصال جديدة' : 'New Contact' }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" @click="clearFilter">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-people fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.total }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'إجمالي جهات الاتصال' : 'Total Contacts' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': statusFilter==='lead'}" @click="filterByStatus('lead')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#D97706"><i class="bi bi-funnel fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.leads }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'عملاء محتملون' : 'Leads' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': statusFilter==='customer'}" @click="filterByStatus('customer')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#059669"><i class="bi bi-person-check fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.customers }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'عملاء' : 'Customers' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="card card-border stat-card" :class="{'stat-active': statusFilter==='inactive'}" @click="filterByStatus('inactive')">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="stat-icon" style="background:rgba(100,116,139,.1);color:#64748B"><i class="bi bi-person-dash fs-5"></i></div>
              <div>
                <div class="stat-number">{{ stats.inactive }}</div>
                <div class="stat-label">{{ locale === 'ar' ? 'غير نشطون' : 'Inactive' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="card card-border contacts-card">
      <!-- Bulk action bar -->
      <div v-if="selectedIds.size > 0" class="bulk-action-bar d-flex align-items-center gap-2 px-4 py-2">
        <span class="text-muted small me-1">{{ selectedIds.size }} {{ locale==='ar'?'محدد':'selected' }}</span>
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            {{ locale==='ar'?'الإجراءات':'Actions' }}
          </button>
          <ul class="dropdown-menu">
            <li>
              <button class="dropdown-item" @click="bulkChangeStatus('lead')">
                <span class="badge badge-soft-warning me-2">Lead</span> {{ locale==='ar'?'تعليم كعميل محتمل':'Mark as Lead' }}
              </button>
            </li>
            <li>
              <button class="dropdown-item" @click="bulkChangeStatus('customer')">
                <span class="badge badge-soft-success me-2">Customer</span> {{ locale==='ar'?'تعليم كعميل':'Mark as Customer' }}
              </button>
            </li>
            <li>
              <button class="dropdown-item" @click="bulkChangeStatus('inactive')">
                <span class="badge badge-soft-secondary me-2">Inactive</span> {{ locale==='ar'?'تعليم كغير نشط':'Mark as Inactive' }}
              </button>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <button class="dropdown-item text-danger" @click="confirmBulkDelete">
                <i class="bi bi-trash3 me-2"></i>{{ locale==='ar'?'حذف المحدد':'Delete Selected' }}
              </button>
            </li>
          </ul>
        </div>
        <button class="btn btn-sm btn-link text-muted" @click="clearSelection">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
      </div>

      <!-- Header -->
      <div class="card-header card-header-action flex-wrap gap-2">
        <h6 class="mb-0 d-flex align-items-center gap-2 flex-wrap">
          <span>{{ locale === 'ar' ? 'قائمة جهات الاتصال' : 'All Contacts' }}</span>
          <span class="badge badge-soft-primary">{{ meta.total ?? 0 }}</span>
          <button v-if="statusFilter" class="btn btn-xs badge-soft-warning d-flex align-items-center gap-1 border-0" @click="clearFilter">
            {{ statusLabel(statusFilter) }} <i class="bi bi-x"></i>
          </button>
        </h6>
        <div class="card-action-wrap d-flex gap-2 flex-wrap">
          <div class="input-group" style="width:210px">
            <span class="input-group-text bg-transparent border-end-0 ps-2">
              <i class="bi bi-search text-muted" style="font-size:13px"></i>
            </span>
            <input v-model="search" @input="debouncedFetch" type="text"
              class="form-control border-start-0 ps-1"
              :placeholder="locale === 'ar' ? 'بحث...' : 'Search contacts...'" />
          </div>
          <select v-model="statusFilter" @change="page=1;fetchContacts()" class="form-select" style="width:auto">
            <option value="">{{ locale === 'ar' ? 'كل الحالات' : 'All Status' }}</option>
            <option value="lead">{{ locale === 'ar' ? 'عميل محتمل' : 'Lead' }}</option>
            <option value="customer">{{ locale === 'ar' ? 'عميل' : 'Customer' }}</option>
            <option value="inactive">{{ locale === 'ar' ? 'غير نشط' : 'Inactive' }}</option>
          </select>
          <select v-model="sourceFilter" @change="page=1;fetchContacts()" class="form-select" style="width:auto">
            <option value="">{{ locale === 'ar' ? 'كل المصادر' : 'All Sources' }}</option>
            <option value="website">{{ locale === 'ar' ? 'الموقع' : 'Website' }}</option>
            <option value="referral">{{ locale === 'ar' ? 'إحالة' : 'Referral' }}</option>
            <option value="social">{{ locale === 'ar' ? 'سوشيال' : 'Social' }}</option>
            <option value="cold">{{ locale === 'ar' ? 'مباشر' : 'Cold' }}</option>
            <option value="event">{{ locale === 'ar' ? 'فعالية' : 'Event' }}</option>
            <option value="other">{{ locale === 'ar' ? 'أخرى' : 'Other' }}</option>
          </select>
          <select v-model="companyFilter" @change="page=1;fetchContacts()" class="form-select" style="width:auto" v-if="companiesList.length">
            <option value="">{{ locale === 'ar' ? 'كل الشركات' : 'All Companies' }}</option>
            <option v-for="c in companiesList" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height:260px">
        <div class="text-center">
          <div class="spinner-border text-primary mb-2" role="status"></div>
          <div class="text-muted small">{{ locale === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="!contacts.length" class="text-center py-5 px-4">
        <div class="empty-icon mb-3"><i class="bi bi-people"></i></div>
        <h6 class="mb-1">{{ search || statusFilter ? (locale==='ar' ? 'لا توجد نتائج' : 'No results found') : (locale==='ar' ? 'لا توجد جهات اتصال بعد' : 'No contacts yet') }}</h6>
        <p class="text-muted small mb-3">
          {{ search || statusFilter
            ? (locale==='ar' ? 'جرّب تغيير معايير البحث' : 'Try changing your search or filter')
            : (locale==='ar' ? 'أضف أول جهة اتصال لتبدأ' : 'Add your first contact to get started') }}
        </p>
        <button v-if="search || statusFilter" class="btn btn-sm btn-outline-secondary me-2" @click="clearFilter">
          <i class="bi bi-x-lg me-1"></i>{{ locale==='ar' ? 'مسح الفلتر' : 'Clear Filter' }}
        </button>
        <button v-else class="btn btn-sm btn-primary" @click="openAddModal">
          <i class="bi bi-plus-lg me-1"></i>{{ locale==='ar' ? 'إضافة جهة اتصال' : 'Add Contact' }}
        </button>
      </div>

      <!-- Table -->
      <div v-else>
        <div class="table-responsive">
          <table class="table contacts-table mb-0">
            <thead>
              <tr>
                <th class="ps-3" style="width:40px">
                  <input type="checkbox" class="form-check-input" :checked="allOnPageSelected" :indeterminate.prop="someSelected" @change="toggleSelectAll" />
                </th>
                <th>{{ locale==='ar' ? 'جهة الاتصال' : 'Contact' }}</th>
                <th class="d-none d-lg-table-cell">{{ locale==='ar' ? 'البريد' : 'Email' }}</th>
                <th>{{ locale==='ar' ? 'الهاتف' : 'Phone' }}</th>
                <th class="d-none d-md-table-cell">{{ locale==='ar' ? 'الشركة' : 'Company' }}</th>
                <th>{{ locale==='ar' ? 'الحالة' : 'Status' }}</th>
                <th class="d-none d-xl-table-cell">{{ locale==='ar' ? 'تاريخ الإضافة' : 'Added' }}</th>
                <th class="pe-4 text-end">{{ locale==='ar' ? 'إجراءات' : 'Actions' }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="c in contacts" :key="c.id" class="contact-row" :class="{ 'table-active': selectedIds.has(c.id) }">
                <td class="ps-3">
                  <input type="checkbox" class="form-check-input" :checked="selectedIds.has(c.id)" @change="toggleSelect(c.id)" />
                </td>
                <td class="ps-0">
                  <div class="d-flex align-items-center gap-3">
                    <div class="c-avatar" :style="avatarStyle(c)">{{ initials(c) }}</div>
                    <div style="min-width:0">
                      <div class="c-name fw-semibold text-truncate" @click="openDetail(c)">{{ c.first_name }} {{ c.last_name }}</div>
                      <div class="text-muted small text-truncate d-lg-none">{{ c.email || '' }}</div>
                    </div>
                  </div>
                </td>
                <td class="d-none d-lg-table-cell">
                  <a v-if="c.email" :href="`mailto:${c.email}`" class="t-link text-muted small">{{ c.email }}</a>
                  <span v-else class="text-muted">—</span>
                </td>
                <td>
                  <a v-if="c.phone" :href="`tel:${c.phone}`" class="t-link text-muted small">{{ c.phone }}</a>
                  <span v-else class="text-muted">—</span>
                </td>
                <td class="d-none d-md-table-cell text-muted small">{{ c.company || '—' }}</td>
                <td><span class="badge" :class="statusBadge(c.status)">{{ statusLabel(c.status) }}</span></td>
                <td class="d-none d-xl-table-cell text-muted small">{{ formatDate(c.created_at) }}</td>
                <td class="pe-4">
                  <div class="d-flex justify-content-end gap-1">
                    <button class="btn btn-xs btn-outline-light" @click="openDetail(c)" :title="locale==='ar'?'عرض':'View'"><i class="bi bi-eye"></i></button>
                    <button class="btn btn-xs btn-outline-light" @click="openEditModal(c)" :title="locale==='ar'?'تعديل':'Edit'"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-xs btn-soft-danger" @click="confirmDelete(c)" :title="locale==='ar'?'حذف':'Delete'"><i class="bi bi-trash3"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
          <small class="text-muted">
            {{ locale==='ar'
              ? `${(page-1)*(meta.per_page??15)+1}–${Math.min(page*(meta.per_page??15),meta.total)} من ${meta.total}`
              : `${(page-1)*(meta.per_page??15)+1}–${Math.min(page*(meta.per_page??15),meta.total)} of ${meta.total}` }}
          </small>
          <div class="d-flex gap-1">
            <button class="btn btn-xs btn-outline-light" :disabled="page<=1" @click="page--;fetchContacts()"><i class="bi bi-chevron-left"></i></button>
            <button v-for="p in pagesToShow" :key="p" @click="page=p;fetchContacts()"
              class="btn btn-xs" :class="p===page?'btn-primary':'btn-outline-light'">{{ p }}</button>
            <button class="btn btn-xs btn-outline-light" :disabled="page>=meta.last_page" @click="page++;fetchContacts()"><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>
      </div>
    </div><!-- /card -->


    <!-- ═══ ADD / EDIT MODAL ═══ -->
    <div class="modal fade" id="contactFormModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header pb-0 border-0">
            <div class="d-flex align-items-center gap-3">
              <div class="modal-icon" :style="formMode==='create'?'background:rgba(37,99,235,.1);color:#2563EB':'background:rgba(124,58,237,.1);color:#7C3AED'">
                <i class="bi" :class="formMode==='create'?'bi-person-plus':'bi-pencil-square'"></i>
              </div>
              <div>
                <h5 class="modal-title mb-0">
                  {{ formMode==='create' ? (locale==='ar'?'إضافة جهة اتصال جديدة':'Add New Contact') : (locale==='ar'?'تعديل جهة الاتصال':'Edit Contact') }}
                </h5>
                <p class="text-muted small mb-0 mt-1">
                  {{ formMode==='create' ? (locale==='ar'?'أدخل بيانات جهة الاتصال':'Fill in the contact details') : (locale==='ar'?'قم بتحديث البيانات أدناه':'Update the information below') }}
                </p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body pt-4">
            <form @submit.prevent="saveContact" id="contactForm">

              <div class="fsect-title"><i class="bi bi-person me-2 text-primary"></i>{{ locale==='ar'?'المعلومات الأساسية':'Basic Information' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'الاسم الأول':'First Name' }} <span class="text-danger">*</span></label>
                  <input
                    v-model="form.first_name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': v$.first_name.$error, 'is-valid': v$.first_name.$dirty && !v$.first_name.$error && form.first_name }"
                    :placeholder="locale==='ar'?'مثال: محمد':'e.g. John'"
                    @blur="v$.first_name.$touch()"
                  />
                  <div class="invalid-feedback" v-if="v$.first_name.$error">
                    {{ v$.first_name.$errors[0]?.$message }}
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'الاسم الأخير':'Last Name' }}</label>
                  <input
                    v-model="form.last_name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': v$.last_name.$error }"
                    :placeholder="locale==='ar'?'مثال: أحمد':'e.g. Smith'"
                    @blur="v$.last_name.$touch()"
                  />
                  <div class="invalid-feedback" v-if="v$.last_name.$error">
                    {{ v$.last_name.$errors[0]?.$message }}
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'الحالة':'Status' }} <span class="text-danger">*</span></label>
                  <Select
                    v-model="form.status"
                    :options="statusOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'اختر الحالة':'Select status'"
                    :invalid="v$.status.$error"
                    class="w-100"
                    append-to="body"
                    @blur="v$.status.$touch()"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'الشركة':'Company' }}</label>
                  <input
                    v-model="form.company"
                    type="text"
                    class="form-control"
                    :placeholder="locale==='ar'?'اسم الشركة':'Company name'"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'مصدر العميل':'Lead Source' }}</label>
                  <Select
                    v-model="form.lead_source"
                    :options="leadSourceOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'— اختر المصدر —':'— Select source —'"
                    :show-clear="true"
                    class="w-100"
                    append-to="body"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'ربط بشركة':'Link to Company' }}</label>
                  <Select
                    v-model="form.company_id"
                    :options="companyOptions"
                    option-label="label"
                    option-value="value"
                    :placeholder="locale==='ar'?'— بدون شركة —':'— No company —'"
                    :show-clear="true"
                    :filter="true"
                    class="w-100"
                    append-to="body"
                  />
                </div>
              </div>

              <div class="fsect-title"><i class="bi bi-telephone me-2 text-primary"></i>{{ locale==='ar'?'معلومات التواصل':'Contact Details' }}</div>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'البريد الإلكتروني':'Email' }}</label>
                  <input
                    v-model="form.email"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': v$.email.$error, 'is-valid': v$.email.$dirty && !v$.email.$error && form.email }"
                    placeholder="example@domain.com"
                    @blur="v$.email.$touch()"
                  />
                  <div class="invalid-feedback" v-if="v$.email.$error">
                    {{ v$.email.$errors[0]?.$message }}
                  </div>
                  <!-- server-side error fallback -->
                  <div class="invalid-feedback d-block" v-if="!v$.email.$error && formErrors.email">
                    {{ formErrors.email[0] }}
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ locale==='ar'?'رقم الهاتف':'Phone' }}</label>
                  <input
                    v-model="form.phone"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': v$.phone.$error, 'is-valid': v$.phone.$dirty && !v$.phone.$error && form.phone }"
                    :placeholder="locale==='ar'?'+966 5X XXX XXXX':'+1 (555) 000-0000'"
                    @blur="v$.phone.$touch()"
                  />
                  <div class="invalid-feedback" v-if="v$.phone.$error">
                    {{ v$.phone.$errors[0]?.$message }}
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">{{ locale==='ar'?'العنوان':'Address' }}</label>
                  <input
                    v-model="form.address"
                    type="text"
                    class="form-control"
                    :placeholder="locale==='ar'?'الشارع، المدينة، الدولة':'Street, City, Country'"
                  />
                </div>
              </div>

              <div class="fsect-title"><i class="bi bi-card-text me-2 text-primary"></i>{{ locale==='ar'?'ملاحظات':'Notes' }}</div>
              <textarea v-model="form.notes" class="form-control" rows="3"
                :placeholder="locale==='ar'?'أضف أي ملاحظات مهمة...':'Add any important notes...'"></textarea>
            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="submit" form="contactForm" class="btn btn-primary px-4" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
              {{ saving ? (locale==='ar'?'جاري الحفظ...':'Saving...') : (formMode==='create' ? (locale==='ar'?'إضافة جهة الاتصال':'Add Contact') : (locale==='ar'?'حفظ التغييرات':'Save Changes')) }}
            </button>
          </div>
        </div>
      </div>
    </div><!-- /contactFormModal -->


    <!-- ═══ DELETE CONFIRM MODAL ═══ -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content">
          <div class="modal-body text-center px-5 pt-5 pb-3">
            <div class="del-icon mb-4"><i class="bi bi-trash3"></i></div>
            <h5 class="mb-2">{{ locale==='ar'?'تأكيد الحذف':'Confirm Delete' }}</h5>
            <p class="text-muted small mb-0">
              {{ locale==='ar'
                ? `هل أنت متأكد من حذف "${deleteTarget?.first_name} ${deleteTarget?.last_name}"؟ لا يمكن التراجع.`
                : `Delete "${deleteTarget?.first_name} ${deleteTarget?.last_name}"? This cannot be undone.` }}
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 pb-4 gap-2">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ locale==='ar'?'إلغاء':'Cancel' }}</button>
            <button type="button" class="btn btn-danger px-4" @click="executeDelete" :disabled="deleting">
              <span v-if="deleting" class="spinner-border spinner-border-sm me-1"></span>
              {{ locale==='ar'?'نعم، احذف':'Yes, Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div><!-- /deleteConfirmModal -->


    <!-- ═══ CONTACT DETAIL OFFCANVAS ═══ -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="contactDetailOffcanvas" aria-labelledby="cdLabel" style="width:400px">
      <div class="offcanvas-header border-bottom">
        <h6 class="offcanvas-title d-flex align-items-center gap-2" id="cdLabel">
          <i class="bi bi-person-lines-fill text-primary"></i>{{ locale==='ar'?'تفاصيل جهة الاتصال':'Contact Details' }}
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
      </div>
      <div v-if="detail" class="offcanvas-body p-0" style="overflow-y:auto">
        <!-- Hero -->
        <div class="cd-hero">
          <div class="cd-avatar" :style="avatarStyle(detail)">{{ initials(detail) }}</div>
          <h5 class="mb-1 mt-3">{{ detail.first_name }} {{ detail.last_name }}</h5>
          <span class="badge" :class="statusBadge(detail.status)">{{ statusLabel(detail.status) }}</span>
          <div class="d-flex gap-2 justify-content-center mt-3">
            <button class="btn btn-sm btn-primary" @click="openEditFromDetail">
              <i class="bi bi-pencil me-1"></i>{{ locale==='ar'?'تعديل':'Edit' }}
            </button>
            <button class="btn btn-sm btn-outline-danger" @click="confirmDeleteFromDetail">
              <i class="bi bi-trash3"></i>
            </button>
            <router-link :to="`/crm/contacts/${detail.id}`" class="btn btn-sm btn-outline-secondary" @click="closeDetail">
              <i class="bi bi-box-arrow-up-right"></i>
            </router-link>
          </div>
        </div>

        <!-- Info -->
        <div class="p-4">
          <div class="cd-section-title">{{ locale==='ar'?'معلومات التواصل':'Contact Information' }}</div>
          <div class="cd-list">
            <div class="cd-item" v-if="detail.email">
              <div class="cd-icon" style="background:rgba(37,99,235,.1);color:#2563EB"><i class="bi bi-envelope"></i></div>
              <div><div class="cd-label">{{ locale==='ar'?'البريد':'Email' }}</div>
                <a :href="`mailto:${detail.email}`" class="text-reset text-decoration-none small">{{ detail.email }}</a></div>
            </div>
            <div class="cd-item" v-if="detail.phone">
              <div class="cd-icon" style="background:rgba(16,185,129,.1);color:#059669"><i class="bi bi-telephone"></i></div>
              <div><div class="cd-label">{{ locale==='ar'?'الهاتف':'Phone' }}</div>
                <a :href="`tel:${detail.phone}`" class="text-reset text-decoration-none small">{{ detail.phone }}</a></div>
            </div>
            <div class="cd-item" v-if="detail.company">
              <div class="cd-icon" style="background:rgba(245,158,11,.1);color:#D97706"><i class="bi bi-building"></i></div>
              <div><div class="cd-label">{{ locale==='ar'?'الشركة':'Company' }}</div><span class="small">{{ detail.company }}</span></div>
            </div>
            <div class="cd-item" v-if="detail.address">
              <div class="cd-icon" style="background:rgba(239,68,68,.1);color:#EF4444"><i class="bi bi-geo-alt"></i></div>
              <div><div class="cd-label">{{ locale==='ar'?'العنوان':'Address' }}</div><span class="small">{{ detail.address }}</span></div>
            </div>
            <div v-if="!detail.email && !detail.phone && !detail.company && !detail.address"
              class="text-muted small text-center py-3">{{ locale==='ar'?'لا توجد معلومات تواصل':'No contact information added' }}</div>
          </div>

          <div v-if="detail.notes" class="mt-4">
            <div class="cd-section-title">{{ locale==='ar'?'ملاحظات':'Notes' }}</div>
            <div class="notes-box">{{ detail.notes }}</div>
          </div>

          <div class="mt-4">
            <div class="cd-section-title">{{ locale==='ar'?'معلومات النظام':'System Info' }}</div>
            <div class="cd-list">
              <div class="cd-item">
                <div class="cd-icon" style="background:rgba(124,58,237,.1);color:#7C3AED"><i class="bi bi-calendar-plus"></i></div>
                <div><div class="cd-label">{{ locale==='ar'?'تاريخ الإضافة':'Date Added' }}</div><span class="small">{{ formatDate(detail.created_at) }}</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="offcanvas-body d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status"></div>
      </div>
    </div><!-- /offcanvas -->

    <!-- Import Modal -->
    <ImportModal v-if="showImportModal" @close="showImportModal=false" @done="onImportDone" />

  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, maxLength, helpers } from '@vuelidate/validators'
import CrmLayout from '../../layouts/CrmLayout.vue'
import PlanLimitBanner from '../../components/PlanLimitBanner.vue'
import UpgradeModal from '../../components/UpgradeModal.vue'
import ImportModal from '../../components/ImportModal.vue'
import Select from 'primevue/select'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store  = useStore()
const toast  = useToast()
const route  = useRoute()
const locale = computed(() => store.state.ui.locale)

const planMaxContacts = computed(() => store.getters['auth/plan']?.max_contacts ?? 0)
const token           = computed(() => store.state.auth.token)
const showUpgrade     = ref(false)

// ── Companies dropdown ─────────────────────────────────
const companiesList = ref([])
async function fetchCompaniesDropdown() {
  try {
    const { data } = await api.get('/companies/dropdown')
    companiesList.value = data.data ?? data
  } catch {}
}
const companyOptions = computed(() =>
  companiesList.value.map(c => ({ label: c.name, value: c.id }))
)

const statusOptions = computed(() => [
  { label: locale.value === 'ar' ? 'عميل محتمل' : 'Lead',     value: 'lead' },
  { label: locale.value === 'ar' ? 'عميل'       : 'Customer',  value: 'customer' },
  { label: locale.value === 'ar' ? 'غير نشط'    : 'Inactive',  value: 'inactive' },
])

const leadSourceOptions = computed(() => [
  { label: locale.value === 'ar' ? 'الموقع الإلكتروني'       : 'Website',      value: 'website'  },
  { label: locale.value === 'ar' ? 'إحالة'                   : 'Referral',     value: 'referral' },
  { label: locale.value === 'ar' ? 'وسائل التواصل الاجتماعي' : 'Social Media', value: 'social'   },
  { label: locale.value === 'ar' ? 'تواصل مباشر'             : 'Cold Outreach',value: 'cold'     },
  { label: locale.value === 'ar' ? 'فعالية'                  : 'Event',        value: 'event'    },
  { label: locale.value === 'ar' ? 'أخرى'                    : 'Other',        value: 'other'    },
])

// ── Stats ──────────────────────────────────────────────
const stats = ref({ total: 0, leads: 0, customers: 0, inactive: 0 })

// ── List ───────────────────────────────────────────────
const contacts     = ref([])
const meta         = ref({})
const loading      = ref(true)
const search       = ref('')
const statusFilter = ref('')
const sourceFilter = ref('')
const companyFilter= ref('')
const page         = ref(1)
let   searchTimer  = null

const pagesToShow = computed(() => {
  const last = meta.value.last_page ?? 1
  const cur  = page.value
  const pages = []
  for (let i = Math.max(1, cur - 2); i <= Math.min(last, cur + 2); i++) pages.push(i)
  return pages
})

// ── Form ───────────────────────────────────────────────
const formMode   = ref('create')
const editingId  = ref(null)
const formErrors = ref({})
const saving     = ref(false)
const form       = ref(emptyForm())

function emptyForm() {
  return { first_name: '', last_name: '', email: '', phone: '', company: '', address: '', notes: '', status: 'lead', lead_source: null, company_id: null }
}

// ── Vuelidate rules ────────────────────────────────────
const validationRules = computed(() => {
  const ar = locale.value === 'ar'
  return {
    first_name: {
      required:  helpers.withMessage(ar ? 'الاسم الأول مطلوب'            : 'First name is required',    required),
      minLength: helpers.withMessage(ar ? 'يجب أن يكون حرفين على الأقل'  : 'At least 2 characters',     minLength(2)),
      maxLength: helpers.withMessage(ar ? 'الحد الأقصى 50 حرفاً'          : 'Max 50 characters',          maxLength(50)),
    },
    last_name: {
      maxLength: helpers.withMessage(ar ? 'الحد الأقصى 50 حرفاً'          : 'Max 50 characters',          maxLength(50)),
    },
    email: {
      validEmail: helpers.withMessage(
        ar ? 'البريد الإلكتروني غير صحيح' : 'Invalid email address',
        (v) => !v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)
      ),
    },
    phone: {
      validPhone: helpers.withMessage(
        ar ? 'رقم الهاتف غير صحيح (أرقام وعلامات فقط، 7-20 خانة)' : 'Invalid phone (digits and symbols only, 7-20 chars)',
        (v) => !v || /^[\d\s\+\-\(\)\.]{7,20}$/.test(v)
      ),
    },
    company:     {},
    address:     {},
    notes:       {},
    lead_source: {},
    company_id:  {},
    status: {
      required: helpers.withMessage(ar ? 'الحالة مطلوبة' : 'Status is required', required),
    },
  }
})
const v$ = useVuelidate(validationRules, form)

// ── Bulk selection ─────────────────────────────────────
const selectedIds = ref(new Set())

const allOnPageSelected = computed(() =>
  contacts.value.length > 0 && contacts.value.every(c => selectedIds.value.has(c.id))
)
const someSelected = computed(() =>
  selectedIds.value.size > 0 && !allOnPageSelected.value
)

function toggleSelectAll() {
  if (allOnPageSelected.value) {
    contacts.value.forEach(c => selectedIds.value.delete(c.id))
  } else {
    contacts.value.forEach(c => selectedIds.value.add(c.id))
  }
  selectedIds.value = new Set(selectedIds.value) // trigger reactivity
}

function toggleSelect(id) {
  const s = new Set(selectedIds.value)
  if (s.has(id)) s.delete(id)
  else s.add(id)
  selectedIds.value = s
}

function clearSelection() {
  selectedIds.value = new Set()
}

async function bulkChangeStatus(status) {
  if (!selectedIds.value.size) return
  try {
    await api.post('/contacts/bulk', { action: 'change_status', ids: [...selectedIds.value], status })
    toast.success(locale.value === 'ar' ? `تم تحديث ${selectedIds.value.size} جهة اتصال` : `${selectedIds.value.size} contacts updated`)
    clearSelection()
    fetchContacts()
    fetchStats()
  } catch { toast.error(locale.value === 'ar' ? 'فشل الإجراء' : 'Action failed') }
}

async function confirmBulkDelete() {
  if (!selectedIds.value.size) return
  const msg = locale.value === 'ar'
    ? `هل تريد حذف ${selectedIds.value.size} جهة اتصال؟ لا يمكن التراجع.`
    : `Delete ${selectedIds.value.size} contacts? This cannot be undone.`
  if (!confirm(msg)) return
  try {
    await api.post('/contacts/bulk', { action: 'delete', ids: [...selectedIds.value] })
    toast.success(locale.value === 'ar' ? 'تم الحذف بنجاح' : 'Deleted successfully')
    clearSelection()
    fetchContacts()
    fetchStats()
  } catch { toast.error(locale.value === 'ar' ? 'فشل الحذف' : 'Delete failed') }
}

// ── Import ─────────────────────────────────────────────
const showImportModal = ref(false)
function openImportModal() { showImportModal.value = true }
function onImportDone()   { showImportModal.value = false; fetchContacts(); fetchStats() }

// ── Delete ─────────────────────────────────────────────
const deleteTarget = ref(null)
const deleting     = ref(false)

// ── Detail ─────────────────────────────────────────────
const detail = ref(null)

// ── Helpers ────────────────────────────────────────────
const COLORS = ['#2563EB','#7C3AED','#0891B2','#059669','#D97706','#DC2626','#DB2777']

function avatarStyle(c) {
  const idx = ((c.first_name || '?').charCodeAt(0) || 0) % COLORS.length
  return `background:${COLORS[idx]};color:#fff;`
}
function initials(c) {
  return ((c.first_name || '?').charAt(0) + (c.last_name || '').charAt(0)).toUpperCase()
}
function statusLabel(s) {
  if (locale.value === 'ar') return { lead:'عميل محتمل', customer:'عميل', inactive:'غير نشط' }[s] || s
  return { lead:'Lead', customer:'Customer', inactive:'Inactive' }[s] || s
}
function statusBadge(s) {
  return { lead:'badge-soft-warning', customer:'badge-soft-success', inactive:'badge-soft-secondary' }[s] || 'badge-soft-secondary'
}
function formatDate(d) {
  if (!d) return '—'
  try { return new Date(d).toLocaleDateString(locale.value === 'ar' ? 'ar-EG' : 'en-US', { year:'numeric', month:'short', day:'numeric' }) }
  catch { return d.substring(0, 10) }
}

// ── Bootstrap instances ────────────────────────────────
function getFormModal()   { return bootstrap.Modal.getOrCreateInstance(document.getElementById('contactFormModal')) }
function getDeleteModal() { return bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteConfirmModal')) }
function getDetailCanvas(){ return bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('contactDetailOffcanvas')) }

// ── Data fetching ──────────────────────────────────────
async function fetchContacts() {
  loading.value = true
  try {
    const { data } = await api.get('/contacts', { params: { search: search.value, status: statusFilter.value, lead_source: sourceFilter.value, company_id: companyFilter.value || undefined, page: page.value } })
    contacts.value = data.data
    meta.value     = data.meta
  } catch {
    toast.error(locale.value === 'ar' ? 'فشل جلب جهات الاتصال' : 'Failed to load contacts')
  } finally {
    loading.value = false
  }
}

async function fetchStats() {
  try {
    const [all, lead, customer, inactive] = await Promise.all([
      api.get('/contacts', { params: { per_page: 1 } }),
      api.get('/contacts', { params: { status: 'lead',     per_page: 1 } }),
      api.get('/contacts', { params: { status: 'customer', per_page: 1 } }),
      api.get('/contacts', { params: { status: 'inactive', per_page: 1 } }),
    ])
    stats.value = {
      total:     all.data.meta?.total      ?? 0,
      leads:     lead.data.meta?.total     ?? 0,
      customers: customer.data.meta?.total ?? 0,
      inactive:  inactive.data.meta?.total ?? 0,
    }
  } catch {}
}

function debouncedFetch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { page.value = 1; fetchContacts() }, 400)
}

function filterByStatus(s) {
  statusFilter.value = statusFilter.value === s ? '' : s
  page.value = 1
  fetchContacts()
}
function clearFilter() {
  search.value       = ''
  statusFilter.value = ''
  sourceFilter.value = ''
  companyFilter.value= ''
  page.value = 1
  fetchContacts()
}

// ── Add / Edit ─────────────────────────────────────────
function openAddModal() {
  formMode.value   = 'create'
  editingId.value  = null
  form.value       = emptyForm()
  formErrors.value = {}
  v$.value.$reset()
  fetchCompaniesDropdown()
  nextTick(() => getFormModal().show())
}

function openEditModal(c) {
  formMode.value   = 'edit'
  editingId.value  = c.id
  formErrors.value = {}
  form.value = {
    first_name: c.first_name || '', last_name: c.last_name || '',
    email: c.email || '', phone: c.phone || '',
    company: c.company || '', address: c.address || '',
    notes: c.notes || '', status: c.status || 'lead',
    lead_source: c.lead_source || null,
    company_id: c.company_id || null,
  }
  v$.value.$reset()
  fetchCompaniesDropdown()
  nextTick(() => getFormModal().show())
}

async function saveContact() {
  const valid = await v$.value.$validate()
  if (!valid) return
  saving.value = true; formErrors.value = {}
  try {
    if (formMode.value === 'create') {
      await api.post('/contacts', form.value)
      toast.success(locale.value === 'ar' ? 'تم إضافة جهة الاتصال بنجاح' : 'Contact added successfully')
    } else {
      await api.put(`/contacts/${editingId.value}`, form.value)
      toast.success(locale.value === 'ar' ? 'تم تحديث جهة الاتصال' : 'Contact updated successfully')
      if (detail.value?.id === editingId.value) Object.assign(detail.value, form.value)
    }
    getFormModal().hide()
    await fetchContacts()
    fetchStats()
  } catch (err) {
    const errors = err.response?.data?.errors
    if (errors) formErrors.value = errors
    else toast.error(err.response?.data?.message || (locale.value === 'ar' ? 'حدث خطأ' : 'Something went wrong'))
  } finally {
    saving.value = false
  }
}

// ── Delete ─────────────────────────────────────────────
function confirmDelete(c) {
  deleteTarget.value = c
  nextTick(() => getDeleteModal().show())
}
function confirmDeleteFromDetail() {
  const c = detail.value
  getDetailCanvas().hide()
  setTimeout(() => { deleteTarget.value = c; nextTick(() => getDeleteModal().show()) }, 300)
}
async function executeDelete() {
  deleting.value = true
  try {
    await api.delete(`/contacts/${deleteTarget.value.id}`)
    toast.success(locale.value === 'ar' ? 'تم حذف جهة الاتصال' : 'Contact deleted')
    getDeleteModal().hide()
    if (contacts.value.length === 1 && page.value > 1) page.value--
    await fetchContacts()
    fetchStats()
  } catch {
    toast.error(locale.value === 'ar' ? 'فشل الحذف' : 'Delete failed')
  } finally {
    deleting.value = false
  }
}

// ── Detail panel ───────────────────────────────────────
function openDetail(c) {
  detail.value = c
  nextTick(() => getDetailCanvas().show())
}
function closeDetail() { getDetailCanvas().hide() }
function openEditFromDetail() {
  const c = detail.value
  closeDetail()
  setTimeout(() => openEditModal(c), 320)
}

onMounted(async () => {
  await fetchContacts()
  fetchStats()
  fetchCompaniesDropdown()
  // Auto-open edit modal when navigated from Contact Show page
  if (route.query.edit) {
    const id = Number(route.query.edit)
    const found = contacts.value.find(c => c.id === id)
    if (found) {
      openEditModal(found)
    } else {
      // Contact not in current page — fetch it
      try {
        const { data } = await api.get(`/contacts/${id}`)
        if (data.data) openEditModal(data.data)
      } catch {}
    }
  }
})
</script>

<style scoped>
/* ─── Stats ─────────────────────── */
.stat-card { cursor: pointer; transition: all .2s ease; border: 1.5px solid var(--bs-border-color) !important; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,.07) !important; border-color: #2563EB !important; }
.stat-card.stat-active { border-color: #2563EB !important; box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important; }
.stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-number { font-size:1.8rem; font-weight:700; line-height:1; color:var(--bs-body-color); }
.stat-label { font-size:11px; color:var(--bs-secondary-color); margin-top:3px; }

/* ─── Table ─────────────────────── */
.contacts-card { overflow:hidden; }
.contacts-table thead th {
  background:var(--bs-tertiary-bg, #f8f9fa);
  font-size:11px; font-weight:600; text-transform:uppercase;
  letter-spacing:.5px; color:var(--bs-secondary-color);
  white-space:nowrap; padding:.875rem .75rem;
  border-bottom:1px solid var(--bs-border-color);
}
.contacts-table tbody td { padding:.85rem .75rem; vertical-align:middle; }
.contact-row { border-bottom:1px solid var(--bs-border-color-translucent); transition:background .15s; }
.contact-row:hover { background:rgba(37,99,235,.03); }
.c-avatar { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; }
.c-name { font-size:14px; cursor:pointer; color:var(--bs-body-color); transition:color .15s; }
.c-name:hover { color:#2563EB !important; }
.t-link:hover { color:#2563EB !important; }

/* Badge-soft-warning fallback */
.badge.badge-soft-warning { background:rgba(245,158,11,.15); color:#B45309; }
.badge.badge-soft-success  { background:rgba(16,185,129,.15); color:#047857; }
.badge.badge-soft-secondary{ background:rgba(100,116,139,.15); color:#4B5563; }
.badge.badge-soft-primary  { background:rgba(37,99,235,.15); color:#1D4ED8; }

/* ─── Modal ─────────────────────── */
.modal-icon { width:44px; height:44px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:19px; flex-shrink:0; }
.fsect-title {
  font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;
  color:var(--bs-secondary-color); padding-bottom:.5rem; margin-bottom:.875rem;
  border-bottom:1px solid var(--bs-border-color); margin-top:.25rem;
}

/* ─── Delete modal ──────────────── */
.del-icon { width:72px; height:72px; border-radius:50%; background:rgba(239,68,68,.1); color:#DC2626; display:flex; align-items:center; justify-content:center; font-size:28px; margin:0 auto; }

/* ─── Empty state ───────────────── */
.empty-icon { width:80px; height:80px; border-radius:20px; background:var(--bs-tertiary-bg,#f8f9fa); display:flex; align-items:center; justify-content:center; font-size:32px; color:var(--bs-secondary-color); margin:0 auto; }

/* ─── Detail offcanvas ──────────── */
.cd-hero { padding:2rem 1.5rem 1.5rem; background:var(--bs-tertiary-bg,#f8f9fa); border-bottom:1px solid var(--bs-border-color); text-align:center; }
.cd-avatar { width:72px; height:72px; border-radius:18px; display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:700; margin:0 auto; }
.cd-section-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--bs-secondary-color); margin-bottom:.75rem; }
.cd-list { display:flex; flex-direction:column; gap:.75rem; }
.cd-item { display:flex; align-items:flex-start; gap:.875rem; }
.cd-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }
.cd-label { font-size:10px; color:var(--bs-secondary-color); margin-bottom:2px; text-transform:uppercase; letter-spacing:.3px; }
.notes-box { background:var(--bs-tertiary-bg,#f8f9fa); border-radius:8px; padding:.875rem; font-size:13px; line-height:1.6; white-space:pre-wrap; }

/* ─── Dark mode ─────────────────── */
[data-bs-theme="dark"] .contacts-table thead th { background:var(--bs-dark-bg-subtle); }
[data-bs-theme="dark"] .contact-row:hover { background:rgba(255,255,255,.04); }

/* --- PrimeVue inside Bootstrap modal --- */
:deep(.p-select)           { width:100%; }
:deep(.p-select-label)     { font-size:.875rem;padding:.375rem .75rem; }
:deep(.p-select-overlay)   { z-index:9999 !important; }
:deep(.p-invalid .p-select-label) { border-color:#EF4444; }
</style>