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

          <!-- Search trigger -->
          <div class="dropdown navbar-search ms-1">
            <div class="input-group d-xl-flex d-none" @click="openGlobalSearch" style="cursor:pointer">
              <span class="input-affix-wrapper input-search affix-border" style="pointer-events:none">
                <i class="bi bi-search" style="color:#94A3B8;font-size:13px;margin-inline-end:6px"></i>
                <span class="form-control bg-transparent text-muted" style="font-size:13px">
                  {{ locale === 'ar' ? 'بحث سريع...' : 'Quick search...' }}
                </span>
                <span class="input-suffix" style="pointer-events:none"><kbd style="font-size:11px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:4px;padding:1px 5px">Ctrl+K</kbd></span>
              </span>
            </div>
            <!-- Mobile search icon -->
            <button class="btn btn-icon btn-rounded btn-flush-dark flush-soft-hover d-xl-none" @click="openGlobalSearch" type="button">
              <span class="icon"><i class="bi bi-search" style="font-size:1rem"></i></span>
            </button>
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
            <li class="nav-item">
              <NotificationBell />
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
                <router-link class="dropdown-item" to="/crm/settings/profile" @click="userOpen = false">
                  <span class="dropdown-icon"><i class="bi bi-person-circle me-2"></i></span>
                  {{ locale === 'ar' ? 'ملفي الشخصي' : 'My Profile' }}
                </router-link>
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

              <!-- Companies -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/companies') }">
                <router-link class="nav-link" to="/crm/companies">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 21l18 0"/>
                        <path d="M9 8l1 0"/>
                        <path d="M9 12l1 0"/>
                        <path d="M9 16l1 0"/>
                        <path d="M14 8l1 0"/>
                        <path d="M14 12l1 0"/>
                        <path d="M14 16l1 0"/>
                        <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ locale === 'ar' ? 'الشركات' : 'Companies' }}</span>
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

              <!-- Tasks (with submenu) -->
              <li class="nav-item has-sub" :class="{ active: route.path.startsWith('/crm/tasks'), 'menu-open': openMenus.tasks }">
                <a class="nav-link" href="#" @click.prevent="toggleMenu('tasks')">
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
                </a>
                <ul class="nav flex-column nav-children" v-show="openMenus.tasks">
                  <li class="nav-item"><ul class="nav flex-column">
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/tasks">
                        <span class="nav-link-text">{{ locale === 'ar' ? 'قائمة المهام' : 'Task List' }}</span>
                      </router-link>
                    </li>
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/tasks/calendar">
                        <span class="nav-link-text">{{ locale === 'ar' ? 'التقويم' : 'Calendar' }}</span>
                      </router-link>
                    </li>
                  </ul></li>
                </ul>
              </li>

              <!-- Meetings -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/meetings') }">
                <router-link class="nav-link" to="/crm/meetings">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="4" y="5" width="16" height="16" rx="2"/>
                        <line x1="16" y1="3" x2="16" y2="7"/>
                        <line x1="8" y1="3" x2="8" y2="7"/>
                        <line x1="4" y1="11" x2="20" y2="11"/>
                        <line x1="11" y1="15" x2="12" y2="15"/>
                        <line x1="12" y1="15" x2="12" y2="18"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ locale === 'ar' ? 'الاجتماعات' : 'Meetings' }}</span>
                </router-link>
              </li>

              <!-- Calls -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/calls') }">
                <router-link class="nav-link" to="/crm/calls">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ locale === 'ar' ? 'المكالمات' : 'Calls' }}</span>
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

              <!-- Products -->
              <li class="nav-item" :class="{ active: route.path.startsWith('/crm/products') }">
                <router-link class="nav-link" to="/crm/products">
                  <span class="nav-icon-wrap">
                    <span class="svg-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"/>
                        <line x1="12" y1="12" x2="20" y2="7.5"/>
                        <line x1="12" y1="12" x2="12" y2="21"/>
                        <line x1="12" y1="12" x2="4" y2="7.5"/>
                      </svg>
                    </span>
                  </span>
                  <span class="nav-link-text">{{ locale === 'ar' ? 'المنتجات' : 'Products' }}</span>
                </router-link>
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
                      <router-link class="nav-link" to="/crm/settings/profile">
                        <span class="nav-link-text">{{ locale === 'ar' ? 'ملفي الشخصي' : 'My Profile' }}</span>
                      </router-link>
                    </li>
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
                    <li class="nav-item">
                      <router-link class="nav-link" to="/crm/settings/stages">
                        <span class="nav-link-text">{{ locale === 'ar' ? 'مراحل الصفقات' : 'Deal Stages' }}</span>
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
    <div class="hk-pg-wrapper pt-5">
      <div class="container-xxl pt-2">
        <!-- <div class="hk-pg-header pt-7">
          <div class="d-flex flex-wrap justify-content-between flex-1">
            <div class="mb-lg-0 mb-2">
              <h1 class="pg-title">{{ pageTitle }}</h1>
            </div>
          </div>
        </div> -->
        <div class="hk-pg-body">
          <slot />
        </div>
      </div>
    </div>
    <!-- /Main Content -->

  </div>

  <!-- Global Search -->
  <GlobalSearch ref="globalSearchRef" />
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { loadLanguageAsync, trans } from 'laravel-vue-i18n'
import GlobalSearch from '../components/GlobalSearch.vue'
import NotificationBell from '../components/NotificationBell.vue'

const globalSearchRef = ref(null)
function openGlobalSearch() {
  globalSearchRef.value?.openSearch()
}

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
  tasks:    route.path.startsWith('/crm/tasks'),
  invoices: route.path.startsWith('/crm/invoices'),
  settings: route.path.startsWith('/crm/settings'),
})

// ── Navbar dropdown state ──────────────────────────────────
const userOpen        = ref(false)
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
  '/crm/tasks/calendar':  'Task Calendar',
  '/crm/invoices':         'Invoices',
  '/crm/invoices/create':  'Create Invoice',
  '/crm/reports':          'Reports',
  '/crm/settings/company': 'Company Settings',
  '/crm/settings/team':    'Team Management',
  '/crm/settings/profile': 'My Profile',
  '/crm/settings/stages':  'Deal Stages',
  '/crm/notifications':    'Notifications',
  '/crm/companies/:id':    'Company Profile',
}
const pageTitlesAr = {
  '/crm/dashboard':        'مرحباً بعودتك',
  '/crm/contacts':         'جهات الاتصال',
  '/crm/contacts/create':  'جهة اتصال جديدة',
  '/crm/deals':            'الصفقات',
  '/crm/deals/list':       'قائمة الصفقات',
  '/crm/tasks':            'المهام',
  '/crm/tasks/calendar':  'تقويم المهام',
  '/crm/invoices':         'الفواتير',
  '/crm/invoices/create':  'إنشاء فاتورة',
  '/crm/reports':          'التقارير',
  '/crm/settings/company': 'إعدادات الشركة',
  '/crm/settings/team':    'إدارة الفريق',
  '/crm/settings/profile': 'ملفي الشخصي',
  '/crm/settings/stages':  'مراحل الصفقات',
  '/crm/notifications':    'الإشعارات',
  '/crm/companies/:id':    'ملف الشركة',
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
function toggleUser()  { userOpen.value = !userOpen.value }

// Close dropdowns when clicking outside
function onDocClick(e) {
  if (userDropdownEl.value && !userDropdownEl.value.contains(e.target)) userOpen.value = false
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
  if (to.path.startsWith('/crm/tasks'))    openMenus.tasks    = true
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
