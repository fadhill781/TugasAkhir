<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Sidebar from '@/includes/Sidebar.vue';

const sidebarOpen = ref(true);

// ── Form utama ──
const form = reactive({
    no_nota: 'NOTA-' + new Date().getFullYear() + '-' + String(Date.now()).slice(-4),
    tanggal: new Date().toISOString().slice(0, 10),
    status: 'Lunas',
    nama: '',
    telepon: '',
    alamat: '',
    tanggal_berangkat: '',
    jumlah_orang: '',
    durasi: '',
    dp: '',
    catatan: '',
});

// ── Items ──
const items = ref([
    { deskripsi: '', keterangan: '', qty: 1, harga: 0 },
]);

function addItem() {
    items.value.push({ deskripsi: '', keterangan: '', qty: 1, harga: 0 });
}

function removeItem(index) {
    if (items.value.length > 1) items.value.splice(index, 1);
}

// ── Computed totals ──
const subtotal = computed(() =>
    items.value.reduce((sum, i) => sum + (Number(i.qty) * Number(i.harga)), 0)
);

const dpValue = computed(() => Number(form.dp) || 0);

const sisaBayar = computed(() => subtotal.value - dpValue.value);

// ── Format currency display ──
function formatRp(val) {
    if (!val && val !== 0) return '-';
    return 'Rp. ' + Number(val).toLocaleString('id-ID');
}

// ── Submit → buka nota di tab baru ──
function printNota() {
    const payload = {
        ...form,
        items: items.value,
    };

    // POST ke /nota-manual/preview, server returns HTML, buka di tab baru
    const form_el = document.createElement('form');
    form_el.method = 'POST';
    form_el.action = '/nota-manual/preview';
    form_el.target = '_blank';

    // CSRF
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrf) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrf;
        form_el.appendChild(csrfInput);
    }

    // Semua field sebagai JSON
    const dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'data';
    dataInput.value = JSON.stringify(payload);
    form_el.appendChild(dataInput);

    document.body.appendChild(form_el);
    form_el.submit();
    document.body.removeChild(form_el);
}

