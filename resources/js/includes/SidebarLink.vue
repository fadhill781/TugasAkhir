<template>
  <a
    :href="href"
    class="flex items-center space-x-3 px-4 py-2 rounded-md transition-all duration-200 hover:bg-white"
    :class="[
      active ? 'bg-white text-dark' : 'text-dark',
      collapsed ? 'justify-center' : ''
    ]"
  >
    <i v-if="icon" :class="icon + ' text-2xl'"></i>
    <span 
      v-if="!collapsed"
      class="whitespace-nowrap"
    >
      {{ label }}
    </span>
  </a>
</template>

<script setup>
defineProps({
  href: String,
  label: String,
  icon: String,
  active: Boolean,
  collapsed: Boolean,
});
</script>
