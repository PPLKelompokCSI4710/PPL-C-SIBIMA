<script setup>
import StaffLayout from '@/Layouts/StaffLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    pendingRequests: { type: Array, default: () => [] },
    historyRequests: { type: Array, default: () => [] }
});

const activeTab = ref('pending');

const handleResponse = (requestId, responseType) => {
    const actionText = responseType === 'approved' ? 'menyetujui' : 'menolak';
    const confirmMsg = `Apakah Anda yakin ingin ${actionText} pengajuan reschedule ini?`;

    if (confirm(confirmMsg)) {
        router.put(route('dosen.reschedule.respond', requestId), {
            response: responseType
        }, {
            preserveScroll: true
        });
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

const formatTime = (timeStr) => timeStr ? timeStr.substring(0, 5) : '-';

const getStatusColor = (status) => {
    switch (status) {
        case 'approved': return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
        case 'rejected': return 'bg-red-50 text-red-700 ring-red-600/20';
        default: return 'bg-amber-50 text-amber-700 ring-amber-600/20';
    }
};

const formatStatus = (status) => {
    switch (status) {
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        default: return 'Menunggu Persetujuan';
    }
};
</script>

<template>
    <Head title="Reschedule Jadwal Bimbingan" />
    <StaffLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl">
                
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                            <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                        <p class="text-sm font-semibold text-emerald-800">{{ $page.props.flash.success }}</p>
                    </div>
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100">
                            <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                        </div>
                        <p class="text-sm font-semibold text-red-800">{{ $page.props.flash.error }}</p>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/25">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">Manajemen Reschedule Jadwal</h1>
                            <p class="text-sm text-slate-500">Kelola dan tinjau permintaan perubahan jadwal bimbingan mahasiswa</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs Container -->
                <div class="mb-6 border-b border-slate-200">
                    <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                        <button
                            @click="activeTab = 'pending'"
                            :class="[
                                activeTab === 'pending'
                                    ? 'border-indigo-600 text-indigo-600 font-bold'
                                    : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'
                            ]"
                            class="flex items-center gap-2 border-b-2 py-4 px-1 text-sm font-medium transition-all"
                        >
                            <span>Permohonan Baru</span>
                            <span 
                                v-if="pendingRequests.length > 0" 
                                class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-600"
                            >
                                {{ pendingRequests.length }}
                            </span>
                        </button>
                        <button
                            @click="activeTab = 'history'"
                            :class="[
                                activeTab === 'history'
                                    ? 'border-indigo-600 text-indigo-600 font-bold'
                                    : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'
                            ]"
                            class="border-b-2 py-4 px-1 text-sm font-medium transition-all"
                        >
                            Riwayat Reschedule
                        </button>
                    </nav>
                </div>

                <!-- Tab Panels -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <!-- TAB 1: PENDING REQUESTS -->
                    <div v-if="activeTab === 'pending'">
                        <div v-if="pendingRequests.length > 0" class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                    <tr>
                                        <th class="px-6 py-4">Mahasiswa</th>
                                        <th class="px-6 py-4">Topik</th>
                                        <th class="px-6 py-4 text-center">Perbandingan Jadwal (Lama &rarr; Baru)</th>
                                        <th class="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="request in pendingRequests" :key="request.id" class="hover:bg-slate-50/50 transition-colors">
                                        <!-- Mahasiswa info -->
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-slate-800">{{ request.jadwal_bimbingan?.mahasiswa?.nama_lengkap }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">NIM: {{ request.jadwal_bimbingan?.mahasiswa?.nim }}</p>
                                        </td>
                                        <!-- Topic & Alasan -->
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-slate-800">{{ request.jadwal_bimbingan?.topik_bimbingan }}</p>
                                            <p class="text-xs text-slate-500 mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                                <span class="font-bold text-slate-600">Alasan:</span> {{ request.alasan }}
                                            </p>
                                        </td>
                                        <!-- Schedule Comparison -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-6">
                                                <!-- Old Schedule -->
                                                <div class="text-right">
                                                    <span class="inline-block px-2 py-0.5 rounded bg-rose-50 text-[10px] font-bold text-rose-700 uppercase tracking-wider mb-1">Jadwal Lama</span>
                                                    <p class="text-xs font-semibold text-slate-600">
                                                        {{ formatDate(request.ketersediaan_jadwal_lama?.tanggal) }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-400">
                                                        {{ formatTime(request.ketersediaan_jadwal_lama?.waktu_mulai) }} - {{ formatTime(request.ketersediaan_jadwal_lama?.waktu_selesai) }} WIB
                                                    </p>
                                                </div>

                                                <!-- Arrow -->
                                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-slate-100 text-slate-500">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </div>

                                                <!-- New Schedule -->
                                                <div class="text-left">
                                                    <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1">Jadwal Baru</span>
                                                    <p class="text-xs font-semibold text-slate-800">
                                                        {{ formatDate(request.ketersediaan_jadwal_baru?.tanggal) }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-500">
                                                        {{ formatTime(request.ketersediaan_jadwal_baru?.waktu_mulai) }} - {{ formatTime(request.ketersediaan_jadwal_baru?.waktu_selesai) }} WIB
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    @click="handleResponse(request.id, 'approved')"
                                                    class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors"
                                                >
                                                    Setujui
                                                </button>
                                                <button
                                                    @click="handleResponse(request.id, 'rejected')"
                                                    class="inline-flex items-center rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-50 transition-colors"
                                                >
                                                    Tolak
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Empty Pending State -->
                        <div v-else class="p-12 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 mb-4">
                                <svg class="h-8 w-8 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700 mb-1">Semua Bersih!</h3>
                            <p class="text-sm text-slate-500">Tidak ada pengajuan reschedule baru yang perlu ditinjau.</p>
                        </div>
                    </div>

                    <!-- TAB 2: HISTORY REQUESTS -->
                    <div v-if="activeTab === 'history'">
                        <div v-if="historyRequests.length > 0" class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                    <tr>
                                        <th class="px-6 py-4">Mahasiswa</th>
                                        <th class="px-6 py-4">Topik</th>
                                        <th class="px-6 py-4 text-center">Perbandingan Jadwal (Lama &rarr; Baru)</th>
                                        <th class="px-6 py-4 text-center">Status Keputusan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="request in historyRequests" :key="request.id" class="hover:bg-slate-50/50 transition-colors">
                                        <!-- Mahasiswa info -->
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-slate-800">{{ request.jadwal_bimbingan?.mahasiswa?.nama_lengkap }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">NIM: {{ request.jadwal_bimbingan?.mahasiswa?.nim }}</p>
                                        </td>
                                        <!-- Topic & Alasan -->
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-slate-800">{{ request.jadwal_bimbingan?.topik_bimbingan }}</p>
                                            <p class="text-xs text-slate-500 mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                                <span class="font-bold text-slate-600">Alasan:</span> {{ request.alasan }}
                                            </p>
                                        </td>
                                        <!-- Schedule Comparison -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-6">
                                                <!-- Old Schedule -->
                                                <div class="text-right">
                                                    <span class="inline-block px-2 py-0.5 rounded bg-rose-50 text-[10px] font-bold text-rose-700 uppercase tracking-wider mb-1">Jadwal Lama</span>
                                                    <p class="text-xs font-semibold text-slate-600">
                                                        {{ formatDate(request.ketersediaan_jadwal_lama?.tanggal) }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-400">
                                                        {{ formatTime(request.ketersediaan_jadwal_lama?.waktu_mulai) }} - {{ formatTime(request.ketersediaan_jadwal_lama?.waktu_selesai) }} WIB
                                                    </p>
                                                </div>

                                                <!-- Arrow -->
                                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-slate-100 text-slate-500">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </div>

                                                <!-- New Schedule -->
                                                <div class="text-left">
                                                    <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1">Jadwal Baru</span>
                                                    <p class="text-xs font-semibold text-slate-800">
                                                        {{ formatDate(request.ketersediaan_jadwal_baru?.tanggal) }}
                                                    </p>
                                                    <p class="text-[11px] text-slate-500">
                                                        {{ formatTime(request.ketersediaan_jadwal_baru?.waktu_mulai) }} - {{ formatTime(request.ketersediaan_jadwal_baru?.waktu_selesai) }} WIB
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Decision Status -->
                                        <td class="px-6 py-4 text-center">
                                            <span 
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset" 
                                                :class="getStatusColor(request.status)"
                                            >
                                                {{ formatStatus(request.status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Empty History State -->
                        <div v-else class="p-12 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 mb-4">
                                <svg class="h-8 w-8 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Riwayat</h3>
                            <p class="text-sm text-slate-500">Belum ada permohonan reschedule bimbingan yang selesai diproses.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </StaffLayout>
</template>
