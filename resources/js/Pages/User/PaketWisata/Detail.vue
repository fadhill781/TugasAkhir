<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import NavbarUser from '@/includes/NavbarUser.vue';
import Footer from '@/includes/Footer.vue';

const props = defineProps({
  paketWisata: Object,
  kategori: Object,
  paketHaris: Array,
  existingDraft: Object,
});

const page = usePage();
const showSuccess = ref(false);
const draftId = ref(null);
const autoSaveStatus = ref(''); // 'saving', 'saved', or ''
const autoSaveTimer = ref(null);

const form = useForm({
  paket_hari_id: props.existingDraft?.paket_hari_id || props.paketWisata.paket_hari_id,
  nama: props.existingDraft?.nama || page.props.auth.user?.name || '',
  email: props.existingDraft?.email || page.props.auth.user?.email || '',
  telepon: props.existingDraft?.telepon || '',
  jumlah_orang: props.existingDraft?.jumlah_orang || 1,
  tanggal_berangkat: props.existingDraft?.tanggal_berangkat || '',
  catatan: props.existingDraft?.catatan || '',
  draft_id: props.existingDraft?.id || null,
});

// Set draft ID if exists
if (props.existingDraft) {
  draftId.value = props.existingDraft.id;
}

const selectedPaketHari = computed(() => {
  return props.paketHaris.find(ph => ph.id === form.paket_hari_id);
});

const totalHarga = computed(() => {
  if (!selectedPaketHari.value) return 0;
  return selectedPaketHari.value.harga * form.jumlah_orang;
});

// Auto-save draft function
const saveDraft = async () => {
  // Don't save if form is being submitted
  if (form.processing) return;
  
  autoSaveStatus.value = 'saving';
  
  try {
    const response = await fetch(route('booking.draft'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        paket_hari_id: form.paket_hari_id || null,
        nama: form.nama || null,
        email: form.email || null,
        telepon: form.telepon || null,
        jumlah_orang: form.jumlah_orang || null,
        tanggal_berangkat: form.tanggal_berangkat || null,
        catatan: form.catatan || null,
      }),
    });
    
    const data = await response.json();
    
    if (data.success) {
      draftId.value = data.draft_id;
      form.draft_id = data.draft_id;
      autoSaveStatus.value = 'saved';
      setTimeout(() => {
        autoSaveStatus.value = '';
      }, 2000);
    }
  } catch (error) {
    console.error('Failed to save draft:', error);
    autoSaveStatus.value = '';
  }
};

// Debounced auto-save
const triggerAutoSave = () => {
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value);
  }
  autoSaveTimer.value = setTimeout(() => {
    saveDraft();
  }, 1500); // Save after 1.5 seconds of inactivity
};

// Watch form changes for auto-save
watch(
  () => ({
    paket_hari_id: form.paket_hari_id,
    nama: form.nama,
    email: form.email,
    telepon: form.telepon,
    jumlah_orang: form.jumlah_orang,
    tanggal_berangkat: form.tanggal_berangkat,
    catatan: form.catatan,
  }),
  () => {
    triggerAutoSave();
  },
  { deep: true }
);

