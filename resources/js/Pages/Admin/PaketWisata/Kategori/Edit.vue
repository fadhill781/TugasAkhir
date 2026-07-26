<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    kategori: Object,
});

const form = useForm({
    _method: 'PUT',
    nama_kategori: props.kategori.nama_kategori,
    deskripsi: props.kategori.deskripsi || '',
    image: null,
});

function submit() {
    form.post(`/paket-wisata/kategori/${props.kategori.id}`, {
        forceFormData: true,
    });
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1 text-2xl font-bold">Edit Kategori</h1>
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
                                <!-- Preview gambar lama -->
                                <div v-if="kategori.image_url && !form.image" class="mb-2">
                                    <img :src="kategori.image_url" alt="Gambar saat ini"
                                         class="w-48 h-32 object-cover rounded-lg border border-gray-200" />
                                    <p class="text-xs text-gray-500 mt-1">Gambar saat ini. Upload baru untuk mengganti.</p>
                                </div>
                                <input type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    @change="e => form.image = e.target.files[0]"
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm" />
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, WebP. Maks 2MB.</p>
                                <p v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" :disabled="form.processing"
                                    class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                                {{ form.processing ? 'Menyimpan...' : 'Update' }}
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
