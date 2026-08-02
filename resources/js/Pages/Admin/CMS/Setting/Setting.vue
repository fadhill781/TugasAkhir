<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
  settings: Array,
});

const sidebarOpen = ref(true);
const currentPage = ref(1);
const perPage = ref(10);
const searchQuery = ref('');

const filteredSetting = computed(() => {
    if (!searchQuery.value) return props.settings;
    return props.settings.filter(s =>
        s.nama.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const totalPages = computed(() => Math.ceil(filteredSetting.value.length / perPage.value));

const paginatedSetting = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return filteredSetting.value.slice(start, end);
});

function handleSearch(event) {
    searchQuery.value = event.target.value;
    currentPage.value = 1;
}

function changePage(page) {
    if (page >= 1 && page <= totalPages.value) currentPage.value = page;
}

function changePerPage(value) {
    perPage.value = value;
    currentPage.value = 1;
}

function goToCreate() { router.get('/CMS/Setting/AddSetting'); }
function goToEdit(id) { router.get(`/cms-setting/${id}/edit`); }
function destroy(id) {
    if (confirm('Apakah Anda yakin ingin menghapus setting ini?')) {
        router.delete(`/cms-setting/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto', sidebarOpen ? 'pl-64' : 'pl-16', 'flex-1']">
            <div class="container mx-auto px-2 md:p-5">
                <h1>CMS Setting</h1>
                <div class="container mx-auto p-4 md:p-5">
                    <div class="bg-white rounded-lg p-3">
                        <h1 class="mb-1">DATA CMS SETTING</h1>
                        <hr>
                        <div class="ml-5 mt-10">
                            <div class="mt-10 flex justify-between items-center">
                                <input type="text" v-model="searchQuery" @input="handleSearch" placeholder="Search"
                                    class="border border-gray-300 rounded-[10px] px-4 py-2 mb-2 w-80" />
                                <button class="bg-green-500 text-white px-5 py-2 rounded hover:bg-green-600"
                                    @click="goToCreate">
                                    Tambah Setting
                                </button>
                            </div>

                            <table class="min-w-full border-collapse">
                                <thead>
                                    <tr>
                                        <th class="border-b border-black border-l-0 px-4 py-4 bg-[#1E3A8A] text-white rounded-tl-2xl">
                                            No
                                        </th>
                                        <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">
                                            Nama
                                        </th>
                                        <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">
                                            Deskripsi
                                        </th>
                                        <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">
                                            Gambar
                                        </th>
                                        <th class="border-b border-black border-r-0 px-4 py-2 bg-[#1E3A8A] text-white rounded-tr-2xl">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredSetting.length === 0">
                                        <td colspan="5" class="border-b border-black text-center px-4 py-2">
                                            Belum ada setting.
                                        </td>
                                    </tr>
                                    <tr v-else v-for="(setting, index) in paginatedSetting" :key="setting.id">
                                        <td class="border-b border-black border-l-0 px-4 py-2 text-center">
                                            {{ (currentPage - 1) * perPage + index + 1 }}
                                        </td>
                                        <td class="border-b border-black px-4 py-2 text-center">
                                            {{ setting.nama }}
                                        </td>
                                        <td class="border-b border-black px-4 py-2 text-center max-w-xs">
                                            <span class="line-clamp-2 block text-sm">{{ setting.deskripsi || '-' }}</span>
                                        </td>
                                        <td class="border-b border-black px-4 py-2 text-center">
                                            <img v-if="setting.image"
                                                 :src="`/storage/${setting.image}`"
                                                 alt="Gambar Setting"
                                                 class="w-24 h-auto mx-auto rounded" />
                                            <span v-else class="text-gray-400 text-sm">-</span>
                                        </td>
                                        <td class="border-b border-black border-r-0 px-4 py-2 text-center space-x-2">
                                            <button class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600"
                                                @click="goToEdit(setting.id)">
                                                Edit
                                            </button>
                                            <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700"
                                                @click="destroy(setting.id)">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="p-0">
                                            <div class="flex items-center justify-between px-4 py-3 bg-[#1E3A8A] rounded-b-2xl">
                                                <div class="flex items-center gap-4">
                                                    <select v-model="perPage" @change="changePerPage($event.target.value)"
                                                        class="bg-white border border-gray-300 rounded px-2 py-1 text-sm">
                                                        <option :value="10">10 per page</option>
                                                        <option :value="25">25 per page</option>
                                                        <option :value="50">50 per page</option>
                                                        <option :value="100">100 per page</option>
                                                    </select>
                                                    <span class="text-sm text-white">
                                                        Showing {{ ((currentPage - 1) * perPage) + 1 }}-{{
                                                            Math.min(currentPage * perPage, filteredSetting.length)
                                                        }} of {{ filteredSetting.length }} items
                                                    </span>
                                                </div>
                                                <div class="flex items-center">
                                                    <button @click="changePage(currentPage - 1)"
                                                        :disabled="currentPage === 1"
                                                        class="p-1 rounded hover:bg-gray-200 disabled:opacity-50 disabled:hover:bg-transparent px-3 text-white">
                                                        &lt;
                                                    </button>
                                                    <div class="flex items-center gap-1">
                                                        <button v-for="page in totalPages" :key="page"
                                                            @click="changePage(page)" :class="[
                                                                'px-3 py-1 rounded',
                                                                currentPage === page ? 'bg-white text-black' : 'hover:bg-gray-200 text-black'
                                                            ]">
                                                            {{ page }}
                                                        </button>
                                                    </div>
                                                    <button @click="changePage(currentPage + 1)"
                                                        :disabled="currentPage === totalPages"
                                                        class="p-1 rounded hover:bg-gray-200 disabled:opacity-50 disabled:hover:bg-transparent px-3 text-white">
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
