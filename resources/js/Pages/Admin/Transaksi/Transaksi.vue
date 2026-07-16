// resources/js/Pages/Transaksi/Transaksi.vue
<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Transaksi</h1>
      <Link href="/admin/createtransaksi" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
        Add
      </Link>
    </div>

    <table class="w-full text-left border rounded shadow">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border">No</th>
          <th class="px-4 py-2 border">Nama</th>
          <th class="px-4 py-2 border">Waktu</th>
          <th class="px-4 py-2 border">Total</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in transaksis" :key="item.id" class="odd:bg-white even:bg-gray-50">
          <td class="px-4 py-2 border">{{ index + 1 }}</td>
          <td class="px-4 py-2 border">{{ item.nama }}</td>
          <td class="px-4 py-2 border">{{ formatDate(item.waktu) }}</td>
          <td class="px-4 py-2 border">{{ formatCurrency(item.total) }}</td>
          <td class="px-4 py-2 border space-x-2">
            <Link :href="`/transaksi/${item.id}/edit`" class="bg-blue-500 text-white px-3 py-1 rounded">edit</Link>
            <button @click="destroy(item.id)" class="bg-red-500 text-white px-3 py-1 rounded">delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
  transaksis: Array
});

const formatDate = (date) => format(new Date(date), 'dd MMMM yyyy');
const formatCurrency = (num) => new Intl.NumberFormat('id-ID').format(num);

const destroy = (id) => {
  if (confirm('Yakin ingin menghapus transaksi ini?')) {
    router.delete(`/transaksi/${id}`);
  }
};
</script>
