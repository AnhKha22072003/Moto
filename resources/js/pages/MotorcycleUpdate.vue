<template>
    <div class="page-body">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cập nhật xe</h3>
            </div>
            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <select v-model="form.maker_code" class="form-select" required>
                                <option :value="null" disabled>Chọn hãng xe</option>
                                <option v-for="maker in makers" :key="maker.code" :value="maker.code">
                                    {{ maker.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                        <select v-model="form.model_code" class="form-select" required>
                            <option :value="null" disabled>Chọn loại xe</option>
                            <option v-for="model in models" :key="model.code" :value="model.code">
                                {{ model.name }}
                            </option>
                        </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giá bán</label>
                            <input v-model="form.ippan_kakaku" type="number" class="form-control" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Giá lên sàn (tự động +15%)</label>
                            <input v-model="form.noridasi_kakaku" type="number" class="form-control" readonly />
                        </div>
                        <div class="col-md-4" v-for="(field, key) in fieldMap" :key="key">
                            <label class="form-label">{{ field.label }}</label>
                            <input v-model="form[key]" :type="field.type" class="form-control" required />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Dung tích xi lanh</label>
                            <div class="input-group">
                                <input v-model="form.haikiryo" type="number" class="form-control" required />
                                <span class="input-group-text">cc</span>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Xác nhận ODO</label>
                            <select v-model="form.soukou_fumei_flg" class="form-select" required>
                                <option :value="1">Chính xác</option>
                                <option :value="0">Không rõ</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Loại xe</label>
                            <select v-model="form.type" class="form-select" required>
                                <option :value="1">Xe mới</option>
                                <option :value="0">Xe cũ</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Màu xe</label>
                            <select v-model="form.color" class="form-select" required>
                                <option :value="null" disabled selected>
                                    Chọn màu xe
                                </option>
                                <option :value="'xanh'">Xanh</option>
                                <option :value="'Đỏ'">Đỏ</option>
                                <option :value="'Tím'">Tím</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Trạng thái xe</label>
                            <select v-model="form.iyoukyo" class="form-select" required>
                                 <option :value="2">đăng</option>
                                <option :value="1">Bán</option>
                                <option :value="0">Không</option>
                            </select>
                        </div>
                    </div>

                    <h4 class="mt-4">Ảnh (cố định 10 ảnh có thể kéo thả)</h4>
                    <div class="mt-4">
                        <draggable tag="div" class="grid-image-wrapper"  :list="images"
                            @end="updateSlots">
                            <div class="card h-100" v-for="(element, index) in images" :key="index"
                                >
                                <img v-if="element.file" :src="element.file
                                    ? getImageSrc(element)
                                    : '/images/default.png'
                                    " class="card-img-top rounded" style="height: 150px; object-fit: fill" />
                                <img v-else :src="'/images/default.png'" style="height: 150px; object-fit: fill" />
                                <div class="card-body p-2 text-center">
                                    <label class="form-label d-block text-center">Ảnh {{ index + 1 }}</label>

                                    <button v-if="!element.file" class="btn btn-secondary btn-sm w-100 mb-2"
                                        @click.prevent="openFileSelector(index)">
                                        Chọn ảnh
                                    </button>

                                    <input type="file" class="d-none" accept="image/*" @change="
                                        onSingleImageSelect($event, index)
                                        " />

                                    <button v-if="element.file" class="btn btn-danger btn-sm w-100 mt-2" @click.prevent="
                                        confirmRemoveImage(index)
                                        ">
                                        Xóa ảnh
                                    </button>
                                </div>
                            </div>
                        </draggable>
                    </div>

                    <button class="btn btn-primary mt-4" type="submit">
                        Cập nhật
                    </button>

                    <div v-if="message" class="alert alert-success mt-3">
                        {{ message }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import { VueDraggableNext as draggable } from "vue-draggable-next";

const route = useRoute();
const motorcycleId = Number(route.params.id);

type ImageBlock = {
    file: File | string | null;
    title: string;
    slot: number;
    is_new: boolean;
};

const token = localStorage.getItem("token");
const router = useRouter();
const makers = ref<{ code: number; name: string }[]>([]);
const models = ref<{ code: number; name: string }[]>([]);

const form = ref({
    maker_code: null,
    model_code: null,
    type: 1,
    ippan_kakaku: null,
    nensiki: null,
    soukou: null,
    color: null,
    iyoukyo: 1,
    soukou_fumei_flg: 1,
    haikiryo: null,
    first_year_registration: null,
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
const fieldMap = {
    first_year_registration: {
        label: "Năm đăng ký",
        type: "number",
        required: true,
    },
    nensiki: { label: "Năm sản xuất", type: "number" },
    soukou: { label: "ODO", type: "number" },
};

const images = ref<ImageBlock[]>(
    Array.from({ length: 10 }, (_, i) => ({
        file: null,
        title: `Ảnh ${i + 1}`,
        slot: i + 1,
        is_new: false,
    }))
);

const message = ref("");

const openFileSelector = (index: number) => {
    const inputs =
        document.querySelectorAll<HTMLInputElement>('input[type="file"]');
    inputs[index]?.click();
};

const onSingleImageSelect = (event: Event, index: number) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        images.value[index].file = file;
        images.value[index].is_new = true;
    }
};

const getImageSrc = (element: ImageBlock) => {
    if (element.file instanceof File) return URL.createObjectURL(element.file);
    if (typeof element.file === "string") return `/storage/${element.file}`;
    return "";
};

const confirmRemoveImage = async (index: number) => {
    if (confirm("Bạn có chắc muốn xoá ảnh này không?")) {
        images.value[index].file = null;
        images.value[index].title = "";
        images.value[index].is_new = false;
        (images.value[index] as any).is_deleted = true;
        images.value[index].slot = index + 1;
    }
};

const removeImage = (index: number) => {
    images.value[index].file = null;
    images.value[index].is_new = false;
};

const updateSlots = () => {
    images.value.forEach((img, index) => {
        img.slot = index + 1;
    });
};

const submitForm = async () => {
    const formData = new FormData();
    for (const key in form.value) {
        const value = (form.value as any)[key];
        if (value !== null) formData.append(key, value);
    }

    images.value.forEach((img, index) => {
        if ((img as any).is_deleted) {
            formData.append(`images[${index}][slot]`, img.slot.toString());
            formData.append(`images[${index}][is_deleted]`, "true");
        } else if (img.file) {
            formData.append(
                `images[${index}][images]`,
                img.file instanceof File ? img.file : img.file
            );
            formData.append(`images[${index}][title]`, img.title);
            formData.append(`images[${index}][slot]`, img.slot.toString());
            formData.append(
                `images[${index}][is_new]`,
                img.is_new ? "true" : "false"
            );
        }
    });

    try {
        await axios.post(`/api/motorcycles/${motorcycleId}`, formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "multipart/form-data",
            },
        });
        router.push("/motorcycles-view");
    } catch (err: any) {
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
};
const skipNextModelLoad = ref(false);
watch(
    () => form.value.maker_code,
    async (makerCode) => {
        if (skipNextModelLoad.value) {
            skipNextModelLoad.value = false;
            return;
        }

        if (!makerCode) {
            models.value = [];
            form.value.model_code = null;
            return;
        }

        try {
            const res = await axios.get(`/api/motorcycles/models/${makerCode}`, {
                headers: { Authorization: `Bearer ${token}` },
            });
            models.value = res.data;

            const modelCodes = res.data.map((m: any) => m.code);
            if (!modelCodes.includes(form.value.model_code)) {
                form.value.model_code = null;
            }
        } catch (error) {
            console.error("Không thể tải model theo maker:", error);
        }
    }
);
const fetchData = async () => {
    try {
        const resMakers = await axios.get("/api/motorcycles/maker-select", {
            headers: { Authorization: `Bearer ${token}` },
        });
        makers.value = resMakers.data;
    } catch (error) {
        console.error("Không thể tải danh sách makers:", error);
    }
};

const fetchMotorcycle = async () => {
    if (!motorcycleId) return;

    try {
        const res = await axios.get(`/api/motorcycles/${motorcycleId}`, {
            headers: { Authorization: `Bearer ${token}` },
        });

        form.value = { ...form.value, ...res.data };
        skipNextModelLoad.value = true;
        form.value = { ...form.value, ...res.data };

        // Gọi models theo maker_code như bạn đang làm
        if (form.value.maker_code) {
            try {
                const modelRes = await axios.get(
                    `/api/motorcycles/models/${form.value.maker_code}`,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                models.value = modelRes.data;
            } catch (modelError) {
                console.error("Không thể tải model theo maker:", modelError);
            }
        }
        // ✅ Tải ảnh
        for (let i = 0; i < 10; i++) {
            const photo = res.data[`photo${i + 1}`];
            const title = res.data[`photo${i + 1}_pr`];
            if (photo) {
                images.value[i].file = photo;
                images.value[i].title = title || `Ảnh ${i + 1}`;
                images.value[i].is_new = false;
            }
        }
    } catch (error) {
        console.error("Lỗi khi lấy dữ liệu xe:", error);
        alert("Không thể tải dữ liệu xe.");
    }
};

onMounted(async () => {
    await fetchData();           // Load makers
    await fetchMotorcycle();     // Load xe => maker_code, model_code có sẵn
    // → Khi maker_code được gán, watcher sẽ tự gọi API models
});
</script>

<style scoped>

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}
.grid-image-wrapper {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(2, 1fr);
    /* default: mobile */
}

@media (min-width: 768px) {
    .grid-image-wrapper {
        grid-template-columns: repeat(5, 1fr);
        /* desktop: 5 ảnh / hàng */
    }
}

.card-img-top {
    height: 150px;
    object-fit: cover;
}
</style>
