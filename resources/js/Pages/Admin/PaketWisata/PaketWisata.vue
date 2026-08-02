<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    paketwisata: Array,
    kategoris: Array,
});

const sidebarOpen = ref(true);

// ========== KATEGORI ==========
const showKategoriForm = ref(false);
const editingKategori = ref(null);

const kategoriForm = useForm({
    _method: '',
    nama_kategori: '',
    deskripsi: '',
    image: null,
});

function openAddKategori() {
    editingKategori.value = null;
    kategoriForm.reset();
    kategoriForm._method = '';
    showKategoriForm.value = true;
}

function openEditKategori(k) {
    editingKategori.value = k;
    kategoriForm._method = 'PUT';
    kategoriForm.nama_kategori = k.nama_kategori;
    kategoriForm.deskripsi = k.deskripsi || '';
    kategoriForm.image = null;
    showKategoriForm.value = true;
}

function submitKategori() {
    if (editingKategori.value) {
        kategoriForm.post(route('kategori.update', editingKategori.value.id), {
            forceFormData: true,
            onSuccess: () => { showKategoriForm.value = false; kategoriForm.reset(); }
        });
    } else {
        kategoriForm.post(route('kategori.store'), {
            forceFormData: true,
            onSuccess: () => { showKategoriForm.value = false; kategoriForm.reset(); }
        });
    }
}

function deleteKategori(id) {
    if (confirm('Hapus kategori ini? Semua paket terkait mungkin terpengaruh.')) {
        router.delete(route('kategori.destroy', id), { preserveScroll: true });
    }
}

// ========== PAKET WISATA ==========
const searchQuery = ref('');
const currentPage = ref(1);
const perPage = ref(10);

