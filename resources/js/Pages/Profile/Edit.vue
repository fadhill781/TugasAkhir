<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import NavbarUser from '@/includes/NavbarUser.vue';
import Footer from '@/includes/Footer.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    pesanans: { type: Array, default: () => [] },
});

const activeTab = ref('profile'); // profile | pesanan
const searchQuery = ref('');
const filterStatus = ref('all');
const uploadingId = ref(null);
const buktiForm = useForm({ bukti: null });

const page = usePage();
const user = computed(() => page.props.auth.user);

const filteredPesanan = computed(() => {
    return props.pesanans.filter(p => {
        const matchSearch = !searchQuery.value ||
            p.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (p.order_id || '').toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchStatus = filterStatus.value === 'all' || p.status === filterStatus.value;
        return matchSearch && matchStatus;
    });
});

function getStatusClass(status) {
    switch(status) {
        case 'confirmed': return 'bg-green-100 text-green-700 border-green-300';
        case 'pending': return 'bg-yellow-100 text-yellow-700 border-yellow-300';
        case 'cancelled': return 'bg-red-100 text-red-700 border-red-300';
        default: return 'bg-gray-100 text-gray-700 border-gray-300';
    }
}

function getStatusLabel(status) {
    switch(status) {
        case 'confirmed': return '✓ Lunas';
        case 'pending': return '⏳ Menunggu Pembayaran';
        case 'cancelled': return '✗ Dibatalkan';
        default: return status;
    }
}

function uploadBukti(id) {
    if (!buktiForm.bukti) return alert('Pilih file terlebih dahulu');
    buktiForm.post(route('pesanan.bukti', id), {
        onSuccess: () => { uploadingId.value = null; buktiForm.reset(); },
    });
}
</script>

