<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    cmsSetting: Object,
});

const form = reactive({
    nama: props.cmsSetting.nama,
    deskripsi: props.cmsSetting.deskripsi,
    footer: props.cmsSetting.footer || '',
    image: null,
    gallery: [],
    testimoni: props.cmsSetting.testimoni || '',
    nama_pentestimoni: props.cmsSetting.nama_pentestimoni || '',
});

function handleGalleryChange(event) {
    form.gallery = Array.from(event.target.files);
}

function submit() {
    const formData = new FormData();
    formData.append('nama', form.nama);
    formData.append('deskripsi', form.deskripsi);
    formData.append('_method', 'PUT');

    if (form.image) {
        formData.append('image', form.image);
    }

    // Append gallery images
    if (form.gallery.length > 0) {
        form.gallery.forEach((file, index) => {
            formData.append(`gallery[${index}]`, file);
        });
    }

    router.post(`/cms-setting/${props.cmsSetting.id}`, formData);
}

</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1 text-xl font-semibold">Edit CMS Setting</h1>
                    <hr class="mb-4">
                    <form @submit.prevent="submit">
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Nama</label>
                            <input type="text" v-model="form.nama"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Deskripsi</label>
                            <textarea v-model="form.deskripsi"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm"></textarea>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Current Image</label>
                            <div class="w-2/3">
                                <img v-if="cmsSetting.image" :src="`/storage/${cmsSetting.image}`" 
                                    alt="Current Image" class="w-32 h-auto rounded-md mb-2" />
                                <span v-else class="text-gray-400">No image</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">New Image</label>
                            <input type="file" @change="event => form.image = event.target.files[0]"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Current Gallery</label>
                            <div class="w-2/3">
                                <div v-if="cmsSetting.gallery && cmsSetting.gallery.length > 0" class="grid grid-cols-3 gap-2">
                                    <img v-for="(img, index) in cmsSetting.gallery" :key="index"
                                        :src="`/storage/${img}`" 
                                        alt="Gallery Image" class="w-full h-20 object-cover rounded-md" />
                                </div>
                                <span v-else class="text-gray-400">No gallery images</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">New Gallery Images</label>
                            <input type="file" multiple @change="handleGalleryChange"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm" />
                        </div>
                        <div v-if="form.gallery.length > 0" class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700"></label>
                            <div class="w-2/3 text-sm text-gray-600">
                                {{ form.gallery.length }} gambar baru dipilih (akan mengganti gallery lama)
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="router.get('/cms-setting')"
                                class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600 transition">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
