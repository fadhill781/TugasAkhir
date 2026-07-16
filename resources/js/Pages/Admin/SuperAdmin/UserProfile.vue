<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    user: Object,
    pesanans: Array,
});

const sidebarOpen = ref(true);

function getStatusBadge(status) {
    switch(status) {
        case 'confirmed': return 'bg-green-100 text-green-800 border border-green-300';
        case 'pending': return 'bg-yellow-100 text-yellow-800 border border-yellow-300';
        case 'cancelled': return 'bg-red-100 text-red-800 border border-red-300';
        default: return 'bg-gray-100 text-gray-700 border border-gray-300';
    }
}

function getStatusLabel(status) {
    switch(status) {
        case 'confirmed': return 'Terkonfirmasi';
        case 'pending': return 'Menunggu';
        case 'cancelled': return 'Dibatalkan';
        default: return status;
    }
}

function backToUserList() {
    router.get(route('superadmin.users'));
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto flex-1', sidebarOpen ? 'pl-64' : 'pl-16']">
            <div class="container mx-auto px-4 py-6">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <button @click="backToUserList"
                                class="p-2 hover:bg-gray-200 rounded-lg transition">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Profil User</h1>
                            <p class="text-gray-500 text-sm mt-1">Detail informasi dan riwayat pesanan</p>
                        </div>
                    </div>
                </div>

                <!-- User Profile Card -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <div class="flex items-start gap-6">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-3xl flex-shrink-0"
                             :class="user.role === 'admin' ? 'bg-blue-500' : 'bg-gray-400'">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-800">{{ user.name }}</h2>
                                <span :class="['px-3 py-1 rounded-full text-xs font-bold', user.role === 'admin' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-gray-100 text-gray-700 border border-gray-300']">
                                    {{ user.role_label }}
                                </span>
                                <span :class="['px-3 py-1 rounded-full text-xs font-bold', user.is_active ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300']">
                                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="space-y-2 text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ user.email }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Bergabung: {{ user.created_at }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Pesanan -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-800">Riwayat Pesanan</h3>
                        <span class="ml-auto bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">
                            {{ pesanans.length }} Pesanan
                        </span>
                    </div>

                    <div v-if="pesanans.length === 0" class="text-center py-12 text-gray-400">
                        <div class="text-5xl mb-3">📦</div>
                        <p class="text-lg font-semibold">Belum ada pesanan</p>
                        <p class="text-sm">User ini belum melakukan pemesanan paket wisata</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="p in pesanans" :key="p.id"
                             class="border-2 border-gray-200 rounded-xl p-6 hover:border-blue-300 transition">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Order ID</p>
                                    <p class="font-mono font-bold text-gray-800">{{ p.order_id }}</p>
                                </div>
                                <span :class="['px-3 py-1 rounded-full text-xs font-bold', getStatusBadge(p.status)]">
                                    {{ getStatusLabel(p.status) }}
                                </span>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500">Paket Wisata</p>
                                    <p class="font-semibold text-gray-800">{{ p.kategori }} - {{ p.paket_hari }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Tanggal Berangkat</p>
                                    <p class="font-semibold text-gray-800">{{ p.tanggal_berangkat }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Jumlah Peserta</p>
                                    <p class="font-semibold text-gray-800">{{ p.jumlah_orang }} orang</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                                    <p class="font-bold text-blue-600 text-lg">
                                        IDR {{ Number(p.total).toLocaleString('id-ID') }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                <div>
                                    <p class="text-sm text-gray-500">Nama Pemesan</p>
                                    <p class="font-semibold text-gray-800">{{ p.nama }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-semibold text-gray-800">{{ p.email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Telepon</p>
                                    <p class="font-semibold text-gray-800">{{ p.telepon }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Waktu Pemesanan</p>
                                    <p class="font-semibold text-gray-800">{{ p.created_at }}</p>
                                </div>
                            </div>

                            <div v-if="p.catatan" class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-1">Catatan</p>
                                <p class="text-gray-700 italic">{{ p.catatan }}</p>
                            </div>

                            <div v-if="p.bukti_pembayaran" class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-2">Bukti Pembayaran</p>
                                <a :href="p.bukti_pembayaran" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Lihat Bukti
                                </a>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    <span class="font-semibold">Status:</span> {{ p.aksi }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
