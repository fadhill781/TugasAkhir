<script setup>
import { onMounted, ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import NavbarUser from '../includes/NavbarUser.vue';
import Footer from '../includes/Footer.vue';

const props = defineProps({
    package: Object,
    order_id: String
})

const isProcessing = ref(false);

const totalAmount = computed(() => 
    props.package.booking_info?.total_harga || props.package.harga
);

const bookingInfo = computed(() => props.package.booking_info);

onMounted(() => {
    if (typeof window.snap === 'undefined') {
        console.error('Midtrans Snap is not loaded!');
    }
});

async function pay() {
    if (isProcessing.value) return;
    if (typeof window.snap === 'undefined') {
        alert('Midtrans belum siap. Silakan refresh halaman dan coba lagi.');
        return;
    }

    isProcessing.value = true;

    try {
        const response = await axios.post(route('midtrans.pay'), {
            order_id: props.order_id,
            amount: totalAmount.value,
            package_id: props.package.id,
            package_name: props.package.nama_wisata
        }, { timeout: 30000 });

        if (!response.data?.snap_token) throw new Error('Snap token not received');

        window.snap.pay(response.data.snap_token, {
            onSuccess: async function(result) {
                try {
                    await axios.post(route('midtrans.update-status'), {
                        order_id: result.order_id,
                        transaction_status: result.transaction_status,
                        fraud_status: result.fraud_status,
                    });
                } catch (e) { console.error(e); }
                isProcessing.value = false;
                window.location.href = route('checkout.success');
            },
            onPending: async function(result) {
                try {
                    await axios.post(route('midtrans.update-status'), {
                        order_id: result.order_id,
                        transaction_status: 'pending',
                        fraud_status: null,
                    });
                } catch (e) { console.error(e); }
                isProcessing.value = false;
                alert("Pembayaran sedang diproses. Cek riwayat pesanan Anda.");
            },
            onError: async function(result) {
                try {
                    await axios.post(route('midtrans.update-status'), {
                        order_id: result.order_id || props.order_id,
                        transaction_status: 'deny',
                        fraud_status: null,
                    });
                } catch (e) { console.error(e); }
                isProcessing.value = false;
                alert("Pembayaran gagal!");
            },
            onClose: function() {
                isProcessing.value = false;
            }
        });
    } catch (error) {
        isProcessing.value = false;
        if (error.code === 'ECONNABORTED') {
            alert("Request timeout. Silakan coba lagi.");
        } else if (error.response) {
            alert("Terjadi kesalahan: " + (error.response.data.message || "Gagal memproses pembayaran"));
        } else {
            alert("Terjadi kesalahan: " + error.message);
        }
    }
}
</script>

<template>
    <NavbarUser />
    <Head title="Checkout - Konfirmasi Pesanan" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 pt-28 pb-12 px-4">
        <div class="max-w-4xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-bold mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Pembayaran Aman & Terenkripsi
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Konfirmasi Pesanan</h1>
                <p class="text-gray-500 mt-1">Periksa detail pesanan Anda sebelum melanjutkan pembayaran</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left - Package Detail (3/5) -->
                <div class="lg:col-span-3 space-y-4">
                    <!-- Package Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-gray-100">
                        <div class="relative h-48">
                            <img v-if="package.image"
                                 :src="`/storage/uploads/admin/paket_wisata/${package.image}`"
                                 :alt="package.nama_wisata"
                                 class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                <span class="text-white text-5xl">🏖️</span>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4">
                                <h2 class="text-2xl font-bold text-white">{{ package.nama_wisata }}</h2>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="text-xs text-gray-400 font-mono mb-3 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Order ID: {{ order_id }}
                            </div>
                            <p v-if="package.short_description" class="text-gray-600 text-sm leading-relaxed">
                                {{ package.short_description }}
                            </p>
                        </div>
                    </div>

                    <!-- Booking Detail -->
                    <div v-if="bookingInfo" class="bg-white rounded-2xl shadow-lg p-5 border-2 border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Detail Pemesanan
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-gray-400 text-xs mb-1">Nama Pemesan</p>
                                <p class="font-semibold text-gray-800">{{ bookingInfo.nama }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-gray-400 text-xs mb-1">Email</p>
                                <p class="font-semibold text-gray-800 truncate">{{ bookingInfo.email }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-gray-400 text-xs mb-1">Telepon</p>
                                <p class="font-semibold text-gray-800">{{ bookingInfo.telepon }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-gray-400 text-xs mb-1">Jumlah Peserta</p>
                                <p class="font-semibold text-gray-800">{{ bookingInfo.jumlah_orang }} Orang</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 col-span-2">
                                <p class="text-gray-400 text-xs mb-1">Tanggal Keberangkatan</p>
                                <p class="font-semibold text-gray-800">{{ bookingInfo.tanggal_berangkat }}</p>
                            </div>
                            <div v-if="bookingInfo.catatan" class="bg-gray-50 rounded-lg p-3 col-span-2">
                                <p class="text-gray-400 text-xs mb-1">Catatan</p>
                                <p class="font-semibold text-gray-800">{{ bookingInfo.catatan }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right - Payment Summary (2/5) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100 sticky top-24">
                        <h3 class="font-bold text-gray-800 mb-5 text-lg">Ringkasan Pembayaran</h3>

                        <div class="space-y-3 mb-5">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Harga Paket</span>
                                <span class="font-semibold">Rp {{ Number(bookingInfo?.total_harga / (bookingInfo?.jumlah_orang || 1) || package.harga).toLocaleString('id-ID') }}</span>
                            </div>
                            <div v-if="bookingInfo?.jumlah_orang > 1" class="flex justify-between text-sm">
                                <span class="text-gray-500">Jumlah Peserta</span>
                                <span class="font-semibold">× {{ bookingInfo.jumlah_orang }}</span>
                            </div>
                            <div class="border-t-2 border-dashed border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800">Total</span>
                                    <span class="text-2xl font-bold text-blue-600">
                                        Rp {{ Number(totalAmount).toLocaleString('id-ID') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pay Button -->
                        <button @click="pay" :disabled="isProcessing"
                                class="w-full px-6 py-4 cursor-pointer rounded-md border-2 border-black font-bold text-base shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-x-0 disabled:translate-y-0 disabled:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                                :class="isProcessing ? 'bg-gray-100 text-gray-500' : 'bg-[#1266AC] text-white'">
                            <span v-if="isProcessing" class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Membuka Pembayaran...
                            </span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Bayar Sekarang
                            </span>
                        </button>

                        <!-- Security badges -->
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-center gap-4 text-xs text-gray-400">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    SSL Aman
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Midtrans
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Footer />
</template>
