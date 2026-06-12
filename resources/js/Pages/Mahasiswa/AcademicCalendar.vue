<template>
    <StudentLayout>
        <Head title="Academic Calendar - SIBIMA" />

        <div class="space-y-6">
            <!-- Back Button and Page Navigation Header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <Link
                    :href="route('mahasiswa.dashboard')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:text-blue-600 hover:border-blue-200 shadow-sm transition-all hover:-translate-x-1"
                >
                    <ArrowLeftIcon class="w-4 h-4" />
                    <span>Kembali ke Dashboard</span>
                </Link>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Tahun Akademik 2025/2026
                    </p>
                </div>
            </div>

            <!-- Month Grid and Interactive Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Section: Interactive Calendar -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Calendar Card -->
                    <div
                        class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm transition-all"
                    >
                        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
                            <div>
                                <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                                    {{ currentMonthName }} {{ currentYear }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Klik tanggal untuk melihat kegiatan akademik dalam pop-up modal
                                </p>
                            </div>



                            <div class="flex gap-2.5">
                                <button
                                    class="p-2 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-blue-600 active:scale-95 transition-all"
                                    title="Bulan Sebelumnya"
                                    @click="prevMonth"
                                >
                                    <ChevronLeftIcon class="w-5 h-5" />
                                </button>
                                <button
                                    class="p-2 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-blue-600 active:scale-95 transition-all"
                                    title="Bulan Berikutnya"
                                    @click="nextMonth"
                                >
                                    <ChevronRightIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Days of Week Headers -->
                        <div class="grid grid-cols-7 gap-2 md:gap-4 mb-4">
                            <div
                                v-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']"
                                :key="day"
                                class="text-center text-xs font-bold text-slate-400 uppercase tracking-wider py-2"
                            >
                                {{ day }}
                            </div>
                        </div>

                        <!-- Calendar Days Grid -->
                        <div class="grid grid-cols-7 gap-2 md:gap-4">
                            <div
                                v-for="(dayItem, idx) in calendarDays"
                                :key="idx"
                                :class="[
                                    !dayItem.isCurrentMonth
                                        ? 'text-slate-300 bg-slate-50/30 cursor-not-allowed pointer-events-none'
                                        : dayItem.day === selectedDay
                                          ? 'bg-blue-600 text-white shadow-xl shadow-blue-200 ring-2 ring-blue-500 ring-offset-2 scale-105 z-10 font-bold'
                                          : 'bg-slate-50/80 text-slate-700 hover:bg-blue-50 hover:text-blue-600 hover:scale-[1.03]',
                                ]"
                                class="h-14 md:h-18 rounded-2xl flex flex-col items-center justify-center relative group transition-all duration-250 cursor-pointer select-none"
                                @click="selectDay(dayItem)"
                            >
                                <span class="text-sm md:text-base font-bold">{{
                                    dayItem.day
                                }}</span>

                                <!-- Dot indicator for event -->
                                <div
                                    v-if="
                                        getDayEvents(dayItem.day, dayItem.isCurrentMonth).length > 0
                                    "
                                    :class="
                                        dayItem.day === selectedDay ? 'bg-white' : 'bg-blue-500'
                                    "
                                    class="w-1.5 h-1.5 rounded-full mt-1.5 transition-colors"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Upcoming Agenda with Custom Scrolling -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <BellIcon class="w-5 h-5 text-blue-500" />
                                <span>Agenda Mendatang</span>
                            </h3>
                            <span
                                class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md"
                            >
                                {{ upcomingEvents.length }} Kegiatan
                            </span>
                        </div>

                        <!-- Scrollable events list (Scrolling if events > 5) -->
                        <div
                            :class="[
                                upcomingEvents.length > 5
                                    ? 'max-h-[460px] overflow-y-auto pr-2 custom-scrollbar'
                                    : '',
                            ]"
                            class="space-y-3.5"
                        >
                            <div
                                v-for="(event, idx) in upcomingEvents"
                                :key="idx"
                                :class="[
                                    event.day === selectedDay &&
                                    event.month === currentDate.getMonth()
                                        ? 'border-blue-500 bg-blue-50/40 ring-1 ring-blue-100 scale-[0.99]'
                                        : 'border-slate-200 hover:border-blue-400 hover:shadow-md',
                                ]"
                                class="bg-white p-5 rounded-[24px] border shadow-sm flex items-center gap-5 transition cursor-pointer group"
                                @click="goToEventDate(event)"
                            >
                                <!-- Date block -->
                                <div 
                                    class="rounded-[20px] flex flex-col justify-center items-center min-w-[76px] h-[80px] border shrink-0 bg-white"
                                    :class="(event.badge || '').toLowerCase().includes('bimbingan') ? 'border-fuchsia-200 shadow-sm shadow-fuchsia-100/50' : 'border-blue-200 shadow-sm shadow-blue-100/50'"
                                >
                                    <span class="text-[11px] font-bold uppercase tracking-widest leading-none mb-1.5" :class="(event.badge || '').toLowerCase().includes('bimbingan') ? 'text-fuchsia-600' : 'text-blue-600'">{{ monthShortNames[event.month] }}</span>
                                    <span class="text-3xl font-bold leading-none" :class="(event.badge || '').toLowerCase().includes('bimbingan') ? 'text-fuchsia-700' : 'text-blue-700'">{{ String(event.day).padStart(2, '0') }}</span>
                                </div>
                                <!-- Detail -->
                                <div class="flex-1 min-w-0 py-1">
                                    <div class="mb-2">
                                        <span 
                                            class="text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-widest inline-block"
                                            :class="(event.badge || '').toLowerCase().includes('bimbingan') ? 'bg-fuchsia-50 text-fuchsia-600' : 'bg-blue-50 text-blue-600'"
                                        >
                                            {{ event.badge || 'Kegiatan' }}
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-[17px] leading-snug mb-1 truncate">{{ event.title }}</h4>
                                    <p class="text-sm text-slate-500 truncate">{{ event.desc || ((event.badge || '').toLowerCase().includes('bimbingan') ? 'Konsultasi bimbingan' : 'Kegiatan akademik') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Popup Modal -->
        <Transition
            enter-active-class="transition ease-out duration-250"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isDetailModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                    @click="isDetailModalOpen = false"
                />

                <!-- Modal Content -->
                <div
                    class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all z-10 flex flex-col"
                >
                    <!-- Modal Header -->
                    <div
                        class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between"
                    >
                        <div class="flex items-center gap-2 text-slate-800">
                            <CalendarIcon class="w-5 h-5 text-blue-600" />
                            <h4 class="font-bold text-sm uppercase tracking-wider text-slate-500">
                                Agenda Kegiatan
                            </h4>
                        </div>
                        <button
                            class="p-1.5 rounded-full hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition"
                            @click="isDetailModalOpen = false"
                        >
                            <XIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 overflow-y-auto max-h-[70vh] space-y-4">
                        <!-- Selected Date Banner -->
                        <div
                            class="flex items-center justify-between pb-2 border-b border-slate-100"
                        >
                            <span class="text-sm font-bold text-slate-800">
                                {{ popupDayLabel }}
                            </span>
                            <span
                                class="text-xs font-semibold bg-blue-50 text-blue-700 px-3 py-1 rounded-full border border-blue-100"
                            >
                                {{
                                    formatDateString(
                                        selectedDay,
                                        currentDate.getMonth(),
                                        currentDate.getFullYear(),
                                    )
                                }}
                            </span>
                        </div>

                        <!-- Switch between Events List and Booking Form -->
                        <div v-if="!showBookingForm" class="space-y-4">
                            <!-- Events List -->
                            <div v-if="selectedDateEvents.length > 0" class="space-y-4 pt-2">
                                <div
                                    v-for="(event, eIdx) in selectedDateEvents"
                                    :key="eIdx"
                                    class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 flex flex-col gap-3.5"
                                >
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="px-2 py-0.5 text-[9px] font-black tracking-wider uppercase rounded"
                                                :class="event.color"
                                            >
                                                {{ event.badge }}
                                            </span>
                                            <h5
                                                class="text-base font-black text-slate-800 leading-snug"
                                            >
                                                {{ event.title }}
                                            </h5>
                                        </div>
                                        <p
                                            class="text-sm text-slate-600 font-medium leading-relaxed"
                                        >
                                            {{ event.desc }}
                                        </p>
                                    </div>

                                    <!-- Time, Location & Google Sync Button -->
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-200/60 text-xs text-slate-500"
                                    >
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-2">
                                                <ClockIcon class="w-4 h-4 text-blue-500 shrink-0" />
                                                <span class="font-semibold">{{ event.time }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <MapPinIcon
                                                    class="w-4 h-4 text-emerald-500 shrink-0"
                                                />
                                                <span
                                                    class="font-semibold truncate max-w-[200px]"
                                                    :title="event.location"
                                                    >{{ event.location }}</span
                                                >
                                            </div>
                                        </div>

                                        <!-- Google Calendar Button -->
                                        <a
                                            :href="getGoogleCalendarUrl(event)"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-[#1a73e8] hover:bg-[#1557b0] text-white font-bold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 active:scale-95 text-center justify-center shrink-0"
                                        >
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"
                                                />
                                            </svg>
                                            <span>Google Calendar</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div
                                v-else
                                class="py-10 text-center flex flex-col items-center justify-center text-slate-400"
                            >
                                <CalendarIcon class="w-16 h-16 text-slate-200 mb-3 stroke-[1.2]" />
                                <p class="text-base font-black text-slate-600">
                                    Tidak ada kegiatan terjadwal
                                </p>
                                <p
                                    class="text-xs text-slate-400 mt-1 max-w-[250px] mx-auto leading-relaxed"
                                >
                                    Hari ini bebas dari kegiatan akademik. Silakan cek tanggal
                                    lainnya.
                                </p>
                            </div>


                        </div>

                        <!-- Advisement Booking Form -->
                        <div v-else class="space-y-4 pt-2">
                            <div class="flex items-center justify-between">
                                <h5
                                    class="text-sm font-bold text-slate-800 uppercase tracking-wider"
                                >
                                    Form Pengajuan Bimbingan
                                </h5>
                                <button
                                    class="text-xs text-blue-600 hover:text-blue-800 font-bold"
                                    @click="showBookingForm = false"
                                >
                                    ← Kembali
                                </button>
                            </div>

                            <form class="space-y-4" @submit.prevent="submitBimbinganBooking">
                                <!-- Dosen Selection -->
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2"
                                        >Dosen Pembimbing</label
                                    >
                                    <select
                                        v-model="bookingForm.dosen_id"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        required
                                    >
                                        <option value="" disabled>Pilih Dosen Pembimbing</option>
                                        <option v-for="d in dosenList" :key="d.id" :value="d.id">
                                            {{ d.nama_lengkap }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Time Slot Selection -->
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2"
                                        >Pilih Slot Waktu</label
                                    >
                                    <select
                                        v-model="bookingForm.ketersediaan_jadwal_id"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50"
                                        :disabled="
                                            !bookingForm.dosen_id ||
                                            isLoadingSchedules ||
                                            availableSchedules.length === 0
                                        "
                                        required
                                    >
                                        <option value="" disabled>
                                            <template v-if="isLoadingSchedules">
                                                Memuat jadwal...
                                            </template>
                                            <template v-else-if="!bookingForm.dosen_id">
                                                Pilih dosen terlebih dahulu
                                            </template>
                                            <template v-else-if="availableSchedules.length === 0">
                                                Tidak ada jadwal tersedia di tanggal ini
                                            </template>
                                            <template v-else> Pilih Slot Tersedia </template>
                                        </option>
                                        <option
                                            v-for="s in availableSchedules"
                                            :key="s.id"
                                            :value="s.id"
                                            :disabled="s.has_clash || s.kuota <= 0"
                                        >
                                            {{ s.waktu_mulai.substring(0, 5) }} -
                                            {{ s.waktu_selesai.substring(0, 5) }}
                                            {{
                                                s.has_clash
                                                    ? ' (Dosen Sedang Ada Kegiatan)'
                                                    : ` (Sisa Kuota: ${s.kuota})`
                                            }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Warning Clash Pop-up Alert -->
                                <div
                                    v-if="selectedSlotHasClash"
                                    class="px-4 py-3 bg-[#FFF9EA] border border-[#FDE68A] rounded-2xl text-sm text-[#92400E] font-medium flex items-center gap-2"
                                >
                                    <svg class="w-5 h-5 text-[#D97706] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Maaf dosen sedang ada kegiatan, mohon pilih jadwal lain.</span>
                                </div>

                                <!-- Judul TA -->
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2"
                                        >Judul Tugas Akhir / Skripsi</label
                                    >
                                    <input
                                        v-model="bookingForm.judul_ta"
                                        type="text"
                                        placeholder="Masukkan judul skripsi..."
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        required
                                    />
                                </div>

                                <!-- Topik Bimbingan -->
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2"
                                        >Topik Bahasan</label
                                    >
                                    <textarea
                                        v-model="bookingForm.topik_bimbingan"
                                        rows="3"
                                        placeholder="Tuliskan topik yang ingin dikonsultasikan..."
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        required
                                    />
                                </div>

                                <div class="flex justify-end gap-3 pt-2">
                                    <button
                                        type="button"
                                        class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-200 transition active:scale-95"
                                        @click="showBookingForm = false"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-sm transition active:scale-95 disabled:opacity-50"
                                        :disabled="selectedSlotHasClash || bookingForm.processing"
                                    >
                                        {{
                                            bookingForm.processing
                                                ? 'Mengirim...'
                                                : 'Kirim Pengajuan'
                                        }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button
                            class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition active:scale-95"
                            @click="
                                isDetailModalOpen = false;
                                showBookingForm = false;
                            "
                        >
                            Tutup
                        </button>
                        <button
                            v-if="!showBookingForm"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition active:scale-95 shadow-sm"
                            @click="initBookingForm"
                        >
                            Ajukan Jadwal
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </StudentLayout>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import {
        ChevronLeftIcon,
        ChevronRightIcon,
        BellIcon,
        ArrowLeftIcon,
        MapPinIcon,
        ClockIcon,
        CalendarIcon,
        XIcon,
    } from 'lucide-vue-next';

    import { usePage, useForm } from '@inertiajs/vue3';
    import axios from 'axios';
    import { watch } from 'vue';

    const props = defineProps({
        auth: { type: Object, default: () => ({}) },
        kalender: { type: Array, default: () => [] },
        requests: { type: Array, default: () => [] },
        dosenList: { type: Array, default: () => [] },
    });

    // Calendar States
    const today = new Date();
    const currentDate = ref(new Date(today.getFullYear(), today.getMonth(), 1)); // Default starts on current month
    const selectedDay = ref(today.getDate()); // Default active date (selected date: today)
    const isDetailModalOpen = ref(false); // Modal visibility state

    const page = usePage();
    const isGoogleConnected = computed(() => !!page.props.auth.user?.google_access_token);

    // Advisement Booking form inside calendar popup
    const showBookingForm = ref(false);
    const bookingForm = useForm({
        dosen_id: '',
        ketersediaan_jadwal_id: '',
        judul_ta: '',
        topik_bimbingan: '',
    });

    const schedulesList = ref([]);
    const isLoadingSchedules = ref(false);

    watch(
        () => bookingForm.dosen_id,
        async (newDosenId) => {
            bookingForm.ketersediaan_jadwal_id = '';
            schedulesList.value = [];
            if (!newDosenId) return;

            isLoadingSchedules.value = true;
            try {
                const timestamp = new Date().getTime();
                const response = await axios.get(
                    route('mahasiswa.jadwal-bimbingan.schedules', newDosenId) + '?t=' + timestamp,
                );
                schedulesList.value = response.data;
            } catch (error) {
                console.error('Failed to load schedules', error);
            } finally {
                isLoadingSchedules.value = false;
            }
        },
    );

    // Filter available schedules to matches selected calendar date
    const availableSchedules = computed(() => {
        if (!selectedDay.value || schedulesList.value.length === 0) return [];
        const pad = (n) => String(n).padStart(2, '0');
        const selectedDateStr = `${currentDate.value.getFullYear()}-${pad(currentDate.value.getMonth() + 1)}-${pad(selectedDay.value)}`;
        return schedulesList.value.filter((s) => s.tanggal === selectedDateStr);
    });

    const selectedSlotHasClash = computed(() => {
        if (bookingForm.ketersediaan_jadwal_id) {
            const selectedSlot = availableSchedules.value.find(
                (s) => s.id === bookingForm.ketersediaan_jadwal_id,
            );
            return selectedSlot ? !!selectedSlot.has_clash : false;
        }

        if (bookingForm.dosen_id && availableSchedules.value.length > 0) {
            const hasClash = availableSchedules.value.every(s => s.has_clash);
            console.log('Clash check for dosen:', bookingForm.dosen_id, 'Result:', hasClash);
            return hasClash;
        }
        
        return false;
    });

    const initBookingForm = () => {
        showBookingForm.value = true;
        bookingForm.reset();
    };

    const submitBimbinganBooking = () => {
        bookingForm.post(route('mahasiswa.jadwal-bimbingan.store'), {
            onSuccess: () => {
                showBookingForm.value = false;
                isDetailModalOpen.value = false;
                bookingForm.reset();
            },
        });
    };

    const monthNames = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    const monthShortNames = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agt',
        'Sep',
        'Okt',
        'Nov',
        'Des',
    ];

    const currentMonthName = computed(() => monthNames[currentDate.value.getMonth()]);
    const currentYear = computed(() => currentDate.value.getFullYear());

    // Navigation Months
    const prevMonth = () => {
        currentDate.value = new Date(
            currentDate.value.getFullYear(),
            currentDate.value.getMonth() - 1,
            1,
        );
        selectedDay.value = 1;
    };

    const nextMonth = () => {
        currentDate.value = new Date(
            currentDate.value.getFullYear(),
            currentDate.value.getMonth() + 1,
            1,
        );
        selectedDay.value = 1;
    };

    // Full List of Academic Calendar Events (load from DB kalender, falling back to mock if empty)
    const events = computed(() => {
        if (props.kalender && props.kalender.length > 0) {
            return props.kalender.map((k) => {
                const date = new Date(k.tanggal_mulai);
                const type = (k.tipe_kegiatan || 'kegiatan').toLowerCase();
                let color = 'bg-blue-50 text-blue-700 border-blue-200';
                if (type === 'kuliah') color = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                if (type === 'rapat') color = 'bg-amber-50 text-amber-700 border-amber-200';
                if (type === 'bimbingan') color = 'bg-purple-50 text-purple-700 border-purple-200';
                if (type === 'asistensi') color = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                if (type === 'libur') color = 'bg-rose-50 text-rose-700 border-rose-200';

                return {
                    id: k.id,
                    day: date.getDate(),
                    month: date.getMonth(),
                    year: date.getFullYear(),
                    title: k.nama_kegiatan,
                    desc: k.deskripsi || '',
                    time: k.jam_mulai ? k.jam_mulai.substring(0, 5) + ' WIB' : 'Seharian penuh',
                    location:
                        k.deskripsi && k.deskripsi.includes('(Lokasi: ')
                            ? k.deskripsi.split('(Lokasi: ')[1].replace(')', '')
                            : 'Portal SIBIMA',
                    color: color,
                    badge: k.tipe_kegiatan ? k.tipe_kegiatan.toUpperCase() : 'KEGIATAN',
                };
            });
        }

        // Fallback to original mock events if no database events exist
        return [
            {
                day: 3,
                month: 4, // Mei (0-indexed)
                year: 2026,
                title: 'Batas Akhir Revisi KRS',
                desc: 'Batas akhir untuk melakukan revisi rencana studi. Pastikan KRS sudah disetujui Dosen Pembimbing Akademik.',
                time: '23:59 WIB',
                location: 'Portal Online SIBIMA',
                color: 'bg-blue-50 text-blue-700 border-blue-200',
                badge: 'KRS',
            },
            {
                day: 10,
                month: 4,
                year: 2026,
                title: 'Awal Perkuliahan Genap',
                desc: 'Hari pertama perkuliahan Semester Genap dimulai. Mahasiswa wajib hadir di kelas masing-masing.',
                time: '08:00 - 16:00 WIB',
                location: 'Gedung A, B & C Ruang Teori',
                color: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                badge: 'KULIAH',
            },
            {
                day: 12,
                month: 4,
                year: 2026,
                title: 'Kelas Metodologi Penelitian',
                desc: 'Pertemuan perdana asistensi proposal tugas akhir/skripsi dan pembagian kelompok dosen pembimbing.',
                time: '10:00 - 12:00 WIB',
                location: 'Gedung C R.302 Fasilkom',
                color: 'bg-indigo-50 text-indigo-700 border-indigo-200',
                badge: 'ASISTENSI',
            },
            {
                day: 15,
                month: 4,
                year: 2026,
                title: 'Dies Natalis Universitas',
                desc: 'Hari libur akademik memperingati hari jadi Universitas SIBIMA yang ke-45. Kegiatan perkuliahan ditiadakan.',
                time: 'Seharian penuh',
                location: 'Kampus Pusat SIBIMA',
                color: 'bg-purple-50 text-purple-700 border-purple-200',
                badge: 'LIBUR',
            },
            {
                day: 20,
                month: 4,
                year: 2026,
                title: 'Bimbingan Akademik Tengah Semester',
                desc: 'Konsultasi wajib perkembangan kemajuan studi dan evaluasi nilai tengah semester bersama Dosen Wali.',
                time: '13:00 - 15:00 WIB',
                location: 'Ruang Rapat Gedung D R.104',
                color: 'bg-teal-50 text-teal-700 border-teal-200',
                badge: 'BIMBINGAN',
            },
            {
                day: 24,
                month: 4,
                year: 2026,
                title: 'Pengumpulan Tugas Mandiri 1',
                desc: 'Batas akhir pengunggahan tugas mandiri pertama untuk mata kuliah keahlian di sistem e-learning.',
                time: '17:00 WIB',
                location: 'E-Learning Center SIBIMA',
                color: 'bg-amber-50 text-amber-700 border-amber-200',
                badge: 'TUGAS',
            },
            {
                day: 28,
                month: 4,
                year: 2026,
                title: 'Webinar Publikasi Karya Ilmiah',
                desc: 'Seminar nasional penulisan karya tulis ilmiah untuk persiapan kelulusan mahasiswa tingkat akhir.',
                time: '09:00 - 12:00 WIB',
                location: 'Zoom Webinar Online',
                color: 'bg-rose-50 text-rose-700 border-rose-200',
                badge: 'SEMINAR',
            },
        ];
    });

    // Format Full Date String Indonesian Style
    const formatDateString = (day, month, year) => {
        return `${day} ${monthNames[month]} ${year}`;
    };

    // Filter events for the currently selected date
    const selectedDateEvents = computed(() => {
        return events.value.filter(
            (e) =>
                e.day === selectedDay.value &&
                e.month === currentDate.value.getMonth() &&
                e.year === currentDate.value.getFullYear(),
        );
    });

    // Returns the day-of-week label for the popup header
    const popupDayLabel = computed(() => {
        if (!selectedDay.value) return '';
        const date = new Date(
            currentDate.value.getFullYear(),
            currentDate.value.getMonth(),
            selectedDay.value,
        );
        return date.toLocaleDateString('id-ID', { weekday: 'long' }).toUpperCase();
    });

    // Dynamic generation of calendar grid days
    const calendarDays = computed(() => {
        const year = currentDate.value.getFullYear();
        const month = currentDate.value.getMonth();

        // 1st Day of month starting weekday index (0 = Sun, 1 = Mon, etc.)
        const firstDayIndex = new Date(year, month, 1).getDay();

        // Days in current month
        const totalDays = new Date(year, month + 1, 0).getDate();

        // Days in previous month
        const prevMonthTotalDays = new Date(year, month, 0).getDate();

        const days = [];

        // Add previous month's ending trailing days
        for (let i = firstDayIndex - 1; i >= 0; i--) {
            days.push({
                day: prevMonthTotalDays - i,
                isCurrentMonth: false,
                dateObject: new Date(year, month - 1, prevMonthTotalDays - i),
            });
        }

        // Add current month's days
        for (let i = 1; i <= totalDays; i++) {
            days.push({
                day: i,
                isCurrentMonth: true,
                dateObject: new Date(year, month, i),
            });
        }

        // Add next month's starting leading days to fit complete grid (6 rows = 42 slots)
        const remainingSlots = 42 - days.length;
        for (let i = 1; i <= remainingSlots; i++) {
            days.push({
                day: i,
                isCurrentMonth: false,
                dateObject: new Date(year, month + 1, i),
            });
        }

        return days;
    });

    // Filter day events helper
    const getDayEvents = (day, isCurrentMonth) => {
        if (!isCurrentMonth) return [];
        return events.value.filter(
            (e) =>
                e.day === day &&
                e.month === currentDate.value.getMonth() &&
                e.year === currentDate.value.getFullYear(),
        );
    };

    // User Selects a day on the calendar, opens popup detail modal
    const selectDay = (dayItem) => {
        if (dayItem.isCurrentMonth) {
            selectedDay.value = dayItem.day;
            isDetailModalOpen.value = true;
        } else {
            // Smoothly navigate month if clicking next/prev month days
            currentDate.value = new Date(
                dayItem.dateObject.getFullYear(),
                dayItem.dateObject.getMonth(),
                1,
            );
            selectedDay.value = dayItem.day;
            isDetailModalOpen.value = true;
        }
    };

    // User Clicks on an upcoming agenda card, jumps to that date and opens modal
    const goToEventDate = (event) => {
        currentDate.value = new Date(event.year, event.month, 1);
        selectedDay.value = event.day;
        isDetailModalOpen.value = true;
    };

    // Google Calendar Sync URL Generator
    const getGoogleCalendarUrl = (event) => {
        const title = encodeURIComponent(event.title);
        const details = encodeURIComponent(event.desc || '');
        const location = encodeURIComponent(event.location || 'SIBIMA - Universitas');

        const pad = (num) => String(num).padStart(2, '0');
        const yStr = String(event.year);
        const mStr = pad(event.month + 1);
        const dStr = pad(event.day);
        let startStr = `${yStr}${mStr}${dStr}`;
        let endStr = startStr;

        if (event.time) {
            const timeMatch = event.time.match(/(\d{2})[.:](\d{2})/);
            if (timeMatch) {
                const startHour = timeMatch[1];
                const startMin = timeMatch[2];
                startStr += `T${startHour}${startMin}00`;

                // default end hour is start hour + 1
                const endHour = pad(Number(startHour) + 1);
                endStr += `T${endHour}${startMin}00`;
            }
        }

        return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${startStr}/${endStr}`;
    };

    // Sorted list of upcoming events (in chronological order)
    const upcomingEvents = computed(() => {
        return [...events.value].sort((a, b) => {
            const dateA = new Date(a.year, a.month, a.day);
            const dateB = new Date(b.year, b.month, b.day);
            return dateA - dateB;
        });
    });
</script>

<style>
    /* Styling Scrollbar kustom yang elegan */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
