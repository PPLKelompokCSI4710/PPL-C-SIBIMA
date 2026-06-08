<template>
    <div class="flex h-screen bg-slate-50 font-sans antialiased text-slate-800">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col z-20 overflow-y-auto">
            <div class="h-16 flex items-center px-6 border-b border-slate-200">
                <div class="flex items-center gap-2 text-indigo-600">
                    <GraduationCapIcon class="w-8 h-8" />
                    <span class="text-xl font-bold tracking-tight">SIBIMA STAFF</span>
                </div>
            </div>

            <div class="p-4">
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl mb-6">
                    <div
                        class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200"
                    >
                        {{ $page.props.auth.user?.name?.charAt(0) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-slate-800 truncate">
                            {{ $page.props.auth.user?.name }}
                        </p>
                        <p class="text-xs text-slate-500 capitalize">
                            {{ userRole }}
                        </p>
                    </div>
                </div>

                <nav class="space-y-1">
                    <Link
                        :href="route('staff.dashboard')"
                        :class="[
                            route().current('staff.dashboard')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <LayoutDashboardIcon class="w-5 h-5" />
                        <span>Dashboard</span>
                    </Link>

                    <!-- Admin Only: Course Management -->
                    <Link
                        v-if="isAdmin"
                        :href="route('staff.courses.index')"
                        :class="[
                            route().current('staff.courses.index')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <BookOpenIcon class="w-5 h-5" />
                        <span>Manajemen Kursus</span>
                    </Link>

                    <!-- Dosen & Admin: KRS Approval -->
                    <Link
                        :href="route('staff.study-plans.index')"
                        :class="[
                            route().current('staff.study-plans.index')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <ClipboardCheckIcon class="w-5 h-5" />
                        <span>Persetujuan KRS</span>
                    </Link>

                    <!-- Dosen & Admin: Student Progress -->
                    <Link
                        :href="route('staff.progress.index')"
                        :class="[
                            route().current('staff.progress.index')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <TrendingUpIcon class="w-5 h-5" />
                        <span>Progres Mahasiswa</span>
                    </Link>

                    <!-- Dosen & Admin: Kalender Akademik -->
                    <Link
                        :href="route('staff.calendar')"
                        :class="[
                            route().current('staff.calendar')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <CalendarDaysIcon class="w-5 h-5" />
                        <span>Kalender Akademik</span>
                    </Link>

                    <!-- Dosen: Ketersediaan Jadwal -->
                    <Link
                        v-if="!isAdmin"
                        :href="route('dosen.ketersediaan-jadwal.index')"
                        :class="[
                            route().current('dosen.ketersediaan-jadwal.*')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <CalendarDaysIcon class="w-5 h-5" />
                        <span>Ketersediaan Jadwal</span>
                    </Link>

                    <!-- Dosen: Monitoring Jadwal -->
                    <Link
                        v-if="!isAdmin"
                        :href="route('dosen.monitoring-jadwal.index')"
                        :class="[
                            route().current('dosen.monitoring-jadwal.*')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <ClipboardCheckIcon class="w-5 h-5" />
                        <span>Monitoring Jadwal</span>
                    </Link>

                    <!-- Dosen: Riwayat Bimbingan -->
                    <Link
                        v-if="!isAdmin"
                        :href="route('dosen.riwayat-bimbingan.index')"
                        :class="[
                            route().current('dosen.riwayat-bimbingan.*')
                                ? 'bg-indigo-50 text-indigo-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Riwayat Bimbingan</span>
                    </Link>
                </nav>
            </div>

            <div class="mt-auto p-4 border-t border-slate-200">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors font-medium"
                >
                    <LogOutIcon class="w-5 h-5" />
                    <span>Keluar</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header
                class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10 sticky top-0"
            >
                <h2 class="text-lg font-bold text-slate-800 capitalize">
                    {{ currentTitle }}
                </h2>
                <div class="flex items-center gap-4">
                    <!-- Notifications (Mock) -->
                    <div class="relative">
                        <button
                            class="relative p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"
                        >
                            <BellIcon class="w-5 h-5" />
                            <span
                                class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"
                            />
                        </button>
                    </div>

                    <div class="h-8 w-px bg-slate-200" />
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-800">
                            {{ $page.props.auth.user?.name }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $page.props.auth.user?.email }}
                        </p>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import { Link, usePage } from '@inertiajs/vue3';
    import {
        GraduationCapIcon,
        LayoutDashboardIcon,
        BookOpenIcon,
        ClipboardCheckIcon,
        TrendingUpIcon,
        LogOutIcon,
        BellIcon,
        CalendarDaysIcon,
    } from 'lucide-vue-next';

    const page = usePage();
    const userRole = computed(() => page.props.auth.user?.roles?.[0] || 'Staff');
    const isAdmin = computed(() => page.props.auth.user?.roles?.includes('admin') || false);

    const currentTitle = computed(() => {
        if (route().current('staff.dashboard')) return 'Dashboard';
        if (route().current('staff.courses.index')) return 'Manajemen Kursus';
        if (route().current('staff.study-plans.index')) return 'Persetujuan KRS';
        if (route().current('staff.progress.index')) return 'Progres Mahasiswa';
        if (route().current('staff.calendar')) return 'Kalender Akademik';
        if (route().current('dosen.ketersediaan-jadwal.*')) return 'Ketersediaan Jadwal';
        if (route().current('dosen.monitoring-jadwal.*')) return 'Monitoring Jadwal';
        return 'Staff Portal';
    });
</script>
