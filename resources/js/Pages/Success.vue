<script setup>
import { Head, Link } from '@inertiajs/vue3';
import NavbarUser from '../includes/NavbarUser.vue';
import Footer from '../includes/Footer.vue';
</script>

<template>
    <NavbarUser />
    <Head title="Pembayaran Berhasil" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 flex flex-col justify-center items-center px-4 py-20">
        
        <!-- Confetti-like decorative circles -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-16 h-16 bg-green-200 rounded-full opacity-40 animate-pulse"></div>
            <div class="absolute top-40 right-20 w-10 h-10 bg-blue-200 rounded-full opacity-40 animate-pulse delay-100"></div>
            <div class="absolute bottom-40 left-20 w-12 h-12 bg-yellow-200 rounded-full opacity-40 animate-pulse delay-200"></div>
            <div class="absolute bottom-20 right-10 w-20 h-20 bg-green-100 rounded-full opacity-40 animate-pulse delay-300"></div>
        </div>

        <div class="relative max-w-md w-full">
            <!-- Main Card -->
            <div class="bg-white rounded-3xl shadow-2xl border-2 border-gray-100 overflow-hidden">
                
                <!-- Top gradient banner -->
                <div class="bg-gradient-to-r from-green-400 to-green-600 p-8 text-center">
                    <!-- Animated checkmark -->
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-14 h-14 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-1">Pembayaran Berhasil!</h1>
                    <p class="text-green-100 text-sm">Pesanan Anda telah dikonfirmasi</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="text-center mb-6">
                        <p class="text-gray-600 leading-relaxed">
                            Terima kasih atas kepercayaan Anda! Pesanan Anda telah kami terima dan sedang diproses. Email konfirmasi telah dikirim ke alamat email Anda.
                        </p>
                    </div>

                    <!-- Info boxes -->
                    <div class="space-y-3 mb-8">
                        <div class="flex items-start gap-3 bg-green-50 rounded-xl p-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Email Konfirmasi Terkirim</p>
                                <p class="text-gray-500 text-xs mt-0.5">Cek inbox email Anda untuk detail pesanan</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 bg-blue-50 rounded-xl p-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Tim Kami Akan Menghubungi Anda</p>
                                <p class="text-gray-500 text-xs mt-0.5">Dalam 1×24 jam untuk konfirmasi lebih lanjut</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 bg-yellow-50 rounded-xl p-4">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Lihat Riwayat Pesanan</p>
                                <p class="text-gray-500 text-xs mt-0.5">Pantau status pesanan di halaman profil Anda</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <Link href="/profile?tab=pesanan"
                              class="w-full flex items-center justify-center gap-2 px-6 py-3 cursor-pointer rounded-md border-2 border-black bg-[#1266AC] text-white font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Lihat Riwayat Pesanan
                        </Link>
                        <Link :href="route('paket-wisata-public')"
                              class="w-full flex items-center justify-center gap-2 px-6 py-3 cursor-pointer rounded-md border-2 border-black bg-white text-black font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px] hover:translate-y-[4px] hover:shadow-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Kembali ke Paket Wisata
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Extra note -->
            <p class="text-center text-gray-400 text-xs mt-4">
                Butuh bantuan? Hubungi kami di
                <a href="https://wa.me/6281234567890" class="text-green-600 font-semibold hover:underline">WhatsApp</a>
            </p>
        </div>
    </div>
    <Footer />
</template>
