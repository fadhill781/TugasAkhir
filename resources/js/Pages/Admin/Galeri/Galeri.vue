<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import Sidebar from '@/includes/Sidebar.vue';
import Navbar from '@/includes/Navbar.vue';

defineProps({
  galeri: Array,
});

const deleteGaleri = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus galeri ini?')) {
    router.delete(`/galeri-admin/${id}`);
  }
};
</script>

<template>
  <Head title="Kelola Galeri" />
  
  <div class="flex h-screen bg-gray-100">
    <Sidebar />
    
    <div class="flex-1 flex flex-col overflow-hidden">
      <Navbar />
      
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
        <div class="container mx-auto">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Kelola Galeri</h1>
            <Link
              :href="'/galeri-admin/create'"
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition"
            >
              + Tambah Galeri
            </Link>
          </div>

          <!-- Gallery Grid -->
          <div v-if="galeri.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="item in galeri"
              :key="item.id"
              class="bg-white rounded-lg shadow-lg overflow-hidden"
            >
              <!-- Image -->
              <div class="relative h-48 overflow-hidden">
                <img
                  :src="item.gambar"
                  :alt="item.judul"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Content -->
              <div class="p-4">
                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ item.judul }}</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                  {{ item.deskripsi || 'Tidak ada deskripsi' }}
                </p>
                <div class="text-xs text-gray-500 mb-4">
                  📅 {{ item.created_at }}
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                  <Link
                    :href="`/galeri-admin/${item.id}/edit`"
                    class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-center transition"
                  >
                    Edit
                  </Link>
                  <button
                    @click="deleteGaleri(item.id)"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-20 bg-white rounded-lg shadow">
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
      </main>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