function submit() {
  if (!selectedPaketHari.value) {
    alert('Silakan pilih durasi paket terlebih dahulu');
    return;
  }

  form.post(route('booking.store'), {
    onSuccess: () => {
      showSuccess.value = true;
      // Clear draft after successful booking
      draftId.value = null;
    },
    onError: (errors) => {
      console.error('Booking error:', errors);
      alert('Terjadi kesalahan saat memproses pemesanan');
    }
  });
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-blue-50">
    <NavbarUser />
    
    <!-- Success Notification -->
    <div v-if="showSuccess" class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-green-500 text-white px-8 py-4 rounded-xl shadow-2xl flex items-center gap-3 border-2 border-black">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <div>
        <p class="font-bold">Pesanan Berhasil Dibuat!</p>
        <p class="text-sm text-green-100">Email konfirmasi telah dikirim ke {{ form.email }}</p>
      </div>
    </div>
    
    <div class="container mx-auto px-4 pt-36 pb-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Package Info (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Breadcrumb -->
          <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="/" class="hover:text-blue-600">Home</a>
            <span>/</span>
            <a :href="`/destinasi/${kategori.id}/paket`" class="hover:text-blue-600">{{ kategori.nama_kategori }}</a>
            <span>/</span>
            <span class="text-gray-800 font-semibold">{{ paketWisata.nama_wisata }}</span>
          </div>

          <!-- Main Image -->
          <div class="relative h-96 rounded-2xl overflow-hidden shadow-xl">
            <img v-if="paketWisata.image_url" :src="paketWisata.image_url" :alt="paketWisata.nama_wisata" 
                 class="w-full h-full object-cover" />
            <div v-else class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
              <span class="text-white text-6xl">📸</span>
            </div>
          </div>

          <!-- Package Title & Info -->
          <div class="bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">{{ paketWisata.nama_wisata }}</h1>
            
            <!-- Quick Info -->
            <div class="flex flex-wrap gap-6 mb-6 pb-6 border-b border-gray-200">
              <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="font-semibold">{{ kategori.nama_kategori }}</span>
              </div>
              <div v-if="paketWisata.max_personil" class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Max {{ paketWisata.max_personil }} Orang</span>
              </div>
            </div>

            <!-- Short Description -->
            <div v-if="paketWisata.short_description" class="mb-6">
              <h3 class="text-xl font-bold text-gray-800 mb-3">Deskripsi Singkat</h3>
              <p class="text-gray-700 leading-relaxed">{{ paketWisata.short_description }}</p>
            </div>

            <!-- Long Description / Itinerary -->
            <div v-if="paketWisata.long_description" class="mb-6">
              <h3 class="text-xl font-bold text-gray-800 mb-3">Detail Paket & Itinerary</h3>
              <div class="prose prose-blue max-w-none">
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ paketWisata.long_description }}</p>
              </div>
            </div>

            <!-- Package Features -->
            <div class="bg-blue-50 rounded-xl p-6 mt-6">
              <h3 class="text-lg font-bold text-gray-800 mb-4">Fasilitas Termasuk:</h3>
              <div class="grid md:grid-cols-2 gap-3">
                <div class="flex items-center gap-2 text-gray-700">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span>Tour Guide Profesional</span>
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span>Transportasi AC</span>
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span>Tiket Masuk Wisata</span>
                </div>
                <div class="flex items-center gap-2 text-gray-700">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span>Dokumentasi Foto</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Booking Form Sidebar (1/3 width) -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-24">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl p-6 mb-6">
              <h2 class="text-2xl font-bold mb-2">Pesan Sekarang</h2>
              <p class="text-blue-100 text-sm">Isi form untuk reservasi paket wisata</p>
              
              <!-- Auto-save indicator -->
              <div v-if="autoSaveStatus" class="mt-3 flex items-center gap-2 text-xs">
                <svg v-if="autoSaveStatus === 'saving'" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else-if="autoSaveStatus === 'saved'" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span v-if="autoSaveStatus === 'saving'">Menyimpan draft...</span>
                <span v-else-if="autoSaveStatus === 'saved'">Draft tersimpan</span>
              </div>
            </div>
            
            <form @submit.prevent="submit" class="space-y-4">
              <!-- Paket Hari Dropdown -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Pilih Durasi <span class="text-red-500">*</span>
                </label>
                <select v-model="form.paket_hari_id" required
                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option value="">Pilih Durasi</option>
                  <option v-for="paket in paketHaris" :key="paket.id" :value="paket.id">
                    {{ paket.durasi_hari }} Hari - Rp {{ Number(paket.harga).toLocaleString('id-ID') }}
                  </option>
                </select>
              </div>

              <!-- Nama -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input v-model="form.nama" type="text" required
                       class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Nama lengkap" />
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Email <span class="text-red-500">*</span>
                </label>
                <input v-model="form.email" type="email" required
                       class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="email@example.com" />
              </div>

              <!-- Telepon -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  No. Telepon <span class="text-red-500">*</span>
                </label>
                <input v-model="form.telepon" type="tel" required
                       class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="08xxxxxxxxxx" />
              </div>

              <!-- Jumlah Orang -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Jumlah Peserta <span class="text-red-500">*</span>
                </label>
                <input v-model.number="form.jumlah_orang" type="number" min="1" :max="paketWisata.max_personil" required
                       class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
              </div>

              <!-- Tanggal Berangkat -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Tanggal Berangkat <span class="text-red-500">*</span>
                </label>
                <input v-model="form.tanggal_berangkat" type="date" required
                       class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
              </div>

              <!-- Catatan -->
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                  Catatan
                </label>
                <textarea v-model="form.catatan" rows="2"
                          class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Catatan tambahan"></textarea>
              </div>

              <!-- Total Harga -->
              <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border-2 border-blue-200">
                <div class="flex justify-between items-center mb-2 text-sm">
                  <span class="text-gray-600">Harga/orang:</span>
                  <span class="font-bold text-gray-800">
                    Rp {{ selectedPaketHari ? Number(selectedPaketHari.harga).toLocaleString('id-ID') : 0 }}
                  </span>
                </div>
                <div class="flex justify-between items-center mb-3 text-sm">
                  <span class="text-gray-600">Peserta:</span>
                  <span class="font-bold text-gray-800">{{ form.jumlah_orang }} orang</span>
                </div>
                <div class="border-t-2 border-blue-300 my-2"></div>
                <div class="flex justify-between items-center">
                  <span class="text-base font-bold text-gray-800">Total:</span>
                  <span class="text-2xl font-bold text-blue-600">
                    Rp {{ Number(totalHarga).toLocaleString('id-ID') }}
                  </span>
                </div>
              </div>

              <!-- Submit Button -->
              <button type="submit" :disabled="form.processing"
                      class="w-full px-4 py-3 cursor-pointer items-center rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none disabled:opacity-50 disabled:cursor-not-allowed uppercase text-sm">
                <span v-if="form.processing">Memproses...</span>
                <span v-else>Pesan Sekarang</span>
              </button>

              <!-- Contact Info -->
              <div class="pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-600 text-center mb-3">Atau hubungi kami:</p>
                <div class="flex gap-2">
                  <a href="https://wa.me/6281234567890" target="_blank"
                     class="flex-1 flex items-center justify-center gap-2 px-3 py-2 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-xs">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                  </a>
                  <a href="tel:+6281234567890"
                     class="flex-1 flex items-center justify-center gap-2 px-3 py-2 cursor-pointer rounded-md border-2 border-black bg-[#FFFFFF] font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Call
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>
