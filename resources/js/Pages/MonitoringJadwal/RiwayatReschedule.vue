<script setup>
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { Head, router } from '@inertiajs/vue3';
    import { 
        CalendarClockIcon, 
        ArrowRightIcon, 
        UserCircleIcon, 
        MessageSquareIcon,
        BookOpenIcon,
        ClockIcon,
        Trash2Icon
    } from 'lucide-vue-next';

    const props = defineProps({
        riwayat: {
            type: Array,
            default: () => [],
        },
    });

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        const dateObj = new Date(dateString);
        return dateObj.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
    };

    const formatTime = (timeString) => {
        if (!timeString) return '';
        return timeString.substring(0, 5) + ' WIB';
    };

    const statusConfig = (status) => {
        const configs = {
            pending: {
                label: 'MENUNGGU KONFIRMASI',
                classes: 'bg-amber-100 text-amber-700 ring-1 ring-amber-600/20',
                dot: 'bg-amber-500'
            },
            approved: {
                label: 'DISETUJUI',
                classes: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-600/20',
                dot: 'bg-emerald-500'
            },
            rejected: {
                label: 'DITOLAK',
                classes: 'bg-rose-100 text-rose-700 ring-1 ring-rose-600/20',
                dot: 'bg-rose-500'
            },
        };
        return configs[status] || {
            label: status.toUpperCase(),
            classes: 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/20',
            dot: 'bg-slate-500'
        };
    };

    const cancelReschedule = (id) => {
        if (confirm('Apakah Anda yakin ingin membatalkan pengajuan reschedule ini? Kuota dosen akan dikembalikan.')) {
            router.delete(route('mahasiswa.jadwal.cancel-reschedule', id), {
                preserveScroll: true,
                onSuccess: () => {
                    // Success is handled by flash messages
                },
            });
        }
    };
</script>

