<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

// ── Props ──────────────────────────────────────────────────────────────────
const props = defineProps({
    upcomingBimbingan: { type: Object, default: () => null },
    schedulePreferences: {
        type: Object,
        default: () => ({
            schedule_reminder_enabled: true,
            stage_h3_enabled: true,
            stage_h1_enabled: true,
            stage_h2_enabled: true,
        }),
    },
    progressData: {
        type: Object,
        default: () => ({
            daysSinceLastBimbingan: 0,
            status: 'Good',
            lastBimbinganDate: '-',
        }),
    },
    progressSettings: {
        type: Object,
        default: () => ({
            inactive_threshold_days: 14,
            frequency: 'biweekly',
            frequency_days: 14,
            enabled: true,
        }),
    },
});

// ── Countdown ──────────────────────────────────────────────────────────────
const daysLeft  = ref(null);
const hoursLeft = ref(null);
onMounted(() => {
    if (props.upcomingBimbingan?.date) {
        const diffMs = new Date(props.upcomingBimbingan.date) - new Date();
        if (diffMs > 0) {
            daysLeft.value  = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            hoursLeft.value = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        } else {
            daysLeft.value  = 0;
            hoursLeft.value = 0;
        }
    }
});

const countdownText = computed(() => {
    if (daysLeft.value === null) return '-';
    if (daysLeft.value > 0)  return `${daysLeft.value} hari lagi`;
    if (hoursLeft.value > 0) return `${hoursLeft.value} jam lagi`;
    return 'Hari ini';
});

// ── Schedule Preferences Form (PBI 32 – AC 32.3) ──────────────────────────
const schedForm = useForm({
    schedule_reminder_enabled: props.schedulePreferences.schedule_reminder_enabled,
    stage_h3_enabled:          props.schedulePreferences.stage_h3_enabled,
    stage_h1_enabled:          props.schedulePreferences.stage_h1_enabled,
    stage_h2_enabled:          props.schedulePreferences.stage_h2_enabled,
});

const isEditingSchedule = ref(false);

const submitSchedulePrefs = () => {
    schedForm.post(route('mahasiswa.bimbingan.reminder.schedule'), {
        preserveScroll: true,
        onSuccess: () => { isEditingSchedule.value = false; },
    });
};

// ── Progress Settings Form (PBI 33 – AC 33.4) ─────────────────────────────
const progForm = useForm({
    frequency:   props.progressSettings.frequency,
    custom_days: props.progressSettings.frequency === 'custom' ? props.progressSettings.frequency_days : 14,
    enabled:     props.progressSettings.enabled,
});

const isEditingProgress = ref(false);

const submitProgressSettings = () => {
    progForm.post(route('mahasiswa.bimbingan.reminder.progress'), {
        preserveScroll: true,
        onSuccess: () => { isEditingProgress.value = false; },
    });
};

// ── Progress status helpers ────────────────────────────────────────────────
const isWarning = computed(() => props.progressData.status === 'Warning');

const frequencyLabel = computed(() => {
    const map = {
        '2_days':   'Setiap 2 hari',
        '3_days':   'Setiap 3 hari',
        'weekly':   'Mingguan',
        'biweekly': 'Dua mingguan',
        'custom':   `Setiap ${props.progressSettings.frequency_days} hari`,
    };
    return map[props.progressSettings.frequency] ?? '-';
});
</script>

