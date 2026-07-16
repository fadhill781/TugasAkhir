<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    testimoni: Object,
});

const form = reactive({
    testimoni: props.testimoni.testimoni,
    nama_pentestimoni: props.testimoni.nama_pentestimoni,
    asal: props.testimoni.asal || '',
    is_active: props.testimoni.is_active,
});

const errors = ref({});
const processing = ref(false);

function submit() {
    processing.value = true;
    errors.value = {};

    router.put(`/testimoni/${props.testimoni.id}`, form, {
        onSuccess: () => {
            processing.value = false;
        },
        onError: (err) => {
            errors.value = err;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        }
    });
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <div class="container mx-auto p-4 md:p-5">
                <div class="bg-white rounded-lg p-6 max-w-3xl mx-auto">
                    <h1 class="mb-4 text-2xl font-semibold">Edit Testimoni</h1>
                    <hr class="mb-6">
                    
                    <div v-if="Object.keys(errors).length > 0" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
                        </ul>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 mb-2">Testimoni *</label>
                            <textarea v-model="form.testimoni" rows="5"
                                class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 mb-2">Nama Pentestimoni *</label>
                            <input type="text" v-model="form.nama_pentestimoni"
                                class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                        </div>
                        
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 mb-2">Asal</label>
                            <input type="text" v-model="form.asal"
                                class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm" />
                        </div>
                        
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.is_active" class="mr-2">
                                <span class="text-gray-700">Tampilkan di homepage</span>
                            </label>
                        </div>
                        
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="router.get('/testimoni')"
                                class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600 transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="processing" 
                                class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition disabled:opacity-50">
                                <span v-if="processing">Menyimpan...</span>
                                <span v-else>Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