<template>
    <Head title="Riwayat Reschedule" />

    <StudentLayout>
        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 px-4 sm:px-0">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                            <CalendarClockIcon class="w-8 h-8 text-blue-600" stroke-width="2" />
                            Riwayat Reschedule
                        </h1>
                        <p class="mt-2 text-sm text-slate-500">Pantau status pengajuan pemindahan jadwal bimbingan Anda dengan mudah.</p>
                    </div>
                </div>

                <!-- Flash Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-xl bg-emerald-50 p-4 border border-emerald-200/60 shadow-sm mx-4 sm:mx-0">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">{{ $page.props.flash.success }}</p>
                        </div>
                    </div>
                </div>

                <!-- Content Layout -->
                <div class="space-y-6 px-4 sm:px-0">
                    
                    <!-- Empty State -->
                    <div v-if="riwayat.length === 0" class="flex flex-col items-center justify-center py-20 px-4 bg-white rounded-2xl shadow-sm border border-slate-200">
                        <div class="w-24 h-24 mb-6 rounded-full bg-slate-50 flex items-center justify-center">
                            <CalendarClockIcon class="w-12 h-12 text-slate-300" stroke-width="1.5" />
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">Belum Ada Riwayat</h3>
                        <p class="text-slate-500 text-center max-w-sm mt-2">Anda belum pernah mengajukan pemindahan jadwal bimbingan.</p>
                    </div>

                    <!-- History Cards -->
                    <div v-for="item in riwayat" :key="item.id" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        
                        <!-- Card Header -->
                        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 bg-blue-50 p-2 rounded-lg text-blue-600">
                                    <BookOpenIcon class="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 leading-tight">
                                        {{ item.jadwal_bimbingan?.judul_ta || 'Tanpa Judul TA' }}
                                    </h3>
                                    <p class="text-xs font-medium text-slate-500 mt-1 uppercase tracking-wider">Topik Bimbingan Akademik</p>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="self-start sm:self-center">
                                <span :class="[statusConfig(item.status).classes, 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold']">
                                    <span :class="[statusConfig(item.status).dot, 'w-1.5 h-1.5 rounded-full']"></span>
                                    {{ statusConfig(item.status).label }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body: Schedule Comparison -->
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row items-center gap-6">
                                
                                <!-- Old Schedule -->
                                <div class="w-full md:w-5/12 bg-slate-50/80 rounded-xl p-4 border border-slate-200/60 relative overflow-hidden group">
                                    <div class="absolute inset-0 bg-gradient-to-r from-slate-100/50 to-transparent pointer-events-none"></div>
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Jadwal Lama</h4>
                                    
                                    <div v-if="item.ketersediaan_jadwal_lama" class="space-y-2 opacity-70 group-hover:opacity-100 transition-opacity">
                                        <div class="flex items-center gap-2 text-slate-600">
                                            <CalendarClockIcon class="w-4 h-4 text-slate-400" />
                                            <span class="text-sm font-medium line-through decoration-slate-400">{{ formatDate(item.ketersediaan_jadwal_lama.tanggal) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-600">
                                            <ClockIcon class="w-4 h-4 text-slate-400" />
                                            <span class="text-sm font-medium line-through decoration-slate-400">{{ formatTime(item.ketersediaan_jadwal_lama.waktu_mulai) }}</span>
                                        </div>
                                    </div>
                                    <div v-else class="text-sm text-slate-400 italic py-2">
                                        Data jadwal lama tidak ditemukan.
                                    </div>
                                </div>

                                <!-- Transition Arrow -->
                                <div class="flex items-center justify-center w-full md:w-2/12 py-2 md:py-0">
                                    <div class="bg-blue-50 rounded-full p-2 text-blue-500 shadow-sm border border-blue-100 hidden md:block">
                                        <ArrowRightIcon class="w-6 h-6" />
                                    </div>
                                    <div class="bg-blue-50 rounded-full p-2 text-blue-500 shadow-sm border border-blue-100 md:hidden rotate-90">
                                        <ArrowRightIcon class="w-5 h-5" />
                                    </div>
                                </div>

                                <!-- New Schedule -->
                                <div class="w-full md:w-5/12 bg-blue-50/50 rounded-xl p-4 border border-blue-100/80 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-l from-blue-100/30 to-transparent pointer-events-none"></div>
                                    <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">Jadwal Baru (Diajukan)</h4>
                                    
                                    <div v-if="item.ketersediaan_jadwal_baru" class="space-y-2">
                                        <div class="flex items-center gap-2 text-slate-800">
                                            <CalendarClockIcon class="w-4 h-4 text-blue-500" />
                                            <span class="text-sm font-bold">{{ formatDate(item.ketersediaan_jadwal_baru.tanggal) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-800">
                                            <ClockIcon class="w-4 h-4 text-blue-500" />
                                            <span class="text-sm font-bold">{{ formatTime(item.ketersediaan_jadwal_baru.waktu_mulai) }}</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-slate-50 px-6 py-5 border-t border-slate-100 flex flex-col sm:flex-row justify-between gap-4">
                            <div class="flex flex-col gap-3.5 flex-1">
                                <!-- Dosen -->
                                <div class="flex items-center gap-3">
                                    <UserCircleIcon class="w-5 h-5 text-slate-500" />
                                    <span class="text-base text-slate-700 font-medium">
                                        Dosen Pembimbing: <span class="text-slate-900 font-semibold">{{ item.ketersediaan_jadwal_baru?.dosen?.nama_lengkap || 'N/A' }}</span>
                                    </span>
                                </div>
                                <!-- Alasan -->
                                <div class="flex items-start gap-3">
                                    <MessageSquareIcon class="w-5 h-5 text-slate-500 mt-0.5" />
                                    <span class="text-base text-slate-800">
                                        "{{ item.alasan || 'Tidak ada alasan yang diberikan' }}"
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Timestamp and Action -->
                            <div class="flex items-end sm:items-center justify-end gap-4 pt-3 sm:pt-0 border-t sm:border-0 border-slate-200 mt-2 sm:mt-0">
                                <span class="text-sm text-slate-600 font-medium">
                                    Diajukan pada: {{ formatDate(item.created_at) }}
                                </span>
                                
                                <button v-if="item.status === 'pending'" @click="cancelReschedule(item.id)" title="Batalkan Pengajuan" class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors border border-rose-200 shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-1 focus:ring-offset-slate-50">
                                    <Trash2Icon class="w-4 h-4" />
                                    <span>Batalkan</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
