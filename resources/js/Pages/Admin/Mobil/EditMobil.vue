<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    mobil: Object,
});

const form = useForm({
    _method: 'put',
    nama: props.mobil.nama,
    harga: props.mobil.harga,
    image: null,
    durasi: props.mobil.durasi,
    unit_tersedia: props.mobil.unit_tersedia ?? 1,
});

function submit(id) {
    form.post(`/mobil/${id}`, {
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

const getMobilImageUrl = (imageName) => {
    return imageName ? `/storage/uploads/admin/mobil/${imageName}` : '';
};
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-3">
                    <h1 class="mb-1">Edit Mobil</h1>
                    <hr>
                    <form @submit.prevent="submit(mobil.id)" enctype="multipart/form-data">
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Nama Mobil</label>
                            <input type="text" v-model="form.nama"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Harga</label>
                            <input type="text" v-model="form.harga"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Image</label>
                            <div class="w-2/3">
                                <div v-if="props.mobil.image" class="text-xs text-gray-500 mb-2">
                                    Image: {{ props.mobil.image }}
                                </div>
                                <input type="file" @change="handleFileChange"
                                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Durasi (Hari)</label>
                            <input type="number" v-model="form.durasi" placeholder="Contoh: 3"
                                class="w-2/3 border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm" />
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="w-1/3 font-medium text-gray-700">Unit Tersedia</label>
                            <input type="number" v-model="form.unit_tersedia" min="0" placeholder="Contoh: 5"
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