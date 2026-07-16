<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faFacebook, faInstagram, faWhatsapp } from "@fortawesome/free-brands-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

// Tambahkan ikon ke library
library.add(faFacebook, faInstagram, faWhatsapp);

export default {
  components: {
    FontAwesomeIcon, 
  },
};
</script>


<style>
@import '../../css/app.css';
</style>

<template>
  <footer class="bg-[#1266AC] text-white py-10 shadow-md">
    <div class="container mx-auto px-6 w-full max-w-screen-lg grid grid-cols-1 sm:grid-cols-3 gap-6 text-center sm:text-left">
      <!-- Tentang Kami (Kiri) -->
      <div>
        <h3 class="text-xl font-bold mb-3">Tentang Kami</h3>
        <p class="text-white text-sm">Kami menyediakan paket wisata terbaik dengan harga kompetitif.</p>
      </div>

      <!-- Navigasi (Tengah) -->
      <div class="flex flex-col items-center sm:items-center">
        <h3 class="text-xl font-bold mb-3">Navigasi</h3>
        <ul class="text-white text-sm space-y-2">
          <li><a href="#" class="hover:text-white">Home</a></li>
          <li><a href="" class="hover:text-white">Destinasi</a></li>
          <li><a href="/paket-wisata-public" class="hover:text-white">Paket Wisata</a></li>
          <li><a href="#" class="hover:text-white">Kontak</a></li>
        </ul>
      </div>

      <!-- Media Sosial (Kanan) -->
      <div class="flex flex-col items-center sm:items-center text-center sm:text-left">
        <h3 class="text-xl font-bold mb-3">Ikuti Kami</h3>
        <div class="flex flex-col justify-center space-y-4 w-full items-center sm:items-end">
          <a href="https://www.facebook.com/profile.php?id=100076061539177"
            class="text-[#1877F2] w-[100%] px-4 py-2 border-2 border-black bg-white font-bold
            shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px]
            hover:translate-y-[4px] hover:shadow-none rounded-md flex items-center justify-center gap-2 ">
            <font-awesome-icon :icon="['fab', 'facebook']" class="text-2xl" />
            <span>Facebook</span>
          </a>
          <a href="https://www.instagram.com/iconictour"
            class="text-[#E4405F] w-[100%] px-4 py-2 border-2 border-black bg-white font-bold
            shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px]
            hover:translate-y-[4px] hover:shadow-none rounded-md flex items-center justify-center gap-2">
            <font-awesome-icon :icon="['fab', 'instagram']" class="text-2xl" />
            <span>Instagram</span>
          </a>
          <a href=""
            class="text-[#25D366] w-[100%] px-4 py-2 border-2 border-black bg-white font-bold
            shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px]
            hover:translate-y-[4px] hover:shadow-none rounded-md flex items-center justify-center gap-2">
            <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-2xl" />
            <span>WhatsApp</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Copyright -->
    <div class="mt-10 text-center text-white text-sm pt-4">
      &copy; 2025 Iconic Tour & Travel
    </div>
  </footer>
</template>




