<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';
import { Caravan } from 'lucide-vue-next';

const props = defineProps({
    mobil: Array,
});


const sidebarOpen = ref(true);
const currentPage = ref(1);
const perPage = ref(10);
const searchQuery = ref('');

const filteredMobil = computed(() => {
    const sorted = [...props.mobil].sort((a, b) => b.id - a.id);
    if (!searchQuery.value) return sorted;
    return sorted.filter(car =>
        car.nama.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const totalPages = computed(() => Math.ceil(filteredMobil.value.length / perPage.value));

const paginatedMobil = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return filteredMobil.value.slice(start, end);
});

function handleSearch(event) {
    searchQuery.value = event.target.value;
    currentPage.value = 1;
}

function changePage(page) {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
}

function changePerPage(value) {
    perPage.value = value;
    currentPage.value = 1;
}

function show(id) {
    router.get('/mobil/' + id + '/edit');
}

function destroy(id) {
    if (confirm('Are you sure you want to delete this vehicle?')) {
        router.post('/mobil/' + id, { _method: 'DELETE' });
    }
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-300 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />
    
    <div
        :class="[
            'transition-all duration-300 ease-in-out overflow-y-auto',
            sidebarOpen ? 'pl-64' : 'pl-16',
            'flex-1'
        ]"
    >
            <div class="container mx-auto px-2 md:p-5">
                <h1>Data Mobil</h1>
                <div class="container mx-auto p-4 md:p-5">
                    <div class="bg-white rounded-lg p-3">
                        <h1 class="mb-1">DATA MOBIL</h1>
                        <hr>
                        <div class="ml-5 mt-10">
                            <div class="mt-10 flex justify-between items-center">
                                <input type="text" v-model="searchQuery" @input="handleSearch" placeholder="Search"
                                    class="border border-gray-300 rounded-[10px] px-4 py-2 mb-2 w-80" />
                                <button class="bg-green-600 text-white px-5 py-2 rounded"
                                    onclick="window.location.href='/add-mobil'">
                                    Tambah Mobil
                                </button>
                            </div>

                            <table class="min-w-full border-black border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border-b border-black border-l-0 px-4 py-4 bg-blue-500 text-black rounded-tl-2xl">
                                            No
                                        </th>
                                        <th class="border-b border-black px-4 py-4 bg-blue-500 text-black">
                                            Nama Mobil
                                        </th>
                                        <th class="border-b border-black px-4 py-4 bg-blue-500 text-black">
                                            Harga
                                        </th>
                                        <th class="border-b border-black px-4 py-4 bg-blue-500 text-black">
                                            Durasi (Hari)
                                        </th>
                                        <th class="border-b border-black px-4 py-4 bg-blue-500 text-black">
                                            Gambar
                                        </th>
                                        <th class="border-b border-black border-r-0 px-4 py-2 bg-blue-500 text-black rounded-tr-2xl">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredMobil.length === 0">
                                        <td colspan="5" class="border-b border-black text-center px-4 py-2">
                                            No vehicles added.
                                        </td>
                                    </tr>
                                    <tr v-else v-for="(car, index) in paginatedMobil" :key="car.id">
                                        <td class="border-b border-black border-l-0 px-4 py-2 text-center">
                                            {{ (currentPage - 1) * perPage + index + 1 }}
                                        </td>
                                        <td class="border-b border-black px-4 py-2 text-center">
                                            {{ car.nama }}
                                        </td>
                                        <td class="border-b border-black px-4 py-2 text-center">
                                            {{ car.harga }}
                                        </td>
                                        <td class="border-b border-black px-4 py-2 text-center">
                                            {{ car.durasi }}
                                        </td>
                                        <td class="border-b border-black px-4 py-2 text-center">
                                            <img :src="`/storage/uploads/admin/mobil/${car.image}`" alt="Foto Mobil" class="w-24 h-auto mx-auto rounded" />
                                        </td>
                                        <td class="border-b border-black border-r-0 px-4 py-2 text-center space-x-2">
                                            <button class="bg-blue-500 text-white px-4 py-2 rounded"
                                                @click.prevent="() => show(car.id)">
                                                Edit
                                            </button>
                                            <button class="bg-red-500 text-white px-4 py-2 rounded"
                                                @click.prevent="() => destroy(car.id)">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="p-0">
                                            <div
                                                class="flex items-center justify-between px-4 py-3 bg-blue-500 rounded-b-2xl">
                                                <div class="flex items-center gap-4">
                                                    <select v-model="perPage"
                                                        @change="changePerPage($event.target.value)"
                                                        class="bg-white border border-gray-300 rounded px-2 py-1 text-sm">
                                                        <option :value="10">10 per page</option>
                                                        <option :value="25">25 per page</option>
                                                        <option :value="50">50 per page</option>
                                                        <option :value="100">100 per page</option>
                                                    </select>
                                                    <span class="text-sm text-black">
                                                        Showing {{ ((currentPage - 1) * perPage) + 1 }}-{{
                                                            Math.min(currentPage * perPage, mobil.length)
                                                        }} of {{ mobil.length }} items
                                                    </span>
                                                </div>

                                                <div class="flex items-center">
                                                    <button @click="changePage(currentPage - 1)"
                                                        :disabled="currentPage === 1"
                                                        class="p-1 rounded hover:bg-gray-200 disabled:opacity-50 disabled:hover:bg-transparent px-3">
                                                        &lt;
                                                    </button>

                                                    <div class="flex items-center gap-1">
                                                        <button v-for="page in totalPages" :key="page"
                                                            @click="changePage(page)" :class="[
                                                                'px-3 py-1 rounded',
                                                                currentPage === page
                                                                    ? 'bg-white text-black'
                                                                    : 'hover:bg-gray-200 text-black'
                                                            ]">
                                                            {{ page }}
                                                        </button>
                                                    </div>
                                                    <button @click="changePage(currentPage + 1)"
                                                        :disabled="currentPage === totalPages"
                                                        class="p-1 rounded hover:bg-gray-200 disabled:opacity-50 disabled:hover:bg-transparent px-3">
                                                        &gt;
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>