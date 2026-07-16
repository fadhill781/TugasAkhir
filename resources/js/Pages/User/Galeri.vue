<script setup>
import { Head } from '@inertiajs/vue3';
import NavbarUser from '@/includes/NavbarUser.vue';
import Footer from '@/includes/Footer.vue';

defineProps({
  gallery: Array,
  setting: Object,
});
</script>

<template>
  <Head title="Galeri" />
  
  <NavbarUser />
  
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 pt-32 pb-16">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Galeri Kami</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
          Lihat momen-momen indah perjalanan wisata bersama kami
        </p>
      </div>

      <!-- Gallery Grid - Only Images -->
      <div v-if="gallery && gallery.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(imageUrl, index) in gallery"
          :key="index"
          class="relative overflow-hidden rounded-lg shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105"
        >
          <img
            :src="imageUrl"
            :alt="`Galeri ${index + 1}`"
            class="w-full h-80 object-cover"
          />
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20">
        <svg
          class="mx-auto h-24 w-24 text-gray-400 mb-4"
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
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Galeri Kosong</h3>
        <p class="text-gray-500">Belum ada foto yang ditambahkan ke galeri.</p>
      </div>
    </div>
  </div>

  <Footer />
</template>
