<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({
    jadwalBimbingans: {
        type: Array,
        default: () => [],
    },
});

const statusLabel = (status) => {
    const labels = {
        pending: 'MENUNGGU KONFIRMASI',
        approved: 'DISETUJUI',
        rejected: 'DITOLAK',
        completed: 'SELESAI',
        canceled: 'DIBATALKAN',
    };
    return labels[status] || status.toUpperCase();
};

const statusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        completed: 'bg-blue-100 text-blue-800',
        canceled: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const tipeLabel = (tipe) => {
    return tipe === 'online' ? 'ONLINE' : 'OFFLINE';
};

const tipeColor = (tipe) => {
    return tipe === 'online' ? 'text-blue-600' : 'text-gray-600';
};

const cancelJadwal = (id) => {
    if (confirm('Apakah Anda yakin ingin membatalkan jadwal ini?')) {
        router.patch(route('mahasiswa.jadwal.cancel', id));
    }
};
</script>

<template>
    <Head title="Monitoring Jadwal Bimbingan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Monitoring Jadwal Bimbingan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div
                    v-if="$page.props.flash?.success"
                    class="mb-4 rounded-md bg-green-50 p-4"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Header Section -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-indigo-600">Monitoring Jadwal Bimbingan</h1>
                        <p class="mt-1 text-sm text-gray-500">Pantau dan kelola jadwal bimbingan akademik Anda.</p>
                    </div>
                    <div class="flex items-center space-x-2 rounded-lg border border-gray-200 bg-white px-4 py-2 shadow-sm">
                        <svg class="h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Total: {{ jadwalBimbingans.length }} Jadwal</span>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Informasi Bimbingan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Dosen Pembimbing
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Mahasiswa
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Tindakan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="jadwal in jadwalBimbingans" :key="jadwal.id" class="hover:bg-gray-50 transition-colors">
                                    <!-- Informasi Bimbingan -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center text-xs text-gray-400 mb-1">
                                            <svg class="mr-1 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                            </svg>
                                            {{ jadwal.tanggal }} • {{ jadwal.waktu }} WIB
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ jadwal.topik_bimbingan }}
                                        </div>
                                        <span :class="tipeColor(jadwal.tipe)" class="mt-1 inline-block text-xs font-semibold uppercase">
                                            {{ tipeLabel(jadwal.tipe) }}
                                        </span>
                                    </td>

                                    <!-- Dosen Pembimbing -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500 text-sm font-bold text-white">
                                                D
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ jadwal.dosen?.nama_lengkap || 'N/A' }}
                                                </div>
                                                <div class="text-xs text-indigo-500">Dosen Pembimbing</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Mahasiswa -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                                                A
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ jadwal.mahasiswa?.nama_lengkap || 'N/A' }}
                                                </div>
                                                <div class="text-xs text-green-500">Mahasiswa</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            :class="statusColor(jadwal.status)"
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                        >
                                            {{ statusLabel(jadwal.status) }}
                                        </span>
                                    </td>

                                    <!-- Tindakan -->
                                    <td class="px-6 py-4 text-center">
                                        <template v-if="jadwal.status === 'pending'">
                                            <button
                                                @click="cancelJadwal(jadwal.id)"
                                                class="inline-flex items-center rounded-md border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-700 shadow-sm hover:bg-red-50 transition-colors"
                                            >
                                                Batalkan
                                            </button>
                                        </template>
                                        <template v-else>
                                            <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-400">
                                                Terkunci
                                            </span>
                                        </template>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="jadwalBimbingans.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Belum ada jadwal bimbingan.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
