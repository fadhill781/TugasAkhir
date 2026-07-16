<script setup>
import { useForm } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';
import { ref } from 'vue';

const props = defineProps({
    pesanan: Object,
});

const sidebarOpen = ref(true);

const form = useForm({
    _method: 'put',
    nama: props.pesanan.nama,
    email: props.pesanan.email || '',
    telepon: props.pesanan.telepon || '',
    jumlah_orang: props.pesanan.jumlah_orang || 1,
    tanggal_berangkat: props.pesanan.tanggal_berangkat || '',
    catatan: props.pesanan.catatan || '',
    waktu: props.pesanan.waktu,
    total: props.pesanan.total,
    status: props.pesanan.status || 'pending',
    aksi: props.pesanan.aksi,
});

function submit(id) {
    form.post(`/pesanan/${id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Success
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto', sidebarOpen ? 'pl-64' : 'pl-16', 'flex-1']">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-6 shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-2xl font-bold">Edit Pesanan</h1>
                        <a href="/pesanan" class="text-blue-600 hover:text-blue-800">← Kembali</a>
                    </div>
                    <hr class="mb-6">
                    
                    <form @submit.prevent="submit(pesanan.id)" class="space-y-4">
                        <!-- Order Info -->
                        <div class="bg-blue-50 p-4 rounded-lg mb-6">
                            <h3 class="font-semibold text-gray-800 mb-2">Informasi Pesanan</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Order ID:</span>
                                    <span class="font-semibold ml-2">{{ pesanan.order_id || '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Paket:</span>
                                    <span class="font-semibold ml-2">{{ pesanan.kategori || '-' }} - {{ pesanan.paket_hari || '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" v-model="form.nama" required
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" v-model="form.email" required
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                            </div>

                            <!-- Telepon -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Nomor Telepon <span class="text-red-500">*</span></label>
                                <input type="tel" v-model="form.telepon" required
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                            </div>

                            <!-- Jumlah Orang -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Jumlah Peserta</label>
                                <input type="number" v-model="form.jumlah_orang" min="1"
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                            </div>

                            <!-- Tanggal Berangkat -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Tanggal Keberangkatan</label>
                                <input type="date" v-model="form.tanggal_berangkat"
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                            </div>

                            <!-- Total -->
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Total Harga (Rp)</label>
                                <input type="number" v-model="form.total"
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Catatan</label>
                            <textarea v-model="form.catatan" rows="3"
                                class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm"></textarea>
                        </div>

                        <!-- Bukti Pembayaran -->
                        <div v-if="pesanan.bukti_pembayaran" class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <label class="block text-gray-700 font-bold mb-2">Bukti Pembayaran dari User</label>
                            <a :href="pesanan.bukti_pembayaran" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-md border-2 border-black bg-white font-bold text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none transition-all text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Bukti Pembayaran
                            </a>
                        </div>

                        <!-- Status Pembayaran -->
                        <div class="p-4 rounded-lg">
                            <label class="block text-gray-700 font-bold mb-3">Status Pembayaran <span class="text-red-500">*</span></label>
                            <div class="flex flex-col gap-3">
                                <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-all"
                                       :class="form.status === 'pending' ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" v-model="form.status" value="pending" class="w-4 h-4 text-yellow-500" />
                                    <div>
                                        <span class="font-semibold text-gray-800">Pending</span>
                                        <p class="text-xs text-gray-500">Menunggu Pembayaran</p>
                                    </div>
                                    <span class="ml-auto px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded">PENDING</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-all"
                                       :class="form.status === 'confirmed' ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" v-model="form.status" value="confirmed" class="w-4 h-4 text-green-500" />
                                    <div>
                                        <span class="font-semibold text-gray-800">Lunas</span>
                                        <p class="text-xs text-gray-500">Pembayaran Berhasil</p>
                                    </div>
                                    <span class="ml-auto px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">LUNAS</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-all"
                                       :class="form.status === 'cancelled' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" v-model="form.status" value="cancelled" class="w-4 h-4 text-red-500" />
                                    <div>
                                        <span class="font-semibold text-gray-800">Dibatalkan</span>
                                        <p class="text-xs text-gray-500">Pesanan Dibatalkan</p>
                                    </div>
                                    <span class="ml-auto px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded">BATAL</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit" :disabled="form.processing"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold disabled:opacity-50">
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                            <a href="/pesanan" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 font-semibold">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
