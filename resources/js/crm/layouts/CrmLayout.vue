<template>
  <div
    class="hk-wrapper"
    data-layout="vertical"
    :data-layout-style="sidebarCollapsed ? 'collapsed' : 'default'"
    :data-hover="sidebarHover ? 'active' : undefined"
    data-menu="light"
    data-footer="simple"
  >

    <!-- ===================== TOP NAVBAR ===================== -->
    <nav class="hk-navbar navbar navbar-expand-xl navbar-light fixed-top">
      <div class="container-fluid">

        <!-- Nav Start -->
        <div class="nav-start-wrap">
          <!-- Mobile toggle -->
          <button
            class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover d-xl-none"
            @click="toggleMobile"
            type="button"
          >
            <span class="icon"><i class="bi bi-list fs-4"></i></span>
          </button>
          <!-- Desktop collapse toggle -->
          <button
            class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover d-none d-xl-inline-flex"
            @click="toggleSidebar"
            type="button"
          >
            <span class="icon"><i class="bi bi-layout-sidebar fs-4"></i></span>
          </button>

          <!-- Search -->
          <div class="dropdown navbar-search ms-1">
            <div class="input-group d-xl-flex d-none">
              <span class="input-affix-wrapper input-search affix-border">
                <input type="text" class="form-control bg-transparent" placeholder="Search..." />
                <span class="input-suffix"><span>/</span></span>
              </span>
            </div>
          </div>
        </div>
        <!-- /Nav Start -->

        <!-- Nav End -->
        <div class="nav-end-wrap">
          <ul class="navbar-nav flex-row align-items-center gap-1">

            <!-- Language Toggle -->
            <li class="nav-item">
              <button
                class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover"
                @click="toggleLang"
                type="button"
                :title="locale === 'ar' ? 'Switch to English' : 'التبديل للعربية'"
              >
                <span class="icon">
                  <span style="font-size:12px;font-weight:700;letter-spacing:0.5px;line-height:1;">
                    {{ locale === 'ar' ? 'EN' : 'ع' }}
                  </span>
                </span>
              </button>
            </li>

            <!-- Dark Mode -->
            <li class="nav-item">
              <button
                class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover"
                @click="toggleDark"
                type="button"
              >
                <span class="icon">
                  <i class="bi" :class="isDark ? 'bi-sun-fill' : 'bi-moon-fill'" style="font-size:1rem;"></i>
                </span>
              </button>
            </li>

            <!-- Notifications -->
            <li class="nav-item" ref="notifDropdownEl">
              <button
                class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover position-relative"
                @click.stop="toggleNotif"
                type="button"
              >
                <span class="icon">
                  <i class="bi bi-bell" style="font-size:1rem;"></i>
                  <span class="badge badge-success badge-indicator position-top-end-overflow-1"></span>
                </span>
              </button>
              <!-- Notification Dropdown -->
              <div
                class="dropdown-menu dropdown-menu-end p-0"
                :class="{ show: notifOpen }"
                style="min-width:350px;"
              >
                <h6 class="dropdown-header px-4 fs-6">
                  {{ trans('notifications.title') }}
                  <button class="btn btn-icon btn-flush-dark btn-rounded flush-soft-hover" type="button">
                    <span class="icon"><i class="bi bi-gear"></i></span>
                  </button>
                </h6>
                <div class="dropdown-body p-2" style="max-height:300px;overflow-y:auto;">
                  <a href="#" class="dropdown-item" @click.prevent>
                    <div class="media">
                      <div class="media-head">
                        <div class="avatar avatar-icon avatar-sm avatar-soft-primary avatar-rounded">
                          <span class="initial-wrap"><i class="bi bi-person-check"></i></span>
                        </div>
                      </div>
                      <div class="media-body">
                        <div class="notifications-text">Welcome to CRM Innovation!</div>
                        <div class="notifications-info">
                          <span class="badge badge-soft-success">System</span>
                          <div class="notifications-time">Just now</div>
                        </div>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="dropdown-item" @click.prevent>
                    <div class="media">
                      <div class="media-head">
                        <div class="avatar avatar-icon avatar-sm avatar-pink avatar-rounded">
                          <span class="initial-wrap"><i class="bi bi-graph-up"></i></span>
                        </div>
                      </div>
                      <div class="media-body">
                        <div class="notifications-text">Check your pipeline — deals need attention</div>
                        <div class="notifications-info">
                          <span class="badge badge-soft-pink">Deals</span>
                          <div class="notifications-time">Today</div>
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="dropdown-footer">
                  <router-link to="/crm/reports" @click="notifOpen = false">
                    <u>View all activity</u>
                  </router-link>
                </div>
              </div>
            </li>

            <!-- User Dropdown -->
            <li class="nav-item ps-2" ref="userDropdownEl">
              <button
                class="btn p-0 border-0 bg-transparent"
                @click.stop="toggleUser"
                type="button"
              >
                <div class="avatar avatar-primary avatar-rounded avatar-xs">
                  <span class="initial-wrap">{{ userInitial }}</span>
                </div>
              </button>
              <div
                class="dropdown-menu dropdown-menu-end"
                :class="{ show: userOpen }"
                style="min-width:240px;"
              >
                <div class="p-2">
                  <div class="media">
                    <div class="media-head me-2">
                      <div class="avatar avatar-primary avatar-sm avatar-rounded">
                        <span class="initial-wrap">{{ userInitial }}</span>
                      </div>
                    </div>
                    <div class="media-body">
                      <div class="fw-medium">{{ userName }}</div>
                      <div class="fs-7 text-muted text-capitalize">{{ userRole }}</div>
                      <div class="fs-7 text-muted">{{ userEmail }}</div>
                    </div>
                  </div>
                </div>
                <div class="dropdown-divider"></div>
                <h6 class="dropdown-header">
                  <span class="badge badge-soft-primary me-1">{{ planName }}</span> Plan
                </h6>
                <router-link class="dropdown-item" to="/crm/settings/company" @click="userOpen = false">
                  <span class="dropdown-icon"><i class="bi bi-gear me-2"></i></span>
                  {{ locale === 'ar' ? 'إعدادات الشركة' : 'Company Settings' }}
                </router-link>
                <router-link class="dropdown-item" to="/crm/settings/team" @click="userOpen = false">
                  <span class="dropdown-icon"><i class="bi bi-people me-2"></i></span>
                  {{ locale === 'ar' ? 'الفريق' : 'Team' }}
                </router-link>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item text-danger w-100 text-start" @click="logout" type="button">
                  <i class="bi bi-box-arrow-right me-2"></i>{{ trans('nav.logout') }}
                </button>
              </div>
            </li>

          </ul>
        </div>
        <!-- /Nav End -->

      </div>
    </nav>
    <!-- /Top Navbar -->

    <!-- ===================== SIDEBAR ===================== -->
    <div class="hk-menu">

      <!-- Brand -->
      <div class="menu-header">
        <span>
          <router-link class="navbar-brand" to="/crm/dashboard">
            <img
              class="brand-img img-fluid"
              src="/images/logo.png"
              alt="CRM Innovation"
              style="max-height:32px;filter:brightness(0) invert(1);"
            />
          </router-link>
          <button
            class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover"
            @click="toggleSidebar"
            type="button"
          >
            <span class="icon">
              <span class="svg-icon fs-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                  stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <line x1="10" y1="12" x2="20" y2="12"/>
                  <line x1="10" y1="12" x2="14" y2="16"/>
                  <line x1="10" y1="12" x2="14" y2="8"/>
                  <line x1="4"  y1="4"  x2="4"  y2="20"/>
                </svg>
              </span>
            </span>
          </button>
        </span>
      </div>
      <!-- /Brand -->

      <!-- Scrollable Menu -->
      <div data-simplebar class="nicescroll-bar">
        <div class="menu-content-wrap">

          <!-- Dashboard -->
          <div class="menu-group">
            <ul class="navbar-nav flex-column">
              <li class="nav-item" :class="{ active: route.path === '/crm/dashboard' }">
                <router-link class="nav-link" to="/crm/dashboard">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="4" y="4" width="16" height="4" rx="1"/>
                        <rect x="4" y="12" width="6" height="8" rx="1"/>
                        <line x1="14" y1="12" x2="20" y2="12"/>
                        <line x1="14" y1="16" x2="20" y2="16"/>
                        <line x1="14" y1="20" x2="20" y2="20"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ trans('nav.dashboard') }}</span>
                  <span class="badge badge-sm badge-soft-pink ms-auto">CRM</span>
                </router-link>
              </li>
            </ul>
          </div>

          <div class="menu-gap"></div>

          <!-- CRM -->
          <div class="menu-group">
            <div class="nav-header"><span>{{ locale === 'ar' ? 'إدارة العملاء' : 'CRM' }}</span></div>
            <ul class="navbar-nav flex-column">

              <!-- Contacts (no sub) -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/contacts') }">
                <router-link class="nav-link" to="/crm/contacts">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ trans('nav.contacts') }}</span>
                </router-link>
              </li>

              <!-- Deals (with submenu) -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/deals') }">
                <a
                  class="nav-link"
                  href="#"
                  @click.prevent="toggleMenu('deals')"
                  :aria-expanded="openMenus.deals"
                >
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="4" y="4" width="6" height="6" rx="1"/>
                        <rect x="14" y="4" width="6" height="6" rx="1"/>
                        <rect x="4" y="14" width="6" height="6" rx="1"/>
                        <rect x="14" y="14" width="6" height="6" rx="1"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ trans('nav.deals') }}</span>
                </a>
                <ul class="nav flex-column nav-children" v-show="openMenus.deals">
                  <li class="nav-item"><ul class="nav flex-column">
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/deals">
                        <span class="nav-link-text">{{ trans('nav.kanban') }}</span>
                      </router-link>
                    </li>
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/deals/list">
                        <span class="nav-link-text">{{ trans('nav.list_view') }}</span>
                      </router-link>
                    </li>
                  </ul></li>
                </ul>
              </li>

              <!-- Tasks (no sub) -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/tasks') }">
                <router-link class="nav-link" to="/crm/tasks">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="2"/>
                        <line x1="9" y1="12" x2="9.01" y2="12"/><line x1="13" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="9.01" y2="16"/><line x1="13" y1="16" x2="15" y2="16"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ trans('nav.tasks') }}</span>
                </router-link>
              </li>

            </ul>
          </div>

          <div class="menu-gap"></div>

          <!-- Finance -->
          <div class="menu-group">
            <div class="nav-header"><span>{{ locale === 'ar' ? 'المالية' : 'Finance' }}</span></div>
            <ul class="navbar-nav flex-column">

              <!-- Invoices (with submenu) -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/invoices') }">
                <a
                  class="nav-link"
                  href="#"
                  @click.prevent="toggleMenu('invoices')"
                  :aria-expanded="openMenus.invoices"
                >
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                        <line x1="9" y1="7" x2="10" y2="7"/>
                        <line x1="9" y1="13" x2="15" y2="13"/>
                        <line x1="13" y1="17" x2="15" y2="17"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ trans('nav.invoices') }}</span>
                </a>
                <ul class="nav flex-column nav-children" v-show="openMenus.invoices">
                  <li class="nav-item"><ul class="nav flex-column">
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/invoices">
                        <span class="nav-link-text">{{ trans('nav.invoice_list') }}</span>
                      </router-link>
                    </li>
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/invoices/create">
                        <span class="nav-link-text">{{ trans('nav.create_invoice') }}</span>
                      </router-link>
                    </li>
                  </ul></li>
                </ul>
              </li>

              <!-- Reports (no sub) -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/reports') }">
                <router-link class="nav-link" to="/crm/reports">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="4" y1="19" x2="20" y2="19"/>
                        <polyline points="4 15 8 9 12 11 16 6 20 10"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ trans('nav.reports') }}</span>
                </router-link>
              </li>

            </ul>
          </div>

          <div class="menu-gap"></div>

          <!-- Account -->
          <div class="menu-group">
            <div class="nav-header"><span>{{ locale === 'ar' ? 'الحساب' : 'Account' }}</span></div>
            <ul class="navbar-nav flex-column">
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/settings') }">
                <a
                  class="nav-link"
                  href="#"
                  @click.prevent="toggleMenu('settings')"
                  :aria-expanded="openMenus.settings"
                >
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ trans('nav.settings') }}</span>
                </a>
                <ul class="nav flex-column nav-children" v-show="openMenus.settings">
                  <li class="nav-item"><ul class="nav flex-column">
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/settings/company">
                        <span class="nav-link-text">{{ trans('nav.company') }}</span>
                      </router-link>
                    </li>
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/settings/team">
                        <span class="nav-link-text">{{ trans('nav.team') }}</span>
                      </router-link>
                    </li>
                  </ul></li>
                </ul>
              </li>
            </ul>
          </div>

          <!-- Upgrade callout -->
          <div class="callout card card-flush bg-primary-light-5 text-center mt-5 w-220p mx-auto">
            <div class="card-body">
              <i class="bi bi-gem fs-3 text-primary d-block mb-2"></i>
              <h6 class="h6">{{ planName }} Plan</h6>
              <p class="p-sm card-text">{{ tenant?.name }}</p>
              <button
                class="btn btn-primary btn-block btn-sm"
                type="button"
                @click="store.commit('ui/SET_PLAN_LIMIT_MODAL', true)"
              >{{ trans('nav.upgrade') }}</button>
            </div>
          </div>

        </div>
      </div>
      <!-- /Scrollable Menu -->
    </div>

    <!-- Mobile Backdrop -->
    <div
      v-if="mobileOpen"
      class="hk-menu-backdrop"
      @click="closeMobile"
    ></div>

    <!-- ===================== MAIN CONTENT ===================== -->
    <div class="hk-pg-wrapper">
      <div class="container-xxl">
        <div class="hk-pg-header pt-7">
          <div class="d-flex flex-wrap justify-content-between flex-1">
            <div class="mb-lg-0 mb-2">
              <h1 class="pg-title">{{ pageTitle }}</h1>
            </div>
          </div>
        </div>
        <div class="hk-pg-body">
          <slot />
        </div>
      </div>
    </div>
    <!-- /Main Content -->

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { loadLanguageAsync, trans } from 'laravel-vue-i18n'

