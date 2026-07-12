<template>
    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">آراء العملاء</h1>
            <div class="ms-md-1 ms-0">
                <nav><ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                    <li class="breadcrumb-item"><span>صفحة الهبوط</span></li>
                    <li class="breadcrumb-item active">آراء العملاء</li>
                </ol></nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <loader v-if="loading" />
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h6 class="card-title mb-0">قائمة آراء العملاء</h6>
                        <button v-if="permission.includes('landing testimonial create')"
                            @click="showModelCreate" class="btn btn-sm btn-primary-light"
                            data-bs-toggle="modal" data-bs-target="#testimonial-modal">
                            <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة رأي
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-2">
                            <table class="table table-striped">
                                <thead><tr>
                                    <th style="width:40px">#</th>
                                    <th style="width:60px">الصورة</th>
                                    <th>الاسم</th>
                                    <th>المسمى (EN)</th>
                                    <th style="width:80px">التقييم</th>
                                    <th style="width:80px">الترتيب</th>
                                    <th style="width:80px">الحالة</th>
                                    <th style="width:100px">الإجراءات</th>
                                </tr></thead>
                                <tbody>
                                    <tr v-if="!loading && items.length === 0">
                                        <td colspan="8" class="text-center text-muted py-4">لا توجد بيانات</td>
                                    </tr>
                                    <tr v-for="(item, i) in items" :key="item.id">
                                        <td>{{ i + 1 }}</td>
                                        <td>
                                            <img v-if="item.photo" :src="'/upload/general/' + item.photo"
                                                alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                                            <div v-else style="width:40px;height:40px;border-radius:50%;background:#E2E8F0;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                        </td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.designation_en }}</td>
                                        <td>
                                            <span v-for="s in 5" :key="s" class="text-warning" style="font-size:11px;">
                                                <i :class="s <= item.rating ? 'fas fa-star' : 'far fa-star'"></i>
                                            </span>
                                        </td>
                                        <td>{{ item.sort_order }}</td>
                                        <td>
                                            <span :class="item.is_active ? 'badge bg-success-transparent' : 'badge bg-danger-transparent'">
                                                {{ item.is_active ? 'مفعّل' : 'معطّل' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button v-if="permission.includes('landing testimonial edit')"
                                                class="btn btn-sm btn-warning-light me-1"
                                                data-bs-toggle="modal" data-bs-target="#testimonial-modal"
                                                @click="showModelUpdate(item)">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button v-if="permission.includes('landing testimonial delete')"
                                                class="btn btn-sm btn-danger-light"
                                                @click="deleteItem(item.id)">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <pagination v-if="pageCount > 1" :pageCount="pageCount" :value="page" @input="changePage" />
                    </div>
                </div>
            </div>
        </div>

        <ModalCreateAndUpdate
            :selectedItem="selectedItem"
            :uri="uri"
            @refresh="getItems"
        />
    </div>
</template>

<script>
import crud from '../../../../composable/crud_structure';
import ModalCreateAndUpdate from './ModalCreateAndUpdate.vue';

export default {
    name: 'LandingTestimonialsIndex',
    components: { ModalCreateAndUpdate },
    setup() {
        const { uri, loading, items, page, pageCount, getItems, changePage, deleteItem, showModelCreate, showModelUpdate, selectedItem, permission } = crud();
        uri.value = 'landing/testimonials';
        getItems();
        return { uri, loading, items, page, pageCount, getItems, changePage, deleteItem, showModelCreate, showModelUpdate, selectedItem, permission };
    },
};
</script>