<template>
    <Head title="Profil Saya" />
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-blue-50">
        <NavbarUser />

        <div class="container mx-auto pt-36 pb-12 px-4">
            <div class="max-w-5xl mx-auto">

                <!-- Profile Header -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 flex items-center gap-6 border-2 border-gray-100">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-3xl font-bold flex-shrink-0">
                        {{ user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ user?.name }}</h1>
                        <p class="text-gray-500">{{ user?.email }}</p>
                        <span class="inline-block mt-1 px-3 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                            {{ user?.role === 'admin' ? 'Admin' : 'User' }}
                        </span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex gap-2 mb-6">
                    <button @click="activeTab = 'profile'"
                            :class="['px-6 py-2 rounded-md border-2 font-bold text-sm transition-all', activeTab === 'profile' ? 'border-black bg-black text-white shadow-none' : 'border-black bg-white text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none']">
                        Profil Saya
                    </button>
                    <button @click="activeTab = 'pesanan'"
                            :class="['px-6 py-2 rounded-md border-2 font-bold text-sm transition-all flex items-center gap-2', activeTab === 'pesanan' ? 'border-black bg-black text-white shadow-none' : 'border-black bg-white text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none']">
                        Riwayat Pesanan
                        <span v-if="pesanans.length > 0" class="px-2 py-0.5 rounded-full text-xs"
                              :class="activeTab === 'pesanan' ? 'bg-white text-black' : 'bg-blue-600 text-white'">
                            {{ pesanans.length }}
                        </span>
                    </button>
                </div>

                <!-- Tab: Profil -->
                <div v-if="activeTab === 'profile'" class="space-y-6">
                    <!-- Flash -->
                    <div v-if="$page.props.flash?.success" class="p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                        {{ $page.props.flash.success }}
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
                        <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" class="max-w-xl" />
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>

                <!-- Tab: Riwayat Pesanan -->
                <div v-if="activeTab === 'pesanan'">
                    <!-- Flash -->
                    <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                        {{ $page.props.flash.success }}
                    </div>

                    <!-- Filter & Search -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <input v-model="searchQuery" type="text" placeholder="Cari nama / order ID..."
                               class="border-2 border-black rounded-md px-4 py-2 text-sm font-medium focus:outline-none w-64" />
                        <div class="flex gap-2 flex-wrap">
                            <button v-for="s in [{v:'all',l:'Semua'},{v:'pending',l:'Pending'},{v:'confirmed',l:'Lunas'},{v:'cancelled',l:'Batal'}]"
                                    :key="s.v" @click="filterStatus = s.v"
                                    :class="['px-4 py-2 rounded-md border-2 text-sm font-bold transition-all', filterStatus === s.v ? 'border-black bg-black text-white shadow-none' : 'border-black bg-white text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none']">
                                {{ s.l }}
                            </button>
                        </div>
                    </div>

                    <!-- Pesanan List -->
                    <div v-if="filteredPesanan.length > 0" class="space-y-4">
                        <div v-for="p in filteredPesanan" :key="p.id"
                             class="bg-white rounded-xl shadow-md border-2 border-gray-100 overflow-hidden">
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                    <!-- Left -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                                            <span :class="['px-3 py-1 rounded-full text-xs font-bold border', getStatusClass(p.status)]">
                                                {{ getStatusLabel(p.status) }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-mono">{{ p.order_id }}</span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ p.kategori || 'Paket Wisata' }}</h3>
                                        <p class="text-gray-500 text-sm mb-4">{{ p.paket_hari }}</p>
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                                            <div>
                                                <p class="text-gray-400 text-xs">Nama Pemesan</p>
                                                <p class="font-semibold">{{ p.nama }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 text-xs">Tanggal Berangkat</p>
                                                <p class="font-semibold">{{ p.tanggal_berangkat || '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 text-xs">Jumlah Peserta</p>
                                                <p class="font-semibold">{{ p.jumlah_orang || '-' }} Orang</p>
                                            </div>
                                        </div>

                                        <!-- Upload Bukti (pending only) -->
                                        <div v-if="p.status === 'pending'" class="mt-4">
                                            <div v-if="p.bukti_pembayaran" class="text-xs text-green-600 font-semibold mb-2">
                                                ✓ Bukti pembayaran sudah diunggah
                                            </div>
                                            <div v-if="uploadingId === p.id" class="flex items-center gap-2 mt-2 flex-wrap">
                                                <input type="file" accept=".jpg,.jpeg,.png,.pdf"
                                                       @change="e => buktiForm.bukti = e.target.files[0]"
                                                       class="text-xs border border-gray-300 rounded px-2 py-1" />
                                                <button @click="uploadBukti(p.id)" :disabled="buktiForm.processing"
                                                        class="px-3 py-1 text-xs rounded-md border-2 border-black bg-white font-bold shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none transition-all disabled:opacity-50">
                                                    Upload
                                                </button>
                                                <button @click="uploadingId = null" class="text-xs text-gray-500 hover:text-gray-700">Batal</button>
                                            </div>
                                            <button v-else @click="uploadingId = p.id"
                                                    class="mt-2 inline-flex items-center gap-1 px-3 py-1 text-xs rounded-md border-2 border-black bg-white font-bold shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                {{ p.bukti_pembayaran ? 'Ganti Bukti' : 'Upload Bukti Pembayaran' }}
                                            </button>
                                            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF. Maks 2MB</p>
                                        </div>
                                    </div>

                                    <!-- Right -->
                                    <div class="flex flex-col items-end gap-3 min-w-[160px]">
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400">Total Pembayaran</p>
                                            <p class="text-2xl font-bold text-blue-600">Rp {{ Number(p.total).toLocaleString('id-ID') }}</p>
                                        </div>
                                        
                                        <!-- Lanjutkan Pesanan button - untuk pending -->
                                         <a v-if="p.status === 'pending'" :href="`/checkout/resume/${p.id}`"
                                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 cursor-pointer rounded-md border-2 border-black bg-blue-600 font-bold text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Lanjutkan Pesanan
                                        </a>
                                        
                                        <!-- Invoice button - untuk semua status -->
                                        <a :href="`/user/invoice/${p.id}`" target="_blank"
                                           :class="['w-full inline-flex items-center justify-center gap-2 px-4 py-2 cursor-pointer rounded-md border-2 border-black font-bold transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-xs shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]', p.status === 'confirmed' ? 'bg-green-500 text-white' : 'bg-white text-black']">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            Cetak Invoice
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-20 bg-white rounded-2xl shadow-md">
                        <div class="text-6xl mb-4">📋</div>
                        <p class="text-2xl font-semibold text-gray-700 mb-2">Belum Ada Pesanan</p>
                        <p class="text-gray-500 mb-6">Anda belum memiliki riwayat pesanan</p>
                        <a href="/paket-wisata-public"
                           class="inline-flex items-center gap-2 px-6 py-3 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none">
                            Lihat Paket Wisata
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <Footer />
    </div>
</template>