const store  = useStore()
const router = useRouter()
const route  = useRoute()

// ── Sidebar state ──────────────────────────────────────────
const sidebarCollapsed = ref(localStorage.getItem('crm_sidebar_collapsed') === 'true')
const sidebarHover     = ref(false)
const mobileOpen       = ref(false)

// ── Submenu open state (Vue-controlled, no Bootstrap collapse needed) ──
const openMenus = reactive({
  deals:    route.path.startsWith('/crm/deals'),
  invoices: route.path.startsWith('/crm/invoices'),
  settings: route.path.startsWith('/crm/settings'),
})

// ── Navbar dropdown state ──────────────────────────────────
const notifOpen       = ref(false)
const userOpen        = ref(false)
const notifDropdownEl = ref(null)
const userDropdownEl  = ref(null)

// ── Auth state ─────────────────────────────────────────────
const isDark    = computed(() => store.state.ui.darkMode)
const locale    = computed(() => store.state.ui.locale)
const user      = computed(() => store.getters['auth/user'])
const tenant    = computed(() => store.getters['auth/tenant'])
const planName  = computed(() => store.getters['auth/plan']?.name || 'Free')
const userName  = computed(() => user.value?.name || '')
const userEmail = computed(() => user.value?.email || tenant.value?.email || '')
const userRole  = computed(() => user.value?.role || '')
const userInitial = computed(() => (userName.value || 'U').charAt(0).toUpperCase())

