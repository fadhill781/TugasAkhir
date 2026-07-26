<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    kategoris: Array,
});

const sidebarOpen = ref(true);

function deleteKategori(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
        router.delete(`/paket-wisata/kategori/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />
    
    <div
        :class="[
            'transition-all duration-300 ease-in-out overflow-y-auto',
            sidebarOpen ? 'pl-64' : 'pl-16',
            'flex-1'
        ]"
    >
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h1 class="text-2xl font-bold">Kategori Wisata</h1>
                            <p class="text-sm text-gray-600">Buat kategori terlebih dahulu sebelum menambah paket wisata</p>
                        </div>
                        <a href="/paket-wisata/kategori/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            + Tambah Kategori
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse text-center">
                            <thead>
                                <tr class="bg-blue-600 text-black">
                                    <th class="px-4 py-3 rounded-tl-2xl">No</th>
                                    <th class="px-4 py-3">Nama Kategori</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Jumlah Paket</th>
                                    <th class="px-4 py-3 rounded-tr-2xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="kategoris.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-gray-500">Belum ada data kategori</td>
                                </tr>
                                <tr v-for="(kategori, index) in kategoris" :key="kategori.id" class="border-t border-gray-300">
                                    <td class="px-4 py-3">{{ index + 1 }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ kategori.nama_kategori }}</td>
                                    <td class="px-4 py-3">{{ kategori.deskripsi || '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-blue-600 font-bold">{{ kategori.jumlah_paket || 0 }} paket</span>
                                    </td>
                                    <td class="px-4 py-3 space-x-2">
                                        <a :href="`/paket-wisata/kategori/${kategori.id}/paket-hari`" 
                                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                            Paket Hari
                                        </a>
                                        <a :href="`/paket-wisata/kategori/${kategori.id}/edit`" 
                                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                            Edit
                                        </a>
                                        <button @click="deleteKategori(kategori.id)" 
                                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-4 px-4 py-3 bg-blue-600 text-black rounded-b-2xl">
                            <span class="font-semibold">Total: {{ kategoris.length }} kategori</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
    </div>
</template>
