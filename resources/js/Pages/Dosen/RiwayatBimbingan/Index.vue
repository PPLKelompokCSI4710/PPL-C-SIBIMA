<script setup>
import StaffLayout from '@/Layouts/StaffLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    riwayatBimbingans: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({ search: '' }) }
});

const search = ref(props.filters.search);

let timeout = null;
watch(search, () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('dosen.riwayat-bimbingan.index'), {
            search: search.value,
        }, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};
const formatTime = (timeStr) => timeStr ? timeStr.substring(0, 5) : '-';

// Modal Catatan Konsultasi Logic (Read Only)
const isModalOpen = ref(false);
const activeJadwal = ref(null);

const openCatatanModal = (jadwal) => {
    activeJadwal.value = jadwal;
    isModalOpen.value = true;
};

const closeCatatanModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        activeJadwal.value = null;
    }, 200);
};
</script>

<template>
    <Head title="Riwayat Bimbingan" />

    <StaffLayout>
        <div class="p-8 max-w-7xl mx-auto w-full">

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-900">Riwayat Bimbingan Mahasiswa</h1>
                <p class="mt-1.5 text-sm text-slate-500">
                    Daftar seluruh bimbingan yang telah selesai dilaksanakan beserta catatan konsultasinya.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
                <div class="w-full sm:w-96 relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        style="padding-left: 2.5rem;"
                        class="block w-full rounded-xl border-0 py-2.5 pr-3 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all"
                        placeholder="Cari nama mahasiswa, NIM, atau topik..."
                    >
                </div>
                <span class="text-sm text-slate-500">
                    Total: <strong class="text-slate-800">{{ riwayatBimbingans.length }}</strong> bimbingan selesai
                </span>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-4 px-6 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider w-80">
                                    Mahasiswa &amp; Topik
                                </th>
                                <th scope="col" class="py-4 px-6 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Jadwal &amp; Tipe
                                </th>
                                <th scope="col" class="py-4 px-6 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider w-32">
                                    Status
                                </th>
                                <th scope="col" class="py-4 px-6 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider w-44">
                                    Catatan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-if="riwayatBimbingans.length === 0">
                                <td colspan="4" class="px-6 py-14 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-base font-medium text-slate-900">Belum ada riwayat bimbingan</p>
                                        <p class="text-sm mt-1 text-slate-500">Data riwayat akan muncul ketika bimbingan telah diselesaikan.</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="jadwal in riwayatBimbingans" :key="jadwal.id" class="hover:bg-slate-50/50 transition-colors">
                                <!-- Mahasiswa & Topik -->
                                <td class="py-5 px-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                            {{ jadwal.mahasiswa?.nama_lengkap?.charAt(0)?.toUpperCase() || 'M' }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 truncate">{{ jadwal.mahasiswa?.nama_lengkap }}</div>
                                            <div class="text-sm text-slate-500 mt-0.5 truncate max-w-xs">{{ jadwal.topik_bimbingan }}</div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Jadwal & Tipe -->
                                <td class="py-5 px-6">
                                    <div class="text-sm font-medium text-slate-900">
                                        {{ formatDate(jadwal.ketersediaan_jadwal?.tanggal) }}
                                    </div>
                                    <div class="text-sm text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                        <span>{{ formatTime(jadwal.ketersediaan_jadwal?.waktu_mulai) }} - {{ formatTime(jadwal.ketersediaan_jadwal?.waktu_selesai) }}</span>
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset"
                                            :class="jadwal.tipe === 'online' ? 'bg-blue-50 text-blue-700 ring-blue-200' : 'bg-orange-50 text-orange-700 ring-orange-200'"
                                        >
                                            {{ jadwal.tipe === 'online' ? 'Online' : 'Offline' }}
                                        </span>
                                    </div>
                                </td>
                                <!-- Status -->
                                <td class="py-5 px-6">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        ✓ Selesai
                                    </span>
                                </td>
                                <!-- Tombol Catatan -->
                                <td class="py-5 px-6 text-right">
                                    <button
                                        @click="openCatatanModal(jadwal)"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold shadow-sm ring-1 ring-inset transition-colors"
                                        :class="jadwal.catatan_konsultasi
                                            ? 'bg-indigo-50 text-indigo-700 ring-indigo-200 hover:bg-indigo-100'
                                            : 'bg-white text-slate-500 ring-slate-200 hover:bg-slate-50'"
                                    >
                                        <svg v-if="jadwal.catatan_konsultasi" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ jadwal.catatan_konsultasi ? 'Lihat Catatan' : 'Belum Ada Catatan' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Catatan Konsultasi (View Only) -->
        <div v-if="isModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="closeCatatanModal"></div>
            
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title">
                                        Detail Catatan Bimbingan
                                    </h3>
                                    <div class="mt-2" v-if="activeJadwal">
                                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 mb-4">
                                            <p class="text-sm text-slate-600">
                                                Mahasiswa: <span class="font-semibold text-slate-900">{{ activeJadwal.mahasiswa?.nama_lengkap }}</span><br>
                                                Topik: <span class="font-semibold text-slate-900">{{ activeJadwal.topik_bimbingan }}</span><br>
                                                Waktu: <span class="text-slate-900">{{ formatDate(activeJadwal.ketersediaan_jadwal?.tanggal) }} ({{ formatTime(activeJadwal.ketersediaan_jadwal?.waktu_mulai) }} - {{ formatTime(activeJadwal.ketersediaan_jadwal?.waktu_selesai) }})</span>
                                            </p>
                                        </div>
                                        
                                        <div class="space-y-4 mt-4">
                                            <div>
                                                <label class="block text-sm font-medium leading-6 text-slate-900">Catatan Konsultasi</label>
                                                <div class="mt-2 bg-white p-3 rounded-xl border border-slate-200 text-sm text-slate-700 min-h-[100px] whitespace-pre-wrap">
                                                    {{ activeJadwal.catatan_konsultasi?.catatan || 'Belum ada catatan yang ditulis untuk bimbingan ini.' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" @click="closeCatatanModal" class="inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:w-auto focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </StaffLayout>
</template>
