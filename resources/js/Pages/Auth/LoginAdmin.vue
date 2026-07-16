<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const showPassword = ref(false)
const errorMessage = ref('')

const form = useForm({
  email: '',
  password: ''
})

function submit() {
  errorMessage.value = ''
  form.post('/login-admin', {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Login errors:', errors)
      if (errors.email) {
        errorMessage.value = errors.email
      } else {
        errorMessage.value = 'Login gagal. Silakan coba lagi.'
      }
    }
  })
}
</script>


<template>
<div class="min-h-screen flex items-center justify-center bg-blue-500">
<div class="bg-white border-[6px] border-black shadow-[10px_10px_0_#000] p-8 w-full max-w-md">
<h1 class="text-3xl text-center font-extrabold mb-6 uppercase">Login</h1>


<form @submit.prevent="submit" class="space-y-4">
<!-- Error Message -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 border-2 border-red-500 text-red-700 rounded">
          {{ errorMessage }}
        </div>

<!-- Email -->
        <label class="block font-bold">Email</label>
        <div class="relative mb-4">
          <img
            src="https://img.icons8.com/ios-filled/24/000000/new-post.png"
            class="absolute left-4 top-1/2 -translate-y-1/2"
            alt="email"
          />
          <input
            v-model="form.email"
            type="email"
            placeholder="Email"
            class="w-full border-4 border-black p-3 pl-12 focus:outline-none"
          />
        </div>

        <!-- Password -->
<label class="block font-bold">Password</label>
<div class="relative mb-4">
  <!-- Icon Lock -->
  <img
    src="https://img.icons8.com/ios-filled/24/000000/lock--v1.png"
    class="absolute left-4 top-1/2 -translate-y-1/2"
    alt="password"
  />

  <!-- Input -->
  <input
    :type="showPassword ? 'text' : 'password'"
    v-model="form.password"
    placeholder="Password"
    class="w-full border-4 border-black p-3 pl-12 pr-12 focus:outline-none"
  />

  <!-- Toggle Eye -->
  <button
    type="button"
    @click="showPassword = !showPassword"
    class="absolute right-4 top-1/2 -translate-y-1/2"
  >
    <img
      :src="
        showPassword
          ? 'https://img.icons8.com/?size=100&id=59814&format=png&color=000000'
          : 'https://img.icons8.com/?size=100&id=85130&format=png&color=000000'
      "
      class="w-5 h-5"
      alt="toggle password"
    />
  </button>
</div>


<button
type="submit"
class="w-full bg-yellow-400 border-4 border-black py-3 font-bold shadow-[6px_6px_0_#000]
hover:translate-x-1 hover:translate-y-1 hover:shadow-none"
>
LOGIN
</button>
</form>


<p class="text-center text-sm mt-6">
        Belum punya akun?
        <a href="/register" class="font-bold underline">Daftar</a>
      </p>
</div>
</div>
</template>