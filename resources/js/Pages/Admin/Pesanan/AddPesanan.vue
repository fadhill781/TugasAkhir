<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Sidebar from '@/includes/Sidebar.vue';

// ── Helpers format Rp ──
function toRpDisplay(val) {
    const n = Number(val);
    if (!n) return '';
    return n.toLocaleString('id-ID');
}

const totalFocused  = ref(false);
const totalDisplay  = ref('');

const props = defineProps({
    kategoris: Array,
});

const sidebarOpen = ref(true);

const form = reactive({
    paket_hari_id:      '',
    nama:               '',
    email:              '',
    telepon:            '',
    jumlah_orang:       1,
    tanggal_berangkat:  '',
    catatan:            '',
    waktu:              new Date().toISOString().slice(0, 10),
    total:              0,
    status:             'pending',
});

// ── Kategori & Paket Hari ──
const selectedKategoriId = ref('');

const paketHariOptions = computed(() => {
    if (!selectedKategoriId.value) return [];
    const k = props.kategoris?.find(k => k.id == selectedKategoriId.value);
    return k ? k.paket_haris : [];
});

// Auto-hitung total saat paket atau jumlah orang berubah
watch([() => form.paket_hari_id, () => form.jumlah_orang], () => {
    if (form.paket_hari_id && form.jumlah_orang) {
        const ph = paketHariOptions.value.find(p => p.id == form.paket_hari_id);
        if (ph) {
            form.total = ph.harga * form.jumlah_orang;
            totalDisplay.value = toRpDisplay(form.total);
            return;
        }
    }
    // Jika tidak ada paket dipilih, biarkan total diisi manual
});

// Reset paket_hari_id saat kategori berubah
watch(selectedKategoriId, () => {
    form.paket_hari_id = '';
    form.total = 0;
    totalDisplay.value = '';
});

function onTotalFocus() {
    totalFocused.value = true;
    totalDisplay.value = form.total ? String(form.total) : '';
}
function onTotalInput(e) {
    const raw = e.target.value.replace(/[^\d]/g, '');
    totalDisplay.value = raw;
    form.total = parseInt(raw, 10) || 0;
}
function onTotalBlur() {
    totalFocused.value = false;
    totalDisplay.value = toRpDisplay(form.total);
}

function formatRp(v) {
    return 'Rp ' + Number(v).toLocaleString('id-ID');
}

const statusOptions = [
    { value: 'pending',   label: 'Pending',   color: 'bg-yellow-100 text-yellow-700' },
    { value: 'confirmed', label: 'Lunas',      color: 'bg-green-100 text-green-700'  },
    { value: 'cancelled', label: 'Dibatalkan', color: 'bg-red-100 text-red-700'      },
];

const statusColor = computed(() => {
    return statusOptions.find(s => s.value === form.status)?.color ?? '';
});

const statusLabel = computed(() => {
    return statusOptions.find(s => s.value === form.status)?.label ?? '-';
});

const selectedPaketHari = computed(() =>
    paketHariOptions.value.find(p => p.id == form.paket_hari_id)
);

const selectedKategori = computed(() =>
    props.kategoris?.find(k => k.id == selectedKategoriId.value)
);

const canSave = computed(() =>
    form.nama.trim() &&
    form.waktu &&
    form.total > 0
);

const loading = ref(false);

