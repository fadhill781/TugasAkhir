<script setup>
import { ref, computed } from 'vue';
import NavbarUser from '@/includes/NavbarUser.vue';
import NavbarGuest from '@/includes/Navbar.vue';
import Footer from '@/includes/Footer.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth?.user);

const props = defineProps({
  kategori: Object,
  paketHaris: Array,
  galleryImages: Array,
});

const sortBy = ref('newest'); // newest, price_asc, price_desc

const allPakets = computed(() => {
  const list = [];
  props.paketHaris?.forEach(ph => {
    ph.paket_wisatas?.forEach(pw => {
      list.push({ ...pw, paketHari: ph });
    });
  });
  return list;
});

const sortedPakets = computed(() => {
  return [...allPakets.value].sort((a, b) => {
    if (sortBy.value === 'price_asc') return a.paketHari.harga - b.paketHari.harga;
    if (sortBy.value === 'price_desc') return b.paketHari.harga - a.paketHari.harga;
    return b.id - a.id; // newest
  });
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <NavbarUser v-if="isLoggedIn" />
    <NavbarGuest v-else />
    
    <div class="container mx-auto px-6 py-16 ">
      <!-- Article-Style About Section -->
      <div class="max-w-5xl mx-auto mb-20">
        <article class="bg-white rounded-2xl shadow-xl overflow-hidden">
          <!-- Featured Image -->
          <div class="mt-16 relative h-[500px] overflow-hidden">
            <img v-if="galleryImages && galleryImages.length > 0" 
                 :src="galleryImages[0]" 
                 :alt="kategori.nama_kategori" 
                 class="w-full h-full object-cover" />
            <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
              <span class="text-white text-6xl">🏖️</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8">
              <h2 class="text-4xl md:text-5xl font-bold text-white mb-2">
                Jelajahi Keindahan {{ kategori.nama_kategori }}
              </h2>
              <p class="text-white/90 text-lg">Destinasi Wisata Pilihan untuk Liburan Tak Terlupakan</p>
            </div>
          </div>

          <!-- Article Content -->
          <div class="p-8 md:p-12">
            <!-- Lead Paragraph -->
            <div class="mb-8">
              <p class="text-xl text-gray-800 leading-relaxed font-medium" v-if="kategori.deskripsi">
                {{ kategori.deskripsi }}
              </p>
            </div>

            <!-- Main Content -->
            <div class="prose prose-lg max-w-none text-gray-700 space-y-6">
              <p class="text-lg leading-relaxed">
                <span class="font-semibold text-gray-900">{{ kategori.nama_kategori }}</span> merupakan salah satu destinasi wisata favorit yang menawarkan pengalaman tak terlupakan bagi setiap pengunjung. Dengan keindahan alam yang memukau, budaya yang kaya, dan keramahan penduduk lokal, destinasi ini telah menjadi pilihan utama wisatawan domestik maupun mancanegara.
              </p>
              
              <p class="text-lg leading-relaxed">
                Terletak di lokasi strategis dengan akses yang mudah, {{ kategori.nama_kategori }} menawarkan berbagai atraksi wisata yang dapat dinikmati oleh semua kalangan. Mulai dari keindahan alam yang menakjubkan, situs bersejarah yang menarik, hingga kuliner khas yang menggugah selera, semuanya dapat Anda temukan di sini.
              </p>

              <!-- Highlight Box -->
              <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-600 p-8 my-8 rounded-r-xl">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Mengapa {{ kategori.nama_kategori }} Istimewa?</h3>
                <div class="grid md:grid-cols-2 gap-4">
                  <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                      <span class="text-white font-bold">✓</span>
                    </div>
                    <div>
                      <h4 class="font-semibold text-gray-900 mb-1">Keindahan Alam</h4>
                      <p class="text-gray-600 text-sm">Pemandangan spektakuler yang memanjakan mata</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                      <span class="text-white font-bold">✓</span>
                    </div>
                    <div>
                      <h4 class="font-semibold text-gray-900 mb-1">Aktivitas Beragam</h4>
                      <p class="text-gray-600 text-sm">Berbagai pilihan kegiatan untuk semua usia</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                      <span class="text-white font-bold">✓</span>
                    </div>
                    <div>
                      <h4 class="font-semibold text-gray-900 mb-1">Fasilitas Lengkap</h4>
                      <p class="text-gray-600 text-sm">Infrastruktur modern dan nyaman</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                      <span class="text-white font-bold">✓</span>
                    </div>
                    <div>
                      <h4 class="font-semibold text-gray-900 mb-1">Harga Terjangkau</h4>
                      <p class="text-gray-600 text-sm">Paket wisata dengan harga bersahabat</p>
                    </div>
                  </div>
                </div>
              </div>

              <p class="text-lg leading-relaxed">
                Kami menyediakan berbagai pilihan paket wisata dengan durasi yang fleksibel, mulai dari paket singkat untuk weekend getaway hingga paket lengkap beberapa hari untuk eksplorasi mendalam. Setiap paket dirancang khusus dengan itinerary yang matang, dipandu oleh tour guide profesional, dan dilengkapi dengan fasilitas terbaik untuk memastikan kenyamanan perjalanan Anda.
              </p>

              <p class="text-lg leading-relaxed">
                Dengan pengalaman bertahun-tahun dalam industri pariwisata, kami berkomitmen untuk memberikan pelayanan terbaik dan pengalaman wisata yang berkesan. Tim kami siap membantu Anda merencanakan perjalanan impian ke {{ kategori.nama_kategori }} dengan harga yang kompetitif dan kualitas layanan yang tidak perlu diragukan lagi.
              </p>     
            </div>
          </div>
        </article>
      </div>

      <!-- Package Options Section -->
      <div class="mb-20">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
          <div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-1">Pilihan Paket Wisata</h2>
            <p class="text-gray-600">Pilih durasi paket yang sesuai dengan kebutuhan Anda</p>
          </div>
          <!-- Sort Controls -->
          <div class="flex items-center gap-3 flex-wrap">
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
        </div>

        <!-- All Packages in One Grid -->
        <div v-if="sortedPakets.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div v-for="paket in sortedPakets" :key="paket.id" 
               class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
              <!-- Image with Overlay -->
              <div class="relative h-48 overflow-hidden">
                <img v-if="paket.image_url" :src="paket.image_url" :alt="paket.nama_wisata" 
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                  <span class="text-white text-5xl">📸</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                <div class="absolute top-3 right-3 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                  {{ paket.paketHari.durasi_hari }} Hari
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                  <h3 class="text-lg font-bold text-white mb-1 line-clamp-2">{{ paket.nama_wisata }}</h3>
                  <div class="flex items-center gap-1 text-white/90 text-xs">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{ kategori.nama_kategori }}</span>
                  </div>
                </div>
              </div>
              <div class="p-4">
                <p v-if="paket.short_description" class="text-gray-600 text-sm mb-3 line-clamp-2">{{ paket.short_description }}</p>
                <div class="flex items-center gap-3 mb-3 text-xs text-gray-600">
                  <div v-if="paket.max_personil" class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Max {{ paket.max_personil }}</span>
                  </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                  <div>
                    <p class="text-xs text-gray-500">Mulai dari</p>
                    <p class="text-xl font-bold text-blue-600">Rp {{ Number(paket.paketHari.harga).toLocaleString('id-ID') }}</p>
                  </div>
                  <a :href="`/paket-wisata-public/${paket.id}`" 
                     class="inline-flex px-4 py-2 cursor-pointer items-center rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-xs uppercase">
                    Detail
                  </a>
                </div>
              </div>
          </div>
        </div>

        <div v-else class="text-center text-gray-500 py-20">
          <div class="text-6xl mb-4">📦</div>
          <p class="text-2xl font-semibold mb-2">Belum Ada Paket Tersedia</p>
          <p class="text-gray-400">Paket wisata akan segera hadir</p>
        </div>
      </div>

      <!-- CTA Section -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-2xl p-12 text-white text-center">
        <h3 class="text-3xl md:text-4xl font-bold mb-4">Siap Memulai Petualangan Anda?</h3>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
          Hubungi kami untuk informasi lebih lanjut atau bantuan dalam memilih paket wisata yang tepat untuk Anda
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
