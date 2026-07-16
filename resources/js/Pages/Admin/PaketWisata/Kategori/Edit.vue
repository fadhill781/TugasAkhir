<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    kategori: Object,
});

const form = useForm({
    nama_kategori: props.kategori.nama_kategori,
    deskripsi: props.kategori.deskripsi,
});

function submit() {
    form.put(`/kategori/${props.kategori.id}`);
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1 text-2xl font-bold">Edit Kategori</h1>
                    <hr class="mb-4">
                    <form @submit.prevent="submit">
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
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                                Update
                            </button>
                            <a href="/kategori" class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
