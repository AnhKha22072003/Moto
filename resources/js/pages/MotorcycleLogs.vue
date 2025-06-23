<template>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center m-2">
                    <h2 class="card-title m-2">Danh sách ghi nhận</h2>
                </div>
                <!-- Filters -->
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Id xe</label>
                        <input
                            v-model="filters.motorcycle_id"
                            type="text"
                            class="form-control"
                            placeholder="VD: 123"
                        />
                    </div>
                    <!-- Từ ngày -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Từ ngày</label>
                        <input
                            v-model="filters.formDate"
                            type="date"
                            class="form-control"
                        />
                    </div>

                    <!-- Đến ngày -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label">Đến ngày</label>
                        <input
                            v-model="filters.toDate"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <!-- Trạng thái -->
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label d-flex align-items-center"
                            >Trạng thái</label
                        >
                        <select v-model="filters.status" class="form-select">
                            <option value="null">Tất cả</option>
                            <option value="created">Tạo</option>
                            <option value="updated">Cập nhật</option>
                            <option value="cloned">Copy</option>
                            <option value="bulkUpdate">
                                Cập nhật hàng loạt
                            </option>
                            <option value="deleted">Xóa</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 d-flex gap-2">
                        <button
                            class="btn btn-primary w-100"
                            @click="fetchData"
                        >
                            Tìm
                        </button>
                        <button
                            class="btn btn-outline-secondary w-100"
                            @click="clearFilters"
                        >
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th @click="sort('id')" class="link-style">
                            motorcycle_id
                        </th>
                        <th @click="sort('motorcycle_id')" class="link-style">
                            Người thực hiện
                        </th>
                        <th @click="sort('event')" class="link-style">
                            Trạng Thái
                        </th>
                        <th @click="sort('created_at')" class="link-style">
                            Ngày thực hiện
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in motorcycleLogs" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.motorcycle_id }}</td>
                        <td>{{ item.user.name }}</td>
                        <td>
                            <span
                                class="badge"
                                :class="{
                                    'bg-success text-white':
                                        item.event === 'created',
                                    'bg-primary text-white':
                                        item.event === 'updated',
                                    'bg-warning text-white':
                                        item.event === 'cloned',
                                    'bg-danger text-white':
                                        item.event === 'deleted',
                                    'bg-secondary text-white':
                                        item.event === 'bulkUpdate',
                                }"
                            >
                                {{
                                    item.event === "created"
                                        ? "Tạo"
                                        : item.event === "updated"
                                        ? "Cập nhật"
                                        : item.event === "cloned"
                                        ? "Copy"
                                        : item.event === "deleted"
                                        ? "Xóa"
                                        : item.event === "bulkUpdate"
                                        ? "Cập nhật hàng loạt"
                                        : "Xóa"
                                }}
                            </span>
                        </td>
                        <td>
                            {{ new Date(item.created_at).toLocaleString() }}
                        </td>
                        <td class="m-1">
                            <button
                                class="btn btn-sm btn-outline-info"
                                @click="showDetailModal(item)"
                            >
                                Xem chi tiết
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="card-footer d-flex align-items-center">
            <div class="dropdown">
                <a class="btn dropdown-toggle" data-bs-toggle="dropdown">
                    {{ perPage }} records
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" @click="setPerPage(10)"
                        >10 records</a
                    >
                    <a class="dropdown-item" @click="setPerPage(20)"
                        >20 records</a
                    >
                    <a class="dropdown-item" @click="setPerPage(50)"
                        >50 records</a
                    >
                    <a class="dropdown-item" @click="setPerPage(100)"
                        >100 records</a
                    >
                </div>
            </div>

            <ul class="pagination m-0 ms-auto">
                <li class="page-item" :class="{ disabled: page === 1 }">
                    <a
                        class="page-link cursor-pointer"
                        @click="goToPage(page - 1)"
                        >«</a
                    >
                </li>
                <li
                    v-for="p in paginationList"
                    :key="p"
                    class="page-item"
                    :class="{ active: p === page, disabled: p === '...' }"
                >
                    <a
                        class="page-link cursor-pointer"
                        v-if="p !== '...'"
                        @click="goToPage(p)"
                        >{{ p }}</a
                    >
                    <span class="page-link" v-else>…</span>
                </li>
                <li class="page-item" :class="{ disabled: !hasNextPage }">
                    <a
                        class="page-link cursor-pointer"
                        @click="goToPage(page + 1)"
                        >»</a
                    >
                </li>
            </ul>
        </div>
    </div>
    <!-- Modal xem chi tiết -->
    <div
        v-if="selectedLog"
        class="modal fade show d-block"
        tabindex="-1"
        style="background: rgba(0, 0, 0, 0.5)"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết ghi nhận</h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="selectedLog = null"
                    ></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID ghi nhận:</strong> {{ selectedLog.id }}</p>
                    <p>
                        <strong>Xe ID:</strong> {{ selectedLog.motorcycle_id }}
                    </p>
                    <p>
                        <strong>Người thực hiện:</strong>
                        {{ selectedLog.user?.name }}
                    </p>
                    <p>
                        <strong>Thời gian:</strong>
                        {{ new Date(selectedLog.created_at).toLocaleString() }}
                    </p>
                    <p>
                        <strong>Trạng thái:</strong>
                        {{ translateEvent(selectedLog.event) }}
                    </p>

                    <div v-if="selectedLog.field">
                        <p>
                            <strong>Trường thay đổi:</strong>
                            {{ selectedLog.field }}
                        </p>
                        <p>
                            <strong>Giá trị cũ:</strong>
                            {{ formatValue(selectedLog.old_value) }}
                        </p>
                        <p>
                            <strong>Giá trị mới:</strong>
                            {{ formatValue(selectedLog.new_value) }}
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-secondary"
                        @click="selectedLog = null"
                    >
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";