const pageTitlesEn = {
  '/crm/dashboard':        'Welcome back',
  '/crm/contacts':         'Contacts',
  '/crm/contacts/create':  'New Contact',
  '/crm/deals':            'Deals',
  '/crm/deals/list':       'Deals List',
  '/crm/tasks':            'Tasks',
  '/crm/invoices':         'Invoices',
  '/crm/invoices/create':  'Create Invoice',
  '/crm/reports':          'Reports',
  '/crm/settings/company': 'Company Settings',
  '/crm/settings/team':    'Team Management',
}
const pageTitlesAr = {
  '/crm/dashboard':        'مرحباً بعودتك',
  '/crm/contacts':         'جهات الاتصال',
  '/crm/contacts/create':  'جهة اتصال جديدة',
  '/crm/deals':            'الصفقات',
  '/crm/deals/list':       'قائمة الصفقات',
  '/crm/tasks':            'المهام',
  '/crm/invoices':         'الفواتير',
  '/crm/invoices/create':  'إنشاء فاتورة',
  '/crm/reports':          'التقارير',
  '/crm/settings/company': 'إعدادات الشركة',
  '/crm/settings/team':    'إدارة الفريق',
}
const pageTitle = computed(() => {
  const titles = locale.value === 'ar' ? pageTitlesAr : pageTitlesEn
  return titles[route.path] || 'CRM Innovation'
})

