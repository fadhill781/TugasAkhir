<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import NavbarUser from '../../../includes/NavbarUser.vue';
import Footer from '../../../includes/Footer.vue';

const props = defineProps({
    mobils: { type: Array, required: true }
});

const sortBy = ref('newest');

const sortedMobils = computed(() => {
    return [...props.mobils].sort((a, b) => {
        if (sortBy.value === 'price_asc') return Number(a.harga) - Number(b.harga);
        if (sortBy.value === 'price_desc') return Number(b.harga) - Number(a.harga);
        return b.id - a.id; // newest
    });
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-blue-50">
        <Head title="Rental Mobil" />
        <NavbarUser />
        
        <div class="container mx-auto pt-36 pb-12 px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Rental Mobil</h1>
                <p class="text-xl text-gray-600">Pilih mobil terbaik untuk perjalanan Anda</p>
            </div>

            <!-- Sort Controls -->
            <div class="flex items-center gap-3 mb-6 flex-wrap">
                <span class="text-sm font-medium text-gray-600">Urutkan:</span>
                <button @click="sortBy = 'newest'"
                        :class="['px-4 py-2 rounded-md border-2 text-sm font-bold transition-all', sortBy === 'newest' ? 'border-black bg-black text-white shadow-none' : 'border-black bg-white text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none']">
                    Terbaru
                </button>
                <button @click="sortBy = 'price_asc'"
                        :class="['px-4 py-2 rounded-md border-2 text-sm font-bold transition-all', sortBy === 'price_asc' ? 'border-black bg-black text-white shadow-none' : 'border-black bg-white text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none']">
                    Harga ↑
                </button>
                <button @click="sortBy = 'price_desc'"
                        :class="['px-4 py-2 rounded-md border-2 text-sm font-bold transition-all', sortBy === 'price_desc' ? 'border-black bg-black text-white shadow-none' : 'border-black bg-white text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none']">
                    Harga ↓
                </button>
            </div>

            <!-- Mobil Grid -->
            <div v-if="sortedMobils.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="mobil in sortedMobils" :key="mobil.id" 
                     class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <!-- Image -->
                    <div class="relative h-56 overflow-hidden">
                        <img v-if="mobil.image_url" :src="mobil.image_url" :alt="mobil.nama" 
                             class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" />
                        <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <span class="text-white text-6xl">🚗</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        
                        <!-- Car Name Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-xl font-bold text-white">{{ mobil.nama }}</h3>
                        </div>
                    </div>
                    
                    <!-- Card Content -->
                    <div class="p-5">
                        <!-- Duration -->
                        <div v-if="mobil.durasi" class="flex items-center gap-2 mb-3 text-gray-600">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-semibold">{{ mobil.durasi }} Hari</span>
                        </div>
                        
                        <!-- Price and Button -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                            <div>
                                <p class="text-xs text-gray-500">Harga</p>
                                <p class="text-lg font-bold text-blue-600">
                                    IDR {{ Number(mobil.harga).toLocaleString('id-ID') }}
                                </p>
                                <p v-if="mobil.durasi" class="text-xs text-gray-500">/ {{ mobil.durasi }} Hari</p>
                            </div>
                            <a :href="`/rental-mobil/${mobil.id}`"
                               class="inline-flex items-center gap-2 px-4 py-2 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-xs uppercase">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center text-gray-500 py-20">
                <div class="text-6xl mb-4">🚗</div>
                <p class="text-2xl font-semibold mb-2">Belum Ada Mobil Tersedia</p>
                <p class="text-gray-400">Mobil rental akan segera hadir</p>
            </div>

            <!-- Info Section -->
            <div class="mt-16 bg-white rounded-2xl shadow-xl p-8 md:p-12">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Mengapa Rental Mobil Bersama Kami?</h2>
                    <p class="text-lg text-gray-600">Nikmati perjalanan Anda dengan kenyamanan maksimal</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Mobil Terawat</h3>
                        <p class="text-gray-600">Semua mobil dalam kondisi prima dan terawat dengan baik</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Harga Terjangkau</h3>
                        <p class="text-gray-600">Harga rental yang kompetitif dan terjangkau untuk semua kalangan</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Layanan 24/7</h3>
                        <p class="text-gray-600">Customer service siap membantu Anda kapan saja</p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="mt-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-2xl p-12 text-white text-center">
                <h3 class="text-3xl md:text-4xl font-bold mb-4">Butuh Bantuan Memilih Mobil?</h3>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Hubungi kami untuk konsultasi gratis dan dapatkan rekomendasi mobil terbaik untuk kebutuhan Anda
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="https://wa.me/6281234567890" target="_blank"
                       class="inline-flex items-center gap-3 px-8 py-4 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                    <a href="tel:+6281234567890"
                       class="inline-flex items-center gap-3 px-8 py-4 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>Telepon</span>
                    </a>
                </div>
            </div>
        </div>

        <Footer />
    </div>
</template>
