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

                    <h4 class="mt-4">Ảnh (tối đa 10 ảnh, kéo thả để sắp xếp)</h4>

                    <!-- Dropzone -->
                    <div class="mt-4">
                        <div class="dropzone p-4 border border-secondary rounded text-center"
                            @drop.prevent="onDropImages" @dragover.prevent @click="multiFileInput?.click()"
                            style="cursor: pointer">
                            <p class="text-muted mb-0">📂 Kéo ảnh vào đây hoặc bấm để chọn ảnh (tối đa 10)</p>
                            <input ref="multiFileInput" type="file" class="d-none" multiple accept="image/*"
                                @change="onMultipleImageSelect" />
                        </div>
                    </div>

                    <!-- Preview ảnh -->
                    <div class="mt-4">
                        <draggable tag="div" class="grid-image-wrapper" :list="images" @end="updateSlots">
                            <div class="card h-100" v-for="(element, index) in images" :key="index">
                                <img :src="element.file ? getImageSrc(element) : '/images/default.png'"
                                    class="card-img-top rounded" />

                                <div class="card-body p-2 text-center">
                                    <input type="text" v-model="element.title" placeholder="Nhập tiêu đề ảnh"
                                        class="form-control form-control-sm mb-2" />

                                    <button v-if="!element.file" class="btn btn-secondary btn-sm w-100 mb-2"
                                        @click.prevent="openFileSelector(index)">
                                        Chọn ảnh
                                    </button>

                                    <!-- ✅ Sửa ref ở đây -->
                                    <input type="file" class="d-none" accept="image/*"
                                        :ref="el => fileInputs[index] = el as HTMLInputElement"
                                        @change="onSingleImageSelect($event, index)" />

                                    <button v-if="element.file" class="btn btn-danger btn-sm w-100"
                                        @click.prevent="removeImage(index)">
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
const fileInputs = ref<HTMLInputElement[]>([]);
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
    fileInputs.value[index]?.click();
};



const getImageSrc = (element: ImageBlock) => {
    if (element.file instanceof File) return URL.createObjectURL(element.file);
    if (typeof element.file === "string") return `/storage/${element.file}`;
    return "";
};
//

const multiFileInput = ref<HTMLInputElement | null>(null);

// Xóa ảnh & tự động đôn ảnh lên
const removeImage = (index: number) => {
    // Xóa ảnh tại vị trí index
    images.value.splice(index, 1);

    // Đôn toàn bộ ảnh phía sau lên và giữ đúng 10 slot
    const filledImages = images.value.filter(img => img.file !== null);
    const newImages: ImageBlock[] = [];

    // Đôn ảnh có file lên đầu, gán lại slot
    for (let i = 0; i < filledImages.length; i++) {
        newImages.push({
            ...filledImages[i],
            slot: i + 1,
        });
    }

    // Đệm ảnh trống để đủ 10 slot
    while (newImages.length < 10) {
        newImages.push({
            file: null,
            title: "",
            slot: newImages.length + 1,
            is_new: false,
        });
    }

    images.value = newImages;
};
// Tự động update lại slot sau khi kéo thả
const updateSlots = () => {
    images.value.forEach((img, idx) => {
        img.slot = idx + 1;
    });
};

// Thêm nhiều ảnh vào các slot trống
const addImagesToEmptySlots = (fileList: FileList) => {
    const files = Array.from(fileList).filter(f => f.type.startsWith("image/"));
    const emptySlots = images.value.filter(img => !img.file);

    if (files.length > emptySlots.length) {
        alert(`Chỉ còn ${emptySlots.length} slot trống, bạn đang upload ${files.length} ảnh.`);
    }

    let fileIndex = 0;

    for (let img of images.value) {
        if (!img.file && fileIndex < files.length) {
            img.file = files[fileIndex++];
            img.title = "";
            img.is_new = true;
        }
    }

    updateSlots();
};

// Xử lý chọn ảnh nhiều file
const onMultipleImageSelect = (event: Event) => {
    const files = (event.target as HTMLInputElement).files;
    if (!files) return;

    addImagesToEmptySlots(files);
    (event.target as HTMLInputElement).value = "";
};

// Kéo thả vào dropzone
const onDropImages = (event: DragEvent) => {
    const droppedFiles = event.dataTransfer?.files;
    if (!droppedFiles) return;

    addImagesToEmptySlots(droppedFiles);
};

// Xử lý chọn 1 ảnh vào slot cụ thể
const onSingleImageSelect = (event: Event, index: number) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        images.value[index].file = file;
        images.value[index].is_new = true;
        images.value[index].title = `Ảnh ${index + 1}`;
    }
};
//
const confirmRemoveImage = async (index: number) => {
    if (confirm("Bạn có chắc muốn xoá ảnh này không?")) {
        images.value[index].file = null;
        images.value[index].title = "";
        images.value[index].is_new = false;
        (images.value[index] as any).is_deleted = true;
        images.value[index].slot = index + 1;
    }
};


const submitForm = async () => {
    const formData = new FormData();

    // Thêm dữ liệu form cơ bản
    for (const key in form.value) {
        const value = (form.value as any)[key];
        if (value !== null) formData.append(key, value);
    }

    // Gom lại danh sách ảnh hợp lệ (đã đôn lên sẵn)
    const filledImages = images.value.filter(img => img.file !== null);
    const newImages: ImageBlock[] = [];

    for (let i = 0; i < filledImages.length; i++) {
        newImages.push({
            ...filledImages[i],
            slot: i + 1,
        });
    }

    while (newImages.length < 10) {
        newImages.push({
            file: null,
            title: "",
            slot: newImages.length + 1,
            is_new: false,
        });
    }

    images.value = newImages;

    // Đưa vào formData
    images.value.forEach((img, index) => {
        const slot = index + 1;
        formData.append(`images[${index}][slot]`, `${slot}`);

        if (img.file) {
            formData.append(`images[${index}][images]`, img.file instanceof File ? img.file : img.file);
            formData.append(`images[${index}][title]`, img.title || `Ảnh ${slot}`);
            formData.append(`images[${index}][is_new]`, img.is_new ? "true" : "false");
        } else {
            formData.append(`images[${index}][is_deleted]`, "true");
        }
    });

    try {
        await axios.post(`/api/motorcycles/${motorcycleId}`, formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "multipart/form-data",
            },
        });

        alert("Cập nhật thành công");
        router.push("/motorcycles-view");
    } catch (err: any) {
        const errorMsg = err.response?.data?.errors
            ? Object.values(err.response.data.errors).flat().join("\n")
            : "Đã xảy ra lỗi khi cập nhật xe!";
        alert(errorMsg);
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
