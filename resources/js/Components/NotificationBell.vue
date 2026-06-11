<template>
    <div class="relative" ref="bellContainer">
        <button
            id="notification-bell-toggle"
            class="relative p-2 rounded-lg transition-all duration-200"
            :class="[
                isOpen
                    ? 'text-blue-600 bg-blue-50'
                    : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50',
            ]"
            @click="toggleDropdown"
            aria-label="Notifikasi"
        >
            <BellIcon class="w-5 h-5" />
            <!-- Unread badge -->
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="scale-0 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-0 opacity-0"
            >
                <span
                    v-if="unreadCount > 0"
                    class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full border-2 border-white shadow-sm"
                >
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
            </Transition>
        </button>

        <!-- Dropdown Panel -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-2"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 mt-3 w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden"
                id="notification-dropdown"
            >
                <!-- Header -->
                <div
                    class="p-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50/30 flex justify-between items-center"
                >
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-800 text-sm">Notifikasi</h3>
                        <span
                            v-if="unreadCount > 0"
                            class="text-[10px] font-bold text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded-full"
                        >
                            {{ unreadCount }} baru
                        </span>
                    </div>
                    <button
                        v-if="unreadCount > 0"
                        class="text-xs text-blue-600 font-bold hover:underline transition-colors"
                        @click.stop="markAllAsRead"
                    >
                        Tandai semua dibaca
                    </button>
                </div>

                <!-- Notification List -->
                <div class="max-h-[400px] overflow-y-auto">
                    <!-- Loading state -->
                    <div v-if="loading && notifications.length === 0" class="p-8 text-center">
                        <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                        <p class="text-xs text-slate-400">Memuat notifikasi...</p>
                    </div>

                    <!-- Empty state -->
                    <div
                        v-else-if="notifications.length === 0"
                        class="p-8 text-center"
                    >
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <BellOffIcon class="w-6 h-6 text-slate-300" />
                        </div>
                        <p class="text-sm font-semibold text-slate-500">Belum ada notifikasi</p>
                        <p class="text-xs text-slate-400 mt-1">Notifikasi baru akan muncul di sini</p>
                    </div>

                    <!-- Notifications -->
                    <div v-else>
                        <div
                            v-for="notif in notifications"
                            :key="notif.id"
                            class="p-4 border-b border-slate-50 hover:bg-slate-50/80 transition-colors cursor-pointer group"
                            :class="{ 'bg-blue-50/30': !notif.read_at }"
                            @click="handleClick(notif)"
                        >
                            <div class="flex gap-3">
                                <!-- Icon -->
                                <div
                                    :class="getNotifStyle(notif.type).bgColor"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                >
                                    <component
                                        :is="getNotifStyle(notif.type).icon"
                                        class="w-5 h-5"
                                        :class="getNotifStyle(notif.type).iconColor"
                                    />
                                </div>
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <p
                                            class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors truncate"
                                        >
                                            {{ notif.title }}
                                        </p>
                                        <span
                                            v-if="!notif.read_at"
                                            class="w-2 h-2 bg-blue-500 rounded-full shrink-0 mt-1.5"
                                        ></span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">
                                        {{ notif.message }}
                                    </p>

                                    <!-- Detail chips for schedule reminder -->
                                    <div
                                        v-if="notif.detail && notif.type === 'bimbingan_schedule_reminder'"
                                        class="flex flex-wrap gap-1 mt-2"
                                    >
                                        <span
                                            v-if="notif.detail.waktu_mulai"
                                            class="text-[10px] font-medium bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded-md"
                                        >
                                            🕐 {{ formatDateTime(notif.detail.waktu_mulai) }}
                                        </span>
                                        <span
                                            v-if="notif.detail.lokasi"
                                            class="text-[10px] font-medium bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded-md"
                                        >
                                            📍 {{ notif.detail.lokasi }}
                                        </span>
                                    </div>

                                    <!-- Detail for progress reminder -->
                                    <div
                                        v-if="notif.type === 'academic_progress_reminder' || notif.type === 'academic_progress_reminder_cc'"
                                        class="flex flex-wrap gap-1 mt-2"
                                    >
                                        <span
                                            v-if="notif.progress_summary?.ipk || notif.detail?.ipk"
                                            class="text-[10px] font-medium bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded-md"
                                        >
                                            📊 IPK {{ notif.progress_summary?.ipk || notif.detail?.ipk }}
                                        </span>
                                        <span
                                            v-if="notif.days_since_last_bimbingan || notif.detail?.days_since_last_bimbingan"
                                            class="text-[10px] font-medium bg-red-50 text-red-700 px-1.5 py-0.5 rounded-md"
                                        >
                                            ⏱️ {{ notif.days_since_last_bimbingan || notif.detail?.days_since_last_bimbingan }} hari
                                        </span>
                                    </div>

                                    <p class="text-[10px] text-slate-400 mt-2 font-medium">
                                        {{ notif.created_at }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div v-if="notifications.length > 0" class="p-3 text-center border-t border-slate-100 bg-slate-50/30">
                    <p class="text-[10px] text-slate-400">
                        Menampilkan {{ notifications.length }} notifikasi terakhir
                    </p>
                </div>
            </div>
        </Transition>
    </div>

    <!-- Click-outside overlay -->
    <div
        v-if="isOpen"
        class="fixed inset-0 z-40"
        @click="isOpen = false"
    />

    <!-- Schedule Detail Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showModal = false"></div>
                
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden ring-1 ring-slate-100">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 text-white text-center">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                            <CalendarClockIcon class="w-8 h-8 text-white" />
                        </div>
                        <h2 class="text-xl font-bold">Detail Jadwal Bimbingan</h2>
                        <p class="text-blue-100 text-sm mt-1 opacity-90">Pengingat Jadwal Anda</p>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Topik Bimbingan</h4>
                            <p class="font-medium text-slate-800">{{ selectedSchedule?.topik || '-' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100/50">
                                <h4 class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Waktu</h4>
                                <p class="font-bold text-blue-900">{{ formatDateTime(selectedSchedule?.waktu_mulai) }}</p>
                            </div>
                            <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100/50">
                                <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1">Tipe / Lokasi</h4>
                                <p class="font-bold text-emerald-900">{{ selectedSchedule?.tipe_pertemuan || '-' }}</p>
                                <p class="text-xs text-emerald-700 mt-0.5 truncate" :title="selectedSchedule?.lokasi">{{ selectedSchedule?.lokasi || '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 border border-slate-100 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                <InfoIcon class="w-5 h-5 text-slate-400" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium">Partisipan</p>
                                <p class="text-sm font-bold text-slate-800">
                                    {{ selectedSchedule?.dosen || 'Dosen' }} & {{ selectedSchedule?.mahasiswa || 'Mahasiswa' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button 
                            @click="showModal = false"
                            class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-sm font-bold hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm"
                        >
                            Tutup
                        </button>
                        <a 
                            v-if="selectedScheduleUrl"
                            :href="selectedScheduleUrl"
                            class="px-5 py-2.5 bg-blue-600 border border-transparent text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors shadow-sm inline-flex items-center"
                        >
                            Lihat Kalender
                        </a>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import axios from 'axios';
    import {
        BellIcon,
        BellOffIcon,
        CalendarClockIcon,
        TrendingUpIcon,
        AlertTriangleIcon,
        InfoIcon,
    } from 'lucide-vue-next';

    const isOpen = ref(false);
    const notifications = ref([]);
    const unreadCount = ref(0);
    const loading = ref(false);
    
    const showModal = ref(false);
    const selectedSchedule = ref(null);
    const selectedScheduleUrl = ref(null);
    
    let pollInterval = null;

    const POLL_INTERVAL_MS = 30000; // 30 seconds

    function toggleDropdown() {
        isOpen.value = !isOpen.value;
        if (isOpen.value && notifications.value.length === 0) {
            fetchNotifications();
        }
    }

    async function fetchNotifications() {
        try {
            loading.value = true;
            const { data } = await axios.get('/api/notifications');
            notifications.value = data.notifications;
            unreadCount.value = data.unread_count;
        } catch (err) {
            console.error('Failed to fetch notifications', err);
        } finally {
            loading.value = false;
        }
    }

    async function markAllAsRead() {
        try {
            await axios.post('/api/notifications/mark-all-read');
            notifications.value = notifications.value.map((n) => ({
                ...n,
                read_at: n.read_at || new Date().toISOString(),
            }));
            unreadCount.value = 0;
        } catch (err) {
            console.error('Failed to mark all as read', err);
        }
    }

    async function markAsRead(notif) {
        if (notif.read_at) return;
        try {
            await axios.post(`/api/notifications/${notif.id}/mark-read`);
            notif.read_at = new Date().toISOString();
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        } catch (err) {
            console.error('Failed to mark notification as read', err);
        }
    }

    async function handleClick(notif) {
        await markAsRead(notif);
        
        if (notif.type === 'bimbingan_schedule_reminder' && notif.detail) {
            selectedSchedule.value = notif.detail;
            selectedScheduleUrl.value = notif.action_url || null;
            showModal.value = true;
            isOpen.value = false; // close the bell dropdown
        } else if (notif.action_url) {
            window.location.href = notif.action_url;
        }
    }

    function getNotifStyle(type) {
        switch (type) {
            case 'bimbingan_schedule_reminder':
                return {
                    icon: CalendarClockIcon,
                    bgColor: 'bg-blue-100',
                    iconColor: 'text-blue-600',
                };
            case 'academic_progress_reminder':
            case 'academic_progress_reminder_cc':
                return {
                    icon: TrendingUpIcon,
                    bgColor: 'bg-amber-100',
                    iconColor: 'text-amber-600',
                };
            case 'eskalasi_progress':
                return {
                    icon: AlertTriangleIcon,
                    bgColor: 'bg-red-100',
                    iconColor: 'text-red-600',
                };
            default:
                return {
                    icon: InfoIcon,
                    bgColor: 'bg-slate-100',
                    iconColor: 'text-slate-600',
                };
        }
    }

    function formatDateTime(dateStr) {
        if (!dateStr) return '';
        try {
            const dt = new Date(dateStr);
            return dt.toLocaleString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        } catch {
            return dateStr;
        }
    }

    onMounted(() => {
        // Initial fetch for badge count
        fetchNotifications();

        // Poll every 30 seconds
        pollInterval = setInterval(fetchNotifications, POLL_INTERVAL_MS);
    });

    onUnmounted(() => {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
    });
</script>
