<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    progressData: {
        type: Object,
        default: () => ({
            daysSinceLastBimbingan: 0,
            status: 'Good',
            lastBimbinganDate: '-',
        })
    },
    reminderSettings: {
        type: Object,
        default: () => ({
            frequency_days: 14,
            enabled: true,
        })
    }
});

const isEditing = ref(false);

const form = useForm({
    frequency_days: props.reminderSettings.frequency_days,
    enabled: props.reminderSettings.enabled,
});

const submitSettings = () => {
    form.post(route('mahasiswa.bimbingan.progress_reminder.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        }
    });
};

const getStatusDetails = () => {
    if (props.progressData.status === 'Warning') {
        return {
            color: 'text-rose-600',
            bg: 'bg-rose-50',
            border: 'border-rose-100',
            gradient: 'from-rose-600 to-red-700',
            iconColor: 'bg-rose-100 text-rose-600',
            title: 'Perhatian!',
            message: `Sudah ${props.progressData.daysSinceLastBimbingan} Hari Anda Belum Bimbingan`,
            subText: 'Dosen pembimbing telah menerima notifikasi tembusan.',
        };
    }
    return {
        color: 'text-emerald-600',
        bg: 'bg-emerald-50',
        border: 'border-emerald-100',
        gradient: 'from-emerald-500 to-teal-600',
        iconColor: 'bg-emerald-100 text-emerald-600',
        title: 'Progres Sangat Baik!',
        message: 'Bimbingan Anda Berjalan Rutin',
        subText: 'Pertahankan kedisiplinan ini untuk lulus tepat waktu.',
    };
};

const statusObj = getStatusDetails();
</script>

<template>
    <Head title="Progress Akademik" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black leading-tight tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 drop-shadow-sm">
                    Monitoring Progres Akademik
                </h2>
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800 ring-1 ring-indigo-400">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    PBI #34 Progress
                </span>
            </div>
        </template>

        <div class="py-12 min-h-screen bg-slate-50 relative overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-indigo-400/20 blur-3xl mix-blend-multiply"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-400/20 blur-3xl mix-blend-multiply"></div>

            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 relative z-10">
                <!-- Status Card -->
                <div class="bg-white/90 backdrop-blur-xl shadow-xl rounded-3xl border border-white/60 overflow-hidden transition-all duration-500 hover:shadow-indigo-500/10 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3">
                        <div :class="`bg-gradient-to-br ${statusObj.gradient} p-8 text-white flex flex-col justify-between relative overflow-hidden`">
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
                            
                            <div class="relative z-10">
                                <div class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold uppercase tracking-wider mb-6 border border-white/30">
                                    Status Saat Ini
                                </div>
                                <h3 class="text-3xl font-black mb-2">{{ statusObj.title }}</h3>
                                <p class="text-white/80 text-lg font-medium">{{ statusObj.subText }}</p>
                            </div>
                        </div>

                        <div class="p-8 md:col-span-2 flex flex-col justify-center">
                            <h4 class="text-2xl font-bold text-gray-800 mb-6 drop-shadow-sm">{{ statusObj.message }}</h4>
                            
                            <div class="flex items-start mb-4">
                                <div :class="`w-12 h-12 rounded-xl flex items-center justify-center mr-5 flex-shrink-0 shadow-sm border ${statusObj.iconColor}`">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Terakhir Kali Bimbingan</p>
                                    <p class="font-bold text-gray-900 mt-1 text-xl">{{ props.progressData.lastBimbinganDate }}</p>
                                </div>
                            </div>

                            <div v-if="props.progressData.status === 'Warning'" class="mt-6 flex gap-4">
                                <button class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl font-bold font-medium shadow-md transition-all duration-300 hover:-translate-y-1">
                                    Ajukan Jadwal Bimbingan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Card -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl text-gray-800">Konfigurasi Reminder Progres</h3>
                                <p class="text-gray-500 text-sm mt-1">Atur frekuensi peringatan jika Anda tidak melakukan bimbingan.</p>
                            </div>
                        </div>
                        <button v-if="!isEditing" @click="isEditing = true" class="px-4 py-2 text-indigo-600 font-semibold hover:bg-indigo-50 rounded-lg transition-colors border border-indigo-100">
                            Edit Pengaturan
                        </button>
                    </div>

                    <form @submit.prevent="submitSettings" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Frekuensi Reminder (Hari)</label>
                                <div class="flex items-center">
                                    <input 
                                        type="number" 
                                        v-model="form.frequency_days" 
                                        :disabled="!isEditing"
                                        class="w-full bg-white border border-gray-300 text-gray-900 text-base rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 disabled:bg-gray-100 disabled:text-gray-500" 
                                    />
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Default rekomendasi adalah 14 hari (2 Minggu).</p>
                                <div v-if="form.errors.frequency_days" class="text-red-500 text-sm mt-1">{{ form.errors.frequency_days }}</div>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 flex flex-col justify-center">
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <input 
                                            type="checkbox" 
                                            v-model="form.enabled" 
                                            :disabled="!isEditing"
                                            class="sr-only" 
                                        />
                                        <div :class="`block w-14 h-8 rounded-full transition-colors ${form.enabled ? 'bg-indigo-500' : 'bg-gray-300'}`"></div>
                                        <div :class="`dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform ${form.enabled ? 'transform translate-x-6' : ''}`"></div>
                                    </div>
                                    <div class="ml-4 font-bold text-gray-700">
                                        Aktifkan Pengiriman Reminder Otomatis
                                    </div>
                                </label>
                                <div v-if="form.errors.enabled" class="text-red-500 text-sm mt-1">{{ form.errors.enabled }}</div>
                            </div>
                        </div>

                        <div v-if="isEditing" class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-100">
                            <button @click="isEditing = false" type="button" class="px-5 py-2.5 text-gray-600 font-semibold hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                                Batal
                            </button>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-indigo-600 text-white font-semibold hover:bg-indigo-700 rounded-xl transition-colors shadow-sm disabled:opacity-50 flex items-center">
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Optional styling */
</style>
