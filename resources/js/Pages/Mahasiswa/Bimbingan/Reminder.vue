<script setup>
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { ref, onMounted } from 'vue';

    // Data passed from BimbinganReminderController
    const props = defineProps({
        upcomingBimbingan: {
            type: Object,
            default: () => null,
        },
    });

    // Calculate countdown
    const daysLeft = ref(0);
    onMounted(() => {
        if (props.upcomingBimbingan && props.upcomingBimbingan.date) {
            const target = new Date(props.upcomingBimbingan.date);
            const today = new Date();
            const diffTime = Math.abs(target - today);
            daysLeft.value = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        }
    });
</script>

<template>
    <Head title="Reminder Bimbingan" />

    <StudentLayout>
        <div class="py-6 bg-brand-bg relative overflow-hidden">
            <!-- Decorative background elements -->
            <div
                class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-400/20 blur-3xl mix-blend-multiply"
            />
            <div
                class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-400/20 blur-3xl mix-blend-multiply"
            />

            <!-- Header Title -->
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 relative z-10 mb-8">
                <h1 class="text-3xl font-black text-brand-primary-dark tracking-tight">
                    Reminder Bimbingan
                </h1>
                <p class="text-brand-text-secondary text-sm mt-1">
                    Detail jadwal bimbingan Anda yang akan datang beserta preferensi pengingat
                    otomatis.
                </p>
            </div>

            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 relative z-10">
                <div
                    v-if="!props.upcomingBimbingan"
                    class="bg-brand-white p-10 rounded-3xl shadow-sm border border-brand-text-secondary/10 text-center"
                >
                    <h3 class="text-2xl font-black text-brand-primary-dark mb-2">
                        Belum ada jadwal bimbingan
                    </h3>
                    <p class="text-brand-text-secondary">
                        Jadwal bimbingan yang sudah disetujui akan muncul di sini dan akan
                        diingatkan otomatis (H-3, H-1, H-2 jam).
                    </p>
                </div>

                <!-- Main Card -->
                <div
                    v-if="props.upcomingBimbingan"
                    class="bg-white/80 backdrop-blur-xl shadow-2xl rounded-3xl border border-white/60 overflow-hidden transition-all duration-500 hover:shadow-blue-500/10"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3">
                        <!-- Left/Top Section: Highlight -->
                        <div
                            class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-white flex flex-col justify-between relative overflow-hidden"
                        >
                            <!-- Abstract pattern overlay -->
                            <div
                                class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"
                            />

                            <div class="relative z-10">
                                <div
                                    class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold uppercase tracking-wider mb-6 border border-white/30"
                                >
                                    {{ props.upcomingBimbingan.type }} Meeting
                                </div>
                                <h3 class="text-4xl font-black mb-2">{{ daysLeft }} Hari Lagi!</h3>
                                <p class="text-blue-100 text-lg font-medium">
                                    Bimbingan Selanjutnya
                                </p>
                            </div>

                            <div class="relative z-10 mt-10 md:mt-0">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-14 h-14 rounded-full bg-white text-blue-600 flex items-center justify-center font-bold text-xl shadow-inner"
                                    >
                                        <svg
                                            class="w-6 h-6"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-brand-secondary-light">
                                            Dosen Pembimbing
                                        </p>
                                        <p class="font-bold text-lg text-brand-white">
                                            {{ props.upcomingBimbingan.dosen }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right/Bottom Section: Details -->
                        <div class="p-8 md:col-span-2">
                            <h4
                                class="text-2xl font-bold text-brand-primary-dark mb-6 drop-shadow-sm"
                            >
                                {{ props.upcomingBimbingan.topic }}
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-brand-bg text-brand-primary flex items-center justify-center mr-4 flex-shrink-0 shadow-sm border border-brand-primary/10"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-brand-text-secondary uppercase tracking-wider"
                                        >
                                            Tanggal & Waktu
                                        </p>
                                        <p class="font-bold text-brand-text-primary mt-1">
                                            {{ props.upcomingBimbingan.dateFormatted }}
                                        </p>
                                        <p class="text-brand-text-secondary">
                                            {{ props.upcomingBimbingan.timeFormatted }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-brand-bg text-brand-secondary flex items-center justify-center mr-4 flex-shrink-0 shadow-sm border border-brand-secondary/10"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-brand-text-secondary uppercase tracking-wider"
                                        >
                                            Lokasi
                                        </p>
                                        <p class="font-bold text-brand-text-primary mt-1">
                                            {{ props.upcomingBimbingan.location }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-brand-accent-light/10 rounded-2xl p-6 border border-brand-accent-light/20"
                            >
                                <h5 class="text-brand-accent font-bold mb-3 flex items-center">
                                    <svg
                                        class="w-5 h-5 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                    Persiapan Anda
                                </h5>
                                <ul class="space-y-3">
                                    <li
                                        v-for="(note, index) in props.upcomingBimbingan
                                            .preparationNotes"
                                        :key="index"
                                        class="flex items-start"
                                    >
                                        <div
                                            class="mt-1 w-5 h-5 rounded-full bg-brand-accent text-white flex items-center justify-center flex-shrink-0 mr-3 text-xs font-bold shadow-sm"
                                        >
                                            {{ index + 1 }}
                                        </div>
                                        <span class="text-brand-text-primary font-medium">{{
                                            note
                                        }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mt-8 flex flex-col sm:flex-row items-center gap-4">
                                <button
                                    class="w-full sm:w-auto px-6 py-3 bg-brand-primary hover:bg-brand-primary-dark text-white rounded-xl font-bold shadow-lg shadow-brand-primary/30 hover:shadow-brand-primary/50 transform transition-all duration-300 hover:-translate-y-1 focus:ring-4 focus:ring-brand-primary/30"
                                >
                                    Siap Hadir
                                </button>
                                <Link
                                    v-if="props.upcomingBimbingan?.id"
                                    :href="route('mahasiswa.jadwal.edit-reschedule', props.upcomingBimbingan.id)"
                                    class="w-full sm:w-auto px-6 py-3 bg-brand-white hover:bg-brand-bg text-brand-text-primary rounded-xl font-bold font-medium shadow-sm border border-brand-text-secondary/20 transition-all duration-300 hover:text-brand-primary focus:ring-4 focus:ring-brand-bg flex items-center justify-center"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    Ajukan Reschedule
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips / Extra Card -->
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div
                        class="bg-brand-white p-6 rounded-2xl shadow-sm border border-brand-text-secondary/10 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-center mb-4">
                            <div class="bg-brand-success/10 p-2 rounded-lg text-brand-success mr-3">
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <h4 class="font-bold text-lg text-brand-primary-dark">
                                Tips Bimbingan Sukses
                            </h4>
                        </div>
                        <p class="text-brand-text-secondary leading-relaxed mb-4">
                            Selalu catat poin-poin revisi yang diberikan oleh dosen pembimbing.
                            Konsistensi dalam memperbaiki draft akan mempercepat proses penyelesaian
                            tugas akhir.
                        </p>
                    </div>

                    <div
                        class="bg-brand-white p-6 rounded-2xl shadow-sm border border-brand-secondary/10 hover:shadow-md transition-shadow flex items-center justify-between"
                    >
                        <div>
                            <h4 class="font-bold text-lg text-brand-primary-dark mb-1">
                                Butuh Dokumen Pendukung?
                            </h4>
                            <p class="text-brand-text-secondary text-sm">
                                Unduh template logbook & form bimbingan.
                            </p>
                        </div>
                        <button
                            class="px-4 py-2 bg-brand-secondary/10 text-brand-secondary hover:bg-brand-secondary/20 rounded-lg font-bold transition-colors"
                        >
                            Unduh Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
