<template>
  <div class="container py-4">
    <div class="card">
      <div class="card-header">
        <div class="flex justify-between items-center mb-4">
          <h2 class="card-title m-2">Danh sách xe</h2>
          <router-link to="/motorcycles-create" class="btn btn-primary m-2">Thêm xe</router-link>
          <router-link to="/bulk-view" class="btn btn-warning m-2">Xem bulk</router-link>
        </div>

        <!-- Filters -->
        <div class="row g-2 align-items-end mb-3">
          <div class="col-md-2 col-sm-6">
            <label class="form-label">Hãng sản xuất</label>
            <select v-model="filters.maker_code" class="form-select">
              <option value="">-- Chọn hãng --</option>
              <option v-for="maker in makers" :key="maker.code" :value="maker.code">{{ maker.name }}</option>
            </select>
          </div>

          <div class="col-md-2 col-sm-6">
            <label class="form-label">Dòng xe</label>
            <select v-model="filters.model_code" class="form-select">
              <option value="">-- Chọn dòng --</option>
              <option v-for="model in models" :key="model.code" :value="model.code">{{ model.name }}</option>
            </select>
          </div>

          <div class="col-md-2 col-sm-6">
            <label class="form-label">Năm SX</label>
            <input v-model="filters.nensiki" type="number" class="form-control" placeholder="VD: 2020" />
          </div>

          <div class="col-md-2 col-sm-6">
            <label class="form-label">ODO tối đa</label>
            <input v-model="filters.odo" type="number" class="form-control" placeholder="VD: 30000" />
          </div>

          <div class="col-md-2 col-sm-6">
            <label class="form-label">Giá tối đa</label>
            <input v-model="filters.price" type="number" class="form-control" placeholder="VD: 100000000" />
          </div>

          <div class="col-md-2 col-sm-6 d-flex gap-2">
            <button class="btn btn-primary w-100" @click="fetchData">Tìm</button>
            <button class="btn btn-outline-secondary w-100" @click="clearFilters">Xóa</button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-vcenter card-table">
          <thead>
            <tr>
              <th>ID</th>
              <th @click="sort('id')" class="link-style">Code</th>
              <th @click="sort('maker_code')" class="link-style">Maker</th>
              <th @click="sort('model_code')" class="link-style">Model</th>
              <th @click="sort('nensiki')" class="link-style">Năm</th>
              <th @click="sort('soukou')" class="link-style">ODO</th>
              <th @click="sort('ippan_kakaku')" class="link-style">Giá</th>
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
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                  <div class="dropdown-menu dropdown-menu-end">
                    <button class="dropdown-item text-success" @click="handleChangeStatus(item)">
                      {{ item.iyoukyo === 0 ? "Ẩn" : item.iyoukyo === 1 ? "Bán" : "Đăng" }}
                    </button>
                    <button class="dropdown-item text-primary" @click="goToUpdate(item.id)">Cập nhật</button>
                    <button class="dropdown-item text-warning" @click="handleClone(item.id)">Clone xe</button>
                    <button class="dropdown-item text-danger" @click="handleDelete(item.id)">Xoá</button>
                  </div>
                </div>
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
            <a class="dropdown-item" @click="setPerPage(10)">10 records</a>
            <a class="dropdown-item" @click="setPerPage(20)">20 records</a>
            <a class="dropdown-item" @click="setPerPage(50)">50 records</a>
            <a class="dropdown-item" @click="setPerPage(100)">100 records</a>
          </div>
        </div>

        <ul class="pagination m-0 ms-auto">
          <li class="page-item" :class="{ disabled: page === 1 }">
            <a class="page-link cursor-pointer" @click="goToPage(page - 1)">«</a>
          </li>
          <li v-for="p in paginationList" :key="p" class="page-item" :class="{ active: p === page, disabled: p === '...' }">
            <a class="page-link cursor-pointer" v-if="p !== '...'" @click="goToPage(p)">{{ p }}</a>
            <span class="page-link" v-else>…</span>
          </li>
          <li class="page-item" :class="{ disabled: !hasNextPage }">
            <a class="page-link cursor-pointer" @click="goToPage(page + 1)">»</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const token = localStorage.getItem("token");
const router = useRouter();

