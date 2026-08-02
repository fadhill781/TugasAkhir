<script setup>
import NavbarUser from '../../../includes/NavbarUser.vue';
import NavbarGuest from '../../../includes/Navbar.vue';
import Footer from '../../../includes/Footer.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    kategoris: {
        type: Array,
        required: true
    }
});

const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth?.user);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Head title="Destinasi Wisata" />
        <NavbarUser v-if="isLoggedIn" />
        <NavbarGuest v-else />
        
        <div class="container mx-auto pt-36 py-12">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Destinasi Wisata</h1>
                <p class="text-xl text-gray-600">Pilih destinasi impian Anda dan temukan paket wisata terbaik</p>
            </div>

            <!-- Kategori Grid -->
            <div v-if="kategoris && kategoris.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="kategori in kategoris" :key="kategori.id" 
                     class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <!-- Image -->
                    <div class="relative h-72 overflow-hidden">
                        <img v-if="kategori.image_url" :src="kategori.image_url" :alt="kategori.nama_kategori" 
                             class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" />
                        <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <span class="text-white text-6xl">🏖️</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        
                        <!-- Content Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <h3 class="text-2xl font-bold text-white mb-2">{{ kategori.nama_kategori }}</h3>
                            <p v-if="kategori.deskripsi" class="text-white/90 text-sm mb-4 line-clamp-2">
                                {{ kategori.deskripsi }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-white/80 text-sm">
                                    <i class="las la-calendar-check"></i> {{ kategori.jumlah_paket }} Paket Tersedia
                                </span>
                                <a :href="`/destinasi/${kategori.id}/paket`" 
                                   class="inline-flex px-4 py-2 cursor-pointer items-center rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-sm">
                                    Lihat Paket →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center text-gray-500 py-20">
                <div class="text-6xl mb-4">🏝️</div>
                <p class="text-2xl font-semibold mb-2">Belum Ada Destinasi</p>
                <p class="text-gray-400">Destinasi wisata akan segera hadir</p>
            </div>
        </div>

        <Footer />
    </div>
</template>
