<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
    nama: null,
    harga: null,
    image: null,
    durasi: null,
    unit_tersedia: 1,
});

function submit() {
    const formData = new FormData();
    formData.append('nama', form.nama);
    formData.append('harga', form.harga);
    formData.append('durasi', form.durasi);
    formData.append('unit_tersedia', form.unit_tersedia);

    if (form.image) {
        formData.append('image', form.image);
    }

    router.post('/add-mobil', formData);
};
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1">Tambah Mobil</h1>
                    <hr>
                    <form @submit.prevent="submit">
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Nama Mobil</label>
                            <input type="text" v-model="form.nama"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Harga</label>
                            <input type="number" v-model="form.harga"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Image</label>
                            <input type="file" @change="event => form.image = event.target.files[0]"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Durasi (Hari)</label>
                            <input type="number" v-model="form.durasi" placeholder="Contoh: 3"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Unit Tersedia</label>
                            <input type="number" v-model="form.unit_tersedia" min="0" placeholder="Contoh: 5"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>