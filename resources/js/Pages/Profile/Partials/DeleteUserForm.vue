<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput          = ref(null);
const showPassword           = ref(false);

const form = useForm({ password: '' });

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError:   () => passwordInput.value?.focus(),
        onFinish:  () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <div class="flex items-start gap-4 mb-6">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Hapus Akun</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Setelah akun dihapus, semua data Anda akan dihapus secara permanen dan tidak dapat dipulihkan.
                </p>
            </div>
        </div>

        <button @click="confirmUserDeletion" type="button"
            class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
            Hapus Akun Saya
        </button>

        <!-- Modal konfirmasi -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200"
                enter-from-class="opacity-0"
                leave-active-class="transition duration-150"
                leave-to-class="opacity-0">
                <div v-if="confirmingUserDeletion"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
                    @click.self="closeModal">
                    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-7">

                        <!-- Icon -->
                        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>

                        <h2 class="text-xl font-bold text-center text-gray-900 mb-2">Hapus Akun?</h2>
                        <p class="text-sm text-gray-500 text-center mb-6">
                            Aksi ini <span class="font-semibold text-red-500">tidak dapat dibatalkan</span>. Semua data Anda akan dihapus permanen. Masukkan password untuk konfirmasi.
                        </p>

                        <!-- Input Password -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'"
                                    ref="passwordInput"
                                    v-model="form.password"
                                    placeholder="Masukkan password Anda"
                                    :class="['w-full px-4 py-3 pr-12 rounded-xl border text-sm transition focus:outline-none focus:ring-2',
                                        form.errors.password
                                            ? 'border-red-400 focus:ring-red-200'
                                            : 'border-slate-300 focus:border-red-500 focus:ring-red-100']"
                                    @keyup.enter="deleteUser" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 transition">
                                    <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        <div class="flex gap-3">
                            <button @click="deleteUser" :disabled="form.processing"
                                class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed
                                       text-white font-semibold text-sm rounded-xl transition-colors">
                                {{ form.processing ? 'Menghapus...' : 'Ya, Hapus Akun' }}
                            </button>
                            <button @click="closeModal" type="button"
                                class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>
