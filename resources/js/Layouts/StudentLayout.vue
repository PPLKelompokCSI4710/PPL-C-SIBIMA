<template>
    <div class="flex h-screen bg-slate-50 font-sans antialiased text-slate-800 overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="w-64 bg-white border-r border-slate-200 flex flex-col hidden md:flex z-30 shrink-0"
        >
            <div class="h-16 flex items-center px-6 border-b border-slate-200">
                <div class="flex items-center gap-2 text-blue-600">
                    <GraduationCapIcon class="w-8 h-8" />
                    <span class="text-xl font-bold tracking-tight">SIBIMA</span>
                </div>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <Link
                    :href="route('mahasiswa.dashboard')"
                    :class="[
                        route().current('mahasiswa.dashboard')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <LayoutDashboardIcon class="w-5 h-5" />
                    <span>Dashboard</span>
                </Link>
                <Link
                    :href="route('mahasiswa.courses.index')"
                    :class="[
                        route().current('mahasiswa.courses.index')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <BookOpenIcon class="w-5 h-5" />
                    <span>Courses</span>
                </Link>
                <Link
                    :href="route('mahasiswa.study-plans.index')"
                    :class="[
                        route().current('mahasiswa.study-plans.index')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <CalendarDaysIcon class="w-5 h-5" />
                    <span>Study Plans (KRS)</span>
                </Link>
                <Link
                    :href="route('mahasiswa.progress.index')"
                    :class="[
                        route().current('mahasiswa.progress.index')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <TrendingUpIcon class="w-5 h-5" />
                    <span>Progress Studi</span>
                </Link>
                <Link
                    :href="route('mahasiswa.jadwal.index')"
                    :class="[
                        route().current('mahasiswa.jadwal.index')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <UsersIcon class="w-5 h-5" />
                    <span>Jadwal Bimbingan</span>
                </Link>
                <Link
                    :href="route('mahasiswa.jadwal.reschedule-list')"
                    :class="[
                        route().current('mahasiswa.jadwal.reschedule-list') || route().current('mahasiswa.jadwal.edit-reschedule')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <CalendarClockIcon class="w-5 h-5" />
                    <span>Reschedule Bimbingan</span>
                </Link>
                <Link
                    :href="route('mahasiswa.calendar')"
                    :class="[
                        route().current('mahasiswa.calendar')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <CalendarDaysIcon class="w-5 h-5" />
                    <span>Kalender Akademik</span>
                </Link>
                <Link
                    :href="route('mahasiswa.bimbingan.reminder')"
                    :class="[
                        route().current('mahasiswa.bimbingan.reminder')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <BellIcon class="w-5 h-5" />
                    <span>Reminder Jadwal</span>
                </Link>
                <Link
                    :href="route('mahasiswa.bimbingan.progress_reminder')"
                    :class="[
                        route().current('mahasiswa.bimbingan.progress_reminder')
                            ? 'bg-blue-50 text-blue-700 font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <TrendingUpIcon class="w-5 h-5" />
                    <span>Reminder Progres</span>
                </Link>
            </nav>
            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border border-blue-200 text-xs shrink-0"
                    >
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-slate-500">Student</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full overflow-hidden relative">
            <!-- Header -->
            <header
                class="h-16 bg-white/90 backdrop-blur-sm border-b border-slate-200 flex items-center justify-between px-8 z-20 sticky top-0 shrink-0"
            >
                <div>
                    <h1 class="text-lg font-bold text-slate-800">
                        {{ pageTitle }}
                    </h1>
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">
                        {{ pageSubtitle }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button
                            class="relative p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                            :class="{ 'text-blue-600 bg-blue-50': showNotifications }"
                            @click="showNotifications = !showNotifications"
                        >
                            <BellIcon class="w-5 h-5" />
                            <span
                                class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"
                            />
                        </button>

                        <!-- Notification Dropdown -->
                        <Transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 scale-95 translate-y-2"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 translate-y-2"
                        >
                            <div
                                v-if="showNotifications"
                                class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden"
                            >
                                <div
                                    class="p-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center"
                                >
                                    <h3 class="font-bold text-slate-800 text-sm">Notifikasi</h3>
                                    <button
                                        class="text-xs text-blue-600 font-bold hover:underline"
                                        @click="showNotifications = false"
                                    >
                                        Tandai dibaca
                                    </button>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <div
                                        v-for="(notif, index) in mockNotifications"
                                        :key="index"
                                        class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors cursor-pointer group"
                                    >
                                        <div class="flex gap-3">
                                            <div
                                                :class="notif.color"
                                                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                            >
                                                <component :is="notif.icon" class="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors"
                                                >
                                                    {{ notif.title }}
                                                </p>
                                                <p
                                                    class="text-xs text-slate-500 mt-0.5 line-clamp-2"
                                                >
                                                    {{ notif.desc }}
                                                </p>
                                                <p
                                                    class="text-[10px] text-slate-400 mt-2 font-medium"
                                                >
                                                    {{ notif.time }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 text-center border-t border-slate-50">
                                    <button
                                        class="text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors"
                                    >
                                        Lihat Semua Aktivitas
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <div class="w-px h-8 bg-slate-200" />

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                        title="Keluar"
                    >
                        <LogOutIcon class="w-5 h-5" />
                    </Link>
                </div>
            </header>

            <!-- Scrollable Page Content -->
            <main class="flex-1 overflow-y-auto p-8 relative">
                <slot />
            </main>
        </div>

        <!-- Overlay for mobile/notifications -->
        <div
            v-if="showNotifications"
            class="fixed inset-0 z-40"
            @click="showNotifications = false"
        />
        <!-- SIBIMA AI Assistant Floating Widget -->
        <AiAssistantWidget />
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import AiAssistantWidget from '@/Components/AiAssistantWidget.vue';
    import {
        GraduationCapIcon,
        LayoutDashboardIcon,
        BookOpenIcon,
        CalendarDaysIcon,
        TrendingUpIcon,
        BellIcon,
        LogOutIcon,
        CheckCircle2Icon,
        AlertCircleIcon,
        InfoIcon,
        UsersIcon,
        CalendarClockIcon,
    } from 'lucide-vue-next';

    const showNotifications = ref(false);

    const pageTitle = computed(() => {
        if (route().current('mahasiswa.dashboard')) return 'Dashboard';
        if (route().current('mahasiswa.courses.index')) return 'Courses';
        if (route().current('mahasiswa.study-plans.index')) return 'Study Plans (KRS)';
        if (route().current('mahasiswa.progress.index')) return 'Progress Studi';
        if (route().current('mahasiswa.calendar')) return 'Academic Calendar';
        if (
            route().current('mahasiswa.jadwal.*') ||
            route().current('mahasiswa.jadwal-bimbingan.*')
        ) {
            if (route().current('mahasiswa.jadwal.reschedule-list') || route().current('mahasiswa.jadwal.edit-reschedule')) {
                return 'Reschedule Bimbingan';
            }
            return 'Jadwal Bimbingan';
        }
        if (route().current('mahasiswa.bimbingan.reminder')) return 'Reminder Jadwal Bimbingan';
        if (route().current('mahasiswa.bimbingan.progress_reminder'))
            return 'Monitoring Progres Akademik';
        return 'SIBIMA';
    });

    const pageSubtitle = computed(() => {
        if (route().current('mahasiswa.dashboard')) return 'Academic Overview';
        if (route().current('mahasiswa.courses.index')) return 'Available Catalog';
        if (route().current('mahasiswa.study-plans.index')) return 'KRS Management';
        if (route().current('mahasiswa.progress.index')) return 'Achievement Tracking';
        if (route().current('mahasiswa.calendar')) return 'Event Schedule';
        if (
            route().current('mahasiswa.jadwal.*') ||
            route().current('mahasiswa.jadwal-bimbingan.*')
        ) {
            if (route().current('mahasiswa.jadwal.reschedule-list') || route().current('mahasiswa.jadwal.edit-reschedule')) {
                return 'Pengajuan Reschedule Bimbingan';
            }
            return 'Bimbingan Akademik';
        }
        if (route().current('mahasiswa.bimbingan.reminder')) return 'Informasi Reminder Jadwal';
        if (route().current('mahasiswa.bimbingan.progress_reminder'))
            return 'Frekuensi Notifikasi Progres';
        return 'Portal Mahasiswa';
    });

    const mockNotifications = [
        {
            title: 'KRS Disetujui',
            desc: 'Mata kuliah "Algoritma & Struktur Data" telah disetujui oleh Dosen Pembimbing.',
            time: '5 menit yang lalu',
            icon: CheckCircle2Icon,
            color: 'bg-emerald-100 text-emerald-600',
        },
        {
            title: 'Peringatan SKS',
            desc: 'Anda belum mencukupi batas minimum 18 SKS untuk semester depan.',
            time: '2 jam yang lalu',
            icon: AlertCircleIcon,
            color: 'bg-amber-100 text-amber-600',
        },
        {
            title: 'Update Kurikulum',
            desc: 'Ada perubahan pada mata kuliah prasyarat untuk Tugas Akhir. Silakan cek katalog.',
            time: 'Kemarin',
            icon: InfoIcon,
            color: 'bg-blue-100 text-blue-600',
        },
    ];
</script>
