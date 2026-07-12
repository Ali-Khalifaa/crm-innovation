<template>
    <aside class="app-sidebar sticky" id="sidebar">
        <div class="main-sidebar-header">
            <router-link :to="{name:'dashboard'}" class="header-logo">
                <img src="/images/logo.png" alt="CRM Innovation" class="desktop-logo" style="max-height:40px;">
                <img src="/images/logo.png" alt="CRM" class="toggle-logo" style="max-height:32px;">
                <img src="/images/logo.png" alt="CRM Innovation" class="desktop-dark" style="max-height:40px;">
                <img src="/images/logo.png" alt="CRM" class="toggle-dark" style="max-height:32px;">
            </router-link>
        </div>

        <div class="main-sidebar" id="sidebar-scroll">
            <nav class="main-menu-container nav nav-pills flex-column sub-open">
                <div class="slide-left" id="slide-left">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path></svg>
                </div>
                <ul class="main-menu">

                    <!-- Dashboard -->
                    <li class="slide">
                        <router-link :to="{name:'dashboard'}" class="side-menu__item" :class="{active: $route.name === 'dashboard'}">
                            <i class="bx bxs-dashboard side-menu__icon"></i>
                            <span class="side-menu__label">الرئيسية</span>
                        </router-link>
                    </li>

                    <!-- Plans -->
                    <li class="slide">
                        <router-link :to="{name:'plans'}" class="side-menu__item" :class="{active: $route.name?.startsWith('plans')}">
                            <i class="bx bx-package side-menu__icon"></i>
                            <span class="side-menu__label">الباقات</span>
                        </router-link>
                    </li>

                    <!-- Tenants -->
                    <li class="slide">
                        <router-link :to="{name:'tenants'}" class="side-menu__item" :class="{active: $route.name?.startsWith('tenants')}">
                            <i class="bx bx-buildings side-menu__icon"></i>
                            <span class="side-menu__label">الشركات</span>
                        </router-link>
                    </li>

                    <!-- Landing Page -->
                    <li class="slide has-sub"
                        :class="[isLandingActive ? 'active open' : '']"
                        v-if="hasAnyLandingPermission">
                        <a href="javascript:void(0);" class="side-menu__item"
                            :class="[isLandingActive ? 'active' : '']">
                            <i class="bx bx-globe side-menu__icon"></i>
                            <span class="side-menu__label">صفحة الهبوط</span>
                            <i class="fe fe-chevron-right side-menu__angle"></i>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1"><a href="javascript:void(0)">صفحة الهبوط</a></li>
                            <li class="slide" v-if="permission.includes('landing settings read')">
                                <router-link :to="{name:'landing.settings'}" class="side-menu__item">الإعدادات العامة</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing hero read')">
                                <router-link :to="{name:'landing.hero'}" class="side-menu__item">قسم الهيرو</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing stat read')">
                                <router-link :to="{name:'landing.stats'}" class="side-menu__item">الإحصاءات</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing feature read')">
                                <router-link :to="{name:'landing.features'}" class="side-menu__item">المميزات</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing how it works read')">
                                <router-link :to="{name:'landing.how_it_works'}" class="side-menu__item">كيف يعمل</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing faq read')">
                                <router-link :to="{name:'landing.faqs'}" class="side-menu__item">الأسئلة الشائعة</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing partner read')">
                                <router-link :to="{name:'landing.partners'}" class="side-menu__item">الشركاء والعملاء</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing about read')">
                                <router-link :to="{name:'landing.about'}" class="side-menu__item">من نحن</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing testimonial read')">
                                <router-link :to="{name:'landing.testimonials'}" class="side-menu__item">آراء العملاء</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing team read')">
                                <router-link :to="{name:'landing.team'}" class="side-menu__item">أعضاء الفريق</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing blog read')">
                                <router-link :to="{name:'landing.blog'}" class="side-menu__item">المدونة</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('landing contact read')">
                                <router-link :to="{name:'landing.contact_messages'}" class="side-menu__item">رسائل التواصل</router-link>
                            </li>
                        </ul>
                    </li>

                    <!-- Admins -->
                    <li class="slide has-sub"
                        :class="[$route.name === 'admin' || $route.name === 'role' ? 'active open' : '']"
                        v-if="permission.includes('admin read') || permission.includes('role read')">
                        <a href="javascript:void(0);" class="side-menu__item"
                            :class="[$route.name === 'admin' || $route.name === 'role' ? 'active' : '']">
                            <i class="bx bx-shield side-menu__icon"></i>
                            <span class="side-menu__label">الصلاحيات</span>
                            <i class="fe fe-chevron-right side-menu__angle"></i>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1"><a href="javascript:void(0)">الصلاحيات</a></li>
                            <li class="slide" v-if="permission.includes('admin read')">
                                <router-link :to="{name:'admin'}" class="side-menu__item">المديرون</router-link>
                            </li>
                            <li class="slide" v-if="permission.includes('role read')">
                                <router-link :to="{name:'role'}" class="side-menu__item">الأدوار والصلاحيات</router-link>
                            </li>
                        </ul>
                    </li>

                </ul>
                <div class="slide-right" id="slide-right">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"><path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path></svg>
                </div>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import defaultrmenu from '../../../helper/defaultrmenu';

const store = useStore();
const route = useRoute();
const { menu } = defaultrmenu();
const permission = computed(() => store.getters['authAdmin/permission']);

const landingRouteNames = ['landing.settings','landing.hero','landing.stats','landing.features','landing.how_it_works','landing.faqs','landing.partners','landing.about','landing.testimonials','landing.team','landing.blog','landing.contact_messages'];
const isLandingActive = computed(() => landingRouteNames.includes(route.name));
const hasAnyLandingPermission = computed(() =>
    ['landing settings read','landing hero read','landing stat read',
     'landing feature read','landing how it works read','landing faq read','landing partner read',
     'landing about read','landing testimonial read','landing team read','landing blog read','landing contact read']
    .some(p => permission.value.includes(p))
);

onMounted(() => { menu(); });
</script>
