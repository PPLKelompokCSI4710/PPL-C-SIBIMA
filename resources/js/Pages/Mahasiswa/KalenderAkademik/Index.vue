<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    const props = defineProps({
        kalender: { type: Array, default: () => [] },
        requests: { type: Array, default: () => [] },
        dosens: { type: Array, default: () => [] },
    });

    const navItems = [
        { label: 'Dashboard', icon: '🏠', active: false },
        { label: 'Calendar', icon: '📅', active: true },
        { label: 'My Courses', icon: '📚', active: false },
        { label: 'Grades', icon: '📈', active: false },
        { label: 'Settings', icon: '⚙️', active: false },
    ];

    // ── Request form ─────────────────────────────────────────────────────────────
    const showRequestModal = ref(false);
    const form = useForm({
        tipe_request: 'bimbingan',
        judul: '',
        dosen_id: '',
        tanggal: '',
        jam: '',
        deskripsi: '',
    });
    function submitRequest() {
        form.post(route('jadwal-request.store'), {
            onSuccess: () => {
                showRequestModal.value = false;
                form.reset();
            },
        });
    }

    // ── Notification ─────────────────────────────────────────────────────────────
    const showNotif = ref(false);
    const notifViewed = ref(false);
    const updatedRequests = computed(() =>
        props.requests.filter(
            (r) =>
                r.status === 'approved_admin' ||
                r.status === 'rejected_dosen' ||
                r.status === 'rejected_admin',
        ),
    );
    const unreadCount = computed(() => (notifViewed.value ? 0 : updatedRequests.value.length));

    function toggleNotif() {
        showNotif.value = !showNotif.value;
        if (showNotif.value) notifViewed.value = true;
    }

    // ── Accordion state ──────────────────────────────────────────────────────────
    const expandedRequestId = ref(null);
    function toggleExpand(id) {
        expandedRequestId.value = expandedRequestId.value === id ? null : id;
    }

    // ── Calendar state & Logic ──────────────────────────────────────────────────
    const today = new Date();
    const currentMonth = ref(today.getMonth());
    const currentYear = ref(today.getFullYear());
    const selectedDay = ref(today.getDate());
    const showDayPopup = ref(false);
    const popupDay = ref(null);

    const monthLabel = computed(() => {
        const date = new Date(currentYear.value, currentMonth.value);
        return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    });

    const monthDays = computed(() => {
        const days = [];
        const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay();
        const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();

        // Padding
        for (let i = 0; i < firstDay; i++) days.push({ empty: true });

        // Days
        for (let i = 1; i <= daysInMonth; i++) {
            const hasKalEvent = props.kalender.some((k) => {
                if (!k.tanggal_mulai) return false;
                // k.tanggal_mulai is "YYYY-MM-DD"
                const [y, m, d] = k.tanggal_mulai.split('-').map(Number);
                return d === i && m - 1 === currentMonth.value && y === currentYear.value;
            });

            const today = new Date();
            const isToday =
                i === today.getDate() &&
                currentMonth.value === today.getMonth() &&
                currentYear.value === today.getFullYear();

            days.push({
                num: i,
                empty: false,
                hasDot: hasKalEvent,
                isSelected: i === selectedDay.value,
                isToday: isToday,
            });
        }
        return days;
    });

    function prevMonth() {
        if (currentMonth.value === 0) {
            currentMonth.value = 11;
            currentYear.value--;
        } else {
            currentMonth.value--;
        }
        selectedDay.value = null;
    }

    function nextMonth() {
        if (currentMonth.value === 11) {
            currentMonth.value = 0;
            currentYear.value++;
        } else {
            currentMonth.value++;
        }
        selectedDay.value = null;
    }

    // ── Merged & sorted event list (Filtered by month) ────────────────────────────
    const allEvents = computed(() => {
        const fromDB = props.kalender
            .filter((k) => {
                if (!k.tanggal_mulai) return false;
                const [y, m] = k.tanggal_mulai.split('-').map(Number);
                return m - 1 === currentMonth.value && y === currentYear.value;
            })
            .map((k) => ({
                id: 'db-' + k.id,
                day: new Date(k.tanggal_mulai).getDate(),
                title: k.nama_kegiatan,
                type: k.tipe_kegiatan ?? 'Kegiatan',
                time: k.jam_mulai ?? '-',
                location: k.deskripsi ?? '-',
            }));
        return [...fromDB].sort((a, b) => a.day - b.day);
    });

    // ── Filter state ──────────────────────────────────────────────────────────────
    const activeFilter = ref('semua');
    const filterOptions = [
        { value: 'semua', label: 'Semua' },
        { value: 'kuliah', label: 'Kuliah' },
        { value: 'bimbingan', label: 'Bimbingan' },
        { value: 'rapat', label: 'Rapat' },
    ];

    const filteredEvents = computed(() => {
        console.log('Props Kalender:', props.kalender);
        console.log('Current Month/Year:', currentMonth.value, currentYear.value);
        if (activeFilter.value === 'semua') return allEvents.value;
        return allEvents.value.filter((e) => (e.type || '').toLowerCase() === activeFilter.value);
    });

    // ── Google Calendar Integration ──────────────────────────────────────────────
    function getGoogleCalendarUrl(ev) {
        const k = props.kalender.find((item) => 'db-' + item.id === ev.id);
        if (!k) return '#';

        const title = encodeURIComponent(k.nama_kegiatan);
        const details = encodeURIComponent(k.deskripsi || '');
        const location = encodeURIComponent('SIBIMA - Universitas');

        let startStr = k.tanggal_mulai.replace(/-/g, '');
        let endStr = (k.tanggal_selesai || k.tanggal_mulai).replace(/-/g, '');

        if (k.jam_mulai) {
            const time = k.jam_mulai.replace(/[:.]/g, '').padEnd(4, '0') + '00';
            startStr += 'T' + time;
            const endH = (parseInt(time.substring(0, 2)) + 1).toString().padStart(2, '0');
            endStr += 'T' + endH + time.substring(2);
        }

        return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${startStr}/${endStr}`;
    }

    // ── Type badge style helper ───────────────────────────────────────────────────
    function typeClass(tipe) {
        const t = (tipe ?? '').toLowerCase();
        if (t === 'kuliah') return 'bg-[#E9F0F8] text-[#1F4C7A]';
        if (t === 'bimbingan') return 'bg-[#EAF5E1] text-[#6DBE45]';
        if (t === 'rapat') return 'bg-[#FDF2E2] text-[#F39C12]';
        if (t === 'ujian') return 'bg-[#FBECEB] text-[#E74C3C]';
        if (t === 'semester') return 'bg-[#E9F0F8] text-[#1F4C7A]';
        return 'bg-slate-100 text-slate-600';
    }

    function clickDay(day) {
        if (day.empty) return;
        selectedDay.value = day.num;
        popupDay.value = day.num;
        showDayPopup.value = true;
    }

    const popupEvents = computed(() => {
        if (!popupDay.value) return [];
        return allEvents.value.filter((e) => e.day === popupDay.value);
    });

    // Returns the day-of-week label for the popup header
    const popupDayLabel = computed(() => {
        if (!popupDay.value) return '';
        const date = new Date(currentYear.value, currentMonth.value, popupDay.value);
        return date.toLocaleDateString('id-ID', { weekday: 'long' }).toUpperCase();
    });

    // ── Type bg colors for popup cards ────────────────────────────────────────────
    function cardBg(tipe) {
        const t = (tipe ?? '').toLowerCase();
        if (t === 'kuliah') return 'bg-blue-50 border-blue-200';
        if (t === 'bimbingan') return 'bg-emerald-50 border-emerald-200';
        if (t === 'rapat') return 'bg-amber-50 border-amber-200';
        if (t === 'ujian') return 'bg-rose-50 border-rose-200';
        return 'bg-slate-50 border-slate-200';
    }
</script>

<template>
    <Head title="Kalender Akademik – Mahasiswa" />
    <AuthenticatedLayout>
        <main class="min-h-screen bg-slate-100 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid lg:grid-cols-[260px_1fr] gap-6">
                    <!-- Sidebar matching Admin design -->
                    <aside
                        class="rounded-[28px] bg-slate-950 p-6 text-slate-200 shadow-xl shadow-slate-950/20 h-max"
                    >
                        <div class="mb-10 flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-3xl bg-blue-500/15 text-blue-300 ring-1 ring-blue-500/20"
                            >
                                <span class="text-2xl">📅</span>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">
                                    UniCalendar
                                </p>
                                <h2 class="text-xl font-semibold text-white">Mahasiswa</h2>
                            </div>
                        </div>

                        <nav class="space-y-2 text-sm font-medium">
                            <a
                                v-for="item in navItems"
                                :key="item.label"
                                href="#"
                                :class="[
                                    'flex items-center gap-3 rounded-3xl px-4 py-3 transition',
                                    item.active
                                        ? 'bg-slate-800 text-white'
                                        : 'text-slate-400 hover:bg-slate-800 hover:text-white',
                                ]"
                            >
                                <span class="text-lg">{{ item.icon }}</span>
                                {{ item.label }}
                            </a>
                        </nav>
                    </aside>

                    <!-- Main Content area -->
                    <section class="space-y-6">
                        <!-- Top Header Profile & Actions -->
                        <div
                            class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 flex justify-between items-center flex-wrap gap-4"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-full bg-[#EAF5E1] text-[#6DBE45] flex items-center justify-center font-bold text-xl tracking-wider"
                                >
                                    MH
                                </div>
                                <div>
                                    <h1 class="text-slate-900 font-bold text-lg leading-tight">
                                        Mahasiswa SIBIMA
                                    </h1>
                                    <p class="text-slate-500 text-sm mt-0.5">
                                        Teknik Informatika - Angkatan 2024
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button
                                    id="btn-request-jadwal"
                                    class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-600"
                                    @click="showRequestModal = true"
                                >
                                    + Request Jadwal Bimbingan
                                </button>
                                <div class="relative">
                                    <button
                                        class="relative p-3 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition focus:outline-none"
                                        @click="toggleNotif"
                                    >
                                        <svg
                                            class="w-5 h-5 text-slate-700"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.8"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                            />
                                        </svg>
                                        <span
                                            v-if="unreadCount > 0"
                                            class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white"
                                        >
                                            {{ unreadCount }}
                                        </span>
                                    </button>
                                    <transition name="notif">
                                        <div
                                            v-if="showNotif"
                                            class="absolute right-0 top-14 w-80 bg-white rounded-3xl shadow-2xl shadow-slate-900/15 border border-slate-100 z-50 overflow-hidden"
                                        >
                                            <div
                                                class="px-5 py-4 border-b border-slate-100 flex justify-between items-center"
                                            >
                                                <h3 class="font-bold text-slate-900 text-sm">
                                                    Notifikasi
                                                </h3>
                                                <button
                                                    class="text-slate-400 hover:text-slate-700 text-lg"
                                                    @click="showNotif = false"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                            <div
                                                class="divide-y divide-slate-50 max-h-64 overflow-y-auto"
                                            >
                                                <div
                                                    v-for="req in updatedRequests"
                                                    :key="req.id"
                                                    class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50"
                                                >
                                                    <span
                                                        class="mt-1 w-2 h-2 rounded-full shrink-0"
                                                        :class="
                                                            req.status === 'approved_admin'
                                                                ? 'bg-emerald-400'
                                                                : 'bg-rose-400'
                                                        "
                                                    />
                                                    <div>
                                                        <p
                                                            class="text-sm font-medium text-slate-800"
                                                        >
                                                            {{
                                                                req.status === 'approved_admin'
                                                                    ? '✅ Jadwal disetujui'
                                                                    : '❌ Jadwal ditolak'
                                                            }}: {{ req.judul }}
                                                        </p>
                                                        <p
                                                            v-if="req.alasan_penolakan"
                                                            class="text-[11px] text-rose-500 font-medium mt-1 italic"
                                                        >
                                                            "{{ req.alasan_penolakan }}"
                                                        </p>
                                                        <p class="text-xs text-slate-500 mt-1">
                                                            {{ req.tanggal }} • {{ req.jam }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    v-if="updatedRequests.length === 0"
                                                    class="px-5 py-6 text-center text-sm text-slate-400"
                                                >
                                                    Tidak ada notifikasi baru.
                                                </div>
                                            </div>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </div>

                        <!-- Lower Grid: Calendar & Agenda -->
                        <div class="grid lg:grid-cols-[1fr_1.5fr] gap-6">
                            <!-- Calendar View -->
                            <div
                                class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 h-max"
                            >
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-slate-900 font-bold text-lg capitalize">
                                        {{ monthLabel }}
                                    </h2>
                                    <div class="flex gap-2">
                                        <button
                                            class="w-8 h-8 flex justify-center items-center rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:bg-slate-100 transition"
                                            @click="prevMonth"
                                        >
                                            &lt;
                                        </button>
                                        <button
                                            class="w-8 h-8 flex justify-center items-center rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:bg-slate-100 transition"
                                            @click="nextMonth"
                                        >
                                            &gt;
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-7 text-center gap-y-4">
                                    <div class="text-slate-400 text-xs font-semibold pb-2">Min</div>
                                    <div class="text-slate-400 text-xs font-semibold pb-2">Sen</div>
                                    <div class="text-slate-400 text-xs font-semibold pb-2">Sel</div>
                                    <div class="text-slate-400 text-xs font-semibold pb-2">Rab</div>
                                    <div class="text-slate-400 text-xs font-semibold pb-2">Kam</div>
                                    <div class="text-slate-400 text-xs font-semibold pb-2">Jum</div>
                                    <div class="text-slate-400 text-xs font-semibold pb-2">Sab</div>

                                    <template v-for="(day, idx) in monthDays" :key="idx">
                                        <div v-if="day.empty" class="text-transparent p-2">0</div>
                                        <div
                                            v-else
                                            class="relative flex flex-col items-center justify-center w-10 h-10 mx-auto cursor-pointer transition-all rounded-xl select-none"
                                            :class="{
                                                'bg-[#1F4C7A] text-white shadow-md': day.isSelected,
                                                'bg-[#E9F0F8] text-[#1F4C7A] font-bold':
                                                    day.isToday && !day.isSelected,
                                                'text-slate-700 hover:bg-slate-100':
                                                    !day.isSelected && !day.isToday,
                                            }"
                                            @click="clickDay(day)"
                                        >
                                            <span
                                                class="text-sm"
                                                :class="{
                                                    'font-semibold': day.isSelected || day.isToday,
                                                }"
                                                >{{ day.num }}</span
                                            >
                                            <span
                                                v-if="day.hasDot"
                                                class="absolute bottom-1 w-1 h-1 rounded-full"
                                                :class="
                                                    day.isSelected ? 'bg-white' : 'bg-emerald-500'
                                                "
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Request List & Agenda Section -->
                            <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5">
                                <h3 class="text-lg font-bold text-slate-900 mb-6">
                                    Status Request Jadwal
                                </h3>

                                <div
                                    v-if="requests.length === 0"
                                    class="text-slate-500 text-sm mb-8 text-center py-4"
                                >
                                    Belum ada request jadwal.
                                </div>
                                <div v-else class="space-y-3 mb-8">
                                    <div
                                        v-for="req in requests"
                                        :key="req.id"
                                        class="rounded-2xl border border-slate-100 bg-slate-50 overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md"
                                    >
                                        <!-- Accordion Header -->
                                        <button
                                            class="w-full text-left p-4 focus:outline-none group"
                                            @click="toggleExpand(req.id)"
                                        >
                                            <div
                                                class="flex justify-between items-center gap-4 flex-wrap"
                                            >
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <h4
                                                        class="font-bold text-slate-900 text-sm truncate pr-2"
                                                    >
                                                        {{ req.judul }}
                                                    </h4>
                                                    <span
                                                        class="text-slate-400 text-[10px] font-medium shrink-0"
                                                        >{{ req.tanggal }}</span
                                                    >
                                                    <span
                                                        class="text-slate-300 text-[10px] shrink-0"
                                                        >•</span
                                                    >
                                                    <span
                                                        class="text-slate-400 text-[10px] font-medium shrink-0"
                                                        >{{ req.jam }}</span
                                                    >
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="px-2.5 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider shadow-sm"
                                                        :class="{
                                                            'bg-amber-100 text-amber-700':
                                                                req.status === 'pending_dosen' ||
                                                                req.status === 'pending_admin',
                                                            'bg-emerald-100 text-emerald-700 border border-emerald-200':
                                                                req.status === 'approved' ||
                                                                req.status === 'approved_admin',
                                                            'bg-rose-100 text-rose-700':
                                                                req.status === 'rejected_dosen' ||
                                                                req.status === 'rejected_admin',
                                                        }"
                                                    >
                                                        {{
                                                            req.status.includes('approved')
                                                                ? 'Disetujui'
                                                                : req.status.replace('_', ' ')
                                                        }}
                                                    </span>
                                                    <svg
                                                        class="w-4 h-4 text-slate-400 transition-transform duration-300"
                                                        :class="{
                                                            'rotate-180':
                                                                expandedRequestId === req.id,
                                                        }"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 9l-7 7-7-7"
                                                        />
                                                    </svg>
                                                </div>
                                            </div>
                                        </button>

                                        <!-- Accordion Content -->
                                        <div
                                            v-show="expandedRequestId === req.id"
                                            class="px-5 pb-5 border-t border-slate-200/50 pt-5 bg-white/50"
                                        >
                                            <div class="grid grid-cols-[80px_1fr] gap-y-3 text-xs">
                                                <div class="text-slate-400 font-medium">Dosen</div>
                                                <div class="text-[#1F4C7A] font-bold">
                                                    {{ req.dosen_name || '-' }}
                                                </div>

                                                <div class="text-slate-400 font-medium">
                                                    Catatan
                                                </div>
                                                <div class="text-slate-600 italic leading-relaxed">
                                                    "{{ req.deskripsi || '-' }}"
                                                </div>

                                                <div class="text-slate-400 font-medium">Status</div>
                                                <div
                                                    class="font-bold flex items-center gap-1.5"
                                                    :class="{
                                                        'text-amber-600':
                                                            req.status.includes('pending'),
                                                        'text-emerald-600':
                                                            req.status.includes('approved'),
                                                        'text-rose-600':
                                                            req.status.includes('rejected'),
                                                    }"
                                                >
                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full bg-current"
                                                    />
                                                    {{
                                                        req.status.includes('approved')
                                                            ? 'Disetujui oleh Admin & Dosen'
                                                            : req.status.replace('_', ' ')
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-between items-center mb-4 pt-4 border-t border-slate-50"
                                >
                                    <h3 class="text-lg font-bold text-slate-900">
                                        Jadwal Mendatang
                                    </h3>
                                </div>

                                <!-- Filter -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <button
                                        v-for="f in filterOptions"
                                        :key="f.value"
                                        :class="[
                                            'px-3 py-1 rounded-full text-[11px] font-semibold transition',
                                            activeFilter === f.value
                                                ? 'bg-[#1F4C7A] text-white shadow-sm'
                                                : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100',
                                        ]"
                                        @click="activeFilter = f.value"
                                    >
                                        {{ f.label }}
                                    </button>
                                </div>

                                <!-- Unified event list (filtered) -->
                                <div
                                    class="space-y-3 max-h-[360px] overflow-y-auto pr-1 scrollbar-thin"
                                >
                                    <div
                                        v-for="ev in filteredEvents"
                                        :id="'agenda-item-' + ev.id"
                                        :key="ev.id"
                                        class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition cursor-pointer"
                                        @click="
                                            popupDay = ev.day;
                                            showDayPopup = true;
                                        "
                                    >
                                        <!-- Date block -->
                                        <div
                                            class="bg-slate-50 border border-slate-100 rounded-xl p-2 flex flex-col justify-center items-center min-w-[50px] h-[50px]"
                                        >
                                            <span
                                                class="text-lg font-bold text-slate-900 leading-none"
                                                >{{ ev.day }}</span
                                            >
                                            <span
                                                class="text-[8px] text-slate-500 font-bold uppercase tracking-widest mt-0.5"
                                                >{{
                                                    monthLabel.split(' ')[0].substring(0, 3)
                                                }}</span
                                            >
                                        </div>
                                        <!-- Detail -->
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="font-bold text-slate-900 text-xs leading-snug mb-1 truncate"
                                            >
                                                {{ ev.title }}
                                            </h4>
                                            <div
                                                class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] text-slate-500"
                                            >
                                                <span
                                                    class="px-2 py-0.5 rounded-md font-bold uppercase text-[8px]"
                                                    :class="typeClass(ev.type)"
                                                    >{{ ev.type }}</span
                                                >
                                                <span v-if="ev.time && ev.time !== '-'"
                                                    >⏰ {{ ev.time }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <p
                                        v-if="filteredEvents.length === 0"
                                        class="text-center py-8 text-slate-400 text-xs"
                                    >
                                        Tidak ada jadwal.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Modal Request -->
            <transition name="modal">
                <div
                    v-if="showRequestModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
                >
                    <div class="w-full max-w-lg rounded-[32px] bg-white p-6 shadow-2xl">
                        <div
                            class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6"
                        >
                            <h2 class="text-xl font-semibold text-slate-900">
                                Request Jadwal Bimbingan
                            </h2>
                            <button
                                class="rounded-full bg-slate-100 p-2 text-slate-600 hover:bg-slate-200"
                                @click="showRequestModal = false"
                            >
                                ✕
                            </button>
                        </div>
                        <form class="space-y-4" @submit.prevent="submitRequest">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2"
                                    >Judul / Topik</label
                                >
                                <input
                                    id="mhs-judul"
                                    v-model="form.judul"
                                    type="text"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2"
                                    >Dosen Pembimbing</label
                                >
                                <select
                                    id="mhs-dosen-id"
                                    v-model="form.dosen_id"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                    required
                                >
                                    <option value="" disabled>Pilih Dosen</option>
                                    <option v-for="d in dosens" :key="d.id" :value="d.id">
                                        {{ d.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2"
                                        >Tanggal</label
                                    >
                                    <input
                                        id="mhs-tanggal"
                                        v-model="form.tanggal"
                                        type="date"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2"
                                        >Jam</label
                                    >
                                    <input
                                        id="mhs-jam"
                                        v-model="form.jam"
                                        type="time"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                        required
                                    />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2"
                                    >Pesan Tambahan</label
                                >
                                <textarea
                                    v-model="form.deskripsi"
                                    rows="3"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                />
                            </div>
                            <div class="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    class="rounded-full bg-slate-100 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                                    @click="showRequestModal = false"
                                >
                                    Batal
                                </button>
                                <button
                                    id="btn-submit-request"
                                    type="submit"
                                    class="rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-600"
                                >
                                    Kirim Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>

            <!-- Day Detail Popup -->
            <transition name="modal">
                <div
                    v-if="showDayPopup"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
                    @click.self="showDayPopup = false"
                >
                    <div class="w-full max-w-sm rounded-[28px] overflow-hidden shadow-2xl">
                        <!-- Dark header -->
                        <div class="bg-[#1F4C7A] px-6 pt-6 pb-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p
                                        class="text-blue-300 text-xs font-bold uppercase tracking-widest"
                                    >
                                        {{ popupDayLabel }}, {{ monthLabel }}
                                    </p>
                                    <h2 class="text-white text-5xl font-bold mt-1 leading-none">
                                        {{ popupDay }}
                                    </h2>
                                    <div class="flex gap-1.5 mt-3">
                                        <span
                                            v-for="ev in popupEvents"
                                            :key="'dot-' + ev.id"
                                            class="w-2.5 h-2.5 rounded-full"
                                            :class="typeClass(ev.type).split(' ')[0]"
                                        />
                                    </div>
                                </div>
                                <button
                                    class="w-9 h-9 flex items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25 transition text-lg"
                                    @click="showDayPopup = false"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>

                        <!-- Event cards -->
                        <div
                            class="bg-white px-4 py-4 space-y-3 max-h-72 overflow-y-auto scrollbar-thin"
                        >
                            <p
                                v-if="popupEvents.length === 0"
                                class="py-6 text-center text-slate-400 text-sm"
                            >
                                Tidak ada kegiatan pada hari ini.
                            </p>
                            <div
                                v-for="ev in popupEvents"
                                :key="ev.id"
                                class="rounded-2xl border p-4"
                                :class="cardBg(ev.type)"
                            >
                                <div class="flex justify-between items-center mb-2">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="typeClass(ev.type)"
                                        >{{ ev.type }}</span
                                    >
                                    <span class="text-xs font-semibold text-slate-600">{{
                                        ev.time !== '-' ? ev.time : ''
                                    }}</span>
                                </div>
                                <div class="flex justify-between items-start gap-2">
                                    <h4
                                        class="font-bold text-slate-900 text-sm leading-snug flex-1"
                                    >
                                        {{ ev.title }}
                                    </h4>
                                    <a
                                        :id="'btn-sync-google-' + ev.id"
                                        :href="getGoogleCalendarUrl(ev)"
                                        target="_blank"
                                        class="p-1.5 rounded-lg bg-white/50 text-[#1F4C7A] hover:bg-white transition group border border-blue-200/50"
                                        title="Simpan ke Google Calendar"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"
                                            />
                                        </svg>
                                    </a>
                                </div>
                                <p
                                    v-if="ev.location && ev.location !== '-'"
                                    class="text-xs text-slate-500 mt-1.5 flex items-center gap-1"
                                >
                                    <svg
                                        class="w-3.5 h-3.5 shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    {{ ev.location }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </main>
    </AuthenticatedLayout>
</template>

<style scoped>
    .modal-enter-active,
    .modal-leave-active {
        transition:
            opacity 0.2s ease,
            transform 0.2s ease;
    }
    .modal-enter-from,
    .modal-leave-to {
        opacity: 0;
        transform: scale(0.97);
    }
    .notif-enter-active,
    .notif-leave-active {
        transition:
            opacity 0.15s ease,
            transform 0.15s ease;
    }
    .notif-enter-from,
    .notif-leave-to {
        opacity: 0;
        transform: translateY(-6px);
    }

    /* Custom thin scrollbar */
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 99px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>
