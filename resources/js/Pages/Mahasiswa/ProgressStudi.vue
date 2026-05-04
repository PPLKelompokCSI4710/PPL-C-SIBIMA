<template>
    <StudentLayout>
        <Head title="Progress Studi - SIBIMA" />

        <div class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Left Col: Visualizations -->
                <div class="md:col-span-2 space-y-6">
                    <div
                        class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm relative overflow-hidden"
                    >
                        <div
                            class="absolute -right-16 -top-16 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-60"
                        />

                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div>
                                <h2 class="text-2xl font-bold text-slate-800">Academic Standing</h2>
                                <p class="text-slate-500 mt-1">
                                    Your overall progress towards graduation
                                </p>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-inner"
                            >
                                <AwardIcon class="w-7 h-7" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8 relative z-10">
                            <div>
                                <p
                                    class="text-sm font-medium text-slate-500 mb-2 uppercase tracking-wide"
                                >
                                    Cumulative GPA
                                </p>
                                <div class="flex items-end gap-2">
                                    <span class="text-5xl font-black text-slate-800">{{
                                        auth.progress?.ipk || '0.00'
                                    }}</span>
                                    <span class="text-lg font-medium text-slate-400 mb-1"
                                        >/ 4.00</span
                                    >
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-slate-500 mb-2 uppercase tracking-wide"
                                >
                                    Passed Courses
                                </p>
                                <div class="flex items-end gap-2">
                                    <span class="text-5xl font-black text-slate-800">{{
                                        auth.progress?.passed_courses || '0'
                                    }}</span>
                                    <span class="text-lg font-medium text-slate-400 mb-1"
                                        >courses</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 relative z-10">
                            <div class="flex justify-between items-end mb-3">
                                <p
                                    class="text-sm font-medium text-slate-500 uppercase tracking-wide"
                                >
                                    Total Credits Completed
                                </p>
                                <div class="text-right">
                                    <span class="text-3xl font-bold text-blue-600">{{
                                        auth.progress?.total_sks || 0
                                    }}</span>
                                    <span class="text-slate-500 font-medium ml-1">/ 144 SKS</span>
                                </div>
                            </div>
                            <div
                                class="w-full bg-slate-100 h-4 rounded-full overflow-hidden shadow-inner"
                            >
                                <div
                                    class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full rounded-full transition-all duration-1000 relative"
                                    :style="`width: ${progressPercentage}%`"
                                >
                                    <div
                                        class="absolute inset-0 bg-white/20 w-full h-full"
                                        style="
                                            background-image: linear-gradient(
                                                45deg,
                                                rgba(255, 255, 255, 0.15) 25%,
                                                transparent 25%,
                                                transparent 50%,
                                                rgba(255, 255, 255, 0.15) 50%,
                                                rgba(255, 255, 255, 0.15) 75%,
                                                transparent 75%,
                                                transparent
                                            );
                                            background-size: 1rem 1rem;
                                        "
                                    />
                                </div>
                            </div>
                            <p class="text-right text-xs font-bold text-slate-400 mt-2">
                                {{ progressPercentage }}% Completed
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Update Form -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <Edit3Icon class="w-5 h-5 text-blue-500" /> Update Progress
                        </h3>
                        <p class="text-sm text-slate-500 mt-1">Keep your records up to date.</p>
                    </div>

                    <form class="p-6 flex-1 flex flex-col" @submit.prevent="submitProgress">
                        <div class="space-y-5 flex-1">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5"
                                    >Current IPK <span class="text-red-500">*</span></label
                                >
                                <div class="relative">
                                    <input
                                        id="ipk"
                                        v-model="form.ipk"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="4"
                                        required
                                        class="w-full rounded-xl border-slate-300 border px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all bg-slate-50 focus:bg-white text-slate-800 font-medium"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5"
                                    >Total SKS Lulus <span class="text-red-500">*</span></label
                                >
                                <input
                                    id="total_sks"
                                    v-model="form.total_sks"
                                    type="number"
                                    min="0"
                                    max="144"
                                    required
                                    class="w-full rounded-xl border-slate-300 border px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all bg-slate-50 focus:bg-white text-slate-800 font-medium"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5"
                                    >Mata Kuliah Lulus</label
                                >
                                <input
                                    id="passed_courses"
                                    v-model="form.passed_courses"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-xl border-slate-300 border px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all bg-slate-50 focus:bg-white text-slate-800 font-medium"
                                />
                                <p class="text-xs text-slate-400 mt-1.5">
                                    Total number of courses you have passed
                                </p>
                            </div>
                        </div>

                        <div class="pt-6 mt-6 border-t border-slate-100">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3 px-4 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-blue-600/20 flex items-center justify-center gap-2"
                            >
                                <SaveIcon class="w-4 h-4" /> Simpan Perubahan
                            </button>

                            <Transition
                                enter-active-class="transition ease-out duration-300"
                                enter-from-class="opacity-0 translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-200"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <p
                                    v-if="form.recentlySuccessful"
                                    class="text-sm font-medium text-emerald-600 text-center mt-3 flex items-center justify-center gap-1.5"
                                >
                                    <CheckCircle2Icon class="w-4 h-4" /> Berhasil disimpan!
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
    import { computed } from 'vue';
    import { useForm, Head } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { AwardIcon, Edit3Icon, SaveIcon, CheckCircle2Icon } from 'lucide-vue-next';

    const props = defineProps({
        auth: { type: Object, default: () => ({}) },
    });

    const progressPercentage = computed(() => {
        const passedSks = props.auth.progress?.total_sks || 0;
        const p = Math.round((passedSks / 144) * 100);
        return p > 100 ? 100 : p;
    });

    const form = useForm({
        ipk: props.auth.progress?.ipk || 0,
        total_sks: props.auth.progress?.total_sks || 0,
        passed_courses: props.auth.progress?.passed_courses || 0,
    });

    const submitProgress = () => {
        form.put(route('mahasiswa.progress.update'), {
            preserveScroll: true,
        });
    };
</script>
