<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-900">Informasi Akun</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui nama dan alamat email akun Anda.</p>
        </div>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-5">

            <!-- Nama -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input v-model="form.name" type="text" required autofocus
                    placeholder="Nama lengkap Anda"
                    :class="['w-full px-4 py-3 rounded-xl border text-sm transition focus:outline-none focus:ring-2',
                        form.errors.name
                            ? 'border-red-400 focus:ring-red-200'
                            : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100']" />
                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1.5">{{ form.errors.name }}</p>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Alamat Email <span class="text-red-500">*</span>
                </label>
                <input v-model="form.email" type="email" required
                    placeholder="email@example.com"
                    :class="['w-full px-4 py-3 rounded-xl border text-sm transition focus:outline-none focus:ring-2',
                        form.errors.email
                            ? 'border-red-400 focus:ring-red-200'
                            : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100']" />
                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
            </div>

            <!-- Verifikasi email -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null"
                class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    Email belum diverifikasi.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="font-semibold underline ml-1 hover:text-amber-900 transition">
                        Kirim ulang email verifikasi
                    </Link>
                    <span v-if="status === 'verification-link-sent'"
                        class="block mt-1.5 text-emerald-600 font-semibold">
                        ✓ Link verifikasi telah dikirim ke email Anda.
                    </span>
                </div>
            </div>

            <!-- Tombol simpan -->
            <div class="flex items-center gap-4 pt-2">
                <button type="submit" :disabled="form.processing"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                           text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0"
                    enter-active-class="transition duration-200" leave-active-class="transition duration-200">
                    <div v-if="form.recentlySuccessful"
                        class="flex items-center gap-1.5 text-sm text-emerald-600 font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Perubahan disimpan!
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
