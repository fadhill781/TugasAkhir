<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
    nama_wisata: null,
    kategori_id: null,
    paket_hari_id: null,
    short_description: null,
    long_description: null,
    image: null,
    max_personil: null,
});

const kategoris = ref([]);
const paketHaris = ref([]);

const filteredPaketHaris = computed(() => {
    if (!form.kategori_id) return [];
    return paketHaris.value.filter(ph => ph.kategori_id == form.kategori_id);
});

onMounted(async () => {
    try {
        const response = await fetch('/api/kategoris');
        kategoris.value = await response.json();
    } catch (error) {
        console.error('Error fetching kategoris:', error);
    }
});

async function loadPaketHaris(kategoriId) {
    if (!kategoriId) {
        paketHaris.value = [];
        form.paket_hari_id = null;
        return;
    }
    
    try {
        const response = await fetch(`/api/kategoris/${kategoriId}/paket-haris`);
        paketHaris.value = await response.json();
    } catch (error) {
        console.error('Error fetching paket haris:', error);
    }
}

function submit() {
    const formData = new FormData();
    formData.append('nama_wisata', form.nama_wisata);
    formData.append('paket_hari_id', form.paket_hari_id);
    formData.append('short_description', form.short_description);
    formData.append('long_description', form.long_description);
    formData.append('max_personil', form.max_personil);

    if (form.image) {
        formData.append('image', form.image);
    }

    router.post('/add-paket-wisata', formData);

};
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1">Paket Wisata</h1>
                    <hr>
                    <form @submit.prevent="submit">
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Nama</label>
                            <input type="text" v-model="form.nama_wisata"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Kategori (Kota)</label>
                            <select v-model="form.kategori_id" @change="loadPaketHaris(form.kategori_id)" required
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm">
                                <option value="">Pilih Kategori</option>
                                <option v-for="kategori in kategoris" :key="kategori.id" :value="kategori.id">
                                    {{ kategori.nama_kategori }}
                                </option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Paket Hari</label>
                            <select v-model="form.paket_hari_id" required :disabled="!form.kategori_id"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm disabled:bg-gray-100">
                                <option value="">Pilih Paket Hari</option>
                                <option v-for="paket in filteredPaketHaris" :key="paket.id" :value="paket.id">
                                    {{ paket.durasi_hari }} Hari - Rp {{ Number(paket.harga).toLocaleString('id-ID') }}
                                </option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Deskripsi Singkat</label>
                            <textarea type="text" v-model="form.short_description" rows="2"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Deskripsi Panjang</label>
                            <textarea type="text" v-model="form.long_description" rows="4"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Image</label>
                            <input type="file" @change="event => form.image = event.target.files[0]"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Max Personil</label>
                            <input type="number" v-model="form.max_personil"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>