const motorcycles = ref<any[]>([]);
const makers = ref<{ code: number; name: string }[]>([]);
const models = ref<{ code: number; name: string }[]>([]);
const page = ref(1);
const perPage = ref(10);
const totalPages = ref(1);
const totalRecords = ref(0);
const sortField = ref("");
const sortDirection = ref("asc");
const hasNextPage = ref(false);

const filters = ref({
  maker_code: "",
  model_code: "",
  nensiki: null,
  odo: null,
  price: null,
});

const paginationList = computed(() => {
  const pages: (number | string)[] = [];
  if (totalPages.value <= 5) {
    for (let i = 1; i <= totalPages.value; i++) pages.push(i);
  } else {
    if (page.value <= 3) {
      pages.push(1, 2, 3, "...", totalPages.value);
    } else if (page.value >= totalPages.value - 2) {
      pages.push(1, "...", totalPages.value - 2, totalPages.value - 1, totalPages.value);
    } else {
      pages.push(1, "...", page.value, "...", totalPages.value);
    }
  }
  return pages;
});

const fetchData = async () => {
  try {
    const params = {
      page: page.value,
      per_page: perPage.value,
      sort_by: sortField.value,
      sort_order: sortDirection.value,
      maker: filters.value.maker_code,
      model: filters.value.model_code,
      nensiki: filters.value.nensiki,
      max_odo: filters.value.odo,
      max_price: filters.value.price,
    };

    const res = await axios.get("/api/motorcycles", {
      params,
      headers: { Authorization: `Bearer ${token}` },
    });

    motorcycles.value = res.data.data;
    page.value = res.data.current_page;
    totalPages.value = res.data.last_page;
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
  if (val === "..." || typeof val !== "number" || val < 1 || val > totalPages.value) return;
  page.value = val;
  fetchData();
};

const sort = (field: string) => {
  sortField.value === field
    ? (sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc")
    : (sortField.value = field, sortDirection.value = "asc");
  fetchData();
};

const clearFilters = () => {
  filters.value = { maker_code: "", model_code: "", nensiki: null, odo: null, price: null };
  fetchData();
};

const handleClone = async (id: number) => {
  if (!confirm("Bạn có chắc muốn nhân bản xe này không?")) return;
  try {
    await axios.post(`/api/motorcycles/${id}/clone`, null, {
      headers: { Authorization: `Bearer ${token}` },
    });
    alert("Clone thành công!");
    fetchData();
  } catch (err: any) {
    const errors = err.response?.data?.errors;
    if (err.response?.status === 422 && errors) {
      alert(Object.values(errors).flat().join("\n"));
    } else {
      alert("Đã có lỗi xảy ra");
    }
  }
};

const handleDelete = async (id: number) => {
  if (!confirm("Bạn có chắc chắn muốn xoá xe này?")) return;
  try {
    await axios.delete(`/api/motorcycles/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    alert("Đã xoá xe thành công!");
    fetchData();
  } catch (error) {
    alert("Lỗi khi xoá xe!");
  }
};

const goToUpdate = (id: number) => {
  router.push(`/motorcycles/${id}/edit`);
};

const handleChangeStatus = async (item: any) => {
  const next = { 0: 1, 1: 2, 2: 0 }[item.iyoukyo];
  if (next === undefined || !confirm("Bạn có chắc muốn chuyển trạng thái?")) return;

  try {
    const res = await axios.post(
      `/api/motorcycles/${item.id}/post`,
      { status: next },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    alert(res.data.message);
    fetchData();
  } catch (err) {
    const missing = err.response?.data?.missing_fields;
    alert(missing ? `Thiếu thông tin:\n${missing.join(", ")}` : "Lỗi chuyển trạng thái.");
  }
};

const fetchModelsByMaker = async (makerCode: string) => {
  try {
    const res = await axios.get(`/api/motorcycles/models/${makerCode}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    models.value = res.data;
  } catch {
    models.value = [];
  }
};

watch(() => filters.value.maker_code, async (makerCode) => {
  filters.value.model_code = "";
  if (!makerCode) return (models.value = []);
  await fetchModelsByMaker(makerCode);
});

watch(perPage, fetchData);

onMounted(async () => {
  try {
    const res = await axios.get("/api/motorcycles/maker-select", {
      headers: { Authorization: `Bearer ${token}` },
    });
    makers.value = res.data;
    fetchData();
  } catch (error) {
    console.error("Không thể tải danh sách maker", error);
  }
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
