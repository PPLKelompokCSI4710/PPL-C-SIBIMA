<script setup>
    import { ref } from 'vue';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { Head, useForm, Link } from '@inertiajs/vue3';

    const props = defineProps({
        jadwal: {
            type: Object,
            required: true,
        },
        ketersediaanJadwals: {
            type: Array,
            default: () => [],
        },
    });

    const form = useForm({
        ketersediaan_jadwal_id: '',
        judul_ta: props.jadwal.judul_ta || '',
        topik_bimbingan: props.jadwal.topik_bimbingan || '',
    });

    const submit = () => {
        form.put(route('mahasiswa.jadwal.reschedule', props.jadwal.id), {
            onSuccess: () => {
                // Berhasil diarahkan kembali via controller
            },
        });
    };
</script>

<template>
    <Head title="Reschedule Jadwal Bimbingan" />

    <StudentLayout>
        <div class="py-6 bg-brand-bg relative min-h-screen overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-400/20 blur-3xl mix-blend-multiply" />
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-400/20 blur-3xl mix-blend-multiply" />

            <!-- Header Title -->
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 relative z-10 mb-8">
                <h1 class="text-3xl font-black text-brand-primary-dark tracking-tight">
                    Reschedule Bimbingan
                </h1>
                <p class="text-brand-text-secondary text-sm mt-1">
                    Silakan atur ulang jadwal bimbingan Anda dengan memilih slot kosong dosen pembimbing.
                </p>
            </div>

            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 relative z-10">
                <!-- Flash Message Error -->
                <div v-if="$page.props.flash?.error" class="mb-6 rounded-2xl bg-red-50 p-4 border border-red-100 shadow-sm transition-all">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 p-2 rounded-full">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-red-800">
                                {{ $page.props.flash.error }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-xl shadow-2xl rounded-3xl border border-white/60 overflow-hidden transition-all duration-500 hover:shadow-blue-500/10">
                    <div class="grid grid-cols-1 md:grid-cols-3">
                        <!-- Left Section: Dosen Info -->
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 text-white flex flex-col justify-between relative overflow-hidden">
                            <!-- Abstract pattern overlay -->
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent" />
                            
                            <div class="relative z-10">
                                <div class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold uppercase tracking-wider mb-6 border border-white/30">
                                    Informasi Dosen
                                </div>
                                <h3 class="text-2xl font-black mb-2 leading-tight">Dosen Pembimbing</h3>
                                <div class="mt-6 flex items-center space-x-4">
                                    <div class="w-14 h-14 rounded-full bg-white text-blue-600 flex items-center justify-center font-bold text-xl shadow-inner flex-shrink-0">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg text-brand-white">
                                            {{ jadwal.dosen?.nama_lengkap }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Context info -->
                            <div class="relative z-10 mt-8 pt-6 border-t border-white/20">
                                <p class="text-blue-100 text-sm font-medium">Jadwal Lama:</p>
                                <p class="font-bold text-white mt-1">
                                    {{ jadwal.ketersediaan_jadwal?.tanggal }}
                                </p>
                            </div>
                        </div>

                        <!-- Right Section: Form -->
                        <div class="p-8 md:col-span-2">
                            <form class="space-y-6" @submit.prevent="submit">
                                <!-- Jadwal Tersedia -->
                                <div>
                                    <label for="ketersediaan_jadwal_id" class="block text-sm font-bold text-brand-primary-dark mb-1">
                                        Pilih Jadwal Baru <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select
                                            id="ketersediaan_jadwal_id"
                                            v-model="form.ketersediaan_jadwal_id"
                                            class="block w-full rounded-xl border-brand-text-secondary/20 bg-brand-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm py-3 transition-colors disabled:bg-gray-100 disabled:text-gray-400"
                                            required
                                            :disabled="ketersediaanJadwals.length === 0"
                                        >
                                            <option value="" disabled>
                                                <template v-if="ketersediaanJadwals.length === 0">
                                                    Tidak ada jadwal pengganti tersedia (H+2)
                                                </template>
                                                <template v-else> -- Pilih Jadwal Tersedia -- </template>
                                            </option>
                                            <option
                                                v-for="schedule in ketersediaanJadwals"
                                                :key="schedule.id"
                                                :value="schedule.id"
                                                :disabled="schedule.kuota <= 0"
                                            >
                                                {{ schedule.tanggal }} |
                                                {{ schedule.waktu_mulai.substring(0, 5) }} -
                                                {{ schedule.waktu_selesai.substring(0, 5) }} (Sisa Kuota: {{ schedule.kuota }})
                                            </option>
                                        </select>
                                    </div>
                                    <p v-if="form.errors.ketersediaan_jadwal_id" class="mt-1 text-sm text-red-600 font-medium">
                                        {{ form.errors.ketersediaan_jadwal_id }}
                                    </p>
                                </div>

                                <!-- Judul TA (Readonly) -->
                                <div>
                                    <label for="judul_ta" class="block text-sm font-bold text-brand-primary-dark mb-1">
                                        Judul Skripsi / Tugas Akhir <span class="text-xs font-normal text-brand-text-secondary ml-1">(Terkunci)</span>
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="judul_ta"
                                            v-model="form.judul_ta"
                                            type="text"
                                            class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-500 shadow-sm sm:text-sm py-3 cursor-not-allowed select-none"
                                            disabled
                                            readonly
                                        />
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Topik Bimbingan -->
                                <div>
                                    <label for="topik_bimbingan" class="block text-sm font-bold text-brand-primary-dark mb-1">
                                        Topik Bimbingan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="topik_bimbingan"
                                        v-model="form.topik_bimbingan"
                                        rows="4"
                                        class="block w-full rounded-xl border-brand-text-secondary/20 bg-brand-white shadow-sm focus:border-brand-primary focus:ring-brand-primary sm:text-sm transition-colors"
                                        required
                                    />
                                    <p v-if="form.errors.topik_bimbingan" class="mt-1 text-sm text-red-600 font-medium">
                                        {{ form.errors.topik_bimbingan }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100 mt-8">
                                    <Link :href="route('mahasiswa.jadwal.riwayat-reschedule')" class="px-5 py-2.5 text-sm font-bold text-brand-text-secondary hover:text-brand-primary-dark transition-colors">
                                        Batal
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing || ketersediaanJadwals.length === 0"
                                        class="inline-flex items-center justify-center px-6 py-2.5 bg-brand-primary hover:bg-brand-primary-dark text-white rounded-xl font-bold shadow-lg shadow-brand-primary/30 hover:shadow-brand-primary/50 transform transition-all duration-300 hover:-translate-y-0.5 focus:ring-4 focus:ring-brand-primary/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none"
                                    >
                                        <svg
                                            v-if="form.processing"
                                            class="mr-2 h-4 w-4 animate-spin"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        {{ form.processing ? 'Menyimpan...' : 'Ajukan Reschedule' }}
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
