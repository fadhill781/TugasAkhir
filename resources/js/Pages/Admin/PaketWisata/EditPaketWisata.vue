<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    paketwisata: Object,
});

const form = useForm({
    _method: 'put',
    nama_wisata: props.paketwisata.nama_wisata,
    kategori_id: props.paketwisata.kategori_id,
    short_description: props.paketwisata.short_description,
    long_description: props.paketwisata.long_description,
    image: null,
    harga: props.paketwisata.harga,
    active_days: props.paketwisata.active_days,
    max_personil: props.paketwisata.max_personil,
});

const kategoris = ref([]);

onMounted(async () => {
    try {
        const response = await fetch('/api/kategoris');
        kategoris.value = await response.json();
    } catch (error) {
        console.error('Error fetching kategoris:', error);
    }
});

function submit(id) {

    form.post(`/paket-wisata/${id}`, {
        forceFormData: true, 
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            console.error(errors);
        },
    });
}

function handleFileChange(event) {
    form.image = event.target.files[0];
}

const getPaketWisataImageUrl = (imageName) => {
    return imageName ? `/storage/uploads/admin/paket_wisata/${imageName}` : '';
};
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1">Edit Paket Wisata</h1>
                    <hr>
                    <form @submit.prevent="submit(paketwisata.id)" enctype="multipart/form-data">
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Name</label>
                            <input type="text" v-model="form.nama_wisata"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Kategori (Destinasi)</label>
                            <select v-model="form.kategori_id" required
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm">
                                <option value="">Pilih Kategori</option>
                                <option v-for="kategori in kategoris" :key="kategori.id" :value="kategori.id">
                                    {{ kategori.nama_kategori }}
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
                            <div class="w-2/3">
                                <div v-if="props.paketwisata.image" class="text-xs text-gray-500 mb-2">
                                    Image: {{ props.paketwisata.image }}
                                </div>
                                <input type="file" @change="handleFileChange"
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Harga</label>
                            <input type="text" v-model="form.harga"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Active</label>
                            <input type="number" v-model="form.active_days"
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