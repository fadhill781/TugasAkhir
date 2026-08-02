<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
  testimonis: Array,
});

const sidebarOpen = ref(true);

function goToCreate() {
  router.get('/testimoni/create');
}

function goToEdit(id) {
  router.get(`/testimoni/${id}/edit`);
}

function destroy(id) {
  if (confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) {
    router.delete(`/testimoni/${id}`, {
      preserveScroll: true,
    });
  }
}

</script>

<template>
  <div class="flex min-h-screen bg-gray-100 overflow-hidden">
    <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

    <div
      :class="[
        'transition-all duration-300 ease-in-out overflow-y-auto',
        sidebarOpen ? 'pl-64' : 'pl-16',
        'flex-1 p-6 bg-white rounded-lg shadow-md max-w-7xl mx-auto mt-6',
      ]"
    >
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Kelola Testimoni</h1>
        <button
          @click="goToCreate"
          class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-md transition duration-200"
        >
          Tambah Testimoni
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
          <thead class="bg-[#1E3A8A]">
            <tr>
              <th class="text-left px-6 py-3 border border-gray-300 text-white">Testimoni</th>
              <th class="text-left px-6 py-3 border border-gray-300 text-white">Nama</th>
              <th class="text-left px-6 py-3 border border-gray-300 text-white">Asal</th>
              <th class="text-center px-6 py-3 border border-gray-300 text-white">Status</th>
              <th class="text-center px-6 py-3 border border-gray-300 text-white">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="testimonis.length === 0">
              <td colspan="5" class="text-center px-6 py-4 border border-gray-300 text-gray-500">
                Belum ada testimoni.
              </td>
            </tr>
            <tr v-for="testimoni in testimonis" :key="testimoni.id"
                class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-6 py-4 border border-gray-300 max-w-md">
                <p class="line-clamp-2">{{ testimoni.testimoni }}</p>
              </td>
              <td class="px-6 py-4 border border-gray-300 whitespace-nowrap">
                {{ testimoni.nama_pentestimoni }}
              </td>
              <td class="px-6 py-4 border border-gray-300 whitespace-nowrap">
                {{ testimoni.asal || '-' }}
              </td>
              <td class="px-6 py-4 border border-gray-300 text-center">
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', testimoni.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                  {{ testimoni.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-6 py-4 border border-gray-300 text-center space-x-2">
                <button @click="goToEdit(testimoni.id)"
                        class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md transition">
                  Edit
                </button>
                <button @click="destroy(testimoni.id)"
                        class="inline-block bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-md transition">
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
