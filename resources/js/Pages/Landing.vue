<script setup>
import Navbar from '../includes/Navbar.vue';
import Footer from '../includes/Footer.vue';
import { computed, onMounted } from 'vue';

const props = defineProps({
  setting: Object,
  kategoris: Array,
  paketPopuler: Array,
  mobils: Array,
  testimonis: Array,
});

const heroTitle = computed(() => props.setting?.nama || 'Liburan Bersama Iconic');
const heroDescription = computed(() => props.setting?.deskripsi || 'Liburan menyenangkan bersama Iconic. Promo tersedia untuk paket wisata ke Bromo. Bisa bersama keluarga ataupun rekan kerja');
const heroBackgroundImage = computed(() => props.setting?.image_url || '/img/pacitanpantai.jpg');
const galleryImages = computed(() => props.setting?.gallery_urls || []);

// Enable smooth scrolling
onMounted(() => {
  document.documentElement.style.scrollBehavior = 'smooth';
});
</script>

<template>
  <Navbar />

  <div class="min-h-screen bg-white">

    <!-- ===== HERO SECTION ===== -->
    <section 
      class="relative w-full h-screen flex flex-col items-start justify-end pb-24 px-8 md:px-20 bg-cover bg-center" 
      :style="`background-image: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0.1) 100%), url('${heroBackgroundImage}');`"
    >
      <div class="max-w-2xl">
        <h1 class="text-4xl md:text-6xl text-white font-extrabold mb-4 leading-tight uppercase tracking-wide drop-shadow-lg">
          {{ heroTitle }}
        </h1>
        <p class="text-base md:text-lg text-white/90 mb-8 leading-relaxed">{{ heroDescription }}</p>
        <a href="/login-user"
           class="inline-flex px-6 py-3 cursor-pointer items-center rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-sm uppercase tracking-wider">
          Pesan Sekarang
        </a>
      </div>
    </section>

    <!-- ===== TEMPAT WISATA POPULER ===== -->
    <section class="py-14 bg-white">
      <div class="container mx-auto px-6">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-1 h-5 bg-blue-600 rounded"></div>
          <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Destinasi Terpopuler</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Tempat Wisata Yang Populer</h2>
        <p class="text-gray-500 mb-8 text-sm">Beberapa tempat yang banyak digemari wisatawan</p>

        <div v-if="kategoris && kategoris.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <a v-for="kategori in kategoris" :key="kategori.id"
             href="/login-user"
             class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 block">
            <div class="relative h-52 overflow-hidden">
              <img v-if="kategori.image_url" :src="kategori.image_url" :alt="kategori.nama_kategori"
                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                <span class="text-white text-5xl">🏖️</span>
              </div>
            </div>
            <div class="p-4">
              <h3 class="text-lg font-bold text-gray-800 mb-1">{{ kategori.nama_kategori }}</h3>
              <p class="text-gray-500 text-xs line-clamp-2 mb-3">{{ kategori.deskripsi || 'Destinasi wisata pilihan dengan berbagai paket menarik.' }}</p>
              <span class="text-xs text-blue-600 font-semibold">{{ kategori.jumlah_paket }} Paket Tersedia →</span>
            </div>
          </a>
        </div>
        <div v-else class="text-center text-gray-400 py-12">Belum ada destinasi tersedia</div>

        <div class="text-center mt-8">
          <a href="/login-user"
             class="inline-flex px-6 py-2 cursor-pointer items-center rounded-md border-2 border-black bg-white font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-sm">
            Lihat Semua →
          </a>
        </div>
      </div>
    </section>

    <!-- ===== PAKET POPULER ===== -->
    <section class="py-14 bg-blue-50">
      <div class="container mx-auto px-6">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-1 h-5 bg-blue-600 rounded"></div>
          <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Paket Wisata</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Paket Populer</h2>
        <p class="text-gray-500 mb-8 text-sm">Paket wisata terbaik yang banyak dipilih wisatawan</p>

        <div v-if="paketPopuler && paketPopuler.length > 0" class="space-y-4">
          <div v-for="paket in paketPopuler" :key="paket.id"
               class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col md:flex-row hover:shadow-lg transition-all duration-300">
            <div class="md:w-56 h-44 md:h-auto flex-shrink-0 overflow-hidden">
              <img v-if="paket.image_url" :src="paket.image_url" :alt="paket.nama_wisata"
                   class="w-[225px] h-[150px] object-cover rounded-l-xl" />
              <div v-else class="w-full h-full bg-gradient-to-br from-blue-300 to-blue-500 flex items-center justify-center">
                <span class="text-white text-4xl">🏔️</span>
              </div>
            </div>
            <div class="flex-1 p-5 flex flex-col justify-between">
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded">{{ paket.kategori }}</span>
                  <span class="text-xs text-gray-400">{{ paket.durasi_hari }} Hari</span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ paket.nama_wisata }}</h3>
                <p class="text-gray-500 text-sm line-clamp-2">{{ paket.short_description }}</p>
              </div>
              <div class="flex items-center gap-2 mt-3 text-xs text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Max {{ paket.max_personil || '?' }} orang</span>
              </div>
            </div>
            <div class="md:w-64 bg-blue-600 flex flex-col items-center justify-center p-5 gap-3">
              <div class="text-center">
                <p class="text-blue-200 text-xs mb-1">Mulai dari</p>
                <p class="text-white font-extrabold text-xl leading-tight">
                  IDR {{ Number(paket.harga).toLocaleString('id-ID') }}
                </p>
                <p class="text-blue-200 text-xs">/ Per Orang</p>
              </div>
              <a href="/login-user"
                 class="inline-flex px-4 py-2 cursor-pointer items-center justify-center text-center rounded-md border-2 border-white bg-white font-bold text-blue-700 shadow-[3px_3px_0px_0px_rgba(255,255,255,0.5)] transition-all hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none text-xs uppercase">
                Pesan Sekarang
              </a>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-400 py-12">Belum ada paket tersedia</div>

        <div class="text-center mt-8">
          <a href="/login-user"
             class="inline-flex px-6 py-2 cursor-pointer items-center rounded-md border-2 border-black bg-white font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-sm">
            Lihat Semua Paket →
          </a>
        </div>
      </div>
    </section>

    <!-- ===== SEWA MOBIL ===== -->
    <section class="py-14 bg-white">
      <div class="container mx-auto px-6">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-1 h-5 bg-blue-600 rounded"></div>
          <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Pilihan Terbaik</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2 uppercase">Sewa Mobil</h2>
        <p class="text-gray-500 mb-8 text-sm">Sewa Mobil Untuk Kebutuhan, Tersedia Dengan Harga Terjangkau.</p>

        <div v-if="mobils && mobils.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <a v-for="mobil in mobils" :key="mobil.id"
             href="/login-user"
             class="group block bg-white rounded-2xl border border-gray-200 hover:border-blue-500 hover:shadow-lg transition-all duration-300 p-5 text-center cursor-pointer">
            <!-- Image Box -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden h-44 flex items-center justify-center mb-4 group-hover:border-blue-400 transition-colors">
              <img v-if="mobil.image_url" :src="mobil.image_url" :alt="mobil.nama"
                   class="h-full w-full object-contain p-3" />
              <span v-else class="text-6xl">🚗</span>
            </div>
            <!-- Info -->
            <h3 class="font-bold text-gray-900 text-base mb-1">{{ mobil.nama }}</h3>
            <p class="text-gray-500 text-sm mb-3">
              IDR {{ Number(mobil.harga).toLocaleString('id-ID') }}
              <span v-if="mobil.durasi"> / {{ mobil.durasi }} Hari</span>
            </p>
            <!-- Button -->
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition-all text-sm w-full">
              Pesan
            </button>
          </a>
        </div>
        <div v-else class="text-center text-gray-400 py-12">Belum ada mobil tersedia</div>
      </div>
    </section>

    <!-- ===== GALERI ===== -->
    <section id="galeri" class="py-14 bg-gray-50">
      <div class="container mx-auto px-6">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-1 h-5 bg-blue-600 rounded"></div>
          <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Galeri Foto</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8">Galeri</h2>
        
        <!-- Gallery Grid -->
        <div v-if="galleryImages.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div v-for="(image, index) in galleryImages" :key="index"
               class="overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 aspect-square">
            <img :src="image" :alt="`Gallery ${index + 1}`"
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
          </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg
            class="mx-auto h-20 w-20 text-gray-400 mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">Galeri Kosong</h3>
          <p class="text-gray-500 text-sm">Belum ada foto yang ditambahkan ke galeri.</p>
        </div>
      </div>
    </section>

    <!-- ===== TESTIMONI ===== -->
    <section class="py-14 bg-white">
      <div class="container mx-auto px-6">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-1 h-5 bg-blue-600 rounded"></div>
          <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Testimoni</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Kata Mereka</h2>
        <p class="text-gray-500 mb-8 text-sm">Apa kata pelanggan yang sudah berwisata bersama kami</p>

        <div v-if="testimonis && testimonis.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="testimoni in testimonis" :key="testimoni.id"
               class="bg-white border border-gray-100 p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300">
            <div class="flex gap-1 mb-3">
              <svg v-for="i in 5" :key="i" class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
            </div>
            <p class="text-gray-600 text-sm italic mb-5 leading-relaxed">"{{ testimoni.testimoni }}"</p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                {{ testimoni.nama_pentestimoni?.charAt(0)?.toUpperCase() }}
              </div>
              <div>
                <p class="font-bold text-gray-800 text-sm">{{ testimoni.nama_pentestimoni }}</p>
                <p v-if="testimoni.asal" class="text-gray-400 text-xs">{{ testimoni.asal }}</p>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-400 py-12">Belum ada testimoni</div>
      </div>
    </section>

  </div>

  <Footer />
</template>
