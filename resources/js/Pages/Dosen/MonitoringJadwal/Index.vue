<script setup>
import StaffLayout from '@/Layouts/StaffLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    jadwalBimbingans: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({ status: 'all', search: '' }) }
});

const search = ref(props.filters.search);
const statusFilter = ref(props.filters.status);

// Debounce search function if needed, or simple watch
let timeout = null;
watch([search, statusFilter], () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('dosen.monitoring-jadwal.index'), {
            search: search.value,
            status: statusFilter.value
        }, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});

const updateStatus = (id, newStatus) => {
    let confirmMsg = '';
    if (newStatus === 'approved') confirmMsg = 'Yakin ingin menyetujui pengajuan bimbingan ini? (Kuota jadwal akan berkurang)';
    else if (newStatus === 'rejected') confirmMsg = 'Yakin ingin menolak pengajuan bimbingan ini?';
    else if (newStatus === 'completed') confirmMsg = 'Tandai bimbingan ini sebagai selesai?';
        
    if (confirm(confirmMsg)) {
        router.put(route('dosen.monitoring-jadwal.update-status', id), {
            status: newStatus
        }, {
            preserveScroll: true
        });
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case 'approved': return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
        case 'rejected': return 'bg-red-50 text-red-700 ring-red-600/20';
        case 'canceled': return 'bg-slate-100 text-slate-700 ring-slate-600/20';
        default: return 'bg-amber-50 text-amber-700 ring-amber-600/20'; // pending
    }
};

const formatStatus = (status) => {
    switch (status) {
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        case 'canceled': return 'Dibatalkan Mahasiswa';
        case 'pending': return 'Menunggu Konfirmasi';
        default: return status;
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};
const formatTime = (timeStr) => timeStr ? timeStr.substring(0, 5) : '-';

// Modal Catatan Konsultasi Logic
const isModalOpen = ref(false);
const activeJadwal = ref(null);

const formCatatan = useForm({
    catatan: ''
});

const openCatatanModal = (jadwal) => {
    activeJadwal.value = jadwal;
    if (jadwal.catatan_konsultasi) {
        formCatatan.defaults({
            catatan: jadwal.catatan_konsultasi.catatan
        });
    } else {
        formCatatan.defaults({
            catatan: ''
        });
    }
    formCatatan.reset();
    formCatatan.clearErrors();
    isModalOpen.value = true;
};

const closeCatatanModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        activeJadwal.value = null;
        formCatatan.defaults({ catatan: '' });
        formCatatan.reset();
        formCatatan.clearErrors();
    }, 200);
};

const submitCatatan = () => {
    if (activeJadwal.value.catatan_konsultasi) {
        formCatatan.put(route('dosen.monitoring-jadwal.update-catatan', activeJadwal.value.catatan_konsultasi.id), {
            onSuccess: () => closeCatatanModal(),
            preserveScroll: true,
            preserveState: true
        });
    } else {
        formCatatan.post(route('dosen.monitoring-jadwal.store-catatan', activeJadwal.value.id), {
            onSuccess: () => closeCatatanModal(),
            preserveScroll: true,
            preserveState: true
        });
    }
};
</script>

