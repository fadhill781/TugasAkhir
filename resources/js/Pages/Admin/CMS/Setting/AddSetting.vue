<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
    nama: '',
    deskripsi: '',
    image: null,
    gallery: [],
});

const errors = ref({});
const processing = ref(false);

function handleGalleryChange(event) {
    form.gallery = Array.from(event.target.files);
}

function submit() {
    const formData = new FormData();
    formData.append('nama', form.nama);
    formData.append('deskripsi', form.deskripsi);

    if (form.image) {
        formData.append('image', form.image);
    }

    // Append gallery images
    if (form.gallery.length > 0) {
        form.gallery.forEach((file, index) => {
            formData.append(`gallery[${index}]`, file);
        });
    }

    processing.value = true;
    errors.value = {};

    router.post('/cms-setting', formData, {
        forceFormData: true,
        onSuccess: () => {
            console.log('Success! Redirecting...');
            processing.value = false;
        },
        onError: (err) => {
            console.error('Error:', err);
            errors.value = err;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        }
    });
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1 text-xl font-semibold">Tambah CMS Setting</h1>
                    <hr class="mb-4">
                    
                    <!-- Error Messages -->
                    <div v-if="Object.keys(errors).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
                        </ul>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Title</label>
                            <input type="text" v-model="form.nama"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Subtitle</label>
                            <textarea v-model="form.deskripsi"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm"></textarea>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Image</label>
                            <input type="file" @change="event => form.image = event.target.files[0]"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Gallery Images</label>
                            <input type="file" multiple @change="handleGalleryChange"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-green-500 shadow-sm" />
                        </div>
                        <div v-if="form.gallery.length > 0" class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700"></label>
                            <div class="w-2/3 text-sm text-gray-600">
                                {{ form.gallery.length }} gambar dipilih
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" :disabled="processing" 
                                class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="processing">Menyimpan...</span>
                                <span v-else>Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
