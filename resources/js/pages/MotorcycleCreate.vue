<template>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tạo xe mới</h3>
            </div>
            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <!-- Maker -->
                        <select v-model="form.maker_code" class="form-select m-3" required>
                            <option :value="null" disabled>Chọn hãng xe</option>
                            <option v-for="maker in makers" :key="maker.code" :value="maker.code">
                                {{ maker.name }}
                            </option>
                        </select>

                        <!-- Model -->
                        <select v-model="form.model_code" class="form-select m-3" required>
                            <option :value="null" disabled>Chọn dòng xe</option>
                            <option v-for="model in models" :key="model.code" :value="model.code">
                                {{ model.name }}
                            </option>
                        </select>
                        <div class="mb-3">
                            <label class="form-label">Giá bán</label>
                            <input v-model="form.ippan_kakaku" type="number" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá lên sàn (tự động +15%)</label>
                            <input v-model="form.noridasi_kakaku" type="number" class="form-control" readonly />
                        </div>
                        <div class="col-md-4 mb-3" v-for="(field, key) in fieldMap" :key="key">
                            <label class="form-label">{{ field.label }}</label>
                            <input v-model="form[key]" :type="field.type" class="form-control"
                                :required="field.required" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Dung tích xi lanh</label>
                            <div class="input-group">
                                <input v-model="form.haikiryo" type="number" class="form-control" required />
                                <span class="input-group-text">cc</span>
                            </div>
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
                            <label class="form-label">Trạng thái xe</label>
                            <select v-model="form.iyoukyo" class="form-select" required>
                                <option :value="1">Bán</option>
                                <option :value="0">Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="mb-3">
                        <button
                            class="btn btn-secondary"
                            @click.prevent="multiFileInput?.click()"
                        >
                            Chọn nhiều ảnh (tối đa 10)
                        </button>
                        <input
                            ref="multiFileInput"
                            type="file"
                            class="d-none"
                            multiple
                            accept="image/*"
                            @change="onMultipleImageSelect"
                        />
                    </div> -->
                    <!-- Dropzone cho ảnh -->
                    <div class="dropzone mb-3 p-4 border border-secondary rounded text-center"
                        @drop.prevent="onDropImages" @dragover.prevent @click="multiFileInput?.click()"
                        style="cursor: pointer">
                        <p class="text-muted mb-0">
                            📂 Kéo ảnh vào đây hoặc bấm để chọn ảnh (tối đa 10)
                        </p>
                        <input ref="multiFileInput" type="file" class="d-none" multiple accept="image/*"
                            @change="onMultipleImageSelect" />
                    </div>
                    <h4 class="mt-4">Ảnh (cố định 10 ảnh có thể kéo thả)</h4>

                    <div class="col-12 mb-3">
                        <draggable class="dragArea d-flex flex-wrap justify-content-start gap-3" :list="images"
                            @drop.prevent="onDropImages" @dragover.prevent>
                            <div v-for="(element, index) in images" :key="index" class="card"
                                style="width: 200px; flex: 0 0 auto">
                                <!-- Ảnh hoặc placeholder -->
                                <img :src="element.file
                                        ? getImageSrc(element)
                                        : '/images/default.png'
                                    " class="card-img-top rounded" style="height: 150px; object-fit: fill" />

                                <div class="card-body p-2">
                                    <label class="form-label d-block text-center">
                                        Ảnh {{ index + 1 }}
                                    </label>

                                    <button v-if="!element.file" class="btn btn-secondary btn-sm w-100 mb-2"
                                        @click.prevent="openFileSelector(index)">
                                        Chọn ảnh
                                    </button>

                                    <input type="file" ref="fileInputs" class="d-none" accept="image/*" @change="
                                        onSingleImageSelect($event, index)
                                        " />

                                    <button v-if="element.file" class="btn btn-danger btn-sm w-100 mt-2"
                                        @click.prevent="removeImage(index)">
                                        Xóa ảnh
                                    </button>
                                </div>
                            </div>
                        </draggable>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary" type="submit">
                            Tạo xe
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { VueDraggableNext } from "vue-draggable-next";
import { useRouter } from "vue-router";
// Đăng ký component
defineOptions({
    components: {
        draggable: VueDraggableNext,
    },
});

// Kiểu dữ liệu ảnh
type ImageBlock = {
    file: File | null;
    title: string;
};
const router = useRouter();
// State cho dropdown hãng và model
const makers = ref<{ code: number; name: string }[]>([]);
const models = ref<{ code: number; name: string }[]>([]);

// Form dữ liệu
const form = ref({
    maker_code: null,
    model_code: null,
    type: 1,
    ippan_kakaku: null,
    nensiki: null,
    soukou: null,
    color: null,
    iyoukyo: 1,
    first_year_registration: null,
    soukou_fumei_flg: 1,
    haikiryo: null,
    noridasi_kakaku: 0,
});

// Tự tính giá "noridasi_kakaku"
watch(
    () => form.value.ippan_kakaku,
    (newVal) => {
        form.value.noridasi_kakaku =
            newVal !== null && !isNaN(newVal) ? Math.round(newVal * 1.15) : 0;
    }
);

