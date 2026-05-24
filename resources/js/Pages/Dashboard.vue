<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const props = defineProps({
        stats: {
            type: Object,
            default: null,
        },
    });

    const statCards = computed(() => {
        if (!props.stats) return [];
        return [
            {
                label: 'Total Jadwal',
                value: props.stats.total,
                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                bg: 'from-blue-500 to-blue-600',
                light: 'bg-blue-50 text-blue-600',
            },
            {
                label: 'Menunggu',
                value: props.stats.pending,
                icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                bg: 'from-amber-400 to-amber-500',
                light: 'bg-amber-50 text-amber-600',
            },
            {
                label: 'Disetujui',
                value: props.stats.approved,
                icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                bg: 'from-emerald-500 to-emerald-600',
                light: 'bg-emerald-50 text-emerald-600',
            },
            {
                label: 'Selesai',
                value: props.stats.completed,
                icon: 'M5 13l4 4L19 7',
                bg: 'from-indigo-500 to-indigo-600',
                light: 'bg-indigo-50 text-indigo-600',
            },
            {
                label: 'Ditolak',
                value: props.stats.rejected,
                icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                bg: 'from-rose-500 to-rose-600',
                light: 'bg-rose-50 text-rose-600',
            },
            {
                label: 'Dibatalkan',
                value: props.stats.canceled,
                icon: 'M6 18L18 6M6 6l12 12',
                bg: 'from-slate-400 to-slate-500',
                light: 'bg-slate-50 text-slate-500',
            },
        ];
    });
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-primary-dark">
                        Dashboard
                    </h2>
                    <p class="mt-1 text-sm font-medium text-neutral-medium">
                        Selamat datang kembali, {{ $page.props.auth.user.name }} 👋
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6 relative min-h-[calc(100vh-100px)] overflow-hidden bg-[#F5F7FA]">
            <!-- Ornamen Background -->
            <div
                class="absolute top-[-10%] left-[-5%] w-[500px] h-[500px] rounded-full bg-[#1F4C7A]/20 blur-[90px] pointer-events-none"
            />
            <div
                class="absolute bottom-[-10%] right-[-5%] w-[600px] h-[600px] rounded-full bg-[#6CCBC3]/30 blur-[90px] pointer-events-none"
            />
            <div
                class="absolute top-[30%] right-[15%] w-[300px] h-[300px] rounded-full bg-[#F39C12]/20 blur-[80px] pointer-events-none animate-pulse"
            />

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 relative z-10 space-y-8">
                <!-- Stats Cards (Dosen) -->
                <div v-if="stats">
                    <h3
                        class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4"
                    >
                        Ringkasan Aktivitas Bimbingan
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div
                            v-for="card in statCards"
                            :key="card.label"
                            class="bg-white/70 backdrop-blur-xl rounded-2xl shadow-sm border border-white/60 p-4 flex flex-col items-center text-center hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
                        >
                            <div
                                :class="[
                                    'w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center mb-3',
                                    card.bg,
                                ]"
                            >
                                <svg
                                    class="w-5 h-5 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        :d="card.icon"
                                    />
                                </svg>
                            </div>
                            <span class="text-3xl font-black text-slate-800">{{ card.value }}</span>
                            <span
                                class="text-[11px] font-semibold text-slate-400 mt-1 leading-tight"
                                >{{ card.label }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div v-if="$page.props.auth.user.role === 'dosen'">
                    <h3
                        class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4"
                    >
                        Akses Cepat
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Monitoring Jadwal -->
                        <Link
                            :href="route('dosen.jadwal.index')"
                            class="group bg-white/70 backdrop-blur-xl rounded-2xl shadow-sm border border-white/60 p-6 flex items-center gap-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-200"
                        >
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shrink-0 shadow-lg shadow-blue-200"
                            >
                                <svg
                                    class="w-7 h-7 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="font-extrabold text-slate-800 text-base group-hover:text-blue-600 transition-colors"
                                >
                                    Monitoring Jadwal
                                </p>
                                <p class="text-sm text-slate-400 mt-0.5">
                                    Kelola & pantau semua jadwal bimbingan
                                </p>
                                <div v-if="stats?.pending > 0" class="mt-2">
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"
                                        />
                                        {{ stats.pending }} menunggu persetujuan
                                    </span>
                                </div>
                            </div>
                            <svg
                                class="w-5 h-5 text-slate-300 group-hover:text-blue-400 group-hover:translate-x-1 transition-all"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </Link>

                        <!-- Ketersediaan Jadwal -->
                        <Link
                            :href="route('dosen.ketersediaan.index')"
                            class="group bg-white/70 backdrop-blur-xl rounded-2xl shadow-sm border border-white/60 p-6 flex items-center gap-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-200"
                        >
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shrink-0 shadow-lg shadow-teal-200"
                            >
                                <svg
                                    class="w-7 h-7 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="font-extrabold text-slate-800 text-base group-hover:text-teal-600 transition-colors"
                                >
                                    Ketersediaan Jadwal
                                </p>
                                <p class="text-sm text-slate-400 mt-0.5">
                                    Atur slot & kuota bimbingan Anda
                                </p>
                            </div>
                            <svg
                                class="w-5 h-5 text-slate-300 group-hover:text-teal-400 group-hover:translate-x-1 transition-all"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Fallback jika bukan dosen -->
                <div
                    v-if="!stats && $page.props.auth.user.role !== 'dosen'"
                    class="bg-white/70 backdrop-blur-xl rounded-2xl shadow-sm border border-white/60 p-10 text-center"
                >
                    <div
                        class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4"
                    >
                        <svg
                            class="w-8 h-8 text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-slate-700 text-lg">Selamat Datang di SIBIMA</h3>
                    <p class="text-slate-400 text-sm mt-1">Sistem Bimbingan Mahasiswa</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
