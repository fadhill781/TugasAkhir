<script setup>
import { reactive, ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    kategori: Object,
    paketHari: Array,
});

const form = reactive({
    kategori_id: props.kategori.id,
    durasi_hari: '',
    harga: '',
    deskripsi: '',
});

const editingId = ref(null);
const editForm = reactive({
    durasi_hari: '',
    harga: '',
    deskripsi: '',
});

function submit() {
    router.post(`/kategori/${props.kategori.id}/paket-hari`, form, {
        onSuccess: () => {
            form.durasi_hari = '';
            form.harga = '';
            form.deskripsi = '';
        }
    });
}

function editPaket(paket) {
    editingId.value = paket.id;
    editForm.durasi_hari = paket.durasi_hari;
    editForm.harga = paket.harga;
    editForm.deskripsi = paket.deskripsi;
}

function updatePaket(id) {
    router.put(`/kategori/${props.kategori.id}/paket-hari/${id}`, editForm, {
        onSuccess: () => {
            editingId.value = null;
        }
    });
}

function deletePaket(id) {
    if (confirm('Apakah Anda yakin ingin menghapus paket ini?')) {
        router.delete(`/kategori/${props.kategori.id}/paket-hari/${id}`);
    }
}

function cancelEdit() {
    editingId.value = null;
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3 mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h1 class="text-2xl font-bold">Paket Hari - {{ kategori.nama_kategori }}</h1>
                            <p class="text-gray-600 text-sm">{{ kategori.deskripsi }}</p>
                        </div>
                        <a href="/kategori" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Kembali
                        </a>
                    </div>
                    <hr class="mb-4">
                    
                    <!-- Form Tambah Paket -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h2 class="text-lg font-semibold mb-3">Tambah Paket Baru</h2>
                        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (Hari)</label>
                                <input type="number" v-model="form.durasi_hari" required
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                                    placeholder="Contoh: 2" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                                <input type="number" v-model="form.harga" required
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                                    placeholder="Contoh: 500000" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <input type="text" v-model="form.deskripsi"
                                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" 
                                    placeholder="Opsional" />
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    Tambah
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Paket -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">No</th>
                                    <th class="px-4 py-2 border">Durasi</th>
                                    <th class="px-4 py-2 border">Harga</th>
                                    <th class="px-4 py-2 border">Deskripsi</th>
                                    <th class="px-4 py-2 border">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!paketHari || paketHari.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada paket untuk kategori ini
                                    </td>
                                </tr>
                                <tr v-for="(paket, index) in paketHari" :key="paket.id">
                                    <td class="px-4 py-2 border text-center">{{ index + 1 }}</td>
                                    <td class="px-4 py-2 border">
                                        <span v-if="editingId !== paket.id">{{ paket.durasi_hari }} Hari</span>
                                        <input v-else type="number" v-model="editForm.durasi_hari"
                                            class="w-full border border-gray-300 px-2 py-1 rounded" />
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <span v-if="editingId !== paket.id">Rp {{ Number(paket.harga).toLocaleString('id-ID') }}</span>
                                        <input v-else type="number" v-model="editForm.harga"
                                            class="w-full border border-gray-300 px-2 py-1 rounded" />
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <span v-if="editingId !== paket.id">{{ paket.deskripsi || '-' }}</span>
                                        <input v-else type="text" v-model="editForm.deskripsi"
                                            class="w-full border border-gray-300 px-2 py-1 rounded" />
                                    </td>
                                    <td class="px-4 py-2 border text-center">
                                        <div v-if="editingId !== paket.id" class="flex gap-2 justify-center">
                                            <button @click="editPaket(paket)" 
                                                    class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                Edit
                                            </button>
                                            <button @click="deletePaket(paket.id)" 
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                Hapus
                                            </button>
                                        </div>
                                        <div v-else class="flex gap-2 justify-center">
                                            <button @click="updatePaket(paket.id)" 
                                                    class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                                Simpan
                                            </button>
                                            <button @click="cancelEdit" 
                                                    class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">
                                                Batal
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