// ── Sidebar toggle ─────────────────────────────────────────
function toggleSidebar() {
  sidebarCollapsed.value = !sidebarCollapsed.value
  if (sidebarCollapsed.value) {
    setTimeout(() => { sidebarHover.value = true }, 250)
  } else {
    sidebarHover.value = false
  }
  localStorage.setItem('crm_sidebar_collapsed', sidebarCollapsed.value)
}

function toggleMobile() {
  mobileOpen.value = !mobileOpen.value
  const wrapper = document.querySelector('.hk-wrapper')
  if (wrapper) {
    if (mobileOpen.value) {
      wrapper.setAttribute('data-layout-style', 'collapsed')
    } else {
      wrapper.setAttribute('data-layout-style', 'default')
    }
  }
}

function closeMobile() {
  mobileOpen.value = false
  const wrapper = document.querySelector('.hk-wrapper')
  if (wrapper) wrapper.setAttribute('data-layout-style', sidebarCollapsed.value ? 'collapsed' : 'default')
}

// ── Submenu toggle (Vue-controlled) ───────────────────────
function toggleMenu(key) {
  openMenus[key] = !openMenus[key]
}

// ── Navbar dropdowns ───────────────────────────────────────
function toggleNotif() { notifOpen.value = !notifOpen.value; userOpen.value = false }
function toggleUser()  { userOpen.value  = !userOpen.value;  notifOpen.value = false }

