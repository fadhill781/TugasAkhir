<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import sidebar from '@/includes/Sidebar.vue';

const props = defineProps({
    users: Array,
});

const sidebarOpen = ref(true);
const searchQuery = ref('');
const filterRole = ref('all');
const showCreateModal = ref(false);
const showPasswordModal = ref(false);
const selectedUser = ref(null);

// Create form
const createForm = useForm({
    name: '',
    email: '',
    password: '',
    role: 'admin',
});

// Password reset form
const passwordForm = useForm({
    password: '',
    password_confirmation: '',
});

const filteredUsers = computed(() => {
    return props.users.filter(u => {
        const matchSearch = !searchQuery.value ||
            u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            u.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchRole = filterRole.value === 'all' || u.role === filterRole.value;
        return matchSearch && matchRole;
    });
});

const userCounts = computed(() => ({
    total: props.users.length,
    superadmin: props.users.filter(u => u.role === 'superadmin').length,
    admin: props.users.filter(u => u.role === 'admin').length,
    user: props.users.filter(u => u.role === 'user').length,
}));

function getRoleBadge(role) {
    switch(role) {
        case 'superadmin': return 'bg-purple-100 text-purple-800 border border-purple-300';
        case 'admin': return 'bg-blue-100 text-blue-800 border border-blue-300';
        case 'user': return 'bg-gray-100 text-gray-700 border border-gray-300';
        default: return 'bg-gray-100 text-gray-700';
    }
}

function submitCreate() {
    createForm.post(route('superadmin.users.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        }
    });
}

function updateRole(user, newRole) {
    if (!confirm(`Ubah role ${user.name} menjadi ${newRole}?`)) return;
    router.put(route('superadmin.users.role', user.id), { role: newRole }, {
        preserveScroll: true,
    });
}

function openPasswordModal(user) {
    selectedUser.value = user;
    passwordForm.reset();
    showPasswordModal.value = true;
}

function submitPassword() {
    passwordForm.put(route('superadmin.users.password', selectedUser.value.id), {
        onSuccess: () => {
            showPasswordModal.value = false;
            passwordForm.reset();
        }
    });
}

function toggleActive(user) {
    const action = user.is_active ? 'menonaktifkan' : 'mengaktifkan';
    if (!confirm(`${action} akun ${user.name}?`)) return;
    router.put(route('superadmin.users.toggle', user.id), {}, {
        preserveScroll: true,
    });
}

function viewProfile(user) {
    router.get(route('superadmin.users.show', user.id));
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100 overflow-hidden">
        <sidebar :sidebarOpen="sidebarOpen" @toggle="sidebarOpen = !sidebarOpen" />

        <div :class="['transition-all duration-300 ease-in-out overflow-y-auto flex-1', sidebarOpen ? 'pl-64' : 'pl-16']">
            <div class="container mx-auto px-4 py-6">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                        <p class="text-gray-500 text-sm mt-1">Kelola akun admin dan user sistem</p>
                    </div>
                    <button @click="showCreateModal = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Akun
                    </button>
                </div>

                <!-- Flash -->
                <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-gray-400">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Total User</p>
                        <p class="text-3xl font-bold text-gray-800">{{ userCounts.total }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-500">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Super Admin</p>
                        <p class="text-3xl font-bold text-purple-700">{{ userCounts.superadmin }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Admin</p>
                        <p class="text-3xl font-bold text-blue-700">{{ userCounts.admin }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                        <p class="text-xs text-gray-500 font-semibold uppercase">User</p>
                        <p class="text-3xl font-bold text-green-700">{{ userCounts.user }}</p>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div class="flex flex-wrap gap-3 mb-4">
                    <input v-model="searchQuery" type="text" placeholder="Cari nama atau email..."
                           class="border border-gray-300 rounded-lg px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <div class="flex gap-2">
                        <button v-for="f in [{v:'all',l:'Semua'},{v:'superadmin',l:'Super Admin'},{v:'admin',l:'Admin'},{v:'user',l:'User'}]"
                                :key="f.v" @click="filterRole = f.v"
                                :class="['px-4 py-2 rounded-lg border-2 text-sm font-bold transition', filterRole === f.v ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400']">
                            {{ f.l }}
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-blue-600 text-white text-sm">
                                <th class="px-4 py-3 text-left rounded-tl-xl">No</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-center">Role</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Bergabung</th>
                                <th class="px-4 py-3 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="7" class="text-center py-10 text-gray-400">
                                    <div class="text-4xl mb-2">👥</div>
                                    Tidak ada data user
                                </td>
                            </tr>
                            <tr v-for="(user, index) in filteredUsers" :key="user.id"
                                class="border-t border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-center text-gray-500 text-sm">{{ index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                             :class="user.role === 'superadmin' ? 'bg-purple-500' : user.role === 'admin' ? 'bg-blue-500' : 'bg-gray-400'">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <span class="font-semibold text-gray-800">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-sm">{{ user.email }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', getRoleBadge(user.role)]">
                                        {{ user.role_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="['px-3 py-1 rounded-full text-xs font-bold', user.is_active ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300']">
                                        {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-500">{{ user.created_at }}</td>
                                <td class="px-4 py-3 text-center">
                                    <!-- Superadmin tidak bisa diubah -->
                                    <div v-if="user.role === 'superadmin'" class="text-xs text-gray-400 italic">
                                        Terlindungi
                                    </div>
                                    <div v-else class="flex items-center justify-center gap-2 flex-wrap">
                                        <!-- View Profile -->
                                        <button @click="viewProfile(user)"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-semibold transition">
                                            Profil
                                        </button>
                                        <!-- Change Role -->
                                        <select @change="updateRole(user, $event.target.value)"
                                                :value="user.role"
                                                class="text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                        </select>
                                        <!-- Reset Password -->
                                        <button @click="openPasswordModal(user)"
                                                class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded text-xs font-semibold transition">
                                            Password
                                        </button>
                                        <!-- Toggle Active/Inactive -->
                                        <button @click="toggleActive(user)"
                                                :class="['px-2 py-1 rounded text-xs font-semibold transition', user.is_active ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white']">
                                            {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal: Tambah Akun -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Tambah Akun Baru</h2>
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input v-model="createForm.name" type="text" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                           placeholder="Nama lengkap" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input v-model="createForm.email" type="email" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                           placeholder="email@example.com" />
                    <p v-if="createForm.errors.email" class="text-red-500 text-xs mt-1">{{ createForm.errors.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input v-model="createForm.password" type="password" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                           placeholder="Min. 8 karakter" />
                    <p v-if="createForm.errors.password" class="text-red-500 text-xs mt-1">{{ createForm.errors.password }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select v-model="createForm.role"
                            class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="createForm.processing"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition disabled:opacity-50">
                        <span v-if="createForm.processing">Menyimpan...</span>
                        <span v-else>Buat Akun</span>
                    </button>
                    <button type="button" @click="showCreateModal = false"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Reset Password -->
    <div v-if="showPasswordModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Reset Password</h2>
            <p class="text-gray-500 text-sm mb-6">untuk <strong>{{ selectedUser?.name }}</strong></p>
            <form @submit.prevent="submitPassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <input v-model="passwordForm.password" type="password" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                           placeholder="Min. 8 karakter" />
                    <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input v-model="passwordForm.password_confirmation" type="password" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                           placeholder="Ulangi password" />
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="passwordForm.processing"
                            class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-lg transition disabled:opacity-50">
                        <span v-if="passwordForm.processing">Menyimpan...</span>
                        <span v-else>Reset Password</span>
                    </button>
                    <button type="button" @click="showPasswordModal = false"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
