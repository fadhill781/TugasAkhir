// resources/js/Pages/Transaksi/CreateTransaksi.vue
<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Transaksi Baru</h1>

    <form @submit.prevent="submit">
      <div class="mb-4">
        <label class="block mb-1 font-semibold">Nama</label>
        <input v-model="form.nama" type="text" class="w-full border rounded px-4 py-2" placeholder="Masukkan nama aplikasi anda" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-semibold">Waktu</label>
        <input v-model="form.waktu" type="date" class="w-full border rounded px-4 py-2" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-semibold">Total</label>
        <input v-model="form.total" type="number" class="w-full border rounded px-4 py-2" placeholder="Masukkan harga" />
      </div>

      <div class="flex space-x-2">
        <Link href="/transaksi" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</Link>
        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Add</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';

const form = reactive({
  nama: '',
  waktu: '',
  total: ''
});

const submit = () => {
  router.post('/transaksi', form);
};
</script>
