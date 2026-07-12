<template>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">الباقات</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><router-link :to="{name:'dashboard'}">الرئيسية</router-link></li>
                        <li class="breadcrumb-item active">الباقات</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <loader v-if="loading" />
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">قائمة الباقات</div>
                        <button @click="openCreate" class="btn btn-sm btn-primary-light"
                            data-bs-toggle="modal" data-bs-target="#plans-modal">
                            <i class="ri-add-line me-1 fw-semibold align-middle"></i>إضافة باقة
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-2">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>السعر الشهري</th>
                                        <th>السعر السنوي</th>
                                        <th>المستخدمين</th>
                                        <th>جهات الاتصال</th>
                                        <th>مميز</th>
                                        <th>الحالة</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody v-if="plans.length">
                                    <tr v-for="(plan, index) in plans" :key="plan.id">
                                        <td>{{ index + 1 }}</td>
                                        <td class="fw-semibold">{{ plan.name }}</td>
                                        <td>${{ plan.monthly_price }}</td>
                                        <td>${{ plan.yearly_price }}</td>
                                        <td>{{ plan.max_users == 0 ? 'غير محدود' : plan.max_users }}</td>
                                        <td>{{ plan.max_contacts == 0 ? 'غير محدود' : plan.max_contacts }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-warning-transparent" v-if="plan.is_featured">مميز</span>
                                            <span class="badge rounded-pill bg-light text-muted" v-else>—</span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-success-transparent" v-if="plan.is_active">نشط</span>
                                            <span class="badge rounded-pill bg-danger-transparent" v-else>متوقف</span>
                                        </td>
                                        <td>
                                            <div class="hstack gap-2 fs-15">
                                                <button @click="openEdit(plan)"
                                                    data-bs-toggle="modal" data-bs-target="#plans-modal"
                                                    class="btn btn-icon btn-sm btn-primary-transparent rounded-pill"
                                                    title="تعديل">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button @click="deletePlan(plan, index)"
                                                    class="btn btn-icon btn-sm btn-danger-transparent rounded-pill"
                                                    title="حذف">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr><td class="text-center" colspan="9">لا توجد باقات</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <PlanModal :type="modalType" :dataRow="selectedPlan" @saved="loadPlans" />
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import adminApi from '../../../api/adminAxios';
import PlanModal from './ModalCreateAndUpdate.vue';

export default {
    components: { PlanModal },
    setup() {
        const plans = ref([]);
        const loading = ref(false);
        const modalType = ref('create');
        const selectedPlan = ref(null);

        const loadPlans = async () => {
            loading.value = true;
            try {
                const res = await adminApi.get('dashboard/plans?per_page=100');
                plans.value = res.data.data || [];
            } catch (e) { console.error(e); }
            finally { loading.value = false; }
        };

        const openCreate = () => {
            selectedPlan.value = null;
            modalType.value = 'create';
        };

        const openEdit = (plan) => {
            selectedPlan.value = { ...plan };
            modalType.value = 'edit';
        };

        const deletePlan = (plan, index) => {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: `سيتم حذف باقة "${plan.name}" نهائياً`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await adminApi.delete(`dashboard/plans/${plan.id}`);
                        plans.value.splice(index, 1);
                        Swal.fire({ icon: 'success', title: 'تم الحذف', showConfirmButton: false, timer: 1500 });
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: 'لا يمكن الحذف', text: e.response?.data?.message || 'هناك شركات مرتبطة بهذه الباقة' });
                    }
                }
            });
        };

        onMounted(loadPlans);
        return { plans, loading, modalType, selectedPlan, openCreate, openEdit, deletePlan, loadPlans };
    }
};
</script>
