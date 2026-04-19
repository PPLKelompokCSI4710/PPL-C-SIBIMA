<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        jadwalBimbingans: {
            type: Array,
            default: () => [],
        },
    });

    const statusConfig = (status) => {
        switch (status) {
            case 'pending':
                return {
                    color: 'text-amber-700',
                    bg: 'bg-amber-100',
                    border: 'border-amber-200',
                    label: 'Menunggu Konfirmasi',
                };
            case 'approved':
                return {
                    color: 'text-emerald-700',
                    bg: 'bg-emerald-100',
                    border: 'border-emerald-200',
                    label: 'Disetujui',
                };
            case 'completed':
                return {
                    color: 'text-blue-700',
                    bg: 'bg-blue-100',
                    border: 'border-blue-200',
                    label: 'Selesai',
                };
            case 'canceled':
                return {
                    color: 'text-slate-600',
                    bg: 'bg-slate-100',
                    border: 'border-slate-200',
                    label: 'Dibatalkan',
                };
            case 'rejected':
                return {
                    color: 'text-rose-700',
                    bg: 'bg-rose-100',
                    border: 'border-rose-200',
                    label: 'Ditolak',
                };
            default:
                return {
                    color: 'text-slate-600',
                    bg: 'bg-slate-100',
                    border: 'border-slate-200',
                    label: status,
                };
        }
    };
</script>

