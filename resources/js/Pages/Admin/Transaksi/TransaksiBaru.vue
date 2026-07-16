<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    kategoris: Array,
    paketHaris: Array,
});

const sidebarOpen = ref(true);

const form = useForm({
    nama: '',
    email: '',
    telepon: '',
    paket_hari_id: '',
    jumlah_orang: 1,
    tanggal_berangkat: '',
    catatan: '',
    total: 0,
    status: 'confirmed',
    waktu: new Date().toISOString().split('T')[0],
});

// Generate order ID otomatis
const orderId = ref('ORDER-' + Date.now());

const selectedPaketHari = computed(() => {
    if (!form.paket_hari_id || !props.paketHaris) return null;
    return props.paketHaris.find(p => p.id == form.paket_hari_id);
});

const totalOtomatis = computed(() => {
    if (!selectedPaketHari.value) return 0;
    return Number(selectedPaketHari.value.harga) * Number(form.jumlah_orang);
});

function updateTotal() {
    form.total = totalOtomatis.value;
}

function submit() {
    form.total = totalOtomatis.value || form.total;
    form.post('/admin/transaksi-baru', {
        onSuccess: () => {
            router.visit('/admin/transaksi-keuangan');
        }
    });
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto flex-1', sidebarOpen ? 'pl-64' : 'pl-16']">
            <div class="container mx-auto px-4 py-6 max-w-3xl">

                <!-- Header -->
                <div class="flex items-center gap-3 mb-6">
                    <button @click="router.visit('/admin/transaksi-keuangan')"
                            class="p-2 rounded-lg hover:bg-gray-200 transition">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Input Transaksi Manual</h1>
                        <p class="text-gray-500 text-sm">Tambahkan transaksi pembayaran secara manual</p>
                    </div>
                </div>

                <!-- Flash error -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                    </ul>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8">

                    <!-- Order ID (readonly) -->
                    <div class="mb-6 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs text-blue-600 font-semibold uppercase mb-1">Order ID (Otomatis)</p>
                        <p class="font-mono font-bold text-blue-800">{{ orderId }}</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">

                        <!-- Data Pemesan -->
                        <div>
                            <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">Data Pemesan</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input v-model="form.nama" type="text" required
                                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm"
                                           placeholder="Nama lengkap" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input v-model="form.email" type="email" required
                                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm"
                                           placeholder="email@example.com" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                                    <input v-model="form.telepon" type="tel" required
                                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm"
                                           placeholder="08xxxxxxxxxx" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Berangkat <span class="text-red-500">*</span></label>
                                    <input v-model="form.tanggal_berangkat" type="date" required
                                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm" />
                                </div>
                            </div>
                        </div>

                        <!-- Detail Paket -->
                        <div>
                            <h2 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-100">Detail Paket</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Paket Hari <span class="text-red-500">*</span></label>
                                    <select v-model="form.paket_hari_id" @change="updateTotal" required
                                            class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm">
                                        <option value="">Pilih Paket</option>
                                        <option v-for="p in paketHaris" :key="p.id" :value="p.id">
                                            {{ p.kategori }} - {{ p.durasi_hari }} Hari (Rp {{ Number(p.harga).toLocaleString('id-ID') }})
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Peserta <span class="text-red-500">*</span></label>
                                    <input v-model.number="form.jumlah_orang" type="number" min="1" required
                                           @input="updateTotal"
                                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm" />
                                </div>
                            </div>
                        </div>

                        <!-- Total Harga -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex justify-between items-center mb-2 text-sm text-gray-600">
                                <span>Harga per orang:</span>
                                <span class="font-semibold">Rp {{ selectedPaketHari ? Number(selectedPaketHari.harga).toLocaleString('id-ID') : 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-2 text-sm text-gray-600">
                                <span>Jumlah peserta:</span>
                                <span class="font-semibold">{{ form.jumlah_orang }} orang</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 flex justify-between items-center">
                                <span class="font-bold text-gray-800">Total Pembayaran:</span>
                                <span class="text-2xl font-bold text-blue-600">Rp {{ Number(totalOtomatis || form.total).toLocaleString('id-ID') }}</span>
                            </div>
                            <!-- Override manual jika perlu -->
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Override Total Manual (opsional)</label>
                                <input v-model.number="form.total" type="number" min="0"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400"
                                       :placeholder="'Otomatis: Rp ' + Number(totalOtomatis).toLocaleString('id-ID')" />
                            </div>
                        </div>

                        <!-- Status & Catatan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                                <div class="space-y-2">
                                    <label v-for="s in [{v:'confirmed',l:'✓ Lunas',c:'text-green-700 border-green-300 bg-green-50'},{v:'pending',l:'⏳ Pending',c:'text-yellow-700 border-yellow-300 bg-yellow-50'},{v:'cancelled',l:'✗ Dibatalkan',c:'text-red-700 border-red-300 bg-red-50'}]"
                                           :key="s.v"
                                           :class="['flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition', form.status === s.v ? s.c : 'border-gray-200 hover:border-gray-300']">
                                        <input type="radio" v-model="form.status" :value="s.v" class="w-4 h-4" />
                                        <span class="text-sm font-semibold">{{ s.l }}</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Transaksi</label>
                                <input v-model="form.waktu" type="date"
                                       class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm mb-3" />
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                                <textarea v-model="form.catatan" rows="3"
                                          class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-sm resize-none"
                                          placeholder="Catatan tambahan (opsional)"></textarea>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3 pt-4 border-t border-gray-100">
                            <button type="submit" :disabled="form.processing"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition disabled:opacity-50 flex items-center justify-center gap-2">
                                <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Transaksi' }}</span>
                            </button>
                            <button type="button" @click="router.visit('/admin/transaksi-keuangan')"
                                    class="px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
