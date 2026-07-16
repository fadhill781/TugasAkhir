<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/includes/Sidebar.vue';
import Navbar from '@/includes/Navbar.vue';
import { ref } from 'vue';

const props = defineProps({
  galeri: Object,
});

const form = useForm({
  judul: props.galeri.judul,
  deskripsi: props.galeri.deskripsi,
  gambar: null,
  _method: 'PUT',
});

const previewImage = ref(props.galeri.gambar);

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.gambar = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const submit = () => {
  form.post(`/galeri-admin/${props.galeri.id}`, {
    forceFormData: true,
  });
};
</script>

<template>
  <Head title="Edit Galeri" />
  
  <div class="flex h-screen bg-gray-100">
    <Sidebar />
    
    <div class="flex-1 flex flex-col overflow-hidden">
      <Navbar />
      
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
        <div class="container mx-auto max-w-3xl">
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Galeri</h1>
            <p class="text-gray-600">Perbarui informasi galeri</p>
          </div>

          <!-- Form -->
          <div class="bg-white rounded-lg shadow-lg p-6">
            <form @submit.prevent="submit">
              <!-- Judul -->
              <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">
                  Judul <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.judul"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Masukkan judul foto"
                  required
                />
                <div v-if="form.errors.judul" class="text-red-500 text-sm mt-1">
                  {{ form.errors.judul }}
                </div>
              </div>

              <!-- Deskripsi -->
              <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">
                  Deskripsi
                </label>
                <textarea
                  v-model="form.deskripsi"
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Masukkan deskripsi foto (opsional)"
                ></textarea>
                <div v-if="form.errors.deskripsi" class="text-red-500 text-sm mt-1">
                  {{ form.errors.deskripsi }}
                </div>
              </div>

              <!-- Upload Gambar -->
              <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">
                  Gambar
                </label>
                <input
                  type="file"
                  @change="handleImageUpload"
                  accept="image/*"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <p class="text-gray-500 text-sm mt-1">
                  Format: JPG, JPEG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar.
                </p>
                <div v-if="form.errors.gambar" class="text-red-500 text-sm mt-1">
                  {{ form.errors.gambar }}
                </div>

                <!-- Preview Image -->
                <div v-if="previewImage" class="mt-4">
                  <p class="text-gray-700 font-semibold mb-2">Preview:</p>
                  <img
                    :src="previewImage"
                    alt="Preview"
                    class="w-full max-w-md h-64 object-cover rounded-lg border-2 border-gray-300"
                  />
                </div>
              </div>

              <!-- Buttons -->
              <div class="flex gap-4">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition disabled:bg-gray-400"
                >
                  <span v-if="form.processing">Menyimpan...</span>
                  <span v-else>Update Galeri</span>
                </button>
                <a
                  href="/galeri-admin"
                  class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold text-center transition"
                >
                  Batal
                </a>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