function submit() {
    if (!canSave.value) return;
    loading.value = true;
    router.post('/admin/add-pesanan', form, {
        onFinish: () => { loading.value = false; },
    });
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <Sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto flex-1', sidebarOpen ? 'pl-64' : 'pl-16']">
            <div class="container mx-auto px-4 py-6 max-w-5xl">

                <!-- Page Title -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Tambah Pesanan</h1>
                        <p class="text-sm text-gray-500 mt-1">Input data pesanan baru secara manual</p>
                    </div>
                    <button @click="router.visit('/admin/pesanan')" type="button"
                        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50">
                        ← Kembali
                    </button>
                </div>

                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <!-- ─── FORM KIRI ─── -->
                        <div class="lg:col-span-2 space-y-5">

                            <!-- Info Pesanan -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                                <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">Info Pesanan</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Pesan <span class="text-red-500">*</span></label>
                                        <input v-model="form.waktu" type="date"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                                        <select v-model="form.status"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option v-for="s in statusOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Pelanggan -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                                <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">Data Pelanggan</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                        <input v-model="form.nama" type="text" placeholder="Nama lengkap pelanggan"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                                        <input v-model="form.email" type="email" placeholder="email@contoh.com"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Telepon</label>
                                        <input v-model="form.telepon" type="tel" placeholder="08xxxxxxxxxx"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Paket -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                                <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">Detail Paket Wisata</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori Destinasi</label>
                                        <select v-model="selectedKategoriId"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option v-for="k in kategoris" :key="k.id" :value="k.id">{{ k.nama_kategori }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Durasi Paket</label>
                                        <select v-model="form.paket_hari_id" :disabled="!paketHariOptions.length"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-400">
                                            <option value="">-- Pilih Durasi --</option>
                                            <option v-for="ph in paketHariOptions" :key="ph.id" :value="ph.id">
                                                {{ ph.durasi_hari }} Hari — {{ formatRp(ph.harga) }}/orang
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Orang</label>
                                        <input v-model.number="form.jumlah_orang" type="number" min="1"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Berangkat</label>
                                        <input v-model="form.tanggal_berangkat" type="date"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                                            Total Harga <span class="text-red-500">*</span>
                                            <span v-if="selectedPaketHari" class="text-gray-400 font-normal normal-case ml-1">
                                                — otomatis dihitung dari paket
                                            </span>
                                        </label>
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                                            <span class="px-3 py-2 bg-gray-50 text-gray-500 text-xs font-semibold border-r border-gray-300 whitespace-nowrap">Rp.</span>
                                            <input
                                                :value="totalFocused ? totalDisplay : toRpDisplay(form.total)"
                                                @focus="onTotalFocus"
                                                @input="onTotalInput"
                                                @blur="onTotalBlur"
                                                type="text"
                                                inputmode="numeric"
                                                placeholder="0"
                                                class="flex-1 px-3 py-2 text-sm focus:outline-none bg-white" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                                <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">
                                    Catatan <span class="text-gray-400 font-normal normal-case">(opsional)</span>
                                </h2>
                                <textarea v-model="form.catatan" rows="3"
                                    placeholder="Catatan atau permintaan khusus pelanggan..."
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 resize-none">
                                </textarea>
                            </div>

                        </div>

                        <!-- ─── RINGKASAN (sticky) ─── -->
                        <div>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                                <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">Ringkasan</h2>

                                <div class="space-y-2.5 text-sm mb-5">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Pelanggan</span>
                                        <span class="font-semibold text-gray-800 text-right max-w-[55%]">{{ form.nama || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Telepon</span>
                                        <span class="font-semibold text-gray-800">{{ form.telepon || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Destinasi</span>
                                        <span class="font-semibold text-gray-800 text-right max-w-[55%]">{{ selectedKategori?.nama_kategori || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Durasi</span>
                                        <span class="font-semibold text-gray-800">
                                            {{ selectedPaketHari ? selectedPaketHari.durasi_hari + ' Hari' : '-' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Jumlah Orang</span>
                                        <span class="font-semibold text-gray-800">{{ form.jumlah_orang || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Tgl Berangkat</span>
                                        <span class="font-semibold text-gray-800">{{ form.tanggal_berangkat || '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Status</span>
                                        <span :class="['text-xs font-bold px-2 py-0.5 rounded-full', statusColor]">
                                            {{ statusLabel }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-gray-800">TOTAL</span>
                                        <span class="font-black text-blue-700 text-base">{{ formatRp(form.total) }}</span>
                                    </div>
                                </div>

                                <!-- Validasi hint -->
                                <div v-if="!canSave" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700">
                                    Lengkapi <strong>Nama</strong>, <strong>Tanggal Pesan</strong>, dan <strong>Total Harga</strong> sebelum menyimpan.
                                </div>

                                <!-- Tombol Simpan -->
                                <button type="submit"
                                    :disabled="!canSave || loading"
                                    class="mt-4 w-full flex items-center justify-center gap-2
                                           bg-blue-600 hover:bg-blue-700
                                           disabled:bg-gray-300 disabled:cursor-not-allowed
                                           text-white font-bold py-3 rounded-xl text-sm transition-colors">
                                    <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ loading ? 'Menyimpan...' : 'Simpan Pesanan' }}
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
