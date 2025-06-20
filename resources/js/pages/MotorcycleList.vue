<template>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="card-title m-2">Danh sách xe</h2>
                    <router-link
                        to="/motorcycles-create"
                        class="btn btn-primary m-2"
                    >
                        Thêm xe
                    </router-link>
                    <router-link to="/bulk-view" class="btn btn-warning m-2">
                        Xem bulk
                    </router-link>
                </div>

                <div class="ms-auto d-flex gap-2">
                    <select
                        v-model="filters.maker"
                        class="form-control form-control-sm"
                    >
                        <option value="">-- Chọn hãng sản xuất --</option>
                        <option
                            v-for="maker in makers"
                            :key="maker.code"
                            :value="maker.code"
                        >
                            {{ maker.name }}
                        </option>
                    </select>

                    <select
                        v-model="filters.model"
                        class="form-control form-control-sm"
                    >
                        <option value="">-- Chọn dòng xe --</option>
                        <option
                            v-for="model in models"
                            :key="model.code"
                            :value="model.code"
                        >
                            {{ model.name }}
                        </option>
                    </select>
                    <input
                        v-model="filters.nensiki"
                        type="number"
                        class="form-control form-control-sm"
                        placeholder="Năm SX"
                    />
                    <input
                        v-model="filters.odo"
                        type="number"
                        class="form-control form-control-sm"
                        placeholder="ODO tối đa"
                    />
                    <input
                        v-model="filters.price"
                        type="number"
                        class="form-control form-control-sm"
                        placeholder="Giá tối đa"
                    />
                    <button class="btn btn-primary btn-sm" @click="fetchData">
                        Tìm
                    </button>
                    <button class="btn btn-danger btn-sm" @click="clearFilters">
                        xóa
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th href="#" class="text-reset" @click="sort('id')">
                                Code
                            </th>
                            <th
                                href="#"
                                class="link-style"
                                @click="sort('maker_code')"
                            >
                                Maker
                            </th>
                            <th
                                href="#"
                                class="link-style"
                                @click="sort('model_code')"
                            >
                                Model
                            </th>
                            <th
                                href="#"
                                class="link-style"
                                @click="sort('nensiki')"
                            >
                                Năm
                            </th>
                            <th
                                href="#"
                                class="link-style"
                                @click="sort('soukou')"
                            >
                                ODO
                            </th>
                            <th
                                href="#"
                                class="link-style"
                                @click="sort('ippan_kakaku')"
                            >
                                Giá
                            </th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in motorcycles" :key="item.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.id }}</td>
                            <td>{{ item.maker.name }}</td>
                            <td>{{ item.model.name }}</td>
                            <td>{{ item.nensiki }}</td>
                            <td>{{ item.soukou }}</td>
                            <td>{{ item.ippan_kakaku }}</td>
                            <td>
                                <button
                                    class="btn btn-success btn-sm ms-1 m-1"
                                    @click="handleChangeStatus(item)"
                                >
                                    {{ item.iyoukyo === 0
                                        ? "Ẩn"
                                        : item.iyoukyo === 1
                                        ? "Bán"
                                        : "Đăng" }}
                                </button>
                                <button
                                    class="btn btn-sm btn-info"
                                    @click="goToUpdate(item.id)"
                                >
                                    Cập nhật
                                </button>
                                <button
                                    class="btn btn-warning btn-sm ms-1"
                                    @click="handleClone(item.id)"
                                >
                                    Clone xe
                                </button>
                                <button
                                    class="btn btn-danger btn-sm ms-1"
                                    @click="handleDelete(item.id)"
                                >
                                    Xoá
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <div>
                    <label>
                        Số dòng / trang:
                        <select v-model="perPage" @change="fetchData">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </label>
                </div>
                <div>
                    <button
                        class="btn btn-sm"
                        :disabled="page === 1"
                        @click="prevPage"
                    >
                        «
                    </button>
                    Trang {{ page }}
                    <button
                        class="btn btn-sm"
                        :disabled="!hasNextPage"
                        @click="nextPage"
                    >
                        »
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
const token = localStorage.getItem("token");

