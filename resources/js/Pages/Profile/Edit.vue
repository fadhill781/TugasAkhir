<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
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

const activeTab    = ref('profile');
const searchQuery  = ref('');
const filterStatus = ref('all');
const uploadingId  = ref(null);
const buktiForm    = useForm({ bukti: null });

const page = usePage();
const user = computed(() => page.props.auth.user);

const filteredPesanan = computed(() =>
    props.pesanans.filter(p => {
        const matchSearch = !searchQuery.value ||
            p.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (p.order_id || '').toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchStatus = filterStatus.value === 'all' || p.status === filterStatus.value;
        return matchSearch && matchStatus;
    })
);

function statusBadge(s) {
    if (s === 'confirmed') return { cls: 'bg-emerald-100 text-emerald-700', label: 'Lunas' };
    if (s === 'pending')   return { cls: 'bg-amber-100 text-amber-700',    label: 'Menunggu Pembayaran' };
    if (s === 'cancelled') return { cls: 'bg-red-100 text-red-600',        label: 'Dibatalkan' };
    return { cls: 'bg-gray-100 text-gray-600', label: s };
}

function uploadBukti(id) {
    if (!buktiForm.bukti) return alert('Pilih file terlebih dahulu');
    buktiForm.post(route('pesanan.bukti', id), {
        onSuccess: () => { uploadingId.value = null; buktiForm.reset(); },
    });
}

const stats = computed(() => ({
    total:   props.pesanans.length,
    lunas:   props.pesanans.filter(p => p.status === 'confirmed').length,
    pending: props.pesanans.filter(p => p.status === 'pending').length,
    nilai:   props.pesanans.filter(p => p.status === 'confirmed').reduce((s, p) => s + Number(p.total), 0),
}));

