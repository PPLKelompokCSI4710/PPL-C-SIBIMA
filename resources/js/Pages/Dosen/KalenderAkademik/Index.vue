<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    const props = defineProps({
        kalender: { type: Array, default: () => [] },
        requests: { type: Array, default: () => [] },
        currentDosen: { type: Object, default: () => ({}) },
    });

    // ── Nav ──────────────────────────────────────────────────────────────────────
    const navItems = [
        { label: 'Dashboard', icon: '🏠', active: false },
        { label: 'Calendar', icon: '📅', active: true },
        { label: 'Classes', icon: '🏫', active: false },
        { label: 'Students', icon: '👩‍🎓', active: false },
        { label: 'Grades', icon: '📝', active: false },
        { label: 'Settings', icon: '⚙️', active: false },
    ];

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

    // ── Calendar state & Logic ──────────────────────────────────────────────────
    const currentMonth = ref(4); // Mei (0-indexed)
    const currentYear = ref(2026);
    const selectedDay = ref(null);

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
            const hasKalenderEvent = props.kalender.some((k) => {
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
                hasDot: hasKalenderEvent,
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
        { value: 'ujian', label: 'Ujian' },
    ];

    const filteredEvents = computed(() => {
        if (activeFilter.value === 'semua') return allEvents.value;
        return allEvents.value.filter((e) => (e.type || '').toLowerCase() === activeFilter.value);
    });

    // ── Google Calendar Integration ──────────────────────────────────────────────
    function getGoogleCalendarUrl(ev) {
        // ev is from allEvents, contains raw data in props.kalender
        const k = props.kalender.find((item) => 'db-' + item.id === ev.id);
        if (!k) return '#';

        const title = encodeURIComponent(k.nama_kegiatan);
        const details = encodeURIComponent(k.deskripsi || '');
        const location = encodeURIComponent('SIBIMA - Universitas');

        // Format dates: YYYYMMDDTHHmmSSZ
        let startStr = k.tanggal_mulai.replace(/-/g, '');
        let endStr = (k.tanggal_selesai || k.tanggal_mulai).replace(/-/g, '');

        if (k.jam_mulai) {
            const time = k.jam_mulai.replace(/[:.]/g, '').padEnd(4, '0') + '00';
            startStr += 'T' + time;
            // Assume 1 hour duration
            const endH = (parseInt(time.substring(0, 2)) + 1).toString().padStart(2, '0');
            endStr += 'T' + endH + time.substring(2);
        }

        return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${startStr}/${endStr}`;
    }

    // ── Day-click popup ───────────────────────────────────────────────────────────
    const showDayPopup = ref(false);
    const popupDay = ref(null);

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
        if (t === 'semester') return 'bg-blue-50 border-blue-200';
        return 'bg-slate-50 border-slate-200';
    }

    function clickDay(day) {
        if (day.empty) return;
        selectedDay.value = day.num;
        popupDay.value = day.num;
        showDayPopup.value = true;
    }

    // ── Notification panel ────────────────────────────────────────────────────────
    const showNotif = ref(false);
    const pendingCount = computed(
        () => props.requests.filter((r) => r.status === 'pending_dosen').length,
    );

    const notifList = computed(() => [
        ...props.requests
            .filter((r) => r.status === 'pending_dosen')
            .map((r) => ({
                id: r.id,
                text: `Request bimbingan: "${r.judul}"`,
                sub: `${r.tanggal} • ${r.jam}`,
                color: 'bg-amber-400',
            })),
        { id: 'n1', text: 'Jadwal UTS mendekati', sub: '26 Mei 2026', color: 'bg-rose-400' },
    ]);

    // ── Approve / Reject ──────────────────────────────────────────────────────────
    const showRejectModal = ref(false);
    const rejectReason = ref('');
    const activeRequestId = ref(null);

    function openRejectModal(id) {
        activeRequestId.value = id;
        rejectReason.value = '';
        showRejectModal.value = true;
    }

    function confirmReject() {
        if (!rejectReason.value) return alert('Mohon isi alasan penolakan.');
        updateStatus(activeRequestId.value, 'rejected_dosen', rejectReason.value);
        showRejectModal.value = false;
    }

    function updateStatus(id, status, reason = null) {
        router.put(
            route('jadwal-request.updateStatus', id),
            {
                status,
                alasan_penolakan: reason,
            },
            { preserveScroll: true },
        );
    }

    // ── Add Schedule Modal ────────────────────────────────────────────────────────
    const showAddModal = ref(false);
    const addForm = ref({
        tipe_kegiatan: 'kuliah',
        nama_kegiatan: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
        jam_mulai: '',
        deskripsi: '',
        // for bimbingan request flow:
        judul: '',
        tanggal: '',
        jam: '',
    });

    const isBimbingan = computed(() => addForm.value.tipe_kegiatan === 'bimbingan');

    const tipeOptions = [
        { value: 'kuliah', label: 'Kuliah' },
        { value: 'rapat', label: 'Rapat' },
    ];

    function submitAddJadwal() {
        if (isBimbingan.value) {
            // Go through JadwalRequest flow
            router.post(
                route('jadwal-request.store'),
                {
                    tipe_request: 'bimbingan',
                    judul: addForm.value.nama_kegiatan,
                    deskripsi: addForm.value.deskripsi,
                    tanggal: addForm.value.tanggal_mulai,
                    jam: addForm.value.jam_mulai,
                },
                {
                    onSuccess: () => {
                        showAddModal.value = false;
                        resetAddForm();
                    },
                },
            );
        } else {
            // Add directly to kalender
            router.post(
                route('dosen.kalender.store'),
                {
                    dosen_id: props.currentDosen.id, // Pastikan ID dikirim agar tidak tertukar ke ID 1
                    nama_kegiatan: addForm.value.nama_kegiatan,
                    tipe_kegiatan: addForm.value.tipe_kegiatan,
                    tanggal_mulai: addForm.value.tanggal_mulai,
                    tanggal_selesai: addForm.value.tanggal_selesai || addForm.value.tanggal_mulai,
                    jam_mulai: addForm.value.jam_mulai,
                    deskripsi: addForm.value.deskripsi,
                },
                {
                    onSuccess: () => {
                        showAddModal.value = false;
                        resetAddForm();
                    },
                },
            );
        }
    }

    function resetAddForm() {
        addForm.value = {
            tipe_kegiatan: 'kuliah',
            nama_kegiatan: '',
            tanggal_mulai: '',
            tanggal_selesai: '',
            jam_mulai: '',
            deskripsi: '',
            judul: '',
            tanggal: '',
            jam: '',
        };
    }
</script>

<template>
    <Head title="Kalender Akademik – Dosen" />
    <AuthenticatedLayout>
        <main class="min-h-screen bg-slate-100 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid lg:grid-cols-[260px_1fr] gap-6">
                    <!-- Sidebar -->
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
                                <h2 class="text-xl font-semibold text-white">Dosen</h2>
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

                    <!-- Main -->
                    <section class="space-y-6">
                        <!-- Header / Profile -->
                        <div
                            class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 flex justify-between items-center flex-wrap gap-4"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-full bg-[#E9F0F8] text-[#1F4C7A] flex items-center justify-center font-bold text-xl"
                                >
                                    {{
                                        currentDosen.name
                                            ? currentDosen.name
                                                  .split(' ')
                                                  .map((n) => n[0])
                                                  .join('')
                                                  .substring(0, 2)
                                                  .toUpperCase()
                                            : 'DR'
                                    }}
                                </div>
                                <div>
                                    <h1 class="text-slate-900 font-bold text-lg leading-tight">
                                        {{ currentDosen.name || 'Dosen SIBIMA' }}
                                    </h1>
                                    <p class="text-slate-500 text-sm mt-0.5">
                                        {{ currentDosen.email || 'Teknik Informatika' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    class="inline-flex items-center gap-2 rounded-full bg-[#1F4C7A] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:bg-[#163a5e]"
                                    @click="showAddModal = true"
                                >
                                    <span class="text-base leading-none">+</span> Tambah Jadwal
                                </button>
                                <!-- Notification Bell -->
                                <div class="relative">
                                    <button
                                        class="relative p-3 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition focus:outline-none"
                                        @click="showNotif = !showNotif"
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
                                            v-if="pendingCount > 0"
                                            class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white"
                                        >
                                            {{ pendingCount }}
                                        </span>
                                    </button>

                                    <!-- Notification Dropdown -->
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
                                                    class="text-slate-400 hover:text-slate-700 text-lg leading-none"
                                                    @click="showNotif = false"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                            <div
                                                class="divide-y divide-slate-50 max-h-72 overflow-y-auto"
                                            >
                                                <div
                                                    v-for="n in notifList"
                                                    :key="n.id"
                                                    class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50 transition"
                                                >
                                                    <span
                                                        class="mt-1 w-2 h-2 rounded-full shrink-0"
                                                        :class="n.color"
                                                    />
                                                    <div>
                                                        <p
                                                            class="text-sm font-medium text-slate-800"
                                                        >
                                                            {{ n.text }}
                                                        </p>
                                                        <p class="text-xs text-slate-500 mt-0.5">
                                                            {{ n.sub }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    v-if="notifList.length === 0"
                                                    class="px-5 py-6 text-center text-sm text-slate-400"
                                                >
                                                    Tidak ada notifikasi.
                                                </div>
                                            </div>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                            <!-- end flex gap-3 -->
                        </div>
                        <!-- end header card -->

                        <!-- Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5">
                                <p class="text-sm text-slate-500 mb-1">Minggu ini</p>
                                <p class="text-3xl font-bold text-slate-900">8</p>
                                <p class="text-xs text-slate-500 mt-1">jadwal aktif</p>
                            </div>
                            <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5">
                                <p class="text-sm text-slate-500 mb-1">SKS diambil</p>
                                <p class="text-3xl font-bold text-slate-900">12</p>
                                <p class="text-xs text-slate-500 mt-1">dari 16 maks</p>
                            </div>
                            <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5">
                                <p class="text-sm text-slate-500 mb-1">Bimbingan</p>
                                <p class="text-3xl font-bold text-slate-900">
                                    {{ pendingCount }}
                                </p>
                                <p class="text-xs text-slate-500 mt-1">permintaan baru</p>
                            </div>
                        </div>

                        <!-- Lower Grid -->
                        <div class="grid lg:grid-cols-[1fr_1.5fr] gap-6">
                            <!-- Calendar -->
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

                                <div class="grid grid-cols-7 text-center gap-y-3">
                                    <div
                                        v-for="d in [
                                            'Min',
                                            'Sen',
                                            'Sel',
                                            'Rab',
                                            'Kam',
                                            'Jum',
                                            'Sab',
                                        ]"
                                        :key="d"
                                        class="text-slate-400 text-xs font-semibold pb-2"
                                    >
                                        {{ d }}
                                    </div>

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

                                <div
                                    class="flex gap-4 mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#E9F0F8]" />Hari
                                        ini
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" />Ada
                                        jadwal
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span
                                            class="w-2.5 h-2.5 rounded-full bg-[#1F4C7A]"
                                        />Dipilih
                                    </div>
                                </div>
                            </div>

                            <!-- Requests & Agenda -->
                            <div class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5">
                                <!-- Pending Requests -->
                                <h3 class="text-lg font-bold text-slate-900 mb-4">
                                    Permintaan Jadwal Bimbingan
                                </h3>
                                <div
                                    v-if="
                                        requests.filter((r) => r.status === 'pending_dosen')
                                            .length === 0
                                    "
                                    class="text-slate-500 text-sm mb-6"
                                >
                                    Tidak ada permintaan baru.
                                </div>
                                <div v-else class="space-y-3 mb-6">
                                    <div
                                        v-for="req in requests.filter(
                                            (r) => r.status === 'pending_dosen',
                                        )"
                                        :key="req.id"
                                        class="p-4 rounded-2xl border border-blue-100 bg-blue-50/40 flex flex-col gap-2"
                                    >
                                        <div class="flex justify-between items-start gap-2">
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-sm">
                                                    {{ req.judul }}
                                                </h4>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    {{ req.tanggal }} • {{ req.jam }}
                                                </p>
                                            </div>
                                            <span
                                                class="shrink-0 px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-lg uppercase"
                                                >Menunggu ACC</span
                                            >
                                        </div>
                                        <p
                                            v-if="req.deskripsi"
                                            class="text-xs text-slate-600 italic"
                                        >
                                            "{{ req.deskripsi }}"
                                        </p>
                                        <div class="flex gap-2 mt-1">
                                            <button
                                                class="px-4 py-1.5 bg-emerald-500 text-white text-xs font-semibold rounded-full hover:bg-emerald-600 transition"
                                                @click="updateStatus(req.id, 'approved_dosen')"
                                            >
                                                ACC
                                            </button>
                                            <button
                                                class="px-4 py-1.5 bg-rose-500 text-white text-xs font-semibold rounded-full hover:bg-rose-600 transition"
                                                @click="openRejectModal(req.id)"
                                            >
                                                Tolak
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agenda -->
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-slate-900">
                                        Jadwal Akan Datang
                                    </h3>
                                </div>
                                <!-- Agenda filter -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <button
                                        v-for="f in filterOptions"
                                        :key="f.value"
                                        :class="[
                                            'px-4 py-1.5 rounded-full text-xs font-medium transition',
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
                                    class="space-y-3 max-h-[420px] overflow-y-auto pr-1 scrollbar-thin"
                                >
                                    <div
                                        v-for="ev in filteredEvents"
                                        :key="ev.id"
                                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition cursor-pointer"
                                        @click="
                                            popupDay = ev.day;
                                            showDayPopup = true;
                                        "
                                    >
                                        <!-- Date block -->
                                        <div
                                            class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center items-center min-w-[60px] h-[60px]"
                                        >
                                            <span
                                                class="text-xl font-bold text-slate-900 leading-none"
                                                >{{ ev.day }}</span
                                            >
                                            <span
                                                class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5"
                                                >{{
                                                    monthLabel.split(' ')[0].substring(0, 3)
                                                }}</span
                                            >
                                        </div>
                                        <!-- Detail -->
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="font-bold text-slate-900 text-sm leading-snug mb-1.5 truncate"
                                            >
                                                {{ ev.title }}
                                            </h4>
                                            <div
                                                class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500"
                                            >
                                                <span
                                                    class="px-2 py-0.5 rounded-md font-semibold text-[10px] uppercase"
                                                    :class="typeClass(ev.type)"
                                                    >{{ ev.type }}</span
                                                >
                                                <span v-if="ev.time && ev.time !== '-'"
                                                    >⏰ {{ ev.time }}</span
                                                >
                                                <span
                                                    v-if="ev.location && ev.location !== '-'"
                                                    class="truncate"
                                                    >📍 {{ ev.location }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- ─── Day Detail Popup ─────────────────────────────────────────── -->
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

            <!-- ─── Tambah Jadwal Modal ─────────────────────────────────────── -->
            <transition name="modal">
                <div
                    v-if="showAddModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
                    @click.self="showAddModal = false"
                >
                    <div class="w-full max-w-lg rounded-[32px] bg-white p-6 shadow-2xl">
                        <div
                            class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6"
                        >
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">Tambah Jadwal</h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Bimbingan memerlukan konfirmasi admin
                                </p>
                            </div>
                            <button
                                class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 text-lg"
                                @click="showAddModal = false"
                            >
                                ✕
                            </button>
                        </div>

                        <form class="space-y-4" @submit.prevent="submitAddJadwal">
                            <!-- Tipe -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2"
                                    >Tipe Kegiatan</label
                                >
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    <button
                                        v-for="opt in tipeOptions"
                                        :key="opt.value"
                                        type="button"
                                        :class="[
                                            'px-3 py-2 rounded-2xl text-xs font-semibold border transition text-left',
                                            addForm.tipe_kegiatan === opt.value
                                                ? 'bg-[#1F4C7A] text-white border-[#1F4C7A] shadow-md'
                                                : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-slate-300',
                                        ]"
                                        @click="addForm.tipe_kegiatan = opt.value"
                                    >
                                        {{ opt.label }}
                                    </button>
                                </div>
                                <!-- Bimbingan notice -->
                                <p
                                    v-if="isBimbingan"
                                    class="mt-2 text-xs text-amber-600 bg-amber-50 rounded-xl px-3 py-2"
                                >
                                    ⚠️ Jadwal bimbingan akan dikirim sebagai request dan perlu
                                    konfirmasi dosen + admin sebelum masuk kalender.
                                </p>
                            </div>

                            <!-- Judul / Nama -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2"
                                    >Judul / Nama Kegiatan</label
                                >
                                <input
                                    v-model="addForm.nama_kegiatan"
                                    type="text"
                                    required
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none"
                                />
                            </div>

                            <!-- Tanggal & Jam -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2"
                                        >Tanggal Mulai</label
                                    >
                                    <input
                                        v-model="addForm.tanggal_mulai"
                                        type="date"
                                        required
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2"
                                        >Jam Mulai</label
                                    >
                                    <input
                                        v-model="addForm.jam_mulai"
                                        type="time"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none"
                                    />
                                </div>
                            </div>

                            <!-- Tanggal Selesai (only non-bimbingan) -->
                            <div v-if="!isBimbingan">
                                <label class="block text-sm font-semibold text-slate-700 mb-2"
                                    >Tanggal Selesai
                                    <span class="text-slate-400 font-normal"
                                        >(opsional, default = tanggal mulai)</span
                                    ></label
                                >
                                <input
                                    v-model="addForm.tanggal_selesai"
                                    type="date"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none"
                                />
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2"
                                    >Deskripsi / Catatan
                                    <span class="text-slate-400 font-normal"
                                        >(opsional)</span
                                    ></label
                                >
                                <textarea
                                    v-model="addForm.deskripsi"
                                    rows="2"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none"
                                />
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    class="rounded-full bg-slate-100 px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                                    @click="showAddModal = false"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    :class="[
                                        'rounded-full px-6 py-2.5 text-sm font-semibold text-white transition',
                                        isBimbingan
                                            ? 'bg-amber-500 hover:bg-amber-600'
                                            : 'bg-[#1F4C7A] hover:bg-[#163a5e]',
                                    ]"
                                >
                                    {{ isBimbingan ? 'Kirim Request' : 'Simpan Jadwal' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
            <!-- Modal Penolakan -->
            <transition name="modal">
                <div
                    v-if="showRejectModal"
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
                >
                    <div class="w-full max-w-sm rounded-[32px] bg-white p-6 shadow-2xl">
                        <h2 class="text-xl font-bold text-slate-900 mb-2">Alasan Penolakan</h2>
                        <p class="text-xs text-slate-500 mb-4">
                            Berikan alasan agar mahasiswa dapat menyesuaikan jadwal kembali.
                        </p>
                        <textarea
                            v-model="rejectReason"
                            rows="4"
                            placeholder="Contoh: Saya ada rapat di jam tersebut. Silakan pilih waktu lain."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-100 outline-none mb-4"
                        />
                        <div class="flex justify-end gap-3">
                            <button
                                class="px-6 py-2.5 rounded-full bg-slate-100 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition"
                                @click="showRejectModal = false"
                            >
                                Batal
                            </button>
                            <button
                                class="px-6 py-2.5 rounded-full bg-rose-600 text-sm font-semibold text-white hover:bg-rose-700 transition shadow-lg shadow-rose-900/20"
                                @click="confirmReject"
                            >
                                Kirim Penolakan
                            </button>
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