<template>
    <Head title="Reminder" />

    <StudentLayout>
        <div class="min-h-screen bg-gray-50 pb-10">

            <!-- ── Page Header ──────────────────────────────────────────────── -->
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <h1 class="text-xl font-semibold text-gray-900">Reminder</h1>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Kelola pengingat jadwal bimbingan dan reminder progres akademik Anda di sini.
                    </p>
                </div>
            </div>

            <!-- ── Content ─────────────────────────────────────────────────── -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    
                    <!-- ════════════════════════════════════════════════════════════
                         KOLOM KIRI: REMINDER JADWAL
                    ════════════════════════════════════════════════════════════ -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Reminder Jadwal</h2>
                        
                        <!-- No schedule state -->
                        <div
                            v-if="!props.upcomingBimbingan"
                            class="bg-white rounded-xl border border-gray-200 p-8 text-center"
                        >
                            <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">Belum ada jadwal bimbingan</h3>
                            <p class="text-sm text-gray-500 max-w-xs mx-auto">
                                Jadwal yang sudah disetujui akan tampil di sini beserta informasi reminder otomatisnya.
                            </p>
                        </div>

                        <!-- Upcoming schedule card -->
                        <div v-if="props.upcomingBimbingan" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="h-0.5 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                            <div class="p-5">
                                <!-- Header row -->
                                <div class="flex items-start justify-between gap-4 mb-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                                Disetujui
                                            </span>
                                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                                                {{ props.upcomingBimbingan.type }}
                                            </span>
                                        </div>
                                        <h2 class="text-base font-semibold text-gray-900 leading-snug">
                                            {{ props.upcomingBimbingan.topic }}
                                        </h2>
                                    </div>
                                    <div class="text-right flex-shrink-0 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                                        <p class="text-[10px] text-blue-500 font-medium uppercase tracking-wider mb-0.5">Countdown</p>
                                        <p class="text-sm font-bold text-blue-700">{{ countdownText }}</p>
                                    </div>
                                </div>

                                <!-- Detail grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Dosen Pembimbing</p>
                                            <p class="text-gray-800 text-sm font-medium">{{ props.upcomingBimbingan.dosen }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Waktu</p>
                                            <p class="text-gray-800 text-sm font-medium">{{ props.upcomingBimbingan.dateFormatted }}</p>
                                            <p class="text-gray-500 text-xs">{{ props.upcomingBimbingan.timeFormatted }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div class="col-span-1 sm:col-span-2">
                                            <p class="text-[11px] text-gray-400 font-medium">Lokasi / Tautan</p>
                                            <p class="text-gray-800 text-sm font-medium truncate" :title="props.upcomingBimbingan.location">{{ props.upcomingBimbingan.location }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="props.upcomingBimbingan.preparationNotes?.length" class="mt-4 pt-3 border-t border-gray-50">
                                    <p class="text-[11px] text-gray-400 font-medium mb-1">Checklist Persiapan</p>
                                    <ul class="space-y-1">
                                        <li
                                            v-for="(note, i) in props.upcomingBimbingan.preparationNotes"
                                            :key="i"
                                            class="text-gray-700 text-xs flex items-start gap-1.5"
                                        >
                                            <span class="text-blue-500 mt-0.5">•</span>
                                            <span>{{ note }}</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Download Logbook -->
                                <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Template Logbook</p>
                                        <p class="text-[11px] text-gray-500">Unduh form logbook bimbingan (Word).</p>
                                    </div>
                                    <a
                                        :href="route('mahasiswa.jadwal.exportPdf')"
                                        class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-600 text-white text-xs font-medium hover:bg-blue-700 transition-colors"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Unduh Logbook
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Reminder Preferences -->
                        <div class="bg-white rounded-xl border border-gray-200">
                            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">Preferensi Reminder Jadwal</h3>
                                </div>
                                <button
                                    v-if="!isEditingSchedule"
                                    class="text-xs font-medium text-blue-600 hover:text-blue-700 px-2.5 py-1 rounded bg-blue-50 hover:bg-blue-100 transition-colors"
                                    @click="isEditingSchedule = true"
                                >
                                    Edit
                                </button>
                            </div>

                            <form class="p-5 space-y-4" @submit.prevent="submitSchedulePrefs">
                                <!-- Master toggle -->
                                <div class="flex items-center justify-between py-1">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Aktifkan reminder</p>
                                        <p class="text-[11px] text-gray-500">Kirim email otomatis sebelum jadwal.</p>
                                    </div>
                                    <button
                                        type="button"
                                        :disabled="!isEditingSchedule"
                                        :class="[
                                            'relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none',
                                            schedForm.schedule_reminder_enabled ? 'bg-blue-600' : 'bg-gray-200',
                                            !isEditingSchedule && 'opacity-60 cursor-not-allowed',
                                        ]"
                                        @click="isEditingSchedule && (schedForm.schedule_reminder_enabled = !schedForm.schedule_reminder_enabled)"
                                    >
                                        <span
                                            :class="[
                                                'inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform',
                                                schedForm.schedule_reminder_enabled ? 'translate-x-4' : 'translate-x-1',
                                            ]"
                                        />
                                    </button>
                                </div>

                                <!-- Stage toggles -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div
                                        v-for="stage in [
                                            { key: 'stage_h3_enabled', label: 'H-3', desc: '3 hari' },
                                            { key: 'stage_h1_enabled', label: 'H-1', desc: '1 hari' },
                                            { key: 'stage_h2_enabled', label: 'H-2 Jam', desc: '2 jam' },
                                        ]"
                                        :key="stage.key"
                                        :class="[
                                            'rounded border p-3 transition-colors',
                                            schedForm[stage.key] && schedForm.schedule_reminder_enabled
                                                ? 'bg-blue-50 border-blue-200'
                                                : 'bg-gray-50 border-gray-200',
                                            !schedForm.schedule_reminder_enabled && 'opacity-50',
                                        ]"
                                    >
                                        <div class="flex items-start justify-between mb-1.5">
                                            <p :class="['text-xs font-semibold', schedForm[stage.key] && schedForm.schedule_reminder_enabled ? 'text-blue-800' : 'text-gray-600']">
                                                {{ stage.label }}
                                            </p>
                                            <button
                                                type="button"
                                                :disabled="!isEditingSchedule || !schedForm.schedule_reminder_enabled"
                                                :class="[
                                                    'relative inline-flex h-4 w-7 items-center rounded-full transition-colors focus:outline-none flex-shrink-0',
                                                    schedForm[stage.key] ? 'bg-blue-600' : 'bg-gray-300',
                                                    (!isEditingSchedule || !schedForm.schedule_reminder_enabled) && 'cursor-not-allowed',
                                                ]"
                                                @click="isEditingSchedule && schedForm.schedule_reminder_enabled && (schedForm[stage.key] = !schedForm[stage.key])"
                                            >
                                                <span
                                                    :class="[
                                                        'inline-block h-2.5 w-2.5 transform rounded-full bg-white shadow transition-transform',
                                                        schedForm[stage.key] ? 'translate-x-3.5' : 'translate-x-0.5',
                                                    ]"
                                                />
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-gray-500">{{ stage.desc }}</p>
                                    </div>
                                </div>

                                <!-- Save/Cancel -->
                                <div v-if="isEditingSchedule" class="flex justify-end gap-2 pt-3 border-t border-gray-100 mt-2">
                                    <button
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium text-gray-600 rounded bg-gray-100 hover:bg-gray-200 transition-colors"
                                        @click="isEditingSchedule = false"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="schedForm.processing"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 transition-colors disabled:opacity-50"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>


                    <!-- ════════════════════════════════════════════════════════════
                         KOLOM KANAN: REMINDER PROGRES
                    ════════════════════════════════════════════════════════════ -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Reminder Progres</h2>
                        
                        <!-- Status Card -->
                        <div
                            :class="[
                                'bg-white rounded-xl border overflow-hidden',
                                isWarning ? 'border-amber-200' : 'border-green-200',
                            ]"
                        >
                            <div :class="['h-0.5', isWarning ? 'bg-amber-400' : 'bg-green-500']"></div>
                            <div class="p-5 flex items-start gap-4">
                                <!-- Status icon -->
                                <div
                                    :class="[
                                        'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 mt-1',
                                        isWarning ? 'bg-amber-50 border border-amber-200' : 'bg-green-50 border border-green-200',
                                    ]"
                                >
                                    <svg
                                        :class="['w-5 h-5', isWarning ? 'text-amber-500' : 'text-green-500']"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            :class="[
                                                'text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full',
                                                isWarning ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700',
                                            ]"
                                        >
                                            {{ isWarning ? 'Perhatian' : 'Status Aktif' }}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-0.5">
                                        {{ isWarning
                                            ? `${props.progressData.daysSinceLastBimbingan} hari tanpa bimbingan`
                                            : 'Bimbingan berjalan rutin'
                                        }}
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        Bimbingan terakhir: <span class="font-medium text-gray-700">{{ props.progressData.lastBimbinganDate }}</span>
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-1.5 leading-relaxed">
                                        {{ isWarning
                                            ? 'Anda telah melewati batas waktu ideal. Segera jadwalkan bimbingan untuk menjaga progres kelulusan Anda.'
                                            : 'Pertahankan kedisiplinan ini untuk memastikan kelulusan tepat pada waktunya.'
                                        }}
                                    </p>

                                    <div v-if="isWarning" class="mt-3 pt-3 border-t border-amber-50">
                                        <Link
                                            :href="route('mahasiswa.jadwal-bimbingan.create')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-amber-500 text-white text-xs font-medium hover:bg-amber-600 transition-colors"
                                        >
                                            Ajukan Jadwal Bimbingan
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Reminder Settings -->
                        <div class="bg-white rounded-xl border border-gray-200">
                            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">Pengaturan Notifikasi Progres</h3>
                                </div>
                                <button
                                    v-if="!isEditingProgress"
                                    class="text-xs font-medium text-blue-600 hover:text-blue-700 px-2.5 py-1 rounded bg-blue-50 hover:bg-blue-100 transition-colors"
                                    @click="isEditingProgress = true"
                                >
                                    Edit
                                </button>
                            </div>

                            <form class="p-5 space-y-4" @submit.prevent="submitProgressSettings">
                                <div class="bg-blue-50/50 border border-blue-100 rounded-lg p-3 text-xs text-blue-800 mb-2">
                                    Reminder dikirim jika Anda tidak bimbingan lebih dari <strong>{{ props.progressSettings.inactive_threshold_days }} hari</strong>.
                                </div>

                                <!-- Enable toggle -->
                                <div class="flex items-center justify-between py-1">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Aktifkan notifikasi</p>
                                        <p class="text-[11px] text-gray-500">Kirim pemberitahuan ke email Anda &amp; dosen.</p>
                                    </div>
                                    <button
                                        type="button"
                                        :disabled="!isEditingProgress"
                                        :class="[
                                            'relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none',
                                            progForm.enabled ? 'bg-blue-600' : 'bg-gray-200',
                                            !isEditingProgress && 'opacity-60 cursor-not-allowed',
                                        ]"
                                        @click="isEditingProgress && (progForm.enabled = !progForm.enabled)"
                                    >
                                        <span
                                            :class="[
                                                'inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform',
                                                progForm.enabled ? 'translate-x-4' : 'translate-x-1',
                                            ]"
                                        />
                                    </button>
                                </div>

                                <!-- Frequency select -->
                                <div
                                    :class="[
                                        'rounded-lg border p-3',
                                        progForm.enabled ? 'border-gray-200 bg-white' : 'border-gray-100 bg-gray-50 opacity-50',
                                    ]"
                                >
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Frekuensi Pengiriman</label>
                                    <select
                                        v-model="progForm.frequency"
                                        :disabled="!isEditingProgress || !progForm.enabled"
                                        class="w-full text-sm border border-gray-300 rounded-md px-2.5 py-1.5 bg-white text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400 transition-colors"
                                    >
                                        <option value="2_days">Setiap 2 hari</option>
                                        <option value="3_days">Setiap 3 hari</option>
                                        <option value="weekly">Mingguan (7 hari)</option>
                                        <option value="biweekly">Dua mingguan (14 hari)</option>
                                        <option value="custom">Kustom</option>
                                    </select>

                                    <!-- Custom days input -->
                                    <div v-if="progForm.frequency === 'custom'" class="mt-2.5">
                                        <label class="block text-[11px] text-gray-500 mb-1">Jumlah hari</label>
                                        <input
                                            v-model="progForm.custom_days"
                                            type="number" min="1" max="365"
                                            :disabled="!isEditingProgress || !progForm.enabled"
                                            class="w-full text-sm border border-gray-300 rounded-md px-2.5 py-1.5 bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400"
                                            placeholder="Contoh: 10"
                                        />
                                        <p v-if="progForm.errors.custom_days" class="text-xs text-red-500 mt-1">
                                            {{ progForm.errors.custom_days }}
                                        </p>
                                    </div>

                                    <p class="text-[11px] text-gray-400 mt-2">
                                        Saat ini: <span class="font-medium text-gray-600">{{ frequencyLabel }}</span>
                                    </p>
                                    <p v-if="progForm.errors.frequency" class="text-xs text-red-500 mt-1">
                                        {{ progForm.errors.frequency }}
                                    </p>
                                </div>

                                <!-- Save/Cancel -->
                                <div v-if="isEditingProgress" class="flex justify-end gap-2 pt-3 border-t border-gray-100 mt-2">
                                    <button
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium text-gray-600 rounded bg-gray-100 hover:bg-gray-200 transition-colors"
                                        @click="isEditingProgress = false"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="progForm.processing"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 transition-colors disabled:opacity-50"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </StudentLayout>
</template>