const token = localStorage.getItem("token");
const router = useRouter();

const motorcycleLogs = ref<any[]>([]);
const page = ref(1);
const perPage = ref(10);
const totalPages = ref(1);
const totalRecords = ref(0);
const sortField = ref("");
const hasNextPage = ref(false);
const sortDirection = ref("asc");

const filters = ref({
    formDate: null,
    toDate: null,
    status: null,
    motorcycle_id: null,
});
const paginationList = computed(() => {
    const pages: (number | string)[] = [];
    if (totalPages.value <= 5) {
        for (let i = 1; i <= totalPages.value; i++) pages.push(i);
    } else {
        if (page.value <= 3) {
            pages.push(1, 2, 3, "...", totalPages.value);
        } else if (page.value >= totalPages.value - 2) {
            pages.push(
                1,
                "...",
                totalPages.value - 2,
                totalPages.value - 1,
                totalPages.value
            );
        } else {
            pages.push(1, "...", page.value, "...", totalPages.value);
        }
    }
    return pages;
});
//
const selectedLog = ref<any | null>(null);

const showDetailModal = (log: any) => {
    selectedLog.value = log;
};

const translateEvent = (event: string) => {
    switch (event) {
        case "created":
            return "Tạo";
        case "updated":
            return "Cập nhật";
        case "cloned":
            return "Copy";
        case "deleted":
            return "Xóa";
        case "bulkUpdate":
            return "Cập nhật hàng loạt";
        default:
            return "Không xác định";
    }
};

const formatValue = (val: any) => {
    if (!val) return "(rỗng)";
    if (typeof val === "object") return JSON.stringify(val);
    return val;
};
//
const fetchData = async () => {
    try {
        const params = {
            page: page.value,
            per_page: perPage.value,
            sort_by: sortField.value,
            sort_order: sortDirection.value,
            from_date: filters.value.formDate,
            to_date: filters.value.toDate,
            status: filters.value.status,
            motorcycle_id: filters.value.motorcycle_id,
        };
        const res = await axios.get("/api/motorcycles/logs-list", {
            params,
            headers: { Authorization: `Bearer ${token}` },
        });
        motorcycleLogs.value = res.data.data;
        console.log("motorcycleLogs", motorcycleLogs.value);
        totalPages.value = res.data.last_page;
        page.value = res.data.current_page;
        totalRecords.value = res.data.total;
        hasNextPage.value = page.value < totalPages.value;
    } catch (error) {
        console.error("Lỗi tải danh sách xe:", error);
    }
};
const setPerPage = (value: number) => {
    perPage.value = value;
    page.value = 1;
    fetchData();
};
const goToPage = (val: number | string) => {
    if (
        val === "..." ||
        typeof val !== "number" ||
        val < 1 ||
        val > totalPages.value
    )
        return;
    page.value = val;
    fetchData();
};
const sort = (field: string) => {
    sortField.value === field
        ? (sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc")
        : ((sortField.value = field), (sortDirection.value = "asc"));
    fetchData();
};
const clearFilters = () => {
    filters.value = {
        formDate: null,
        toDate: null,
        status: null,
        motorcycle_id: null,
    };
    fetchData();
};
watch(perPage, fetchData);
onMounted(() => {
    fetchData();
});
</script>
