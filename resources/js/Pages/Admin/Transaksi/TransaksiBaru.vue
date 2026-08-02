<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Sidebar from '@/includes/Sidebar.vue';

const sidebarOpen = ref(true);

// ── Form ──
const form = reactive({
    no_invoice: 'INV-' + new Date().getFullYear() + '-' + String(Date.now()).slice(-4),
    tanggal:    new Date().toISOString().slice(0, 10),
    status:     'Lunas',
    nama:       '',
    telepon:    '',
    tanggal_berangkat: '',
    keterangan: '',
    dp:         '',
});

// ── Items ──
const items = ref([
    { deskripsi: '', keterangan: '', qty: 1, harga: 0 },
]);

function addItem() {
    items.value.push({ deskripsi: '', keterangan: '', qty: 1, harga: 0 });
}

function removeItem(idx) {
    if (items.value.length > 1) items.value.splice(idx, 1);
}

// ── Totals ──
const subtotal = computed(() =>
    items.value.reduce((s, i) => s + Number(i.qty) * Number(i.harga), 0)
);
const dpValue    = computed(() => Number(form.dp) || 0);
const sisaBayar  = computed(() => subtotal.value - dpValue.value);

function formatRp(v) {
    return 'Rp. ' + Number(v).toLocaleString('id-ID');
}

// ── Validation ──
const canPrint = computed(() =>
    form.nama.trim() &&
    form.telepon.trim() &&
    form.tanggal_berangkat &&
    items.value.every(i => i.deskripsi.trim())
);

