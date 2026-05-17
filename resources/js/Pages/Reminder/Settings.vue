<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/vue3';

    const props = defineProps({
        preferences: {
            type: Object,
            required: true,
        },
    });

    const form = useForm({
        schedule_reminder_enabled: props.preferences.schedule_reminder_enabled,
        stage_h3_enabled: props.preferences.stage_h3_enabled,
        stage_h1_enabled: props.preferences.stage_h1_enabled,
        stage_h2_enabled: props.preferences.stage_h2_enabled,
    });

    const submit = () => {
        form.post(route('reminders.update'), { preserveScroll: true });
    };
</script>

<template>
    <Head title="Pengaturan Reminder" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2
                    class="text-2xl font-black leading-tight tracking-tight text-brand-primary-dark"
                >
                    Pengaturan Reminder
                </h2>
            </div>
        </template>

        <div class="py-10 min-h-screen bg-brand-bg">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div
                    class="bg-brand-white p-8 rounded-3xl shadow-sm border border-brand-text-secondary/10"
                >
                    <h3 class="text-xl font-bold text-brand-primary-dark mb-2">
                        Reminder Jadwal Bimbingan Multi-Tahap
                    </h3>
                    <p class="text-brand-text-secondary text-sm mb-6">
                        Aktifkan/nonaktifkan tahap reminder (H-3, H-1, H-2 jam) sesuai preferensi
                        Anda.
                    </p>

                    <form class="space-y-6" @submit.prevent="submit">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                v-model="form.schedule_reminder_enabled"
                                type="checkbox"
                                class="rounded"
                            />
                            <span class="font-semibold text-brand-text-primary"
                                >Aktifkan reminder jadwal</span
                            >
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label
                                class="flex items-center gap-3 cursor-pointer bg-brand-bg p-4 rounded-2xl border border-brand-text-secondary/10"
                            >
                                <input
                                    v-model="form.stage_h3_enabled"
                                    :disabled="!form.schedule_reminder_enabled"
                                    type="checkbox"
                                    class="rounded"
                                />
                                <span class="font-semibold text-brand-text-primary">H-3</span>
                            </label>
                            <label
                                class="flex items-center gap-3 cursor-pointer bg-brand-bg p-4 rounded-2xl border border-brand-text-secondary/10"
                            >
                                <input
                                    v-model="form.stage_h1_enabled"
                                    :disabled="!form.schedule_reminder_enabled"
                                    type="checkbox"
                                    class="rounded"
                                />
                                <span class="font-semibold text-brand-text-primary">H-1</span>
                            </label>
                            <label
                                class="flex items-center gap-3 cursor-pointer bg-brand-bg p-4 rounded-2xl border border-brand-text-secondary/10"
                            >
                                <input
                                    v-model="form.stage_h2_enabled"
                                    :disabled="!form.schedule_reminder_enabled"
                                    type="checkbox"
                                    class="rounded"
                                />
                                <span class="font-semibold text-brand-text-primary">H-2 jam</span>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-5 py-2.5 bg-brand-primary text-white font-semibold hover:bg-brand-primary-dark rounded-xl transition-colors shadow-sm disabled:opacity-50"
                            >
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