<template>
    <Head title="Monitoring Jadwal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <!-- Font Gradient Super Keren -->
                    <h2
                        class="text-3xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600"
                    >
                        Monitoring Jadwal Bimbingan
                    </h2>
                    <p class="mt-2 text-sm font-medium text-indigo-900/60">
                        Pantau dan kelola jadwal bimbingan akademik dengan mudah.
                    </p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 text-blue-700 shadow-sm"
                    >
                        <svg
                            class="w-5 h-5 text-blue-500"
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
                        <span class="font-bold text-sm"
                            >Total: {{ jadwalBimbingans.length }} Jadwal</span
                        >
                    </div>
                </div>
            </div>
        </template>

        <!-- BACKGROUND MODERN DENGAN BLOBS GLOWING -->
        <div class="py-4 relative min-h-[calc(100vh-100px)] overflow-hidden bg-slate-50">
            <!-- Decorative Glowing Blobs -->
            <div
                class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-gradient-to-br from-blue-400/40 to-indigo-500/40 blur-[100px] pointer-events-none"
            />
            <div
                class="absolute bottom-[-10%] right-[-5%] w-[35%] h-[35%] rounded-full bg-gradient-to-tr from-rose-400/40 to-pink-500/40 blur-[100px] pointer-events-none"
            />
            <div
                class="absolute top-[20%] right-[15%] w-[25%] h-[25%] rounded-full bg-gradient-to-bl from-amber-300/30 to-orange-400/30 blur-[80px] pointer-events-none"
            />

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 relative z-10">
                <!-- Notifikasi -->
                <transition name="slide-fade">
                    <div
                        v-if="$page.props.flash?.success"
                        class="mb-6 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4 text-white shadow-lg shadow-emerald-500/20 flex items-center justify-between border border-emerald-400"
                    >
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 p-2 rounded-full">
                                <svg
                                    class="w-6 h-6 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                            </div>
                            <p class="font-bold text-lg tracking-wide">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </transition>

                <!-- Tabel Solid Putih Elegan dengan Glassmorphism -->
                <div
                    class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-white/60 overflow-hidden relative"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100/50">
                            <thead class="bg-slate-50/40">
                                <tr>
                                    <th
                                        scope="col"
                                        class="py-3 pl-4 pr-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest"
                                    >
                                        Informasi Bimbingan
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest"
                                    >
                                        Dosen Pembimbing
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-widest"
                                    >
                                        Mahasiswa
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-widest"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="relative py-3 pl-3 pr-4 text-right text-xs font-bold text-slate-500 uppercase tracking-widest"
                                    >
                                        Tindakan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100/50 bg-transparent">
                                <tr
                                    v-for="jadwal in jadwalBimbingans"
                                    :key="jadwal.id"
                                    class="hover:bg-blue-50/30 transition-all duration-200 group"
                                >
                                    <!-- Kolom Topik & Waktu -->
                                    <td class="whitespace-normal py-3 pl-4 pr-3">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-xs font-bold text-indigo-600 bg-indigo-50 w-max px-2 py-0.5 rounded-md flex items-center gap-1"
                                            >
                                                <svg
                                                    class="w-3.5 h-3.5"
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
                                                {{ jadwal.tanggal }} &bull; {{ jadwal.waktu }} WIB
                                            </span>
                                            <div
                                                class="font-extrabold text-slate-800 text-sm leading-snug group-hover:text-blue-600 transition-colors max-w-sm"
                                            >
                                                {{ jadwal.topik_bimbingan }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider"
                                                >
                                                    {{ jadwal.tipe }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kolom Dosen -->
                                    <td class="whitespace-nowrap px-3 py-3">
                                        <div class="flex items-center gap-2">
                                            <!-- AVATAR GRADIENT KEREN -->
                                            <div
                                                class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-md shadow-blue-500/20"
                                            >
                                                {{
                                                    jadwal.dosen
                                                        ? jadwal.dosen.nama_lengkap.charAt(0)
                                                        : 'D'
                                                }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-slate-800 font-bold text-xs">{{
                                                    jadwal.dosen
                                                        ? jadwal.dosen.nama_lengkap
                                                        : 'Dosen ID: ' + jadwal.dosen_id
                                                }}</span>
                                                <span
                                                    class="text-indigo-500 text-[10px] font-semibold"
                                                    >Dosen Pembimbing</span
                                                >
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kolom Mahasiswa -->
                                    <td class="whitespace-nowrap px-3 py-3">
                                        <div class="flex items-center gap-2">
                                            <!-- AVATAR GRADIENT KEREN -->
                                            <div
                                                class="h-8 w-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center font-bold text-xs shadow-md shadow-emerald-500/20"
                                            >
                                                {{
                                                    jadwal.mahasiswa
                                                        ? jadwal.mahasiswa.nama_lengkap.charAt(0)
                                                        : 'M'
                                                }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-slate-800 font-bold text-xs">{{
                                                    jadwal.mahasiswa
                                                        ? jadwal.mahasiswa.nama_lengkap
                                                        : 'Mhs ID: ' + jadwal.mahasiswa_id
                                                }}</span>
                                                <span
                                                    class="text-teal-600 text-[10px] font-semibold"
                                                    >Mahasiswa</span
                                                >
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kolom Status -->
                                    <td class="whitespace-nowrap px-3 py-3 text-center">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-widest border',
                                                statusConfig(jadwal.status).bg,
                                                statusConfig(jadwal.status).color,
                                                statusConfig(jadwal.status).border,
                                            ]"
                                        >
                                            {{ statusConfig(jadwal.status).label }}
                                        </span>
                                    </td>

                                    <!-- Kolom Aksi -->
                                    <td class="whitespace-nowrap py-3 pl-3 pr-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <!-- Tampilan Aksi (Dosen & Mahasiswa sama-sama hanya melihat untuk PBI 1) -->
                                            <span
                                                class="inline-flex items-center text-indigo-500 font-bold text-[10px] bg-indigo-50 px-3 py-1.5 rounded-md border border-indigo-100"
                                            >
                                                Detail (Segera Hadir)
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="jadwalBimbingans.length === 0">
                                    <td colspan="5" class="px-6 py-24 text-center">
                                        <div
                                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-blue-50"
                                        >
                                            <svg
                                                class="h-10 w-10 text-blue-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                                />
                                            </svg>
                                        </div>
                                        <h3 class="mt-5 text-lg font-extrabold text-slate-800">
                                            Ruang Kosong
                                        </h3>
                                        <p class="mt-2 text-sm text-slate-500 font-medium">
                                            Belum ada aktivitas bimbingan apa pun saat ini.
                                        </p>
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

<style scoped>
    .slide-fade-enter-active {
        transition: all 0.4s ease-out;
    }
    .slide-fade-leave-active {
        transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
    }
    .slide-fade-enter-from,
    .slide-fade-leave-to {
        transform: translateY(-20px) scale(0.98);
        opacity: 0;
    }
</style>