// ── Print: POST form ke blade, buka di tab baru ──
function printInvoice() {
    const payload = { ...form, items: items.value };

    const el = document.createElement('form');
    el.method = 'POST';
    el.action = '/transaksi-manual/preview';
    el.target = '_blank';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrf) {
        const t = document.createElement('input');
        t.type = 'hidden'; t.name = '_token'; t.value = csrf;
        el.appendChild(t);
    }

    const d = document.createElement('input');
    d.type = 'hidden'; d.name = 'data'; d.value = JSON.stringify(payload);
    el.appendChild(d);

    document.body.appendChild(el);
    el.submit();
    document.body.removeChild(el);
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
                        <h1 class="text-2xl font-bold text-gray-800">Cetak Invoice</h1>
                        <p class="text-sm text-gray-500 mt-1"></p>
                    </div>
                    <button @click="router.visit('/admin/transaksi-keuangan')"
                        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50">
                        ← Kembali
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- ─── FORM ─── -->
                    <div class="lg:col-span-2 space-y-5">

                        <!-- Info Invoice -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">Info Invoice</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">No. Invoice <span class="text-red-500">*</span></label>
                                    <input v-model="form.no_invoice" type="text"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal <span class="text-red-500">*</span></label>
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
                            <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">Data Pelanggan</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                                    <input v-model="form.nama" type="text" placeholder="Nama lengkap pelanggan"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Telepon <span class="text-red-500">*</span></label>
                                    <input v-model="form.telepon" type="tel" placeholder="08xxxxxxxxxx"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Berangkat <span class="text-red-500">*</span></label>
                                    <input v-model="form.tanggal_berangkat" type="date"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Rincian Item -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-blue-100">
                                <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest">Rincian Layanan</h2>
                                <button @click="addItem" type="button"
                                    class="text-xs font-bold text-blue-600 border border-blue-300 rounded-lg px-3 py-1.5 hover:bg-blue-50">
                                    + Tambah Baris
                                </button>
                            </div>

                            <!-- Header kolom -->
                            <div class="hidden sm:grid grid-cols-12 gap-2 mb-1 px-1">
                                <div class="col-span-4 text-xs font-semibold text-gray-400 uppercase">Deskripsi</div>
                                <div class="col-span-3 text-xs font-semibold text-gray-400 uppercase">Keterangan</div>
                                <div class="col-span-2 text-xs font-semibold text-gray-400 uppercase text-center">Qty</div>
                                <div class="col-span-2 text-xs font-semibold text-gray-400 uppercase text-right">Harga (Rp)</div>
                                <div class="col-span-1"></div>
                            </div>

                            <!-- Rows -->
                            <div v-for="(item, idx) in items" :key="idx"
                                class="grid grid-cols-12 gap-2 mb-2 items-start">
                                <div class="col-span-12 sm:col-span-4">
                                    <input v-model="item.deskripsi" type="text" placeholder="Nama paket / layanan"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div class="col-span-12 sm:col-span-3">
                                    <input v-model="item.keterangan" type="text" placeholder="Keterangan (opsional)"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div class="col-span-4 sm:col-span-2">
                                    <input v-model.number="item.qty" type="number" min="1"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div class="col-span-7 sm:col-span-2">
                                    <input v-model.number="item.harga" type="number" min="0" placeholder="0"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-right focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div class="col-span-1 flex justify-center pt-1.5">
                                    <button @click="removeItem(idx)" type="button"
                                        :disabled="items.length === 1"
                                        class="text-red-400 hover:text-red-600 disabled:opacity-25 disabled:cursor-not-allowed text-base leading-none">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <!-- DP -->
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                                <label class="text-xs font-semibold text-gray-600 w-36 flex-shrink-0">DP / Uang Muka (Rp)</label>
                                <input v-model.number="form.dp" type="number" min="0"
                                    placeholder="0 — kosongkan jika tidak ada DP"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                        <!-- Keterangan umum -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">
                                Keterangan <span class="text-gray-400 font-normal normal-case text-xs">(opsional)</span>
                            </h2>
                            <textarea v-model="form.keterangan" rows="3"
                                placeholder="Keterangan tambahan yang akan tampil di invoice..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 resize-none">
                            </textarea>
                        </div>

                    </div>

                    <!-- ─── SUMMARY (sticky) ─── -->
                    <div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                            <h2 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 pb-2 border-b border-blue-100">Ringkasan</h2>

                            <div class="space-y-2 text-sm mb-5">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">No. Invoice</span>
                                    <span class="font-semibold text-gray-800 text-right max-w-[55%] break-all text-xs">{{ form.no_invoice }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Pelanggan</span>
                                    <span class="font-semibold text-gray-800">{{ form.nama || '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Telepon</span>
                                    <span class="font-semibold text-gray-800">{{ form.telepon || '-' }}</span>
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
                                    <span class="text-gray-500">Item</span>
                                    <span class="font-semibold text-gray-800">{{ items.length }} baris</span>
                                </div>
                            </div>

                            <!-- Totals -->
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
                                        <span class="text-gray-500">Sisa Tagihan</span>
                                        <span class="font-semibold text-red-600">{{ formatRp(sisaBayar) }}</span>
                                    </div>
                                </template>
                                <div class="flex justify-between pt-2 border-t border-gray-300">
                                    <span class="font-bold text-gray-800">TOTAL</span>
                                    <span class="font-black text-blue-700 text-base">{{ formatRp(subtotal) }}</span>
                                </div>
                            </div>

                            <!-- Validasi hint -->
                            <div v-if="!canPrint" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700">
                                Lengkapi <strong>Nama, Telepon, Tanggal Berangkat</strong>, dan minimal satu <strong>deskripsi item</strong> sebelum mencetak.
                            </div>

                            <!-- Tombol cetak -->
                            <button @click="printInvoice" type="button"
                                :disabled="!canPrint"
                                class="mt-4 w-full flex items-center justify-center gap-2
                                       bg-blue-600 hover:bg-blue-700
                                       disabled:bg-gray-300 disabled:cursor-not-allowed
                                       text-white font-bold py-3 rounded-xl text-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Invoice
                            </button>
                            <p class="text-xs text-gray-400 text-center mt-1.5">Terbuka di tab baru</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