<template>
    <Head title="Monitoring Jadwal Bimbingan" />
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

                <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/25">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">Monitoring Pengajuan Bimbingan</h1>
                            <p class="text-sm text-slate-500">Kelola daftar pengajuan bimbingan dari mahasiswa Anda</p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select 
                            v-model="statusFilter"
                            class="block rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                        >
                            <option value="all">Semua Status</option>
                            <option value="pending">Menunggu Konfirmasi</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                            <option value="canceled">Dibatalkan</option>
                        </select>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                v-model="search"
                                placeholder="Cari mahasiswa/topik..." 
                                class="block w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                style="padding-left: 2.5rem;"
                            />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div v-if="jadwalBimbingans.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <tr>
                                    <th class="px-6 py-4">Mahasiswa</th>
                                    <th class="px-6 py-4">Topik / Judul TA</th>
                                    <th class="px-6 py-4">Jadwal & Tipe</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="jadwal in jadwalBimbingans" :key="jadwal.id" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-800">{{ jadwal.mahasiswa?.nama_lengkap }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">NIM: {{ jadwal.mahasiswa?.nim }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-800">{{ jadwal.topik_bimbingan }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate max-w-[250px]" :title="jadwal.judul_ta">
                                            TA: {{ jadwal.judul_ta }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div v-if="jadwal.ketersediaan_jadwal">
                                            <p class="font-medium text-slate-800">{{ formatDate(jadwal.ketersediaan_jadwal.tanggal) }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                {{ formatTime(jadwal.ketersediaan_jadwal.waktu_mulai) }} - {{ formatTime(jadwal.ketersediaan_jadwal.waktu_selesai) }} WIB
                                            </p>
                                        </div>
                                        <p v-else class="text-slate-500 text-sm">Jadwal dihapus</p>
                                        <span class="mt-2 inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset uppercase"
                                              :class="jadwal.tipe === 'online' ? 'bg-blue-50 text-blue-700 ring-blue-600/20' : 'bg-slate-100 text-slate-700 ring-slate-600/20'">
                                            {{ jadwal.tipe }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset" :class="getStatusColor(jadwal.status)">
                                            {{ formatStatus(jadwal.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div v-if="jadwal.status === 'pending'" class="flex items-center justify-end gap-2">
                                            <button 
                                                @click="updateStatus(jadwal.id, 'approved')"
                                                class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                                            >
                                                Acc
                                            </button>
                                            <button 
                                                @click="updateStatus(jadwal.id, 'rejected')"
                                                class="inline-flex items-center rounded-lg bg-white px-3 py-1.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-50 transition-colors"
                                            >
                                                Tolak
                                            </button>
                                        </div>
                                        <div v-else-if="jadwal.status === 'approved'" class="flex items-center justify-end gap-2">
                                            <button 
                                                @click="updateStatus(jadwal.id, 'completed')"
                                                class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors"
                                            >
                                                Selesaikan Bimbingan
                                            </button>
                                        </div>
                                        <div v-else-if="jadwal.status === 'completed'" class="flex items-center justify-end gap-2">
                                            <button 
                                                @click="openCatatanModal(jadwal)"
                                                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                                            >
                                                {{ jadwal.catatan_konsultasi ? 'Lihat/Edit Catatan' : '+ Catatan' }}
                                            </button>
                                        </div>
                                        <div v-else class="text-sm text-slate-400">
                                            -
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-else class="p-12 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 mb-4">
                            <svg class="h-8 w-8 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Pengajuan</h3>
                        <p class="text-sm text-slate-500">Tidak ada pengajuan bimbingan dari mahasiswa yang sesuai dengan filter.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Catatan Konsultasi -->
        <div v-if="isModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="closeCatatanModal"></div>
            
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form @submit.prevent="submitCatatan">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title">
                                            Catatan Konsultasi
                                        </h3>
                                        <div class="mt-2" v-if="activeJadwal">
                                            <p class="text-sm text-slate-500 mb-4">
                                                Mahasiswa: <span class="font-semibold text-slate-700">{{ activeJadwal.mahasiswa?.nama_lengkap }}</span><br>
                                                Topik: <span class="font-semibold text-slate-700">{{ activeJadwal.topik_bimbingan }}</span>
                                            </p>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium leading-6 text-slate-900">Catatan Konsultasi</label>
                                                    <div class="mt-2">
                                                        <textarea v-model="formCatatan.catatan" rows="4" class="block w-full rounded-xl border-slate-300 py-1.5 text-slate-900 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm sm:leading-6" placeholder="Tuliskan hasil diskusi atau catatan konsultasi di sini..."></textarea>
                                                        <p v-if="formCatatan.errors.catatan" class="mt-1 text-sm text-red-600">{{ formCatatan.errors.catatan }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" :disabled="formCatatan.processing" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50">
                                    Simpan Catatan
                                </button>
                                <button type="button" @click="closeCatatanModal" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </StaffLayout>
</template>
