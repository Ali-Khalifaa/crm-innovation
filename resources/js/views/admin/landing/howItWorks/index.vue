<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">كيف يعمل</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">كيف يعمل</li>
                </ol></nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <loader v-if="loading" />
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h6 class="card-title mb-0">خطوات كيف يعمل</h6>
                        <button v-if="permission.includes('landing how it works create')"
                            @click="showModelCreate" class="btn btn-sm btn-primary-light"
                            data-bs-toggle="modal" data-bs-target="#howitworks-modal">
                            <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة خطوة
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-2">
                            <table class="table text-nowrap table-striped">
                                <thead><tr>
                                    <th>#</th><th>رقم الخطوة</th><th>العنوان (EN)</th><th>العنوان (AR)</th>
                                    <th>الصورة</th><th>الترتيب</th><th>الحالة</th><th>إجراء</th>
                                </tr></thead>
                                <tbody v-if="data && data.length">
                                    <tr v-for="(item, index) in data" :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td><span class="badge bg-primary-transparent fs-14">{{ String(item.step_number).padStart(2,'0') }}</span></td>
                                        <td>{{ item.title_en }}</td>
                                        <td>{{ item.title_ar }}</td>
                                        <td>
                                            <img v-if="item.image" :src="item.image" style="height:40px;border-radius:6px;" alt="">
                                            <span v-else class="text-muted">—</span>
                                        </td>
                                        <td>{{ item.sort_order }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-success-transparent" v-if="item.is_active">نشط</span>
                                            <span class="badge rounded-pill bg-danger-transparent" v-else>غير نشط</span>
                                        </td>
                                        <td>
                                            <div class="hstack gap-2">
                                                <button v-if="permission.includes('landing how it works edit')"
                                                    @click.prevent="showEditMode(item)"
                                                    data-bs-toggle="modal" data-bs-target="#howitworks-modal"
                                                    class="btn btn-icon btn-sm btn-info-transparent rounded-pill"><i class="ri-edit-line"></i></button>
                                                <button v-if="permission.includes('landing how it works delete')"
                                                    @click.prevent="deleteData(item.id, index)"
                                                    class="btn btn-icon btn-sm btn-danger-transparent rounded-pill"><i class="ri-delete-bin-line"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else><tr><td class="text-center" colspan="8">{{ $t('global.NoDataFound') }}</td></tr></tbody>
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
    name: 'LandingHowItWorks',
    components: { ModalCreateAndUpdate },
    setup() {
        const { getData, loading, data, dataPaginate, permission, uri, showModelCreate, showEditMode, deleteData, search, type, dataRow, modalShow, pagePaginate } = crud();
        search.value = { searchKey: '', searchInTranslations: false, columns: ['title_en'] };
        onBeforeMount(() => { uri.value = 'landing/how-it-works'; getData(); });
        return { getData, loading, data, dataPaginate, permission, showModelCreate, showEditMode, deleteData, search, type, dataRow, modalShow, pagePaginate };
    }
};
</script>