// Close dropdowns when clicking outside
function onDocClick(e) {
  if (notifDropdownEl.value && !notifDropdownEl.value.contains(e.target)) notifOpen.value = false
  if (userDropdownEl.value  && !userDropdownEl.value.contains(e.target))  userOpen.value  = false
}

// ── Language toggle ────────────────────────────────────────
async function toggleLang() {
  const next = locale.value === 'ar' ? 'en' : 'ar'
  await loadLanguageAsync(next)
  store.commit('ui/SET_LOCALE', next)
  // Apply RTL/LTR
  document.documentElement.dir  = next === 'ar' ? 'rtl' : 'ltr'
  document.documentElement.lang = next
}

// ── Dark mode ──────────────────────────────────────────────
function toggleDark() {
  store.commit('ui/TOGGLE_DARK')
  const theme = store.state.ui.darkMode ? 'dark' : 'light'
  document.documentElement.setAttribute('data-bs-theme', theme)
  localStorage.setItem('theme', theme)
}

// ── Logout ─────────────────────────────────────────────────
async function logout() {
  userOpen.value = false
  await store.dispatch('auth/logout')
  router.push('/crm/login')
}

// ── Lifecycle ──────────────────────────────────────────────
onMounted(async () => {
  // Sync dark mode
  const theme = store.state.ui.darkMode ? 'dark' : 'light'
  document.documentElement.setAttribute('data-bs-theme', theme)
  localStorage.setItem('theme', theme)

  // Apply saved locale
  const savedLocale = store.state.ui.locale
  document.documentElement.dir  = savedLocale === 'ar' ? 'rtl' : 'ltr'
  document.documentElement.lang = savedLocale
  await loadLanguageAsync(savedLocale)

  // Initialize simplebar on sidebar scrollable area
  const nicescroll = document.querySelector('.nicescroll-bar')
  if (nicescroll && window.SimpleBar) {
    new window.SimpleBar(nicescroll, { autoHide: true })
  }

  // Close dropdowns on outside click
  document.addEventListener('click', onDocClick)
})

// Auto-expand active submenu on route change
const stopWatch = router.afterEach((to) => {
  if (to.path.startsWith('/crm/deals'))    openMenus.deals    = true
  if (to.path.startsWith('/crm/invoices')) openMenus.invoices = true
  if (to.path.startsWith('/crm/settings')) openMenus.settings = true
  // Close mobile sidebar on navigation
  closeMobile()
})

onUnmounted(() => {
  document.removeEventListener('click', onDocClick)
  stopWatch()
})
</script>

<style scoped>
/* Submenu transition */
.nav-children {
  overflow: hidden;
}
</style>
