<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { CalendarClockIcon, MapPinIcon, ClockIcon, UserIcon } from 'lucide-vue-next';

    const props = defineProps({
        jadwals: {
            type: Array,
            required: true,
        },
    });
</script>

<template>
    <Head title="Pilih Jadwal Reschedule" />

    <StudentLayout>
        <div class="py-6 bg-brand-bg relative min-h-screen overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-400/20 blur-3xl mix-blend-multiply" />
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-400/20 blur-3xl mix-blend-multiply" />

            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 relative z-10 mb-8">
                <div class="flex items-center gap-4 mb-2">
                    <div class="p-3 bg-white rounded-2xl shadow-sm border border-brand-primary/10">
                        <CalendarClockIcon class="w-8 h-8 text-brand-primary" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-brand-primary-dark tracking-tight">
                            Input Reschedule Bimbingan
                        </h1>
                        <p class="text-brand-text-secondary text-sm mt-1 font-medium">
                            Pilih jadwal bimbingan aktif Anda di bawah ini yang ingin Anda ajukan perubahan waktunya.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 relative z-10">
                <!-- Jika tidak ada jadwal sama sekali -->
                <div v-if="jadwals.length === 0" class="bg-white/80 backdrop-blur-xl shadow-xl rounded-3xl border border-white/60 p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                        <CalendarClockIcon class="w-12 h-12 text-blue-300" />
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Belum ada jadwal aktif</h3>
                    <p class="text-slate-500 max-w-md mx-auto mb-8">
                        Anda tidak memiliki jadwal bimbingan berstatus Approved yang dapat di-reschedule saat ini.
                    </p>
                    <Link :href="route('mahasiswa.jadwal-bimbingan.create')" class="px-6 py-3 bg-brand-primary hover:bg-brand-primary-dark text-white rounded-xl font-bold shadow-lg shadow-brand-primary/30 transition-all inline-flex items-center">
                        Buat Jadwal Baru
                    </Link>
                </div>

                <!-- Daftar Jadwal -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div 
                        v-for="jadwal in jadwals" 
                        :key="jadwal.id"
                        class="bg-white/80 backdrop-blur-xl shadow-xl hover:shadow-2xl hover:shadow-brand-primary/10 rounded-3xl border border-white/60 p-6 transition-all duration-300 flex flex-col justify-between"
                        :class="!jadwal.can_reschedule ? 'opacity-75 grayscale-[0.2]' : ''"
                    >
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span 
                                    class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-lg"
                                    :class="jadwal.status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                                >
                                    {{ jadwal.status }}
                                </span>
                                
                                <span v-if="!jadwal.can_reschedule" class="text-[10px] bg-red-50 text-red-600 px-2 py-1 rounded-md border border-red-100 font-bold">
                                    Melewati Batas H-1
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-800 line-clamp-2 mb-4">
                                {{ jadwal.judul_ta || 'Bimbingan Tugas Akhir' }}
                            </h3>

                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-sm text-slate-600">
                                    <UserIcon class="w-4 h-4 mr-3 text-slate-400" />
                                    <span class="font-medium">{{ jadwal.dosen?.nama_lengkap }}</span>
                                </div>
                                <div class="flex items-center text-sm text-slate-600">
                                    <CalendarClockIcon class="w-4 h-4 mr-3 text-slate-400" />
                                    <span class="font-medium" v-if="jadwal.ketersediaan_jadwal">
                                        {{ jadwal.ketersediaan_jadwal.tanggal }}
                                    </span>
                                    <span v-else class="text-slate-400 italic">Tanggal belum diatur</span>
                                </div>
                                <div class="flex items-center text-sm text-slate-600">
                                    <ClockIcon class="w-4 h-4 mr-3 text-slate-400" />
                                    <span class="font-medium" v-if="jadwal.ketersediaan_jadwal">
                                        {{ jadwal.ketersediaan_jadwal.waktu_mulai.substring(0,5) }} - {{ jadwal.ketersediaan_jadwal.waktu_selesai.substring(0,5) }} WIB
                                    </span>
                                </div>
                                <div class="flex items-center text-sm text-slate-600">
                                    <MapPinIcon class="w-4 h-4 mr-3 text-slate-400" />
                                    <span class="font-medium">
                                        {{ jadwal.tipe === 'offline' ? (jadwal.ketersediaan_jadwal?.ruangan || 'Ruang Dosen') : 'Online' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <Link 
                                v-if="jadwal.can_reschedule"
                                :href="route('mahasiswa.jadwal.edit-reschedule', jadwal.id)"
                                class="w-full px-4 py-3 bg-brand-white hover:bg-brand-primary text-brand-primary hover:text-white border-2 border-brand-primary/20 hover:border-brand-primary rounded-xl font-bold shadow-sm transition-all duration-300 flex items-center justify-center gap-2 group"
                            >
                                <CalendarClockIcon class="w-4 h-4 group-hover:animate-bounce" />
                                Pilih untuk Reschedule
                            </Link>
                            
                            <button 
                                v-else
                                disabled
                                class="w-full px-4 py-3 bg-gray-100 text-gray-400 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <CalendarClockIcon class="w-4 h-4" />
                                Tidak Dapat Di-reschedule
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
