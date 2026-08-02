<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    stats: Object,
    transaksis: Array,
    pendapatan_per_bulan: Array,
    filter: Object,
});

const sidebarOpen = ref(true);
const searchQuery = ref('');
const filterStatus = ref('all');

const filtered = computed(() => {
    return props.transaksis.filter(t => {
        const matchSearch = !searchQuery.value ||
            t.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (t.order_id || '').toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchStatus = filterStatus.value === 'all' || t.status === filterStatus.value;
        return matchSearch && matchStatus;
    });
});

// Total dari filtered
const totalFiltered = computed(() =>
    filtered.value.reduce((sum, t) => sum + Number(t.total), 0)
);

// Bar chart max value
const maxPendapatan = computed(() =>
    Math.max(...props.pendapatan_per_bulan.map(p => p.total), 1)
);

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
                    <h1 class="text-2xl font-bold text-gray-800">Laporan Transaksi Keuangan</h1>
                    <p class="text-gray-500 text-sm mt-1">Ringkasan pemasukan dan riwayat transaksi</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    <!-- Total Pendapatan -->
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Total Pendapatan</span>
                            <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xl font-bold text-gray-800">Rp {{ Number(stats.total_pendapatan).toLocaleString('id-ID') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ stats.total_transaksi }} transaksi lunas</p>
                    </div>

                    <!-- Pendapatan Bulan Ini -->
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Bulan Ini</span>
                            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xl font-bold text-gray-800">Rp {{ Number(stats.pendapatan_bulan_ini).toLocaleString('id-ID') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ stats.transaksi_bulan_ini }} transaksi bulan ini</p>
                    </div>

                    <!-- Menunggu Pembayaran -->
                    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Menunggu Bayar</span>
                            <div class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xl font-bold text-gray-800">Rp {{ Number(stats.nilai_pending).toLocaleString('id-ID') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ stats.total_pending }} pesanan pending</p>
                    </div>
                </div>

                <!-- Bar Chart Pendapatan 12 Bulan -->
                <div class="bg-white rounded-xl shadow p-5 mb-6">
                    <h2 class="font-bold text-gray-800 mb-4">Pendapatan 12 Bulan Terakhir</h2>
                    <div class="flex items-end gap-2 h-40">
                        <div v-for="item in pendapatan_per_bulan" :key="item.bulan"
                             class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-xs text-gray-500 font-semibold">
                                {{ item.total > 0 ? (item.total / 1000000).toFixed(1) + 'jt' : '' }}
                            </span>
                            <div class="w-full bg-[#1E3A8A] rounded-t transition-all duration-500 hover:bg-blue-600 cursor-pointer relative group"
                                 :style="`height: ${maxPendapatan > 0 ? Math.max((item.total / maxPendapatan) * 120, item.total > 0 ? 8 : 2) : 2}px`">
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10">
                                    Rp {{ Number(item.total).toLocaleString('id-ID') }}
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 truncate w-full text-center">{{ item.bulan.split(' ')[0] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div class="bg-white rounded-xl shadow p-5 mb-4">
                    <div class="flex flex-wrap gap-3 items-center justify-between">
                        <div class="flex gap-3 flex-wrap">
                            <input v-model="searchQuery" type="text" placeholder="Cari nama / order ID..."
                                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-60 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <div class="flex gap-2">
                                <button v-for="f in [{v:'all',l:'Semua'},{v:'confirmed',l:'Lunas'},{v:'pending',l:'Pending'},{v:'cancelled',l:'Dibatalkan'}]"
                                        :key="f.v" @click="filterStatus = f.v"
                                        :class="['px-4 py-2 rounded-lg border-2 text-sm font-bold transition', filterStatus === f.v ? 'bg-[#1E3A8A] text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400']">
                                    {{ f.l }}
                                </button>
                            </div>
                        </div>
                        <!-- Total filtered -->
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Total ditampilkan</p>
                            <p class="font-bold text-green-600">Rp {{ Number(totalFiltered).toLocaleString('id-ID') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tabel Transaksi -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-bold text-gray-800">Riwayat Transaksi</h2>
                        <span class="text-sm text-gray-500">{{ filtered.length }} data</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                                    <th class="px-4 py-3 text-left">No</th>
                                    <th class="px-4 py-3 text-left">Order ID</th>
                                    <th class="px-4 py-3 text-left">Nama</th>
                                    <th class="px-4 py-3 text-left">Paket</th>
                                    <th class="px-4 py-3 text-center">Peserta</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Waktu</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="filtered.length === 0">
                                    <td colspan="9" class="text-center py-10 text-gray-400">
                                        <div class="text-4xl mb-2">💳</div>
                                        Tidak ada data transaksi
                                    </td>
                                </tr>
                                <tr v-for="(t, index) in filtered" :key="t.id"
                                    class="border-t border-gray-50 hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ index + 1 }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ t.order_id || '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-800">{{ t.nama }}</div>
                                        <div class="text-xs text-gray-400">{{ t.email }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-gray-700">{{ t.kategori }}</div>
                                        <div class="text-xs text-gray-400">{{ t.paket_hari }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600">{{ t.jumlah_orang }} orang</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-800">
                                        Rp {{ Number(t.total).toLocaleString('id-ID') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span :class="['px-2 py-1 rounded-full text-xs font-bold', getStatusClass(t.status)]">
                                            {{ getStatusLabel(t.status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-400">{{ t.waktu_bayar }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="router.get(`/pesanan/${t.id}/edit`)"
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
