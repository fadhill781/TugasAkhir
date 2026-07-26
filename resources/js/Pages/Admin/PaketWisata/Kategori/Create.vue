<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
    nama_kategori: '',
    deskripsi: '',
    image: null,
});

function submit() {
    const data = new FormData();
    data.append('nama_kategori', form.nama_kategori);
    if (form.deskripsi) data.append('deskripsi', form.deskripsi);
    if (form.image) data.append('image', form.image);

    router.post('/paket-wisata/kategori', data, { forceFormData: true });
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1 text-2xl font-bold">Tambah Kategori</h1>
                    <hr class="mb-4">
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Nama Kategori</label>
                            <input type="text" v-model="form.nama_kategori" required
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" 
                                placeholder="Contoh: Jogjakarta, Bali, Bromo" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Deskripsi</label>
                            <textarea v-model="form.deskripsi" rows="3"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" 
                                placeholder="Deskripsi singkat tentang kategori"></textarea>
                        </div>
                        <div class="flex justify-between items-start mb-3">
                            <label class="w-1/3 font-medium text-gray-700 pt-2">Gambar Kategori</label>
                            <div class="w-2/3">
                                <input type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    @change="e => form.image = e.target.files[0]"
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm" />
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, WebP. Maks 2MB.</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                                Simpan
                            </button>
                            <a href="/paket-wisata" class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
