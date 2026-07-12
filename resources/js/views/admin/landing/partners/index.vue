<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">الشركاء والعملاء</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">الشركاء</li>
                </ol></nav>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <loader v-if="loading" />
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">لوجوهات الشركاء في السلايدر</h6>
                            <p class="text-muted mb-0 mt-1" style="font-size:12px;">
                                الصور النشطة فقط تظهر في اللاندينج بيدج — الحجم المقترح: عرض 200px
                            </p>
                        </div>
                        <button v-if="permission.includes('landing partner create')"
                            @click="showModelCreate" class="btn btn-sm btn-primary-light"
                            data-bs-toggle="modal" data-bs-target="#partner-modal">
                            <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة شريك
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-2">
                            <table class="table table-striped align-middle">
                                <thead><tr>
                                    <th style="width:50px">#</th>
                                    <th style="width:120px">اللوجو</th>
                                    <th>الاسم</th>
                                    <th>الرابط</th>
                                    <th style="width:80px">الترتيب</th>
                                    <th style="width:90px">الحالة</th>
                                    <th style="width:100px">إجراء</th>
                                </tr></thead>
                                <tbody v-if="data && data.length">
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>
                                            <img v-if="item.image" :src="item.image"
                                                style="height:44px;max-width:100px;object-fit:contain;border-radius:6px;border:1px solid #e2e8f0;padding:4px;background:#fff;" alt="">
                                            <span v-else class="text-muted">—</span>
                                        </td>
                                        <td>{{ item.name || '—' }}</td>
                                        <td>
                                            <a v-if="item.url" :href="item.url" target="_blank"
                                                class="text-primary" style="font-size:13px;">
                                                {{ item.url.replace('https://','').replace('http://','') }}
                                            </a>
                                            <span v-else class="text-muted">—</span>
                                        </td>
                                        <td>{{ item.sort_order }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-success-transparent" v-if="item.is_active">نشط</span>
                                            <span class="badge rounded-pill bg-danger-transparent" v-else>غير نشط</span>
                                        </td>
                                        <td>
                                            <div class="hstack gap-2">
                                                <button v-if="permission.includes('landing partner edit')"
                                                    @click.prevent="showEditMode(item)"
                                                    data-bs-toggle="modal" data-bs-target="#partner-modal"
                                                    class="btn btn-icon btn-sm btn-info-transparent rounded-pill">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button v-if="permission.includes('landing partner delete')"
                                                    @click.prevent="deleteData(item.id, index)"
                                                    class="btn btn-icon btn-sm btn-danger-transparent rounded-pill">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr><td class="text-center" colspan="7">{{ $t('global.NoDataFound') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :limit="2" :data="dataPaginate" @pagination-change-page="getData">
                            <template #prev-nav><span>&lt; {{ $t('global.Previous') }}</span></template>
                            <template #next-nav><span>{{ $t('global.Next') }} &gt;</span></template>
                        </Pagination>
                    </div>
                </div>
            </div>
        </div>

        <ModalCreateAndUpdate v-model="modalShow" :type="type" :dataRow="dataRow" @created="getData(pagePaginate)" />
    </div>
</template>

<script>
import { onBeforeMount } from 'vue';
import crud from '../../../../composable/crud_structure';
import ModalCreateAndUpdate from './ModalCreateAndUpdate.vue';

export default {
    name: 'LandingPartners',
    components: { ModalCreateAndUpdate },
    setup() {
        const { getData, loading, data, dataPaginate, permission, uri, showModelCreate, showEditMode, deleteData, search, type, dataRow, modalShow, pagePaginate } = crud();
        search.value = { searchKey: '', searchInTranslations: false, columns: ['name'] };
        onBeforeMount(() => { uri.value = 'landing/partners'; getData(); });
        return { getData, loading, data, dataPaginate, permission, showModelCreate, showEditMode, deleteData, search, type, dataRow, modalShow, pagePaginate };
    }
};
</script>
