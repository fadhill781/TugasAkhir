<template>
  <div 
    class="bg-blue-500 h-screen border-r shadow-md fixed z-50 transition-all duration-300 ease-in-out flex flex-col"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
  >
    <div class="flex justify-center items-center py-6 px-4 relative">
      <img 
        src="/img/logorill.png" 
        class="h-16 w-16 transition-all duration-300" 
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0 w-0'" 
        alt="Logo" 
      />
      <button 
        @click="toggleSidebar" 
        class="text-dark absolute top-6"
        :class="sidebarOpen ? 'right-4' : 'right-8'"
      >
        <i class="las la-bars text-2xl"></i>
      </button>
    </div>

    <nav class="px-4 space-y-2 font-semibold text-dark overflow-y-auto flex-1 mt-2 pb-2 scrollbar-hide"
         style="max-height: calc(100vh - 120px);"
    >
      <SidebarLink 
        href="/admin/dashboard" 
        label="Dashboard" 
        icon="las la-home"
        :active="$page.url === '/admin/dashboard'" 
        :collapsed="!sidebarOpen"
      />
      
      <!-- CMS Menu with Submenu -->
      <div>
        <button
          @click="toggleCmsMenu"
          class="w-full flex items-center justify-between gap-3 rounded-md text-dark overflow-hidden px-4 py-2"
        >
          <div class="flex items-center gap-3">
            <i class="las la-file-alt text-2xl"></i>
            <span 
              class="whitespace-nowrap transition-all duration-300"
              :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'"
            >
              CMS
            </span>
          </div>
          <i 
            v-if="sidebarOpen"
            class="las text-xl transition-transform duration-200"
            :class="cmsMenuOpen ? 'la-angle-down' : 'la-angle-right'"
          ></i>
        </button>
        <transition
          enter-active-class="transition-all duration-200 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 max-h-0"
          enter-to-class="opacity-100 max-h-40"
          leave-from-class="opacity-100 max-h-40"
          leave-to-class="opacity-0 max-h-0"
        >
          <ul class="ml-4 space-y-1 overflow-hidden" v-if="sidebarOpen && cmsMenuOpen">
            <li>
              <SidebarLink
                href="/cms-setting"
                label="Settings"
                :active="$page.url.startsWith('/cms-setting') || $page.url.startsWith('/CMS/Setting')"
                :collapsed="!sidebarOpen"
              />
            </li>
            <li>
              <SidebarLink
                href="/testimoni"
                label="Testimoni"
                :active="$page.url.startsWith('/testimoni')"
                :collapsed="!sidebarOpen"
              />
            </li>
          </ul>
        </transition>
      </div>
      
      <!-- Paket Wisata Menu with Submenu -->
      <div>
        <button
          @click="togglePaketWisataMenu"
          class="w-full flex items-center justify-between gap-3 rounded-md text-dark overflow-hidden px-4 py-2"
        >
          <div class="flex items-center gap-3">
            <i class="las la-umbrella-beach text-2xl"></i>
            <span 
              class="whitespace-nowrap transition-all duration-300"
              :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'"
            >
              Paket Wisata
            </span>
          </div>
          <i 
            v-if="sidebarOpen"
            class="las text-xl transition-transform duration-200"
            :class="paketWisataMenuOpen ? 'la-angle-down' : 'la-angle-right'"
          ></i>
        </button>
        <transition
          enter-active-class="transition-all duration-200 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 max-h-0"
          enter-to-class="opacity-100 max-h-40"
          leave-from-class="opacity-100 max-h-40"
          leave-to-class="opacity-0 max-h-0"
        >
          <ul class="ml-4 space-y-1 overflow-hidden" v-if="sidebarOpen && paketWisataMenuOpen">
            <li>
              <SidebarLink
                href="/paket-wisata"
                label="Daftar Paket"
                :active="$page.url.startsWith('/paket-wisata')"
                :collapsed="!sidebarOpen"
              />
            </li>
          </ul>
        </transition>
      </div>
      
      <SidebarLink 
        href="/mobil" 
        label="Mobil" 
        icon="las la-car"
        :active="$page.url === '/mobil'" 
        :collapsed="!sidebarOpen"
      />
      <SidebarLink 
        href="/pesanan" 
        label="Pesanan" 
        icon="las la-clipboard-list"
        :active="$page.url === '/pesanan'" 
        :collapsed="!sidebarOpen"
      />

      <!-- SuperAdmin Menu: hanya muncul untuk superadmin -->
      <div v-if="$page.props.auth?.user?.role === 'superadmin'">
        <button
          @click="toggleUserMenu"
          class="w-full flex items-center justify-between gap-3 rounded-md text-dark overflow-hidden px-4 py-2"
        >
          <div class="flex items-center gap-3">
            <i class="las la-users-cog text-2xl"></i>
            <span 
              class="whitespace-nowrap transition-all duration-300"
              :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'"
            >
              Superadmin
            </span>
          </div>
          <i 
            v-if="sidebarOpen"
            class="las text-xl transition-transform duration-200"
            :class="userMenuOpen ? 'la-angle-down' : 'la-angle-right'"
          ></i>
        </button>
        <transition
          enter-active-class="transition-all duration-200 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 max-h-0"
          enter-to-class="opacity-100 max-h-40"
          leave-from-class="opacity-100 max-h-40"
          leave-to-class="opacity-0 max-h-0"
        >
          <ul class="ml-4 space-y-1 overflow-hidden" v-if="sidebarOpen && userMenuOpen">
            <li>
              <SidebarLink
                href="/superadmin/users"
                label="User Management"
                :active="$page.url.startsWith('/superadmin/users')"
                :collapsed="!sidebarOpen"
              />
            </li>
          </ul>
        </transition>
      </div>

      <div>
        <button
          @click="toggleTransaksiMenu"
          class="w-full flex items-center justify-between gap-3 rounded-md text-dark overflow-hidden px-4 py-2"
          :class="$page.url.startsWith('/transaksi') ? 'bg-white' : ''"
        >
          <div class="flex items-center gap-3">
            <i class="las la-exchange-alt text-2xl"></i>
            <span 
              class="whitespace-nowrap transition-all duration-300"
              :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'"
            >
              Transaksi
            </span>
          </div>
          <i 
            v-if="sidebarOpen"
            class="las text-xl transition-transform duration-200"
            :class="transaksiMenuOpen ? 'la-angle-down' : 'la-angle-right'"
          ></i>
        </button>
        <transition
          enter-active-class="transition-all duration-200 ease-out"
          leave-active-class="transition-all duration-200 ease-in"
          enter-from-class="opacity-0 max-h-0"
          enter-to-class="opacity-100 max-h-40"
          leave-from-class="opacity-100 max-h-40"
          leave-to-class="opacity-0 max-h-0"
        >
          <ul class="ml-4 space-y-1 overflow-hidden" v-if="sidebarOpen && transaksiMenuOpen">
            <li>
              <SidebarLink
                href="/admin/transaksi-keuangan"
                label="Riwayat Transaksi"
                :active="$page.url === '/admin/transaksi-keuangan'"
                :collapsed="!sidebarOpen"
              />
            </li>
            <!-- <li>
              <SidebarLink
                href="/admin/transaksi-baru"
                label="Transaksi Baru"
                :active="$page.url === '/admin/transaksi-baru'"
                :collapsed="!sidebarOpen"
              />
            </li> -->
          </ul>
        </transition>
      </div>
    </nav>
    <!-- Logout -->
