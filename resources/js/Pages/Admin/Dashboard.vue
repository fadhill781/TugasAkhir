<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    stats: Object,
    pesanan_terbaru: Array,
});

const sidebarOpen = ref(true);

function getStatusClass(status) {
    switch(status) {
        case 'confirmed': return 'bg-green-100 text-green-700';
        case 'pending':   return 'bg-yellow-100 text-yellow-700';
        case 'cancelled': return 'bg-red-100 text-red-700';
        default:          return 'bg-gray-100 text-gray-600';
    }
}

function getStatusLabel(status) {
    switch(status) {
        case 'confirmed': return 'Lunas';
        case 'pending':   return 'Pending';
        case 'cancelled': return 'Dibatalkan';
        default:          return status;
    }
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto flex-1', sidebarOpen ? 'pl-64' : 'pl-16']">
            <div class="container mx-auto px-4 py-6">

                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <p class="text-gray-500 text-sm mt-1">Selamat datang di panel admin</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <!-- Total Pesanan -->
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Total Pesanan</span>
                            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-800">{{ stats.total_pesanan }}</p>
                    </div>

                    <!-- Pending -->
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Menunggu Bayar</span>
                            <div class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-800">{{ stats.pesanan_pending }}</p>
                    </div>

                    <!-- Lunas -->
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Pembayaran Lunas</span>
                            <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-gray-800">{{ stats.pesanan_lunas }}</p>
                    </div>

                    <!-- Total Pendapatan -->
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Total Pendapatan</span>
                            <div class="w-9 h-9 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-800">
                            Rp {{ Number(stats.total_pendapatan).toLocaleString('id-ID') }}
                        </p>
                    </div>
                </div>

                <!-- Second row stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Kategori Wisata</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.total_kategori }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Paket Wisata</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.total_paket }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l3-1m6 1H4m10 0l3 1V7h-3v9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Armada Mobil</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.total_mobil }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="font-bold text-gray-800">Pesanan Terbaru</h2>
                        <a href="/pesanan" class="text-blue-600 text-sm font-semibold hover:underline">Lihat Semua →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                                    <th class="px-5 py-3 text-left">Order ID</th>
                                    <th class="px-5 py-3 text-left">Nama</th>
                                    <th class="px-5 py-3 text-left">Paket</th>
                                    <th class="px-5 py-3 text-right">Total</th>
                                    <th class="px-5 py-3 text-center">Status</th>
                                    <th class="px-5 py-3 text-center">Tanggal</th>
                                    <th class="px-5 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!pesanan_terbaru || pesanan_terbaru.length === 0">
                                    <td colspan="7" class="text-center py-8 text-gray-400">Belum ada pesanan</td>
                                </tr>
                                <tr v-for="p in pesanan_terbaru" :key="p.id"
                                    class="border-t border-gray-50 hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ p.order_id || '-' }}</td>
                                    <td class="px-5 py-3 font-semibold text-gray-800">{{ p.nama }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ p.kategori }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                        Rp {{ Number(p.total).toLocaleString('id-ID') }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span :class="['px-2 py-1 rounded-full text-xs font-bold', getStatusClass(p.status)]">
                                            {{ getStatusLabel(p.status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center text-gray-500 text-xs">{{ p.created_at }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <button @click="router.get(`/pesanan/${p.id}/edit`)"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