const navItems = [
    { key: 'profile',  icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', label: 'Informasi Akun' },
    { key: 'password', icon: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z', label: 'Ubah Password' },
    { key: 'pesanan',  icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', label: 'Riwayat Pesanan' },
    { key: 'danger',   icon: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', label: 'Hapus Akun' },
];
</script>

<template>
    <Head title="Profil Saya" />
    <div class="min-h-screen bg-slate-50">
        <NavbarUser />

        <div class="max-w-6xl mx-auto px-4 pt-28 pb-16">

            <!-- ── PROFILE HEADER CARD ── -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <!-- Cover -->
                <div class="h-32 bg-gradient-to-r from-blue-600 via-blue-500 to-sky-400 relative">
                    <div class="absolute inset-0"
                         style="background:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
                    </div>
                </div>

                <div class="px-6 pb-5">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-4">
                        <!-- Avatar -->
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 border-4 border-white shadow-lg flex items-center justify-center text-white text-4xl font-black flex-shrink-0">
                            {{ user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <!-- Name area -->
                        <div class="pb-1 flex-1 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ user?.name }}</h1>
                                <p class="text-gray-500 text-sm mt-0.5">{{ user?.email }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                                         bg-blue-100 text-blue-700 self-start sm:self-auto">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                {{ user?.role === 'admin' ? 'Admin' : 'Member' }}
                            </span>
                        </div>
                    </div>

                    <!-- Stats row -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-slate-100">
                        <div class="text-center py-2">
                            <p class="text-2xl font-bold text-gray-800">{{ stats.total }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Total Pesanan</p>
                        </div>
                        <div class="text-center py-2 border-l border-slate-100">
                            <p class="text-2xl font-bold text-emerald-600">{{ stats.lunas }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Lunas</p>
                        </div>
                        <div class="text-center py-2 border-l border-slate-100">
                            <p class="text-2xl font-bold text-amber-500">{{ stats.pending }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Pending</p>
                        </div>
                        <div class="text-center py-2 border-l border-slate-100">
                            <p class="text-lg font-bold text-blue-600">Rp. {{ (stats.nilai / 1000000).toFixed(1) }}jt</p>
                            <p class="text-xs text-gray-500 mt-0.5">Total Belanja</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── MAIN LAYOUT ── -->
            <div class="flex flex-col lg:flex-row gap-6">

                <!-- Sidebar Navigasi -->
                <aside class="lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden sticky top-24">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Menu Akun</p>
                        </div>
                        <nav class="p-2">
                            <button v-for="item in navItems" :key="item.key"
                                @click="activeTab = item.key"
                                :class="['w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all text-left',
                                    activeTab === item.key
                                        ? 'bg-blue-600 text-white shadow-md'
                                        : 'text-gray-600 hover:bg-slate-50']">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
                                </svg>
                                <span>{{ item.label }}</span>
                                <span v-if="item.key === 'pesanan' && stats.total > 0"
                                    :class="['ml-auto text-xs px-2 py-0.5 rounded-full font-semibold',
                                        activeTab === 'pesanan' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600']">
                                    {{ stats.total }}
                                </span>
                            </button>
                        </nav>
                    </div>
                </aside>

                <!-- Konten Utama -->
                <div class="flex-1 min-w-0">

                    <!-- Flash -->
                    <div v-if="$page.props.flash?.success"
                        class="mb-4 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $page.props.flash.success }}
                    </div>

                    <!-- ── TAB: INFORMASI AKUN ── -->
                    <div v-if="activeTab === 'profile'"
                        class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
                        <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                    </div>

                    <!-- ── TAB: UBAH PASSWORD ── -->
                    <div v-if="activeTab === 'password'"
                        class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
                        <UpdatePasswordForm />
                    </div>

                    <!-- ── TAB: RIWAYAT PESANAN ── -->
                    <div v-if="activeTab === 'pesanan'">
                        <!-- Filter -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-4 flex flex-wrap gap-3 items-center">
                            <div class="relative flex-1 min-w-[200px]">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input v-model="searchQuery" type="text" placeholder="Cari nama atau order ID..."
                                    class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <button v-for="s in [{v:'all',l:'Semua'},{v:'pending',l:'Pending'},{v:'confirmed',l:'Lunas'},{v:'cancelled',l:'Batal'}]"
                                    :key="s.v" @click="filterStatus = s.v"
                                    :class="['px-4 py-2 rounded-lg text-sm font-semibold transition-all',
                                        filterStatus === s.v
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200']">
                                    {{ s.l }}
                                </button>
                            </div>
                        </div>

                        <!-- Pesanan list -->
                        <div v-if="filteredPesanan.length > 0" class="space-y-4">
                            <div v-for="p in filteredPesanan" :key="p.id"
                                class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">

                                <!-- Header pesanan -->
                                <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold', statusBadge(p.status).cls]">
                                            {{ statusBadge(p.status).label }}
                                        </span>
                                        <span class="text-xs text-slate-400 font-mono">{{ p.order_id }}</span>
                                    </div>
                                    <span class="text-xs text-slate-400">{{ p.tanggal_berangkat || '-' }}</span>
                                </div>

                                <div class="p-5 flex flex-col md:flex-row gap-5">
                                    <!-- Detail -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900">{{ p.kategori || 'Paket Wisata' }}</h3>
                                        <p class="text-sm text-gray-500 mb-4">{{ p.paket_hari }}</p>

                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Nama Pemesan</p>
                                                <p class="text-sm font-semibold text-gray-800">{{ p.nama }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Tanggal Berangkat</p>
                                                <p class="text-sm font-semibold text-gray-800">{{ p.tanggal_berangkat || '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Jumlah Peserta</p>
                                                <p class="text-sm font-semibold text-gray-800">{{ p.jumlah_orang || '-' }} Orang</p>
                                            </div>
                                        </div>

                                        <!-- Upload Bukti -->
                                        <div v-if="p.status === 'pending'" class="mt-4 pt-4 border-t border-slate-100">
                                            <div v-if="p.bukti_pembayaran"
                                                class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-semibold mb-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Bukti pembayaran sudah diunggah
                                            </div>
                                            <div v-if="uploadingId === p.id" class="flex flex-wrap items-center gap-2">
                                                <input type="file" accept=".jpg,.jpeg,.png,.pdf"
                                                    @change="e => buktiForm.bukti = e.target.files[0]"
                                                    class="text-xs border border-slate-300 rounded-lg px-3 py-1.5 focus:outline-none" />
                                                <button @click="uploadBukti(p.id)" :disabled="buktiForm.processing"
                                                    class="px-4 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition disabled:opacity-50">
                                                    {{ buktiForm.processing ? 'Uploading...' : 'Upload' }}
                                                </button>
                                                <button @click="uploadingId = null"
                                                    class="text-xs text-gray-500 hover:text-gray-700">Batal</button>
                                            </div>
                                            <button v-else @click="uploadingId = p.id"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                {{ p.bukti_pembayaran ? 'Ganti Bukti' : 'Upload Bukti Pembayaran' }}
                                            </button>
                                            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF. Maks 2MB</p>
                                        </div>
                                    </div>

                                    <!-- Kanan: total + aksi -->
                                    <div class="flex flex-col gap-3 min-w-[170px] md:items-end">
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400">Total Pembayaran</p>
                                            <p class="text-xl font-bold text-blue-600">
                                                Rp. {{ Number(p.total).toLocaleString('id-ID') }}
                                            </p>
                                        </div>

                                        <a v-if="p.status === 'pending'" :href="`/checkout/resume/${p.id}`"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5
                                                   bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Lanjutkan Bayar
                                        </a>

                                        <a :href="`/user/invoice/${p.id}`" target="_blank"
                                            :class="['w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 font-semibold text-sm rounded-xl transition-colors',
                                                p.status === 'confirmed'
                                                    ? 'bg-emerald-500 hover:bg-emerald-600 text-white'
                                                    : 'bg-slate-100 hover:bg-slate-200 text-slate-700']">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            Invoice
                                        </a>

                                        <a :href="`/user/nota/${p.id}`" target="_blank"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5
                                                   bg-amber-400 hover:bg-amber-500 text-gray-900 font-semibold text-sm rounded-xl transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Nota
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty state -->
                        <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 py-20 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-lg font-semibold text-gray-700 mb-1">Belum Ada Pesanan</p>
                            <p class="text-sm text-gray-400 mb-6">Yuk mulai perjalanan pertama Anda!</p>
                            <a href="/paket-wisata-public"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700
                                       text-white font-semibold text-sm rounded-xl transition-colors">
                                Lihat Paket Wisata
                            </a>
                        </div>
                    </div>

                    <!-- ── TAB: HAPUS AKUN ── -->
                    <div v-if="activeTab === 'danger'"
                        class="bg-white rounded-2xl shadow-sm border border-red-100 p-6 md:p-8">
                        <DeleteUserForm />
                    </div>

                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>