<div class="mt-auto px-4 pb-6">
  <div class="border-t border-blue-400 my-4"></div>

  <button
    class="w-full flex items-center gap-3 rounded-md 
           text-dark overflow-hidden transition px-4 py-2 space-y-2 font-semibold text-bold"
    @click="logout"
  >
    <span 
      class="whitespace-nowrap transition-all duration-300 "
      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'"
    >
      Logout
    </span>
  </button>
</div>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import SidebarLink from "./SidebarLink.vue";
import { router } from '@inertiajs/vue3';

const props = defineProps({
  sidebarOpen: Boolean
});

const emit = defineEmits(['toggle']);

// State untuk dropdown menu
const cmsMenuOpen = ref(true);
const paketWisataMenuOpen = ref(true);
const transaksiMenuOpen = ref(true);
const userMenuOpen = ref(true);

const toggleSidebar = () => {
  emit('toggle'); 
};

const toggleCmsMenu = () => {
  cmsMenuOpen.value = !cmsMenuOpen.value;
};

const togglePaketWisataMenu = () => {
  paketWisataMenuOpen.value = !paketWisataMenuOpen.value;
};

const toggleTransaksiMenu = () => {
  transaksiMenuOpen.value = !transaksiMenuOpen.value;
};

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value;
};

const logout = () => {
  router.post('/logout');
};

</script>