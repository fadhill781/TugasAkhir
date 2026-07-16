<script>
export default {
  name: "Navbar",
  data() {
    return {
      isMenuOpen: false,
      isSearchOpen: false, 
      searchQuery: "", 
    };
  },
  methods: {
    toggleSearch() {
      this.isSearchOpen = !this.isSearchOpen;
    },
  },
};
</script>

<template>
  <!-- Navbar -->
  <header class="fixed w-screen bg-[#1266AC] text-white z-50 shadow-md">
    <div class="container mx-auto flex justify-between items-center px-4 py-3">
      <!-- Logo -->
      <img src="../../../public/img/logorill.png" alt="Logo Iconic" class="h-24 w-24">

      <!-- Navbar Menu (Desktop) -->
      <nav class="hidden sm:flex ml-8">
        <ul class="flex gap-4">
          <li v-for="item in ['Home', 'Destinasi', 'Paket', 'Galeri']" :key="item">
            <a 
              :href="item === 'Galeri' ? '#galeri' : '#'" 
              class="px-4 py-2 my-2 flex cursor-pointer items-center rounded-md
              border-2 border-black bg-[#FFFFFF] font-bold text-black
              shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px]
              hover:translate-y-[4px] hover:shadow-none">
              {{ item }}
            </a>
          </li>
        </ul>
      </nav>

      <!-- Search Bar & Login/Register (Desktop) -->
      <div class="hidden sm:flex gap-4 items-center ml-auto">
        <input type="text" placeholder="Cari..." class="flex h-10 w-full rounded-md border-2 border-black bg-white px-3 py-2 text-sm 
 font-bold text-black placeholder-black focus:outline-none focus:ring-2 
 focus:ring-black focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
        
        <a
  href="/login-user"
  class="px-4 py-2 my-2 flex cursor-pointer items-center rounded-md
         border-2 border-black bg-[#FFD700] font-bold text-black
         shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all
         hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none
         whitespace-nowrap"
>
  Login
</a>

<a
  href="/register"
  class="px-4 py-2 my-2 flex cursor-pointer items-center rounded-md
         border-2 border-black bg-[#87C8FA] font-bold text-black
         shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all
         hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none
         whitespace-nowrap"
>
  Register
</a>

      </div>

      <!-- Search Icon & Burger Menu (Mobile) -->
      <div class="sm:hidden flex items-center gap-4">
        <!-- Search Button -->
        <button @click="toggleSearch" class="focus:outline-none">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1 0 10.5 18 7.5 7.5 0 0 0 16.65 16.65z"></path>
          </svg>
        </button>

        <!-- Menu Burger (Mobile) -->
        <button @click="isMenuOpen = !isMenuOpen" class="focus:outline-none">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16m-7 6h7"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Search Bar (Mobile) -->
    <div v-if="isSearchOpen" class="sm:hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white w-4/5 max-w-md p-4 rounded-lg shadow-lg flex items-center gap-2">
        <input v-model="searchQuery" type="text" placeholder="Cari..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0047AB]">
        
        <!-- Close Search Button -->
        <button @click="toggleSearch" class="text-gray-600 hover:text-gray-900">
          ✖
        </button>
      </div>
    </div>

    <!-- Sidebar Menu (Mobile) -->
    <div v-if="isMenuOpen" class="sm:hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex">
      <div class="bg-[#0047AB] w-2/3 max-w-xs h-full shadow-md p-6">
        <button @click="isMenuOpen = false" class="absolute top-4 right-4 text-white">
          ✖
        </button>
        <ul class="space-y-4 mt-8">
          <li v-for="item in ['Home', 'Destinasi', 'Paket', 'Galeri', 'Kontak']" :key="item">
            <a 
              :href="item === 'Galeri' ? '#galeri' : '#'" 
              @click="isMenuOpen = false"
              class="block px-4 py-3 bg-[#88AAEE] text-black font-bold rounded-md text-center">
              {{ item }}
            </a>
          </li>
        </ul>
        <!-- Tombol Login & Register di Sidebar -->
        <div class="mt-6 space-y-4">
          <a href="/login-user" class="block w-full bg-[#FFD700] text-black font-bold py-3 rounded-md shadow-md text-center">
            Login
          </a>
          <a href="/register" class="block w-full bg-[#FF4500] text-black font-bold py-3 rounded-md shadow-md text-center">
            Register
          </a>
        </div>
      </div>
    </div>
  </header>
</template>
