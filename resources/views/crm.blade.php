<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRM Innovation</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    {{-- Theme & locale initializer (runs before render to avoid flash) --}}
    <script>
        (function () {
            var t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
            var l = localStorage.getItem('crm_locale') || 'en';
            document.documentElement.dir  = l === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.lang = l;
        })();
    </script>

    {{-- Google Fonts: DM Sans (template font) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

    {{-- Template built CSS (Bootstrap 5 + layout + all components) --}}
    <link rel="stylesheet" href="{{ asset('dashboard/assets/main-CcWGXzrt.css') }}">

    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Simplebar CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@6.2.7/dist/simplebar.min.css">

    {{-- ===== ARABIC RTL LAYOUT FIXES ===== --}}
    <style>

        /* ─── 1. SIDEBAR: position on right side ──────────────── */
        [dir="rtl"] .hk-menu {
            left:  auto !important;
            right: -270px !important;
            border-right: none !important;
            border-left: 1px solid rgba(0,0,0,.08) !important;
        }
        /* Mobile: show sidebar on the right when open (collapsed state) */
        [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"] .hk-menu {
            right: 0 !important;
            left: auto !important;
        }

        @media (min-width: 1200px) {
            /* Default — full sidebar on right */
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="default"] .hk-menu {
                right: 0 !important; left: auto !important;
            }
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="default"] .hk-pg-wrapper {
                margin-left: 0 !important; margin-right: 270px !important;
            }
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="default"] .hk-navbar {
                left: 0 !important; right: 270px !important;
            }
            /* Collapsed — 72px icon-only on right */
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"] .hk-pg-wrapper {
                margin-left: 0 !important; margin-right: 72px !important;
            }
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"] .hk-navbar {
                left: 0 !important; right: 72px !important;
            }
            /* Hover-expand on collapsed */
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"][data-hover="active"] .hk-menu:hover {
                right: 0 !important; left: auto !important;
            }

            /* ── Collapsed: center icon at LEFT, clip invisible overflow ──
               The invisible text has flex-shrink:0 so it keeps its full
               natural width, pushing the icon toward the RIGHT in RTL.
               overflow:hidden clips it; direction:ltr!important puts icon
               at flex-start (LEFT) = centered in the 72px strip.         */
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"] .hk-menu .navbar-nav .nav-item .nav-link {
                direction: ltr !important;
                overflow: hidden;
            }
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"] .hk-menu .nav-icon-wrap {
                margin-right: 0 !important;
                margin-left: 0 !important;
            }
            /* Hover-expand: restore overflow so labels are visible */
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"][data-hover="active"] .hk-menu:hover .navbar-nav .nav-item .nav-link {
                overflow: visible;
            }
            [dir="rtl"] .hk-wrapper[data-layout="vertical"][data-layout-style="collapsed"][data-hover="active"] .hk-menu:hover .nav-icon-wrap {
                margin-right: 0.875rem !important;
                margin-left: 0 !important;
            }

        }

        /* ─── 2. NAVBAR SEARCH input ──────────────────────────── */
        [dir="rtl"] .navbar-search .input-affix-wrapper input {
            direction: rtl; text-align: right;
        }

        /* ─── 3. SIDEBAR NAV ITEMS ────────────────────────────── */
        /* Force LTR on every nav-link so icon sits at the LEFT
           (same as English). Arabic label text is forced RTL inside. */
        [dir="rtl"] .hk-menu .navbar-nav .nav-item .nav-link {
            direction: ltr !important;
        }
        /* Arabic label: RTL text inside the LTR flex container */
        [dir="rtl"] .hk-menu .nav-link-text {
            direction: rtl !important;
            text-align: right;
            flex: 1;
        }
        /* Icon margin: keep LTR original (gap to the RIGHT of icon) */
        [dir="rtl"] .hk-menu .nav-icon-wrap {
            margin-right: 0.875rem !important;
            margin-left: 0 !important;
        }
        /* Badge: push to the right end in LTR nav-link */
        [dir="rtl"] .hk-menu .nav-link .badge {
            margin-left: auto !important;
            margin-right: 0 !important;
        }
        [dir="rtl"] .hk-menu .nav-header { text-align: right; letter-spacing: 0; }
        [dir="rtl"] .hk-menu .menu-content-wrap { direction: rtl; }

        /* ─── 4. SUBMENU ──────────────────────────────────────── */
        /* Keep guide-line and indentation on the LEFT (same as LTR)
           since nav-links now use LTR layout. */
        [dir="rtl"] .hk-menu .navbar-nav > .nav-item > ul::after {
            right: auto !important;
            left: 10px !important;
        }
        [dir="rtl"] .hk-menu .nav-children .nav-item .nav-link {
            padding-right: 0 !important;
            padding-left: 1.4rem !important;
            margin-right: 0 !important;
            margin-left: 1rem !important;
        }

        /* ─── 5. HOVER ANIMATION: slide rightward (same feel as LTR) ── */
        [dir="rtl"] .hk-menu .nav-link:hover > * {
            transform: translateX(5px) !important;
        }

        /* ─── 6. DROPDOWN MENUS ──────────────────────────────── */
        [dir="rtl"] .dropdown-menu { direction: rtl; text-align: right; }
        [dir="rtl"] .dropdown-menu-end { left: 0 !important; right: auto !important; }
        [dir="rtl"] .dropdown-item { direction: rtl; text-align: right; }
        [dir="rtl"] .dropdown-icon { margin-right: 0 !important; margin-left: 0.5rem !important; }
        [dir="rtl"] .dropdown-header { text-align: right; }

        /* ─── 7. MEDIA ROWS (avatar + text) ─────────────────── */
        [dir="rtl"] .media { flex-direction: row-reverse; }
        [dir="rtl"] .media-head { margin-right: 0 !important; margin-left: 0.75rem !important; }
        [dir="rtl"] .media-body { text-align: right; }

        /* ─── 8. PAGE CONTENT ────────────────────────────────── */
        [dir="rtl"] .hk-pg-header { direction: rtl; }
        [dir="rtl"] .pg-title      { direction: rtl; }
        [dir="rtl"] .hk-pg-body   { direction: rtl; }

        /* ─── 9. CARDS ───────────────────────────────────────── */
        [dir="rtl"] .card      { direction: rtl; }
        [dir="rtl"] .card-body { direction: rtl; }
        /* card-action-wrap: auto margin to push it left */
        [dir="rtl"] .card-action-wrap { margin-right: auto !important; margin-left: 0 !important; }

        /* ─── 10. TABLES ─────────────────────────────────────── */
        [dir="rtl"] .table { direction: rtl; }
        [dir="rtl"] .table th,
        [dir="rtl"] .table td { text-align: right; }
        [dir="rtl"] .table .text-end { text-align: left !important; }

        /* ─── 11. FORMS ──────────────────────────────────────── */
        [dir="rtl"] .form-label { text-align: right; }
        [dir="rtl"] .form-control,
        [dir="rtl"] .form-select { direction: rtl; text-align: right; }
        /* Input groups: keep visual LTR order (icon left, input right) via row-reverse */
        [dir="rtl"] .input-group { flex-direction: row-reverse; }
        [dir="rtl"] .input-group .form-control:not(:last-child) {
            border-radius: 0 var(--bs-border-radius) var(--bs-border-radius) 0 !important;
            border-right: 1px solid var(--bs-border-color) !important;
            border-left: 0 !important;
        }
        [dir="rtl"] .input-group .input-group-text:first-child {
            border-radius: var(--bs-border-radius) 0 0 var(--bs-border-radius) !important;
        }

        /* ─── 12. NOTIFICATIONS ──────────────────────────────── */
        [dir="rtl"] .notifications-text,
        [dir="rtl"] .notifications-info,
        [dir="rtl"] .notifications-time { direction: rtl; text-align: right; }

        /* ─── 13. BADGE INDICATOR (bell button) ──────────────── */
        [dir="rtl"] .position-top-end-overflow-1 {
            right: auto !important; left: -6px !important;
        }

        /* ─── 14. UPGRADE CALLOUT ────────────────────────────── */
        [dir="rtl"] .callout { direction: rtl; }

        /* ─── 15. BOOTSTRAP SPACING UTILITIES ───────────────── */
        [dir="rtl"] .ms-auto { margin-left: 0 !important; margin-right: auto !important; }
        [dir="rtl"] .ms-1    { margin-left: 0 !important; margin-right: .25rem !important; }
        [dir="rtl"] .ms-2    { margin-left: 0 !important; margin-right: .5rem  !important; }
        [dir="rtl"] .ms-3    { margin-left: 0 !important; margin-right: 1rem   !important; }
        [dir="rtl"] .ms-4    { margin-left: 0 !important; margin-right: 1.5rem !important; }
        [dir="rtl"] .me-1    { margin-right: 0 !important; margin-left: .25rem !important; }
        [dir="rtl"] .me-2    { margin-right: 0 !important; margin-left: .5rem  !important; }
        [dir="rtl"] .me-3    { margin-right: 0 !important; margin-left: 1rem   !important; }
        [dir="rtl"] .me-4    { margin-right: 0 !important; margin-left: 1.5rem !important; }
        [dir="rtl"] .me-8    { margin-right: 0 !important; margin-left: 3rem   !important; }
        [dir="rtl"] .ps-2    { padding-left: 0 !important; padding-right: .5rem  !important; }
        [dir="rtl"] .ps-4    { padding-left: 0 !important; padding-right: 1.5rem !important; }
        [dir="rtl"] .pe-2    { padding-right: 0 !important; padding-left: .5rem  !important; }
        [dir="rtl"] .text-end   { text-align: left !important; }
        [dir="rtl"] .text-start { text-align: right !important; }
        [dir="rtl"] .border-end-0   { border-right: 0 !important; border-left: inherit; }
        [dir="rtl"] .border-start-0 { border-left: 0 !important; border-right: inherit; }

        /* ─── 16. MISC ───────────────────────────────────────── */
        [dir="rtl"] .btn .bi + span,
        [dir="rtl"] .btn i + span  { margin-left: 0 !important; margin-right: .25rem !important; }
        [dir="rtl"] .avatar { flex-shrink: 0; }
        [dir="rtl"] .separator-full { direction: rtl; }

        /* ─── 17. ICON FLIPS ──────────────────────────────────
           The sidebar collapse SVG and navbar layout-sidebar icon
           are "LTR directional" icons (arrow/panel on the LEFT).
           In RTL the sidebar lives on the RIGHT, so flip them.  */

        /* Sidebar header: collapse-left arrow → collapse-right */
        [dir="rtl"] .hk-menu .menu-header .svg-icon svg {
            transform: scaleX(-1);
        }
        /* When collapsed, template rotates the button 180°; the
           combined scaleX(-1) + rotate(180deg) still resolves
           correctly as a right-pointing expand arrow.           */

        /* Navbar: bi-layout-sidebar shows panel on left → flip */
        [dir="rtl"] .hk-navbar .nav-start-wrap .bi-layout-sidebar {
            display: inline-block;
            transform: scaleX(-1);
        }

        /* ─── 18. ACTIVE NAV ITEM HIGHLIGHT RADIUS ──────────── */
        /* In RTL the rounded side should stay consistent.
           The nav-link already gets border-radius: 0.5rem;
           no change needed — just ensure direction doesn't flip it. */

        /* ─── 19. PAGE HEADER FLEX DIRECTION ─────────────────── */
        /* d-flex containers inside hk-pg-header that use
           justify-content-between will naturally flow RTL:
           title on RIGHT, actions on LEFT — which is correct. */
        [dir="rtl"] .hk-pg-header .d-flex {
            flex-direction: row;   /* keep natural RTL row flow */
        }

    </style>
</head>
<body>
    <div id="crm-app"></div>

    {{-- Simplebar CDN (custom scrollbar for sidebar) --}}
    <script src="https://cdn.jsdelivr.net/npm/simplebar@6.2.7/dist/simplebar.min.js"></script>

    {{-- Vue CRM App --}}
    @vite(['resources/js/crm/main.js'])
</body>
</html>
