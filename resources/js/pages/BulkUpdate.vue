<template>
  <div class="container py-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Cập nhật nhiều xe</h3>
      </div>
      <div class="card-body">
        <!-- BẢNG XE -->
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 50px">
                  <input type="checkbox" @change="toggleAll" :checked="allSelected" />
                </th>
                <th>ID</th>
                <th>Hãng</th>
                <th>Dòng</th>
                <th>Năm SX</th>
                <th>Màu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="m in motorcycles" :key="m.id">
                <td><input type="checkbox" :value="m.id" v-model="selectedIds" /></td>
                <td>{{ m.id }}</td>
                <td>{{ m.maker_code }}</td>
                <td>{{ m.model_code }}</td>
                <td>{{ m.nensiki }}</td>
                <td>{{ m.color }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PHÂN TRANG -->
        <nav class="mt-3">
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
              <a class="page-link" href="#" @click.prevent="fetchMotorcycles(pagination.current_page - 1)">«</a>
            </li>
            <li class="page-item" v-for="link in pagination.links" :key="link.label"
                :class="{ active: link.active, disabled: !link.url }">
              <a class="page-link" href="#" v-html="link.label" @click.prevent="goToPage(link)"></a>
            </li>
            <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
              <a class="page-link" href="#" @click.prevent="fetchMotorcycles(pagination.current_page + 1)">»</a>
            </li>
          </ul>
        </nav>

        <!-- FORM CẬP NHẬT -->
        <div class="row mt-4">
          <div class="col-md-4 mb-3">
            <label class="form-label">ODO</label>
            <input type="number" v-model="form.soukou" class="form-control" />
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Giá bán</label>
            <input type="number" v-model="form.ippan_kakaku" class="form-control" />
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Giá lên sàn (tự động +15%)</label>
            <input type="number" v-model="form.noridasi_kakaku" class="form-control" readonly />
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Năm sản xuất</label>
            <input type="number" v-model="form.nensiki" class="form-control" />
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Màu xe</label>
            <select v-model="form.color" class="form-select">
              <option :value="null" disabled selected>Chọn màu xe</option>
              <option value="xanh">Xanh</option>
              <option value="Đỏ">Đỏ</option>
              <option value="Tím">Tím</option>
            </select>
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Trạng thái</label>
            <select v-model="form.iyoukyo" class="form-select">
              <option value="1">Bán</option>
              <option value="0">Không</option>
            </select>
          </div>
        </div>

        <!-- NÚT GỬI -->
        <div class="text-end">
          <button class="btn btn-primary" @click="submit">Cập nhật</button>
        </div>

        <div v-if="message" class="alert alert-success mt-3">
          {{ message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";

<<<<<<< HEAD
const route = useRoute();
const router = useRouter();
=======
// Router và Route
const route = useRoute();
const router = useRouter();

// Kiểu dữ liệu xe
>>>>>>> 022a8811203f58a2f62e4c7de2cfc0f35bb1d60d
interface Motorcycle {
  id: number;
  maker_code: string;
  model_code: string;
  nensiki: number;
  color: string;
}

<<<<<<< HEAD
=======
// Danh sách xe và phân trang
>>>>>>> 022a8811203f58a2f62e4c7de2cfc0f35bb1d60d
const motorcycles = ref<Motorcycle[]>([]);
const selectedIds = ref<number[]>([]);
const allSelected = ref(false);
const message = ref("");

const pagination = ref({
  current_page: 1,
  last_page: 1,
  links: [] as { label: string; url: string | null; active: boolean }[],
  prev_page_url: null as string | null,
  next_page_url: null as string | null,
});

// Form cập nhật
const form = ref({
  soukou: null as number | null,
  ippan_kakaku: null as number | null,
  nensiki: null as number | null,
  color: null as string | null,
  iyoukyo: 1,
  noridasi_kakaku: 0,
});

<<<<<<< HEAD
=======
// Tự động tính giá lên sàn
>>>>>>> 022a8811203f58a2f62e4c7de2cfc0f35bb1d60d
watch(
  () => form.value.ippan_kakaku,
  (newVal) => {
    if (newVal !== null && !isNaN(newVal)) {
      form.value.noridasi_kakaku = Math.round(newVal * 1.15);
    } else {
      form.value.noridasi_kakaku = 0;
    }
  }
);

const token = localStorage.getItem("token");

// Lấy danh sách xe
const fetchMotorcycles = async (page = 1) => {
  const res = await axios.get(`/api/motorcycles?page=${page}`, {
    headers: { Authorization: `Bearer ${token}` },
  });
  motorcycles.value = res.data.data;
  pagination.value = {
    current_page: res.data.current_page,
    last_page: res.data.last_page,
    links: res.data.links,
    prev_page_url: res.data.prev_page_url,
    next_page_url: res.data.next_page_url,
  };
  allSelected.value = false;
  selectedIds.value = [];
};

// Chuyển trang
const goToPage = (link: { url: string | null }) => {
  if (link.url) {
    const url = new URL(link.url);
    const page = url.searchParams.get("page");
    if (page) fetchMotorcycles(parseInt(page));
  }
};

// Chọn tất cả
const toggleAll = () => {
  if (allSelected.value) {
    selectedIds.value = [];
  } else {
    selectedIds.value = motorcycles.value.map((m) => m.id);
  }
  allSelected.value = !allSelected.value;
};

// Gửi dữ liệu cập nhật
const submit = async () => {
  if (selectedIds.value.length === 0) {
    alert("Bạn phải chọn ít nhất 1 xe để cập nhật.");
    return;
  }

  const hasInput =
    form.value.soukou !== null ||
    form.value.ippan_kakaku !== null ||
    form.value.nensiki !== null ||
    form.value.color !== null ||
    form.value.iyoukyo !== null;

  if (!hasInput) {
    alert("Bạn phải nhập ít nhất một trường để cập nhật.");
    return;
  }

  try {
    await axios.post(
      "/api/motorcycles/bulk-update",
      {
        ids: selectedIds.value,
        fields: form.value,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );
    message.value = "Cập nhật thành công!";
    setTimeout(() => router.push("/motorcycles-view"), 1000);
  } catch (err: any) {
<<<<<<< HEAD
        if (err.response?.status === 422 && err.response?.data?.errors) {
            const validationErrors = err.response.data.errors;
            let errorMessages: string[] = [];

            for (const field in validationErrors) {
                if (Array.isArray(validationErrors[field])) {
                    errorMessages.push(...validationErrors[field]);
                }
            }

            alert(errorMessages.join('\n'));
        } else {
            alert("Đã có lỗi xảy ra");
        }
    }
=======
    console.error("Lỗi cập nhật:", err);
    alert(err.response?.data?.message || "Lỗi cập nhật");
  }
>>>>>>> 022a8811203f58a2f62e4c7de2cfc0f35bb1d60d
};

// Gọi khi khởi tạo
onMounted(() => fetchMotorcycles());
</script>
