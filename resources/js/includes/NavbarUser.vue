<script>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'     


export default {
   components: { Link },
  name: "NavbarUser",

  data() {
    return {
      isMenuOpen: false,
      isSearchOpen: false, 
      searchQuery: "", 
      isProfileOpen: false,
      menus: [
        { label: 'Home', href: '/homepage' },
        { label: 'Paket', href: '/paket-wisata-public' },
        { label: 'Mobil', href: '/rental-mobil' },
        { label: 'Galeri', href: '/galeri' },
      ],
    };
  },
  computed: {
    user() {
      return this.$page.props.auth.user
    }
  },

  methods: {
    toggleSearch() {
      this.isSearchOpen = !this.isSearchOpen;
    },
  }
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
          <li v-for="item in menus" :key="item.label">
            <Link
          :href="item.href"
          class="px-4 py-2 my-2 flex cursor-pointer items-center rounded-md
                 border-2 border-black bg-[#FFFFFF] font-bold text-black
                 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all
                 hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none"
        >
          {{ item.label }}
        </Link>
          </li>
        </ul>
      </nav>

  <!-- Search Bar  (Desktop) -->
<div class="hidden sm:flex gap-4 items-center ml-auto">

  <!-- SEARCH -->
  <input
    v-model="searchQuery"
    type="text"
    placeholder="Cari..."
    class="flex h-10 w-64 rounded-md border-2 border-black
           bg-white px-3 py-2 text-sm font-bold text-black
           placeholder-black focus:outline-none focus:ring-2
           focus:ring-black focus:ring-offset-2"
  />

  <!-- PROFIL -->
  <div class="relative">
    <button
      @click="isProfileOpen = !isProfileOpen"
      class="px-4 py-2 my-2 flex cursor-pointer items-center rounded-md
              border-2 border-black bg-[#FFFFFF] font-bold text-black
              shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px]
              hover:translate-y-[4px] hover:shadow-none"
    >
      {{ user.name }}
    </button>

    <!-- DROPDOWN -->
    <div
  v-if="isProfileOpen"
  class="absolute right-0 mt-2 w-40
         bg-white border-2 border-black
         rounded-md shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]
         z-50"
>

      <Link
        href="/profile"
        class="block px-4 py-2 font-semibold hover:bg-gray-300 text-black rounded-t-md"
      >
        Profile
      </Link>

      <Link
        href="/logout"
        method="post"
        as="button"
        class="block w-full text-left px-4 py-2
               font-semibold text-black hover:bg-gray-300 rounded-b-md"
      >
        Logout
      </Link>
    </div>
  </div>
</div>
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
          <li v-for="item in ['Home', 'Destinasi', 'Paket', 'Kontak']" :key="item">
            <a href="#" class="block px-4 py-3 bg-[#88AAEE] text-black font-bold rounded-md text-center">
              {{ item }}
            </a>
          </li>
        </ul>
        <!-- Tombol Login & Register di Sidebar -->
        <div class="mt-6 space-y-4">
          <!-- PROFIL (MOBILE) -->
<div class="mt-6 space-y-4">

  <div class="text-center font-bold text-white">
    {{ user.name }}
  </div>

  <Link
    href="/profile"
    class="block w-full bg-white text-black font-bold py-3 rounded-md text-center">
    Profil
  </Link>

  <Link
    href="/logout"
    method="post"
    as="button"
    class="block w-full bg-red-500 text-white font-bold py-3 rounded-md">
    Logout
  </Link>

</div>

        </div>
      </div>
    </div>
  </header>
</template>
