<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/vue3';

    const props = defineProps({
        settings: { type: Object, required: true },
    });

    const form = useForm({
        progress_reminder_inactive_days: props.settings.progress_reminder_inactive_days,
        escalation_reminder_threshold: props.settings.escalation_reminder_threshold,
    });

    const submit = () => {
        form.post(route('admin.settings.reminders.update'), { preserveScroll: true });
    };
</script>

<template>
    <Head title="Konfigurasi Reminder (Admin)" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2
                    class="text-2xl font-black leading-tight tracking-tight text-brand-primary-dark"
                >
                    Konfigurasi Reminder
                </h2>
                <span
                    class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-brand-secondary/10 text-brand-secondary ring-1 ring-brand-secondary/30"
                >
                    Admin
                </span>
            </div>
        </template>

        <div class="py-10 min-h-screen bg-brand-bg">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div
                    class="bg-brand-white p-8 rounded-3xl shadow-sm border border-brand-text-secondary/10"
                >
                    <h3 class="text-xl font-bold text-brand-primary-dark mb-2">
                        Reminder Progres & Target Akademik
                    </h3>
                    <p class="text-brand-text-secondary text-sm mb-6">
                        Atur ambang deteksi mahasiswa yang tidak bimbingan selama N hari.
                    </p>

                    <form class="space-y-6" @submit.prevent="submit">
                        <div
                            class="bg-brand-bg p-6 rounded-2xl border border-brand-text-secondary/10"
                        >
                            <label class="block text-sm font-bold text-brand-text-primary mb-3">
                                Ambang Tidak Bimbingan (Hari)
                            </label>
                            <input
                                v-model="form.progress_reminder_inactive_days"
                                type="number"
                                min="1"
                                max="365"
                                class="w-full bg-brand-white border border-brand-text-secondary/30 text-brand-text-primary text-base rounded-lg focus:ring-brand-primary focus:border-brand-primary block p-3"
                            />
                            <div
                                v-if="form.errors.progress_reminder_inactive_days"
                                class="text-brand-accent text-sm mt-2"
                            >
                                {{ form.errors.progress_reminder_inactive_days }}
                            </div>
                        </div>

                        <div
                            class="bg-brand-bg p-6 rounded-2xl border border-brand-text-secondary/10"
                        >
                            <label class="block text-sm font-bold text-brand-text-primary mb-3">
                                Ambang Eskalasi (Jumlah Reminder Progres Berturut-turut)
                            </label>
                            <p class="text-brand-text-secondary text-sm mb-3">
                                Setelah mahasiswa menerima reminder progres sebanyak N kali
                                berturut-turut tanpa booking baru, sistem membuat eskalasi dan
                                mengirim notifikasi ke admin.
                            </p>
                            <input
                                v-model="form.escalation_reminder_threshold"
                                type="number"
                                min="1"
                                max="50"
                                class="w-full bg-brand-white border border-brand-text-secondary/30 text-brand-text-primary text-base rounded-lg focus:ring-brand-primary focus:border-brand-primary block p-3"
                            />
                            <div
                                v-if="form.errors.escalation_reminder_threshold"
                                class="text-brand-accent text-sm mt-2"
                            >
                                {{ form.errors.escalation_reminder_threshold }}
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-5 py-2.5 bg-brand-primary text-white font-semibold hover:bg-brand-primary-dark rounded-xl transition-colors shadow-sm disabled:opacity-50"
                            >
                                Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