// Input file tham chiếu
const fileInputs = ref<HTMLInputElement[]>([]);

// Hàm mở file selector
const openFileSelector = (index: number) => {
    fileInputs.value[index]?.click();
};

// Mapping field để render động nếu cần
const fieldMap: Record<
    string,
    { label: string; type: string; required?: boolean }
> = {
    first_year_registration: {
        label: "Năm đăng ký",
        type: "number",
        required: true,
    },
    nensiki: { label: "Năm sản xuất", type: "number" },
    soukou: { label: "ODO", type: "number" },
};

// Danh sách ảnh (10 ảnh)
const images = ref<ImageBlock[]>(
    Array.from({ length: 10 }, (_, i) => ({
        file: null,
        title: `Ảnh ${i + 1}`,
    }))
);
const multiFileInput = ref<HTMLInputElement | null>(null);
// Xử lý chọn ảnh
const onSingleImageSelect = (event: Event, index: number) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) images.value[index].file = file;
};

// Xóa ảnh
const removeImage = (index: number) => {
    images.value[index].file = null;
};

// Hiển thị ảnh (file mới)
const getImageSrc = (element: ImageBlock) => {
    return element.file instanceof File
        ? URL.createObjectURL(element.file)
        : "";
};
//
const onMultipleImageSelect = (event: Event) => {
    const files = (event.target as HTMLInputElement).files;
    if (!files) return;

    const fileList = Array.from(files).slice(0, 10); // giới hạn tối đa 10 ảnh
    const max = Math.min(10, fileList.length);

    for (let i = 0; i < max; i++) {
        const file = fileList[i];
        images.value[i].file = file;
        images.value[i].title = `Ảnh ${i + 1}`;
    }

    // Các slot còn lại nếu chưa có ảnh thì giữ nguyên hoặc để null
    for (let i = max; i < 10; i++) {
        if (!images.value[i].file) {
            images.value[i].file = null;
            images.value[i].title = `Ảnh ${i + 1}`;
        }
    }

    (event.target as HTMLInputElement).value = "";
};

const onDropImages = (event: DragEvent) => {
    const droppedFiles = event.dataTransfer?.files;
    if (!droppedFiles) return;

    const files = Array.from(droppedFiles).filter(f => f.type.startsWith("image/")).slice(0, 10);

    for (let i = 0; i < files.length; i++) {
        images.value[i].file = files[i];
        images.value[i].title = `Ảnh ${i + 1}`;
    }

    for (let i = files.length; i < 10; i++) {
        if (!images.value[i].file) {
            images.value[i].file = null;
            images.value[i].title = `Ảnh ${i + 1}`;
        }
    }
};
const token = localStorage.getItem("token");

const submitForm = async () => {
    const formData = new FormData();

    for (const key in form.value) {
        const value = (form.value as any)[key];
        if (value !== null) formData.append(key, value);
    }

    // Thêm ảnh
    images.value.forEach((img, index) => {
        if (img.file) {
            formData.append(`images[${index}][images]`, img.file);
            formData.append(`images[${index}][title]`, img.title);
        }
    });

    try {
        const response=  await axios.post("/api/motorcycles", formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "multipart/form-data",
            },
        });
        form.value = {
            maker_code: null,
            model_code: null,
            type: 1,
            ippan_kakaku: null,
            nensiki: null,
            soukou: null,
            color: null,
            iyoukyo: 1,
            first_year_registration: null,
            soukou_fumei_flg: 1,
            haikiryo: null,
            noridasi_kakaku: 0,
        };

        images.value = Array.from({ length: 10 }, (_, i) => ({
            file: null,
            title: `Ảnh ${i + 1}`,
        }));

        alert(response.data.message); // hoặc toast.success()
        router.push("/motorcycles-view"); // ✅ chuyển trang không reload
    } catch (err: any) {
        alert(err.response?.data?.message || "Đã có lỗi xảy ra");
    }
};
watch(
    () => form.value.maker_code,
    async (makerCode) => {
        form.value.model_code = null;
        if (!makerCode) {
            models.value = [];
            return;
        }

        try {
            const res = await axios.get(`/api/motorcycles/models/${makerCode}`, {
                headers: { Authorization: `Bearer ${token}` },
            });
            models.value = res.data;
        } catch (error) {
            console.error("Không thể tải model theo maker", error);
            models.value = [];
        }
    }
);

onMounted(async () => {
    try {
        const res = await axios.get("/api/motorcycles/maker-select", {
            headers: { Authorization: `Bearer ${token}` },
        });
        makers.value = res.data;
    } catch (error) {
        console.error("Không thể tải danh sách maker", error);
    }
});
</script>

<style scoped>
.card-img-top {
    height: 120px;
    object-fit: cover;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}

.dropzone {
    background-color: #f8f9fa;
    transition: background-color 0.3s;
}

.dropzone:hover {
    background-color: #e2e6ea;
}
</style>
