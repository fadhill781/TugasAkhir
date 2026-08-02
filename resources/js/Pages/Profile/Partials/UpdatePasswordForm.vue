<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showCurrent = ref(false);
const showNew     = ref(false);
const showConfirm = ref(false);

const passwordInput        = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900">Ubah Password</h2>
            <p class="text-sm text-gray-500 mt-1">Pastikan akun Anda menggunakan password yang kuat dan aman.</p>
        </div>

        <form @submit.prevent="updatePassword" class="space-y-5">

            <!-- Password saat ini -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Saat Ini</label>
                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        placeholder="Masukkan password saat ini"
                        :class="['w-full px-4 py-3 pr-12 rounded-xl border text-sm transition focus:outline-none focus:ring-2',
                            form.errors.current_password
                                ? 'border-red-400 focus:ring-red-200'
                                : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100']" />
                    <button type="button" @click="showCurrent = !showCurrent"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 transition">
                        <svg v-if="showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p v-if="form.errors.current_password" class="text-red-500 text-xs mt-1.5">{{ form.errors.current_password }}</p>
            </div>

            <!-- Password baru -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'"
                        ref="passwordInput"
                        v-model="form.password"
                        placeholder="Masukkan password baru"
                        :class="['w-full px-4 py-3 pr-12 rounded-xl border text-sm transition focus:outline-none focus:ring-2',
                            form.errors.password
                                ? 'border-red-400 focus:ring-red-200'
                                : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100']" />
                    <button type="button" @click="showNew = !showNew"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 transition">
                        <svg v-if="showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
            </div>

            <!-- Konfirmasi password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'"
                        v-model="form.password_confirmation"
                        placeholder="Ulangi password baru"
                        :class="['w-full px-4 py-3 pr-12 rounded-xl border text-sm transition focus:outline-none focus:ring-2',
                            form.errors.password_confirmation
                                ? 'border-red-400 focus:ring-red-200'
                                : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100']" />
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 transition">
                        <svg v-if="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1.5">{{ form.errors.password_confirmation }}</p>
            </div>

            <!-- Tombol -->
            <div class="flex items-center gap-4 pt-2">
                <button type="submit" :disabled="form.processing"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                           text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                    {{ form.processing ? 'Menyimpan...' : 'Ubah Password' }}
                </button>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0"
                    enter-active-class="transition duration-200" leave-active-class="transition duration-200">
                    <div v-if="form.recentlySuccessful"
                        class="flex items-center gap-1.5 text-sm text-emerald-600 font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Password berhasil diubah!
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
