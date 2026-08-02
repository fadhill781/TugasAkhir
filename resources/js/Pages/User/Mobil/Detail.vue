<script setup>
import { Head } from '@inertiajs/vue3';
import NavbarUser from '../../../includes/NavbarUser.vue';
import Footer from '../../../includes/Footer.vue';

const props = defineProps({
    mobil: {
        type: Object,
        required: true
    }
});

// WhatsApp message template
const getWhatsAppLink = () => {
    const formattedPrice = `Rp. ${Number(props.mobil.harga).toLocaleString('id-ID')}`;
    const message = `Halo, saya tertarik untuk menyewa mobil:\n\nNama Mobil: ${props.mobil.nama}\nHarga: ${formattedPrice}\nDurasi: ${props.mobil.durasi} Hari\n\nMohon informasi lebih lanjut. Terima kasih!`;
    return `https://wa.me/6281234567890?text=${encodeURIComponent(message)}`;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-blue-50">
        <Head :title="`Detail ${mobil.nama}`" />
        <NavbarUser />
        
        <div class="container mx-auto pt-36 pb-12 px-4">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-8">
                <a href="/homepage" class="hover:text-blue-600">Home</a>
                <span>/</span>
                <a href="/rental-mobil" class="hover:text-blue-600">Rental Mobil</a>
                <span>/</span>
                <span class="text-gray-800 font-semibold">{{ mobil.nama }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column - Image -->
                <div class="space-y-6">
                    <div class="relative h-96 lg:h-[500px] rounded-2xl overflow-hidden shadow-2xl">
                        <img v-if="mobil.image_url" :src="mobil.image_url" :alt="mobil.nama" 
                             class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <span class="text-white text-9xl">🚗</span>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Fasilitas Termasuk:</h3>
                        <div class="grid md:grid-cols-2 gap-3">
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>AC & Audio System</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Kondisi Prima</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Asuransi</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>BBM Penuh</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Details & Booking -->
                <div class="space-y-6">
                    <!-- Car Details Card -->
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">{{ mobil.nama }}</h1>
                        
                        <!-- Specifications -->
                        <div class="space-y-4 mb-8">
                            <!-- Duration -->
                            <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Durasi Rental</p>
                                    <p class="text-xl font-bold text-gray-800">{{ mobil.durasi }} Hari</p>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Harga Rental</p>
                                    <p class="text-2xl font-bold text-blue-600">Rp. {{ Number(mobil.harga).toLocaleString('id-ID') }}</p>
                                    <p class="text-xs text-gray-500">untuk {{ mobil.durasi }} hari</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-blue-50 rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-3">Tentang Mobil Ini</h3>
                            <p class="text-gray-700 leading-relaxed">
                                {{ mobil.nama }} adalah pilihan sempurna untuk perjalanan Anda. Mobil ini dilengkapi dengan fasilitas lengkap dan dalam kondisi terawat. 
                                Cocok untuk perjalanan wisata, keperluan bisnis, atau acara keluarga. Dengan harga yang terjangkau, Anda akan mendapatkan kenyamanan maksimal selama {{ mobil.durasi }} hari.
                            </p>
                        </div>

                        <!-- Booking Button -->
                        <a :href="getWhatsAppLink()" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-3 px-8 py-4 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-lg">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span>Sewa via WhatsApp</span>
                        </a>

                        <!-- Contact Info -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-600 text-center mb-3">Atau hubungi kami langsung:</p>
                            <div class="flex gap-3">
                                <a href="tel:+6281234567890"
                                   class="flex-1 flex items-center justify-center gap-2 px-4 py-3 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>Telepon</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-6 text-white">
                        <h3 class="text-xl font-bold mb-3">Syarat & Ketentuan</h3>
                        <ul class="space-y-2 text-sm text-blue-100">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Memiliki SIM yang masih berlaku</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Fotocopy KTP & KK</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Deposit sesuai ketentuan</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Pengembalian tepat waktu</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-12 text-center">
                <a href="/rental-mobil" 
                   class="inline-flex items-center gap-2 px-6 py-3 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Daftar Mobil</span>
                </a>
            </div>
        </div>

        <Footer />
    </div>
</template>
