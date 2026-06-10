<script>
    export default { name: 'AdminKalenderAkademik' };
</script>

<script setup>
    import StaffLayout from '@/Layouts/StaffLayout.vue';
    import { Head, useForm, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    const props = defineProps({
        kalender: { type: Array, default: () => [] },
        requests: { type: Array, default: () => [] },
        flash: { type: Object, default: () => ({}) },
    });

    const successMessage = ref(props.flash?.success ?? '');

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
    const today = new Date();
    const currentMonth = ref(today.getMonth());
    const currentYear = ref(today.getFullYear());
    const selectedDay = ref(today.getDate());

    const monthLabel = computed(() => {
        const date = new Date(currentYear.value, currentMonth.value);
        return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    });

    const monthDays = computed(() => {
        const days = [];
        const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay();
        const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) days.push({ empty: true });

        for (let i = 1; i <= daysInMonth; i++) {
            const hasKalenderEvent = props.kalender.some((k) => {
                if (!k.tanggal_mulai) return false;
                const [y, m, d] = k.tanggal_mulai.substring(0, 10).split('-').map(Number);
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
                const [y, m] = k.tanggal_mulai.substring(0, 10).split('-').map(Number);
                return m - 1 === currentMonth.value && y === currentYear.value;
            })
            .map((k) => ({
                id: k.id,
                day: parseInt(k.tanggal_mulai.substring(8, 10), 10),
                title: k.nama_kegiatan,
                type: k.tipe_kegiatan ?? 'Kegiatan',
                time: k.jam_mulai ?? '-',
                location: k.deskripsi ?? '-',
                raw: k
            }));
        return [...fromDB].sort((a, b) => a.day - b.day);
    });

    // ── Filter state ──────────────────────────────────────────────────────────────
    const filteredEvents = computed(() => allEvents.value);

    // ── Day-click popup ───────────────────────────────────────────────────────────
    const showDayPopup = ref(false);
    const popupDay = ref(null);

    const popupEvents = computed(() => {
        if (!popupDay.value) return [];
        return allEvents.value.filter((e) => e.day === popupDay.value);
    });

    const popupDayLabel = computed(() => {
        if (!popupDay.value) return '';
        const date = new Date(currentYear.value, currentMonth.value, popupDay.value);
        return date.toLocaleDateString('id-ID', { weekday: 'long' }).toUpperCase();
    });

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

    // ── Modals logic (Admin) ──────────────────────────────────────────────────────
    const showModal = ref(false);
    const isEditing = ref(false);
    const editingId = ref(null);
    const showDeleteDialog = ref(false);
    const deletingId = ref(null);

    const form = useForm({
        nama_kegiatan: '',
        tanggal_mulai: '',
        jam_mulai: '',
        tanggal_selesai: '',
        deskripsi: '',
        status: 'Active',
    });

    function openCreate() {
        form.reset();
        form.clearErrors();
        isEditing.value = false;
        editingId.value = null;
        showModal.value = true;
    }

    function openEdit(item) {
        form.nama_kegiatan = item.raw.nama_kegiatan;
        form.tanggal_mulai = item.raw.tanggal_mulai ? item.raw.tanggal_mulai.substring(0, 10) : '';
        form.jam_mulai = item.raw.jam_mulai ? item.raw.jam_mulai.substring(0, 5) : '';
        form.tanggal_selesai = item.raw.tanggal_selesai ? item.raw.tanggal_selesai.substring(0, 10) : '';
        form.deskripsi = item.raw.deskripsi || '';
        form.clearErrors();
        isEditing.value = true;
        editingId.value = item.id;
        showDayPopup.value = false; // close popup
        showModal.value = true;
    }

    function closeModal() {
        showModal.value = false;
        form.reset();
    }

    function submitForm() {
        if (!form.tanggal_selesai) {
            form.tanggal_selesai = form.tanggal_mulai;
        }

        if (isEditing.value) {
            form.put(route('admin.kalender-akademik.update', editingId.value), {
                onSuccess: () => {
                    closeModal();
                    successMessage.value = 'Kegiatan berhasil diperbarui.';
                },
            });
        } else {
            form.post(route('admin.kalender-akademik.store'), {
                onSuccess: () => {
                    closeModal();
                    successMessage.value = 'Kegiatan berhasil ditambahkan.';
                },
            });
        }
    }

    function confirmDelete(id) {
        deletingId.value = id;
        showDayPopup.value = false; // close popup
        showDeleteDialog.value = true;
    }

    function doDelete() {
        router.delete(route('admin.kalender-akademik.destroy', deletingId.value), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deletingId.value = null;
                successMessage.value = 'Kegiatan berhasil dihapus.';
            },
        });
    }

    const navItems = [
        { label: 'Dashboard', icon: '💻', active: false },
        { label: 'Calendar', icon: '📅', active: true },
        { label: 'Events', icon: '📝', active: false },
        { label: 'Courses', icon: '📚', active: false },
        { label: 'Faculty', icon: '👥', active: false },
        { label: 'Settings', icon: '⚙️', active: false },
    ];
