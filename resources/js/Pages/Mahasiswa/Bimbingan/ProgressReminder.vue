<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    progressData: {
        type: Object,
        default: () => ({
            daysSinceLastBimbingan: 0,
            status: 'Good',
            lastBimbinganDate: '-',
        }),
    },
    reminderSettings: {
        type: Object,
        default: () => ({
            inactive_threshold_days: 14,
            frequency: 'biweekly',
            frequency_days: 14,
            enabled: true,
        }),
    },
});

const isEditing = ref(false);

const form = useForm({
    frequency: props.reminderSettings.frequency,
    custom_days: props.reminderSettings.frequency === 'custom' ? props.reminderSettings.frequency_days : 14,
    enabled: props.reminderSettings.enabled,
});

const submitSettings = () => {
    form.post(route('mahasiswa.bimbingan.progress_reminder.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const getStatusDetails = () => {
    if (props.progressData.status === 'Warning') {
        return {
            color: 'text-brand-accent',
            bg: 'bg-brand-accent/10',
            border: 'border-brand-accent/20',
            gradient: 'bg-brand-accent',
            iconColor: 'bg-brand-accent/10 text-brand-accent',
            title: 'Perhatian!',
            message: `Sudah ${props.progressData.daysSinceLastBimbingan} Hari Anda Belum Bimbingan`,
            subText: 'Dosen pembimbing akan menerima notifikasi tembusan.',
        };
    }
    return {
        color: 'text-brand-success',
        bg: 'bg-brand-success/10',
        border: 'border-brand-success/20',
        gradient: 'bg-brand-success',
        iconColor: 'bg-brand-success/10 text-brand-success',
        title: 'Progres Sangat Baik!',
        message: 'Bimbingan Anda Berjalan Rutin',
        subText: 'Pertahankan kedisiplinan ini untuk lulus tepat waktu.',
    };
};

const statusObj = getStatusDetails();
</script>

<template>
    <Head title="Progress Akademik" />

    <StudentLayout>
        <div class="py-6 bg-brand-bg relative overflow-hidden">
            <!-- Decorative background elements -->
            <div
                class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-brand-secondary/20 blur-3xl mix-blend-multiply"
            />
            <div
                class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-brand-primary/20 blur-3xl mix-blend-multiply"
            />

            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 relative z-10">
                <!-- Status Card -->
                <div
                    class="bg-brand-white/90 backdrop-blur-xl shadow-xl rounded-3xl border border-white/60 overflow-hidden transition-all duration-500 hover:shadow-brand-primary/10 mb-8"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3">
                        <div
                            :class="`${statusObj.gradient} p-8 text-white flex flex-col justify-between relative overflow-hidden`"
                        >
                            <div
                                class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"
                            />

                            <div class="relative z-10">
                                <div
                                    class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold uppercase tracking-wider mb-6 border border-white/30 text-white"
                                >
                                    Status Saat Ini
                                </div>
                                <h3 class="text-3xl font-black mb-2">
                                    {{ statusObj.title }}
                                </h3>
                                <p class="text-white/90 text-lg font-medium">
                                    {{ statusObj.subText }}
                                </p>
                            </div>
                        </div>

                        <div class="p-8 md:col-span-2 flex flex-col justify-center bg-brand-white">
                            <h4
                                class="text-2xl font-bold text-brand-primary-dark mb-6 drop-shadow-sm"
                            >
                                {{ statusObj.message }}
                            </h4>

                            <div class="flex items-start mb-4">
                                <div
                                    :class="`w-12 h-12 rounded-xl flex items-center justify-center mr-5 flex-shrink-0 shadow-sm border ${statusObj.iconColor}`"
                                >
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
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-semibold text-brand-text-secondary uppercase tracking-wider"
                                    >
                                        Terakhir Kali Bimbingan
                                    </p>
                                    <p class="font-bold text-brand-text-primary mt-1 text-xl">
                                        {{ props.progressData.lastBimbinganDate }}
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="props.progressData.status === 'Warning'"
                                class="mt-6 flex gap-4"
                            >
                                <Link
                                    :href="route('mahasiswa.bimbingan.reminder')"
                                    class="inline-block px-6 py-3 bg-brand-accent hover:bg-brand-accent-light text-white rounded-xl font-bold font-medium shadow-md transition-all duration-300 hover:-translate-y-1 hover:text-white"
                                >
                                    Ajukan Jadwal Bimbingan Sekarang
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Card -->
                <div
                    class="bg-brand-white p-8 rounded-3xl shadow-sm border border-brand-text-secondary/10 transition-all hover:shadow-md"
                >
                    <div
                        class="flex items-center justify-between mb-6 border-b border-brand-text-secondary/10 pb-4"
                    >
                        <div class="flex items-center">
                            <div class="bg-brand-primary/10 p-3 rounded-xl text-brand-primary mr-4">
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
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl text-brand-primary-dark">
                                    Konfigurasi Reminder Progres
                                </h3>
                                <p class="text-brand-text-secondary text-sm mt-1">
                                    Atur frekuensi peringatan jika Anda tidak melakukan bimbingan.
                                </p>
                            </div>
                        </div>
                        <button
                            v-if="!isEditing"
                            class="px-4 py-2 text-brand-primary font-semibold hover:bg-brand-primary/10 rounded-lg transition-colors border border-brand-primary/20"
                            @click="isEditing = true"
                        >
                            Edit Pengaturan
                        </button>
                    </div>

                    <form class="space-y-6" @submit.prevent="submitSettings">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-brand-bg p-6 rounded-2xl border border-brand-text-secondary/10"
                            >
                                <label class="block text-sm font-bold text-brand-text-primary mb-3">
                                    Frekuensi Reminder
                                </label>
                                <select
                                    v-model="form.frequency"
                                    :disabled="!isEditing"
                                    class="w-full bg-brand-white border border-brand-text-secondary/30 text-brand-text-primary text-base rounded-lg focus:ring-brand-primary focus:border-brand-primary block p-3 disabled:bg-gray-100 disabled:text-gray-500 rounded-lg"
                                >
                                    <option value="2_days">Setiap 2 Hari</option>
                                    <option value="3_days">Setiap 3 Hari</option>
                                    <option value="weekly">Mingguan (7 Hari)</option>
                                    <option value="biweekly">Dua Mingguan (14 Hari)</option>
                                    <option value="custom">Kustom (Pilih Hari)</option>
                                </select>

                                <div v-if="form.frequency === 'custom'" class="mt-4">
                                    <label class="block text-sm font-bold text-brand-text-primary mb-2">
                                        Jumlah Hari Kustom
                                    </label>
                                    <input
                                        v-model="form.custom_days"
                                        type="number"
                                        min="1"
                                        max="365"
                                        :disabled="!isEditing"
                                        class="w-full bg-brand-white border border-brand-text-secondary/30 text-brand-text-primary text-base rounded-lg focus:ring-brand-primary focus:border-brand-primary block p-3 disabled:bg-gray-100 disabled:text-gray-500"
                                    />
                                    <div
                                        v-if="form.errors.custom_days"
                                        class="text-brand-accent text-sm mt-1"
                                    >
                                        {{ form.errors.custom_days }}
                                    </div>
                                </div>

                                <p class="text-xs text-brand-text-secondary mt-2">
                                    Reminder dikirim berkala setiap
                                    <strong>
                                        {{ form.frequency === 'custom' ? form.custom_days : (form.frequency === '2_days' ? 2 : (form.frequency === '3_days' ? 3 : (form.frequency === 'weekly' ? 7 : 14))) }} hari
                                    </strong>, tetapi hanya jika Anda melewati ambang tidak bimbingan selama
                                    <strong>{{ props.reminderSettings.inactive_threshold_days }} hari</strong>.
                                </p>
                                <div
                                    v-if="form.errors.frequency"
                                    class="text-brand-accent text-sm mt-1"
                                >
                                    {{ form.errors.frequency }}
                                </div>
                            </div>

                            <div
                                class="bg-brand-bg p-6 rounded-2xl border border-brand-text-secondary/10 flex flex-col justify-center"
                            >
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input
                                            v-model="form.enabled"
                                            type="checkbox"
                                            :disabled="!isEditing"
                                            class="sr-only"
                                        />
                                        <div
                                            :class="`block w-14 h-8 rounded-full transition-colors ${form.enabled ? 'bg-brand-primary' : 'bg-gray-300'}`"
                                        />
                                        <div
                                            :class="`dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform ${form.enabled ? 'transform translate-x-6' : ''}`"
                                        />
                                    </div>
                                    <div class="ml-4 font-bold text-brand-text-primary">
                                        Aktifkan Pengiriman Reminder Otomatis
                                    </div>
                                </label>
                                <div
                                    v-if="form.errors.enabled"
                                    class="text-brand-accent text-sm mt-1"
                                >
                                    {{ form.errors.enabled }}
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="isEditing"
                            class="flex justify-end gap-3 mt-4 pt-4 border-t border-brand-text-secondary/10"
                        >
                            <button
                                type="button"
                                class="px-5 py-2.5 text-brand-text-secondary font-semibold hover:bg-brand-text-secondary/10 rounded-xl transition-colors border border-brand-text-secondary/20"
                                @click="isEditing = false"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-5 py-2.5 bg-brand-primary text-white font-semibold hover:bg-brand-primary-dark rounded-xl transition-colors shadow-sm disabled:opacity-50 flex items-center"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    />
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    />
                                </svg>
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<style scoped>
    /* Optional styling */
</style>
