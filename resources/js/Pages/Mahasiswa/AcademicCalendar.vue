<template>
    <StudentLayout>
        <Head title="Academic Calendar - SIBIMA" />

        <div class="space-y-8">
            <!-- Month Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Calendar View Placeholder -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-black text-slate-800">Mei 2026</h3>
                            <div class="flex gap-2">
                                <button class="p-2 bg-slate-100 rounded-xl hover:bg-slate-200">
                                    <ChevronLeftIcon class="w-4 h-4" />
                                </button>
                                <button class="p-2 bg-slate-100 rounded-xl hover:bg-slate-200">
                                    <ChevronRightIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-4 mb-4">
                            <div
                                v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                                :key="day"
                                class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest"
                            >
                                {{ day }}
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-4">
                            <div
                                v-for="n in 31"
                                :key="n"
                                :class="[
                                    n === 15
                                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-200'
                                        : 'bg-slate-50 text-slate-600',
                                ]"
                                class="h-16 rounded-2xl flex flex-col items-center justify-center relative group hover:bg-blue-50 hover:text-blue-600 transition-all cursor-pointer"
                            >
                                <span class="text-sm font-bold">{{ n }}</span>
                                <div
                                    v-if="[3, 10, 15, 24].includes(n)"
                                    class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agenda List -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <BellIcon class="w-5 h-5 text-blue-500" /> Agenda Mendatang
                    </h3>

                    <div class="space-y-4">
                        <div
                            v-for="event in events"
                            :key="event.title"
                            class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:border-blue-200 transition-colors group"
                        >
                            <div class="flex gap-4">
                                <div
                                    :class="event.color"
                                    class="w-12 h-12 rounded-xl flex flex-col items-center justify-center shrink-0"
                                >
                                    <span class="text-xs font-bold uppercase">{{
                                        event.month
                                    }}</span>
                                    <span class="text-lg font-black leading-tight">{{
                                        event.day
                                    }}</span>
                                </div>
                                <div>
                                    <h4
                                        class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors"
                                    >
                                        {{ event.title }}
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ event.desc }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
    import { Head } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';

    defineProps({
        auth: { type: Object, default: () => ({}) },
    });

    const events = [
        {
            day: '03',
            month: 'Mei',
            title: 'Batas Akhir Revisi KRS',
            desc: 'Pastikan seluruh mata kuliah sudah disetujui Dosen PA.',
            color: 'bg-blue-100 text-blue-700',
        },
        {
            day: '10',
            month: 'Mei',
            title: 'Awal Perkuliahan',
            desc: 'Kuliah perdana semester genap dimulai pukul 08:00.',
            color: 'bg-emerald-100 text-emerald-700',
        },
        {
            day: '15',
            month: 'Mei',
            title: 'Dies Natalis Univ',
            desc: 'Libur Akademik dalam rangka perayaan HUT Universitas.',
            color: 'bg-purple-100 text-purple-700',
        },
        {
            day: '24',
            month: 'Mei',
            title: 'Input Nilai Tugas 1',
            desc: 'Batas akhir pengumpulan tugas mandiri pertama.',
            color: 'bg-amber-100 text-amber-700',
        },
    ];
</script>
