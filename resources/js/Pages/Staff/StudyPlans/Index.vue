<template>
    <StaffLayout>
        <Head title="Persetujuan KRS" />

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Persetujuan KRS</h3>
                    <p class="text-sm text-slate-500">
                        Tinjau dan setujui rencana studi (KRS) yang diajukan mahasiswa.
                    </p>
                </div>

                <div class="flex gap-2 bg-white p-1 rounded-xl border border-slate-200">
                    <button
                        :class="[
                            !filters.status
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-600 hover:bg-slate-50',
                        ]"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all"
                        @click="filterStatus(null)"
                    >
                        Semua
                    </button>
                    <button
                        :class="[
                            filters.status === 'pending'
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-600 hover:bg-slate-50',
                        ]"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all"
                        @click="filterStatus('pending')"
                    >
                        Pending
                    </button>
                    <button
                        :class="[
                            filters.status === 'approved'
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-600 hover:bg-slate-50',
                        ]"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all"
                        @click="filterStatus('approved')"
                    >
                        Disetujui
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider"
                        >
                            <th class="px-6 py-4 font-bold">Mahasiswa</th>
                            <th class="px-6 py-4 font-bold">Mata Kuliah</th>
                            <th class="px-6 py-4 font-bold text-center">SKS</th>
                            <th class="px-6 py-4 font-bold">Status</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="plan in studyPlans"
                            :key="plan.id"
                            class="hover:bg-slate-50/50 transition-colors group"
                        >
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-sm">
                                    {{ plan.student_name }}
                                </p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">
                                    {{ plan.student_nim }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-700 text-sm">
                                    {{ plan.course_name }}
                                </p>
                                <p class="text-xs text-indigo-500 font-mono mt-0.5">
                                    {{ plan.course_code }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-bold"
                                    >{{ plan.credits }} SKS</span
                                >
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    v-if="plan.status === 'approved'"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold"
                                >
                                    <CheckCircleIcon class="w-3 h-3" /> Disetujui
                                </span>
                                <span
                                    v-else-if="plan.status === 'rejected'"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold"
                                >
                                    <XCircleIcon class="w-3 h-3" /> Ditolak
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold"
                                >
                                    <ClockIcon class="w-3 h-3 animate-pulse" /> Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div
                                    v-if="plan.status === 'pending'"
                                    class="flex items-center justify-end gap-2"
                                >
                                    <button
                                        class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold hover:bg-emerald-100 transition-colors flex items-center gap-1"
                                        @click="approve(plan.id)"
                                    >
                                        <CheckIcon class="w-3.5 h-3.5" /> Approve
                                    </button>
                                    <button
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors flex items-center gap-1"
                                        @click="reject(plan.id)"
                                    >
                                        <XIcon class="w-3.5 h-3.5" /> Reject
                                    </button>
                                </div>
                                <p v-else class="text-xs text-slate-400 font-medium">
                                    Diajukan: {{ plan.created_at }}
                                </p>
                            </td>
                        </tr>
                        <tr v-if="studyPlans.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-sm">
                                Tidak ada data pengajuan KRS yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
    import StaffLayout from '@/Layouts/StaffLayout.vue';
    import { Head, router } from '@inertiajs/vue3';
    import { CheckCircleIcon, XCircleIcon, ClockIcon, CheckIcon, XIcon } from 'lucide-vue-next';

    defineProps({
        studyPlans: { type: Array, default: () => [] },
        filters: { type: Object, default: () => ({}) },
    });

    const filterStatus = (status) => {
        router.get(route('staff.study-plans.index'), { status }, { preserveState: true });
    };

    const approve = (id) => {
        router.post(route('staff.study-plans.approve', id));
    };

    const reject = (id) => {
        router.post(route('staff.study-plans.reject', id));
    };
</script>