const router = useRouter();
const hasNextPage = ref(false);
const motorcycles = ref<any[]>([]);
const page = ref(1);
const perPage = ref(10);
const sortField = ref("");
const sortDirection = ref("asc");
const makers = ref<{ code: number; name: string }[]>([]);
const models = ref<{ code: number; name: string }[]>([]);
const filters = ref({
    maker: "",
    model: "",
    nensiki: null,
    odo: null,
    price: null,
});
const clearFilters = () => {
    filters.value = {
        maker: "",
        model: "",
        nensiki: null,
        odo: null,
        price: null,
    };
    fetchData();
};
const fetchData = async () => {
    const params: any = {
        page: page.value,
        per_page: perPage.value,
        sort_by: sortField.value,
        sort_order: sortDirection.value,
        maker: filters.value.maker,
        model: filters.value.model,
        nensiki: filters.value.nensiki,
        max_odo: filters.value.odo,
        max_price: filters.value.price,
    };

    const token = localStorage.getItem("token");
    const response = await axios.get("/api/motorcycles", {
        params,
        headers: { Authorization: `Bearer ${token}` },
    });

    motorcycles.value = response.data.data;
    hasNextPage.value = motorcycles.value.length === perPage.value;
};

const sort = (field: string) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    fetchData();
};
const prevPage = () => {
    if (page.value > 1) {
        page.value--;
        fetchData();
    }
};

const nextPage = () => {
    if (hasNextPage.value) {
        page.value++;
        fetchData();
    }
};
const handleClone = async (id: number) => {
    if (!confirm("Bạn có chắc muốn nhân bản xe này không?")) return;

    try {
        const token = localStorage.getItem("token");
        const response = await axios.post(
            `/api/motorcycles/${id}/clone`,
            null,
            {
                headers: { Authorization: `Bearer ${token}` },
            }
        );

        alert("Clone thành công!");
        fetchData(); 
    } catch (error) {
        console.error(error);
        alert("Lỗi khi clone xe.");
    }
};
const handleDelete = async (id: number) => {
    if (!confirm("Bạn có chắc chắn muốn xoá xe này?")) return;

    try {
        const token = localStorage.getItem("token");
        await axios.delete(`/api/motorcycles/${id}`, {
            headers: { Authorization: `Bearer ${token}` },
        });
        alert("Đã xoá xe thành công!");
        fetchData();
    } catch (error) {
        alert("Lỗi khi xoá xe!");
        console.error(error);
    }
};
const goToUpdate = (id: number) => {
    router.push(`/motorcycles/${id}/edit`);
};

const handleChangeStatus = async (item) => {
    const nextStatusMap = {
        0: 1, // Ẩn → Bán
        1: 2, // Bán → Đăng
        2: 0, // Đăng → Ẩn
    };
   

    const nextStatus = nextStatusMap[item.iyoukyo];

    if (nextStatus === undefined) {
        alert("Không thể chuyển trạng thái cho xe này.");
        return;
    }

    const confirmed = confirm("Bạn có chắc muốn chuyển trạng thái?");
    if (!confirmed) return;

    try {
        const res = await axios.post(
            `/api/motorcycles/${item.id}/post`,
            {
                status: nextStatus,
            },
            {
                headers: { Authorization: `Bearer ${token}` },
            }
        );

        alert(res.data.message);
        fetchData(); 
    } catch (err) {
        if (err.response?.data?.missing_fields) {
            alert(
                `Thiếu thông tin để đăng xe:\n${err.response.data.missing_fields.join(
                    ", "
                )}`
            );
        } else {
            alert(err.response?.data?.error || "Lỗi chuyển trạng thái.");
        }
    }
};
onMounted(async () => {
    try {
        const res = await axios.get("/api/motorcycles/maker-select", {
            headers: { Authorization: `Bearer ${token}` },
        });
        const resModel = await axios.get("/api/motorcycles/model-select", {
            headers: { Authorization: `Bearer ${token}` },
        });
        makers.value = res.data;
        models.value = resModel.data;
        fetchData();
    } catch (error) {
        console.error("Không thể tải danh sách maker", error);
    }
});

watch(perPage, () => {
    page.value = 1;
    fetchData();
});
</script>
<style scoped>
.link-style {
    color: var(--bs-primary);
    cursor: pointer;
    text-decoration: none;
}
.link-style:hover {
    text-decoration: underline;
}
</style>
