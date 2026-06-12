<template>
    <div class="flex h-screen bg-slate-50 font-sans antialiased text-slate-800 overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="w-64 bg-white border-r border-slate-200 flex flex-col hidden md:flex z-30 shrink-0"
        >
            <div class="h-16 flex items-center px-6 border-b border-slate-200">
                <Link href="/" class="flex items-center">
                    <img src="/images/logo-sibima.svg" alt="SIBIMA Logo" class="h-8 w-auto" />
                </Link>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <Link
                    :href="route('mahasiswa.dashboard')"
                    :class="[
                        route().current('mahasiswa.dashboard')
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
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
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
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
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
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
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <TrendingUpIcon class="w-5 h-5" />
                    <span>Progress Studi</span>
                </Link>
                <Link
                    :href="route('mahasiswa.draft-skripsi.index')"
                    :class="[
                        route().current('mahasiswa.draft-skripsi.*')
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <FileTextIcon class="w-5 h-5" />
                    <span>Draft Skripsi</span>
                </Link>
                <Link
                    :href="route('mahasiswa.jadwal.index')"
                    :class="[
                        route().current('mahasiswa.jadwal.*') ||
                            route().current('mahasiswa.jadwal-bimbingan.*')
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <UsersIcon class="w-5 h-5" />
                    <span>Jadwal Bimbingan</span>
                </Link>
                <Link
                    :href="route('mahasiswa.jadwal.riwayat-reschedule')"
                    :class="[
                        route().current('mahasiswa.jadwal.riwayat-reschedule')
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <HistoryIcon class="w-5 h-5" />
                    <span>Riwayat Reschedule</span>
                </Link>
                <Link
                    :href="route('mahasiswa.calendar')"
                    :class="[
                        route().current('mahasiswa.calendar')
                            ? 'bg-brand-primary/5 text-brand-primary font-semibold'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors"
                >
                    <CalendarDaysIcon class="w-5 h-5" />
                    <span>Kalender Akademik</span>
                </Link>
            </nav>
            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl">
                    <div
                        class="w-10 h-10 rounded-full bg-brand-primary/10 flex items-center justify-center text-brand-primary font-bold border border-brand-primary/20 text-xs shrink-0"
                    >
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <div class="flex items-center justify-between mt-0.5">
                            <span class="text-xs text-slate-500">Student</span>
                            <Link
                                :href="route('profile.edit')"
                                class="text-xs text-brand-primary hover:text-brand-primary-dark font-bold hover:underline"
                            >
                                Edit Profile
                            </Link>
                        </div>
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
                    <!-- Notification Bell -->
                    <NotificationBell />

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


        <!-- SIBIMA AI Assistant Floating Widget -->
        <AiAssistantWidget />
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import AiAssistantWidget from '@/Components/AiAssistantWidget.vue';
    import NotificationBell from '@/Components/NotificationBell.vue';
    import {
        LayoutDashboardIcon,
        BookOpenIcon,
        CalendarDaysIcon,
        TrendingUpIcon,
        LogOutIcon,
        UsersIcon,
        HistoryIcon,
        FileTextIcon,
    } from 'lucide-vue-next';

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
            if (route().current('mahasiswa.jadwal.riwayat-reschedule')) {
                return 'Riwayat Reschedule';
            }
            return 'Jadwal Bimbingan';
        }
        if (route().current('mahasiswa.bimbingan.reminder')) return 'Reminder Jadwal Bimbingan';
        if (route().current('mahasiswa.bimbingan.progress_reminder'))
            return 'Monitoring Progres Akademik';
        if (route().current('mahasiswa.draft-skripsi.*')) return 'Manajemen Draft Skripsi';
        if (route().current('profile.edit')) return 'Profile Settings';
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
            if (route().current('mahasiswa.jadwal.riwayat-reschedule')) {
                return 'Status Pemindahan Jadwal';
            }
            return 'Bimbingan Akademik';
        }
        if (route().current('mahasiswa.bimbingan.reminder')) return 'Informasi Reminder Jadwal';
        if (route().current('mahasiswa.bimbingan.progress_reminder'))
            return 'Frekuensi Notifikasi Progres';
        if (route().current('mahasiswa.draft-skripsi.*')) return 'Upload & Catatan Draft';
        if (route().current('profile.edit')) return 'Update Your Account Credentials';
        return 'Portal Mahasiswa';
    });
</script>