const filteredPaket = computed(() => {
    const sorted = [...props.paketwisata].sort((a, b) => b.id - a.id);
    if (!searchQuery.value) return sorted;
    return sorted.filter(p =>
        p.nama_wisata.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const totalPages = computed(() => Math.ceil(filteredPaket.value.length / perPage.value));

const paginatedPaket = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filteredPaket.value.slice(start, start + perPage.value);
});

function changePage(page) {
    if (page >= 1 && page <= totalPages.value) currentPage.value = page;
}

function editPaket(id) { router.get('/paket-wisata/' + id + '/edit'); }

function destroyPaket(id) {
    if (confirm('Hapus paket wisata ini?')) {
        router.post('/paket-wisata/' + id, { _method: 'delete' });
    }
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto flex-1', sidebarOpen ? 'pl-64' : 'pl-16']">
            <div class="container mx-auto px-2 md:p-5 space-y-6">

                <!-- ===== BAGIAN KATEGORI ===== -->
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="flex justify-between items-center mb-1">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Kategori Wisata</h1>
                            <p class="text-xs text-gray-500">Buat kategori terlebih dahulu sebelum menambah paket wisata</p>
                        </div>
                        <button @click="openAddKategori"
                                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-sm font-semibold">
                            + Tambah Kategori
                        </button>
                    </div>
                    <hr class="mb-4">

                    <!-- Flash -->
                    <div v-if="$page.props.flash?.success" class="mb-3 p-3 bg-green-100 border border-green-400 text-green-800 rounded text-sm">
                        {{ $page.props.flash.success }}
                    </div>

                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b border-black border-l-0 px-4 py-3 bg-[#1E3A8A] text-white rounded-tl-2xl">No</th>
                                <th class="border-b border-black px-4 py-3 bg-[#1E3A8A] text-white">Nama Kategori</th>
                                <th class="border-b border-black px-4 py-3 bg-[#1E3A8A] text-white">Deskripsi</th>
                                <th class="border-b border-black px-4 py-3 bg-[#1E3A8A] text-white">Gambar</th>
                                <th class="border-b border-black px-4 py-3 bg-[#1E3A8A] text-white">Jumlah Paket</th>
                                <th class="border-b border-black border-r-0 px-4 py-2 bg-[#1E3A8A] text-white rounded-tr-2xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!kategoris || kategoris.length === 0">
                                <td colspan="6" class="text-center py-6 text-gray-400 border-b border-black">
                                    Belum ada kategori. Tambah kategori dulu sebelum membuat paket wisata.
                                </td>
                            </tr>
                            <tr v-for="(k, index) in kategoris" :key="k.id">
                                <td class="border-b border-black border-l-0 px-4 py-2 text-center">{{ index + 1 }}</td>
                                <td class="border-b border-black px-4 py-2 font-semibold">{{ k.nama_kategori }}</td>
                                <td class="border-b border-black px-4 py-2 text-sm text-gray-600">{{ k.deskripsi || '-' }}</td>
                                <td class="border-b border-black px-4 py-2 text-center">
                                    <img v-if="k.image_url" :src="k.image_url" :alt="k.nama_kategori"
                                         class="w-16 h-12 object-cover rounded mx-auto" />
                                    <span v-else class="text-gray-400 text-xs">-</span>
                                </td>
                                <td class="border-b border-black px-4 py-2 text-center">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                        {{ k.jumlah_paket ?? 0 }} paket hari
                                    </span>
                                </td>
                                <td class="border-b border-black border-r-0 px-4 py-2 text-center space-x-2">
                                    <a :href="`/paket-wisata/kategori/${k.id}/paket-hari`"
                                       class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
                                        Paket Hari
                                    </a>
                                    <button @click="openEditKategori(k)"
                                            class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-xs">
                                        Edit
                                    </button>
                                    <button @click="deleteKategori(k.id)"
                                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <!-- Pagination row placeholder -->
                            <tr>
                                <td colspan="6" class="p-0">
                                    <div class="px-4 py-2 bg-[#1E3A8A] rounded-b-2xl text-sm text-white">
                                        Total: {{ kategoris?.length || 0 }} kategori
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ===== BAGIAN PAKET WISATA ===== -->
                <div class="bg-white rounded-lg p-4 shadow">
                    <div class="flex justify-between items-center mb-1">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Paket Wisata</h1>
                            <p class="text-xs text-gray-500">
                                <span v-if="!kategoris || kategoris.length === 0" class="text-red-500 font-semibold">
                                    ⚠ Buat kategori dulu sebelum menambah paket wisata
                                </span>
                                <span v-else class="text-green-600">
                                    ✓ Kategori tersedia, Anda bisa menambah paket wisata
                                </span>
                            </p>
                        </div>
                        <button :disabled="!kategoris || kategoris.length === 0"
                                @click="router.get('/add-paket-wisata')"
                                :class="['px-4 py-2 rounded text-sm font-semibold transition', kategoris && kategoris.length > 0 ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-gray-300 text-gray-500 cursor-not-allowed']">
                            + Tambah Paket Wisata
                        </button>
                    </div>
                    <hr class="mb-4">

                    <div class="flex justify-between items-center mb-4">
                        <input type="text" v-model="searchQuery" placeholder="Cari nama paket..."
                               class="border border-gray-300 rounded-[10px] px-4 py-2 w-80" />
                        <span class="text-sm text-gray-500">{{ filteredPaket.length }} paket ditemukan</span>
                    </div>

                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b border-black border-l-0 px-4 py-4 bg-[#1E3A8A] text-white rounded-tl-2xl">No</th>
                                <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">Nama Wisata</th>
                                <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">Kategori</th>
                                <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">Durasi</th>
                                <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">Max Personil</th>
                                <th class="border-b border-black px-4 py-4 bg-[#1E3A8A] text-white">Gambar</th>
                                <th class="border-b border-black border-r-0 px-4 py-2 bg-[#1E3A8A] text-white rounded-tr-2xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="filteredPaket.length === 0">
                                <td colspan="7" class="border-b border-black text-center px-4 py-6 text-gray-400">
                                    {{ kategoris && kategoris.length > 0 ? 'Belum ada paket wisata.' : 'Tambah kategori dulu.' }}
                                </td>
                            </tr>
                            <tr v-else v-for="(paket, index) in paginatedPaket" :key="paket.id">
                                <td class="border-b border-black border-l-0 px-4 py-2 text-center">
                                    {{ (currentPage - 1) * perPage + index + 1 }}
                                </td>
                                <td class="border-b border-black px-4 py-2 font-semibold">{{ paket.nama_wisata }}</td>
                                <td class="border-b border-black px-4 py-2 text-center">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">
                                        {{ paket.kategori }}
                                    </span>
                                </td>
                                <td class="border-b border-black px-4 py-2 text-center text-sm">{{ paket.durasi_hari }} Hari</td>
                                <td class="border-b border-black px-4 py-2 text-center">{{ paket.max_personil || '-' }} Orang</td>
                                <td class="border-b border-black px-4 py-2 text-center">
                                    <img v-if="paket.image" :src="`/storage/uploads/admin/paket_wisata/${paket.image}`"
                                         alt="Foto" class="w-20 h-auto mx-auto rounded" />
                                    <span v-else class="text-gray-400 text-xs">-</span>
                                </td>
                                <td class="border-b border-black border-r-0 px-4 py-2 text-center space-x-2">
                                    <button class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-xs"
                                        @click="editPaket(paket.id)">Edit</button>
                                    <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 text-xs"
                                        @click="destroyPaket(paket.id)">Hapus</button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" class="p-0">
                                    <div class="flex items-center justify-between px-4 py-3 bg-[#1E3A8A] rounded-b-2xl">
                                        <span class="text-sm text-white">
                                            Showing {{ Math.min((currentPage-1)*perPage+1, filteredPaket.length) }}-{{ Math.min(currentPage*perPage, filteredPaket.length) }}
                                            of {{ filteredPaket.length }} items
                                        </span>
                                        <div class="flex items-center gap-1">
                                            <button @click="changePage(currentPage-1)" :disabled="currentPage===1"
                                                    class="px-3 py-1 rounded hover:bg-gray-200 disabled:opacity-50 text-white">&lt;</button>
                                            <button v-for="page in totalPages" :key="page" @click="changePage(page)"
                                                    :class="['px-3 py-1 rounded', currentPage===page ? 'bg-white text-black' : 'hover:bg-gray-200 text-black']">
                                                {{ page }}
                                            </button>
                                            <button @click="changePage(currentPage+1)" :disabled="currentPage===totalPages"
                                                    class="px-3 py-1 rounded hover:bg-gray-200 disabled:opacity-50 text-white">&gt;</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kategori -->
    <div v-if="showKategoriForm" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">
                {{ editingKategori ? 'Edit Kategori' : 'Tambah Kategori' }}
            </h2>
            <form @submit.prevent="submitKategori" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input v-model="kategoriForm.nama_kategori" type="text" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-[#1E3A8A] text-sm"
                           placeholder="Contoh: Bromo, Yogyakarta, Bali" />
                    <p v-if="kategoriForm.errors.nama_kategori" class="text-red-500 text-xs mt-1">{{ kategoriForm.errors.nama_kategori }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea v-model="kategoriForm.deskripsi" rows="3"
                              class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-[#1E3A8A] text-sm resize-none"
                              placeholder="Deskripsi singkat destinasi..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar Kategori</label>
                    <!-- Preview gambar lama saat edit -->
                    <div v-if="editingKategori?.image_url && !kategoriForm.image" class="mb-2">
                        <img :src="editingKategori.image_url" alt="Gambar saat ini"
                             class="w-full h-32 object-cover rounded-lg border border-gray-200" />
                        <p class="text-xs text-gray-500 mt-1">Gambar saat ini. Upload baru untuk mengganti.</p>
                    </div>
                    <input type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           @change="e => kategoriForm.image = e.target.files[0]"
                           class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1E3A8A]" />
                    <p v-if="kategoriForm.errors.image" class="text-red-500 text-xs mt-1">{{ kategoriForm.errors.image }}</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="kategoriForm.processing"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition disabled:opacity-50">
                        {{ kategoriForm.processing ? 'Menyimpan...' : (editingKategori ? 'Simpan' : 'Tambah') }}
                    </button>
                    <button type="button" @click="showKategoriForm = false"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