</script>

<template>
    <Head title="Kalender Akademik - Admin" />
    <StaffLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div
                class="rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 flex justify-between items-center flex-wrap gap-4"
            >
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-[#E9F0F8] text-[#1F4C7A] flex items-center justify-center font-bold text-xl">
                                    AD
                                </div>
                                <div>
                                    <h1 class="text-slate-900 font-bold text-lg leading-tight">Admin SIBIMA</h1>
                                    <p class="text-slate-500 text-sm mt-0.5">Administrator</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button class="inline-flex items-center gap-2 rounded-full bg-[#1F4C7A] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:bg-[#163a5e]" @click="openCreate">
                                    <span class="text-base leading-none">+</span> Tambah Jadwal
                                </button>
                            </div>
                        </div>

                        <!-- Lower Grid: Calendar + Agenda -->
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                            <!-- Calendar -->
                            <div class="xl:col-span-2 rounded-[32px] bg-white p-6 shadow-xl shadow-slate-900/5 h-max">
                                <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
                                    <div>
                                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ monthLabel }}</h3>
                                    </div>
                                    <div class="flex gap-2.5">
                                        <button class="p-2 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-blue-600 active:scale-95 transition-all flex items-center justify-center" title="Bulan Sebelumnya" @click="prevMonth">&lt;</button>
                                        <button class="p-2 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-blue-600 active:scale-95 transition-all flex items-center justify-center" title="Bulan Berikutnya" @click="nextMonth">&gt;</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-7 gap-2 md:gap-4 mb-4">
                                    <div v-for="d in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']" :key="d" class="text-center text-xs font-bold text-slate-400 uppercase tracking-wider py-2">
                                        {{ d }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-7 gap-2 md:gap-4">
                                    <template v-for="(day, idx) in monthDays" :key="idx">
                                        <div v-if="day.empty" class="h-14 md:h-18 text-transparent p-2"></div>
                                        <div v-else :class="[day.isSelected ? 'bg-blue-600 text-white shadow-xl shadow-blue-200 ring-2 ring-blue-500 ring-offset-2 scale-105 z-10 font-bold' : 'bg-slate-50/80 text-slate-700 hover:bg-blue-50 hover:text-blue-600 hover:scale-[1.03]']" class="h-14 md:h-18 rounded-2xl flex flex-col items-center justify-center relative group transition-all duration-250 cursor-pointer select-none" @click="clickDay(day)">
                                            <span class="text-sm md:text-base font-bold">{{ day.num }}</span>
                                            <div v-if="day.hasDot" :class="day.isSelected ? 'bg-white' : 'bg-blue-500'" class="w-1.5 h-1.5 rounded-full mt-1.5 transition-colors"/>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex gap-4 mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500">
                                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-50/80 ring-1 ring-slate-200" />Biasa</div>
                                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500" />Ada jadwal</div>
                                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600" />Dipilih</div>
                                </div>
                            </div>

                            <!-- Agenda -->
                            <div class="space-y-6 h-full">
                                <div class="bg-white p-6 md:p-8 rounded-[32px] border border-slate-100 shadow-sm flex flex-col h-full max-h-[800px]">
                                    <div class="flex justify-between items-center mb-6">
                                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                            Agenda Mendatang
                                        </h3>
                                    </div>
                                    <!-- Unified event list (filtered) -->
                                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 scrollbar-thin">
                                        <div 
                                            v-for="ev in filteredEvents" 
                                            :key="ev.id" 
                                            class="bg-white p-5 rounded-[24px] border border-slate-200 shadow-sm flex items-center gap-5 hover:border-blue-400 hover:shadow-md transition cursor-pointer" 
                                            @click="popupDay = ev.day; showDayPopup = true;"
                                        >
                                            <!-- Date block -->
                                            <div 
                                                class="rounded-[20px] flex flex-col justify-center items-center min-w-[76px] h-[80px] border shrink-0 bg-white"
                                                :class="(ev.type ?? '').toLowerCase().includes('bimbingan') ? 'border-fuchsia-200 shadow-sm shadow-fuchsia-100/50' : 'border-blue-200 shadow-sm shadow-blue-100/50'"
                                            >
                                                <span class="text-[11px] font-bold uppercase tracking-widest leading-none mb-1.5" :class="(ev.type ?? '').toLowerCase().includes('bimbingan') ? 'text-fuchsia-600' : 'text-blue-600'">{{ monthLabel.split(' ')[0].substring(0, 3) }}</span>
                                                <span class="text-3xl font-bold leading-none" :class="(ev.type ?? '').toLowerCase().includes('bimbingan') ? 'text-fuchsia-700' : 'text-blue-700'">{{ String(ev.day).padStart(2, '0') }}</span>
                                            </div>
                                            <!-- Detail -->
                                            <div class="flex-1 min-w-0 py-1">
                                                <div class="mb-2">
                                                    <span 
                                                        class="text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-widest inline-block"
                                                        :class="(ev.type ?? '').toLowerCase().includes('bimbingan') ? 'bg-fuchsia-50 text-fuchsia-600' : 'bg-blue-50 text-blue-600'"
                                                    >
                                                        {{ ev.type ?? 'Kegiatan' }}
                                                    </span>
                                                </div>
                                                <h4 class="font-bold text-slate-800 text-[17px] leading-snug mb-1 truncate">{{ ev.title }}</h4>
                                                <p class="text-sm text-slate-500 truncate">{{ ev.location || ev.deskripsi || ((ev.type ?? '').toLowerCase().includes('bimbingan') ? 'Konsultasi bimbingan' : 'Kegiatan akademik') }}</p>
                                            </div>
                                        </div>
                                        <div v-if="filteredEvents.length === 0" class="text-sm text-slate-500 text-center py-6 border-2 border-dashed border-slate-100 rounded-[24px]">
                                            Tidak ada jadwal di bulan ini.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div>

        <!-- Day Detail Popup -->
            <transition name="modal">
                <div v-if="showDayPopup" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm" @click.self="showDayPopup = false">
                    <div class="w-full max-w-xl bg-white rounded-[24px] shadow-2xl overflow-hidden flex flex-col">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
                            <div class="flex items-center gap-2 text-slate-500 font-bold text-sm tracking-wide">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                AGENDA KEGIATAN
                            </div>
                            <button
                                class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                                @click="showDayPopup = false"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Date Header -->
                        <div class="px-6 pt-6 pb-4 flex justify-between items-center shrink-0">
                            <h3 class="text-slate-900 font-bold text-lg uppercase tracking-wide">
                                {{ popupDayLabel }}
                            </h3>
                            <div class="bg-blue-50/50 text-blue-600 border border-blue-100 px-4 py-1.5 rounded-full text-sm font-semibold">
                                {{ popupDay }} {{ monthLabel }}
                            </div>
                        </div>

                        <!-- Event cards -->
                        <div class="px-6 pb-6 max-h-[60vh] overflow-y-auto scrollbar-thin space-y-4">
                            <p v-if="popupEvents.length === 0" class="py-8 text-center text-slate-400 text-sm border-2 border-dashed border-slate-100 rounded-2xl">
                                Tidak ada kegiatan pada hari ini.
                            </p>
                            <div
                                v-for="ev in popupEvents"
                                :key="ev.id"
                                class="bg-slate-50 border border-slate-100 rounded-[20px] p-5 hover:border-blue-100 transition-colors"
                            >
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex items-start gap-3">
                                        <span class="bg-blue-100/50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider shrink-0 mt-0.5 border border-blue-200/50">
                                            {{ ev.type }}
                                        </span>
                                        <h4 class="text-slate-900 font-bold text-lg leading-tight">
                                            {{ ev.title }}
                                        </h4>
                                    </div>
                                    <div class="flex gap-1 shrink-0">
                                        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-blue-600 hover:bg-blue-50 transition border border-blue-100 shadow-sm" title="Edit Event" @click="openEdit(ev)">✎</button>
                                        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-white text-rose-500 hover:bg-rose-50 transition border border-rose-100 shadow-sm" title="Delete Event" @click="confirmDelete(ev.id)">🗑</button>
                                    </div>
                                </div>
                                <p class="text-slate-500 text-sm mt-1 mb-4 leading-relaxed pl-[3.5rem] sm:pl-0" v-if="ev.deskripsi || true">
                                    {{ ev.deskripsi || 'Jadwal kegiatan akademik' }}
                                </p>
                                
                                <div class="border-t border-slate-200/60 pt-4 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                                    <div class="space-y-2.5">
                                        <div class="flex items-center gap-2.5 text-slate-600 text-sm font-medium">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>
                                            {{ ev.time !== '-' ? ev.time + ' WIB' : 'Sepanjang Hari' }}
                                        </div>
                                        <div class="flex items-center gap-2.5 text-slate-600 text-sm font-medium">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ ev.location && ev.location !== '-' ? ev.location : 'Portal SIBIMA' }}
                                        </div>
                                    </div>
                                    <div class="flex shrink-0">
                                        <a
                                            :href="'#'"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm w-full sm:w-auto justify-center"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                            Google Calendar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Actions -->
                        <div class="px-6 py-4 border-t border-slate-100 bg-white flex justify-end gap-3 shrink-0">
                            <button
                                class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors"
                                @click="showDayPopup = false"
                            >
                                Tutup
                            </button>
                            <button
                                class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition-colors shadow-sm"
                                @click="showDayPopup = false; openCreate(); form.tanggal_mulai = (new Date().getFullYear()) + '-' + String(month + 1).padStart(2, '0') + '-' + String(popupDay).padStart(2, '0')"
                            >
                                Tambah Jadwal
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Tambah/Edit Jadwal Modal -->
            <transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" @click.self="showModal = false">
                    <div class="w-full max-w-lg rounded-[32px] bg-white p-6 shadow-2xl">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">{{ isEditing ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h2>
                            </div>
                            <button class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 text-lg" @click="showModal = false">✕</button>
                        </div>

                        <form class="space-y-4" @submit.prevent="submitForm">
                            <!-- Tipe Kegiatan Removed -->

                            <!-- Judul / Nama -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Judul / Nama Kegiatan</label>
                                <input v-model="form.nama_kegiatan" type="text" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none" />
                            </div>

                            <!-- Tanggal & Jam -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Mulai</label>
                                    <input v-model="form.tanggal_mulai" type="date" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jam Mulai</label>
                                    <input v-model="form.jam_mulai" type="time" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none" />
                                </div>
                            </div>

                            <!-- Tanggal Selesai -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Selesai <span class="text-slate-400 font-normal">(opsional)</span></label>
                                <input v-model="form.tanggal_selesai" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none" />
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi / Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
                                <textarea v-model="form.deskripsi" rows="2" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1F4C7A] focus:ring-2 focus:ring-blue-100 outline-none" />
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" class="rounded-full bg-slate-100 px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200" @click="showModal = false">Batal</button>
                                <button type="submit" class="rounded-full px-6 py-2.5 text-sm font-semibold text-white transition bg-[#1F4C7A] hover:bg-[#163a5e]">{{ isEditing ? 'Simpan' : 'Tambah' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>

            <!-- Modal Delete -->
            <transition name="modal">
                <div v-if="showDeleteDialog" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm">
                    <div class="w-full max-w-sm rounded-[32px] bg-white p-6 shadow-2xl text-center">
                        <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-500 mx-auto flex items-center justify-center mb-4 text-3xl">🗑</div>
                        <h3 class="text-lg font-bold text-slate-900">Hapus Kegiatan?</h3>
                        <p class="mt-2 text-sm text-slate-500 mb-6">Apakah Anda yakin ingin menghapus kegiatan ini? Aksi ini tidak dapat dibatalkan.</p>
                        <div class="flex justify-center gap-3">
                            <button class="rounded-full bg-slate-100 px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200" @click="showDeleteDialog = false">Batal</button>
                            <button class="rounded-full bg-rose-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-rose-600 shadow-lg shadow-rose-900/20" @click="doDelete">Hapus</button>
                        </div>
                    </div>
                </div>
            </transition>
    </StaffLayout>
</template>

<style scoped>
    .modal-enter-active,
    .modal-leave-active {
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .modal-enter-from,
    .modal-leave-to {
        opacity: 0;
        transform: scale(0.97);
    }
</style>
