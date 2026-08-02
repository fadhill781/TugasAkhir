<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    pesanan: Array,
});

const page = usePage();
const sidebarOpen = ref(true);
const currentPage = ref(1);
const perPage = ref(10);
const searchQuery = ref('');
const sortOrder = ref('newest'); // newest, oldest

const filteredPesanan = computed(() => {
    let data = props.pesanan;
    if (searchQuery.value) {
        data = data.filter(p =>
            p.nama.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }
    // Sort
    return [...data].sort((a, b) => {
        if (sortOrder.value === 'newest') return b.id - a.id;
        return a.id - b.id;
    });
});

const totalPages = computed(() => Math.ceil(filteredPesanan.value.length / perPage.value));

const paginatedPesanan = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filteredPesanan.value.slice(start, start + perPage.value);
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

function show(id) { router.get(`/pesanan/${id}/edit`); }

function getStatusBadgeClass(status) {
    switch(status) {
        case 'confirmed': return 'bg-green-500 text-white';
        case 'pending': return 'bg-yellow-500 text-white';
        case 'cancelled': return 'bg-red-500 text-white';
        default: return 'bg-gray-100 text-gray-700';
    }
}

function getStatusLabel(status) {
    switch(status) {
        case 'confirmed': return 'Lunas';
        case 'pending': return 'Pending';
        case 'cancelled': return 'Dibatalkan';
        default: return status;
    }
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto', sidebarOpen ? 'pl-64' : 'pl-16', 'flex-1']">
            <div class="container mx-auto px-2 md:p-5">
                <h1 class="text-2xl font-bold mb-5">Pesanan</h1>

                <!-- Flash Message -->
                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $page.props.flash.success }}
                </div>

                <div class="bg-white rounded-lg p-5 shadow">
                    <div class="flex justify-between items-center mb-4 gap-3 flex-wrap">
                        <input type="text" v-model="searchQuery" @input="handleSearch" placeholder="Cari nama pesanan..."
                            class="border border-gray-300 rounded px-4 py-2 w-64" />
                        <!-- Sort -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">Urutkan:</label>
                            <select v-model="sortOrder" class="border border-gray-300 rounded px-3 py-2 text-sm">
                                <option value="newest">Terbaru</option>
                                <option value="oldest">Terlama</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-sm font-semibold"
                                @click="() => router.get('/add-pesanan')">
                                Tambah Pesanan
                            </button>
                            <a href="/nota-manual"
                                class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm font-semibold inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Buat Nota
                            </a>
                        </div>
                    </div>

                    <table class="min-w-full border-collapse text-center">
                        <thead>
                            <tr class="bg-[#1E3A8A] text-white">
                                <th class="px-4 py-3 rounded-tl-2xl">No</th>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Paket</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 rounded-tr-2xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="filteredPesanan.length === 0">
                                <td colspan="8" class="py-4">Tidak ada data pesanan.</td>
                            </tr>
                            <tr v-for="(p, index) in paginatedPesanan" :key="p.id" class="border-t border-gray-300">
                                <td class="py-3 px-4">{{ (currentPage - 1) * perPage + index + 1 }}</td>
                                <td class="py-3 px-4 text-sm font-mono text-gray-700">{{ p.order_id || '-' }}</td>
                                <td class="py-3 px-4 text-sm">{{ p.tanggal_berangkat || '-' }}</td>
                                <td class="py-3 px-4">{{ p.nama }}</td>
                                <td class="py-3 px-4 text-sm">
                                    <div>{{ p.kategori || '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ p.paket_hari || '-' }}</div>
                                </td>
                                <td class="py-3 px-4 font-semibold">Rp. {{ p.total.toLocaleString('id-ID') }}</td>
                                <td class="py-3 px-4">
                                    <span :class="['px-3 py-1 rounded text-l font-bold', getStatusBadgeClass(p.status)]">
                                        {{ getStatusLabel(p.status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 space-x-2">
                                    <a :href="`/pesanan/${p.id}/invoice`" target="_blank"
                                       class="inline-block bg-green-500 text-white px-4 py-1 rounded hover:bg-green-600">
                                        Invoice
                                    </a>
                                    <a :href="`/pesanan/${p.id}/nota`" target="_blank"
                                       class="inline-block bg-yellow-500 text-white px-4 py-1 rounded hover:bg-yellow-600">
                                        Nota
                                    </a>
                                    <button class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600" @click="() => show(p.id)">Edit</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

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
                                Menampilkan {{ ((currentPage - 1) * perPage) + 1 }} - {{ Math.min(currentPage * perPage, props.pesanan.length) }}
                                dari {{ props.pesanan.length }} data
                            </span>
                        </div>

                        <div class="flex items-center space-x-1">
                            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                                class="px-3 py-1 rounded hover:bg-gray-200 disabled:opacity-50 text-white">
                                &lt;
                            </button>
                            <button v-for="page in totalPages" :key="page" @click="changePage(page)"
                                :class="['px-3 py-1 rounded', currentPage === page ? 'bg-white text-black' : 'hover:bg-gray-200 text-black']">
                                {{ page }}
                            </button>
                            <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
                                class="px-3 py-1 rounded hover:bg-gray-200 disabled:opacity-50 text-white">
                                &gt;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