const statusOptions = ['Lunas', 'Pending', 'DP'];
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <Sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto flex-1', sidebarOpen ? 'pl-64' : 'pl-16']">
            <div class="container mx-auto px-4 py-6 max-w-5xl">

                <!-- Page Title -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Buat Nota </h1>
                        <p class="text-sm text-gray-500 mt-1"></p>
                    </div>
                    <button @click="router.get('/pesanan')"
                        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50">
                        ← Kembali
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- ─── FORM (kiri 2/3) ─── -->
                    <div class="lg:col-span-2 space-y-5">

                        <!-- Info Nota -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wide mb-4 pb-2 border-b border-blue-100">Info Nota</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">No. Nota <span class="text-red-500">*</span></label>
                                    <input v-model="form.no_nota" type="text"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Nota <span class="text-red-500">*</span></label>
                                    <input v-model="form.tanggal" type="date"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status <span class="text-red-500">*</span></label>
                                    <select v-model="form.status"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option v-for="s in statusOptions" :key="s" :value="s">{{ s }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Data Pelanggan -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wide mb-4 pb-2 border-b border-blue-100">Data Pelanggan</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                                    <input v-model="form.nama" type="text" placeholder="Contoh: Budi Santoso"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Telepon</label>
                                    <input v-model="form.telepon" type="text" placeholder="08xxxxxxxxxx"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
                                    <input v-model="form.alamat" type="text" placeholder="Alamat pelanggan"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Detail Perjalanan -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wide mb-4 pb-2 border-b border-blue-100">Detail Perjalanan <span class="text-gray-400 font-normal text-xs">(opsional)</span></h2>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Berangkat</label>
                                    <input v-model="form.tanggal_berangkat" type="date"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Peserta</label>
                                    <input v-model="form.jumlah_orang" type="number" min="1" placeholder="0"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Durasi</label>
                                    <input v-model="form.durasi" type="text" placeholder="Contoh: 3 Hari 2 Malam"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Item / Rincian -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-blue-100">
                                <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wide">Rincian Item</h2>
                                <button @click="addItem" type="button"
                                    class="flex items-center gap-1 text-xs font-bold text-blue-600 border border-blue-300 rounded-lg px-3 py-1.5 hover:bg-blue-50">
                                    + Tambah Baris
                                </button>
                            </div>

                            <!-- Header -->
                            <div class="hidden sm:grid grid-cols-12 gap-2 mb-2 px-1">
                                <div class="col-span-4 text-xs font-semibold text-gray-500 uppercase">Deskripsi</div>
                                <div class="col-span-3 text-xs font-semibold text-gray-500 uppercase">Keterangan</div>
                                <div class="col-span-2 text-xs font-semibold text-gray-500 uppercase text-center">Qty</div>
                                <div class="col-span-2 text-xs font-semibold text-gray-500 uppercase text-right">Harga</div>
                                <div class="col-span-1"></div>
                            </div>

                            <!-- Rows -->
                            <div v-for="(item, idx) in items" :key="idx"
                                class="grid grid-cols-12 gap-2 mb-2 items-start">
                                <div class="col-span-12 sm:col-span-4">
                                    <input v-model="item.deskripsi" type="text" placeholder="Nama paket / item"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div class="col-span-12 sm:col-span-3">
                                    <input v-model="item.keterangan" type="text" placeholder="Keterangan (opsional)"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div class="col-span-4 sm:col-span-2">
                                    <input v-model.number="item.qty" type="number" min="1" placeholder="1"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div class="col-span-7 sm:col-span-2">
                                    <input v-model.number="item.harga" type="number" min="0" placeholder="0"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div class="col-span-1 flex items-center justify-center pt-1">
                                    <button @click="removeItem(idx)" type="button"
                                        :disabled="items.length === 1"
                                        class="text-red-400 hover:text-red-600 disabled:opacity-30 disabled:cursor-not-allowed text-lg leading-none">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <!-- DP -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-3">
                                    <label class="text-xs font-semibold text-gray-600 w-36 flex-shrink-0">DP / Uang Muka (Rp)</label>
                                    <input v-model.number="form.dp" type="number" min="0" placeholder="0 (kosongkan jika tidak ada DP)"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wide mb-4 pb-2 border-b border-blue-100">Catatan <span class="text-gray-400 font-normal text-xs">(opsional)</span></h2>
                            <textarea v-model="form.catatan" rows="3" placeholder="Catatan tambahan untuk pelanggan..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">
                            </textarea>
                        </div>

                    </div>

                    <!-- ─── SUMMARY (kanan 1/3) ─── -->
                    <div class="space-y-5">

                        <!-- Ringkasan -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                            <h2 class="text-sm font-bold text-blue-700 uppercase tracking-wide mb-4 pb-2 border-b border-blue-100">Ringkasan</h2>

                            <div class="space-y-2 text-sm mb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">No. Nota</span>
                                    <span class="font-semibold text-gray-800 text-right max-w-[60%] break-all">{{ form.no_nota || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Pelanggan</span>
                                    <span class="font-semibold text-gray-800">{{ form.nama || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Status</span>
                                    <span :class="[
                                        'text-xs font-bold px-2 py-0.5 rounded-full',
                                        form.status === 'Lunas'   ? 'bg-green-100 text-green-700' :
                                        form.status === 'DP'      ? 'bg-blue-100 text-blue-700'   :
                                                                     'bg-yellow-100 text-yellow-700'
                                    ]">{{ form.status }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Jumlah Item</span>
                                    <span class="font-semibold text-gray-800">{{ items.length }}</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-3 space-y-1.5 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="font-semibold">{{ formatRp(subtotal) }}</span>
                                </div>
                                <template v-if="dpValue > 0">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">DP Dibayar</span>
                                        <span class="font-semibold text-blue-600">{{ formatRp(dpValue) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Sisa Bayar</span>
                                        <span class="font-semibold text-red-600">{{ formatRp(sisaBayar) }}</span>
                                    </div>
                                </template>
                                <div class="flex justify-between pt-2 border-t border-gray-300 mt-2">
                                    <span class="font-bold text-gray-800">TOTAL</span>
                                    <span class="font-black text-blue-700 text-base">{{ formatRp(subtotal) }}</span>
                                </div>
                            </div>

                            <!-- Tombol Cetak -->
                            <button @click="printNota" type="button"
                                :disabled="!form.nama || !form.no_nota || items.some(i => !i.deskripsi)"
                                class="mt-5 w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700
                                       disabled:bg-gray-300 disabled:cursor-not-allowed
                                       text-white font-bold py-3 rounded-xl text-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Nota
                            </button>
                            <p class="text-xs text-gray-400 text-center mt-2">Nota akan terbuka di tab baru</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
