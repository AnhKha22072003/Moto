<template>
    <div class="p-4">
        <h3 class="mb-3">Cập nhật nhiều xe</h3>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <input
                            type="checkbox"
                            @change="toggleAll"
                            :checked="allSelected"
                        />
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
                    <td>
                        <input
                            type="checkbox"
                            :value="m.id"
                            v-model="selectedIds"
                        />
                    </td>
                    <td>{{ m.id }}</td>
                    <td>{{ m.maker_code }}</td>
                    <td>{{ m.model_code }}</td>
                    <td>{{ m.nensiki }}</td>
                    <td>{{ m.color }}</td>
                </tr>
            </tbody>
        </table>

        <nav>
            <ul class="pagination">
                <li
                    class="page-item"
                    :class="{ disabled: !pagination.prev_page_url }"
                >
                    <a
                        class="page-link"
                        href="#"
                        @click.prevent="
                            fetchMotorcycles(pagination.current_page - 1)
                        "
                        >«</a
                    >
                </li>
                <li
                    class="page-item"
                    v-for="link in pagination.links"
                    :key="link.label"
                    :class="{ active: link.active, disabled: !link.url }"
                >
                    <a
                        class="page-link"
                        href="#"
                        v-html="link.label"
                        @click.prevent="goToPage(link)"
                    ></a>
                </li>
                <li
                    class="page-item"
                    :class="{ disabled: !pagination.next_page_url }"
                >
                    <a
                        class="page-link"
                        href="#"
                        @click.prevent="
                            fetchMotorcycles(pagination.current_page + 1)
                        "
                        >»</a
                    >
                </li>
            </ul>
        </nav>

        <div class="row">
            <div class="col-md-4 mb-2">
                <label>ODO</label>
                <input
                    type="number"
                    v-model="form.soukou"
                    class="form-control"
                />
            </div>
            <div class="mb-3">
                <label class="form-label">Giá bán</label>
                <input
                    v-model="form.ippan_kakaku"
                    type="number"
                    class="form-control"
                />
            </div>

            <div class="mb-3">
                <label class="form-label">Giá lên sàn (tự động +15%)</label>
                <input
                    v-model="form.noridasi_kakaku"
                    type="number"
                    class="form-control"
                    readonly
                />
            </div>
            <div class="col-md-4 mb-2">
                <label>Năm SX</label>
                <input
                    type="number"
                    v-model="form.nensiki"
                    class="form-control"
                />
            </div>
             <div class="col-md-4 mb-3">
                            <label class="form-label">Màu xe</label>
                            <select
                                v-model="form.color"
                                class="form-select"
                                required
                            >
                                <option :value="null" disabled selected>
                                    Chọn màu xe
                                </option>
                                <option :value="'xanh'">Xanh</option>
                                <option :value="'Đỏ'">Đỏ</option>
                                <option :value="'Tím'">Tím</option>
                            </select>
                        </div>
            <div class="col-md-4 mb-2">
                <label>Trạng thái</label>
                <select v-model="form.iyoukyo" class="form-select">
                    <option>Không</option>
                    <option :value="1">Bán</option>
                    <option :value="0">Không</option>
                </select>
            </div>
        </div>

        <button class="btn btn-primary mt-3" @click="submit">Cập nhật</button>
        <div v-if="message" class="alert alert-success mt-3">{{ message }}</div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";

const motorcycles = ref([]);
const selectedIds = ref([]);
const allSelected = ref(false);
const message = ref("");
const pagination = ref({
    current_page: 1,
    last_page: 1,
    links: [],
});

const form = ref({
    soukou: null,
    ippan_kakaku: null,
    nensiki: null,
    color: null,
    iyoukyo: 1,
    noridasi_kakaku: 0,
});
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

const goToPage = (link) => {
    if (link.url) {
        const url = new URL(link.url);
        const page = url.searchParams.get("page");
        fetchMotorcycles(page);
    }
};

const toggleAll = () => {
    if (allSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = motorcycles.value.map((m) => m.id);
    }
    allSelected.value = !allSelected.value;
};

const submit = async () => {
    if (selectedIds.value.length === 0) {
        alert("Bạn phải chọn ít nhất 1 xe để cập nhật.");
        return;
    }
    if (
        !form.value.soukou &&
        !form.value.ippan_kakaku &&
        !form.value.nensiki &&
        !form.value.color &&
        form.value.iyoukyo === 1
    ) {
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
        window.location.href = "/motorcycles-view";
    } catch (err) {
        alert(err.response?.data?.message || "Lỗi cập nhật");
    }
};

onMounted(() => fetchMotorcycles());
</script>
