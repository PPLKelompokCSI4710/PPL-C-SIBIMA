<template>
    <StaffLayout>
        <Head title="Monitoring Progres Mahasiswa" />

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Progres Mahasiswa</h3>
                    <p class="text-sm text-slate-500">
                        Pantau dan perbarui pencapaian akademik mahasiswa bimbingan.
                    </p>
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
                            <th class="px-6 py-4 font-bold text-center">IPK</th>
                            <th class="px-6 py-4 font-bold text-center">SKS Lulus</th>
                            <th class="px-6 py-4 font-bold text-center">MK Lulus</th>
                            <th class="px-6 py-4 font-bold">Progres Lulus</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="student in students"
                            :key="student.id"
                            class="hover:bg-slate-50/50 transition-colors group"
                        >
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-sm">
                                    {{ student.name }}
                                </p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">
                                    {{ student.nim }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-black text-indigo-600">{{
                                    student.ipk || '0.00'
                                }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-slate-700"
                                    >{{ student.total_sks }} SKS</span
                                >
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-600">
                                {{ student.passed_courses }} MK
                            </td>
                            <td class="px-6 py-4 w-48">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex-1 bg-slate-100 h-1.5 rounded-full overflow-hidden"
                                    >
                                        <div
                                            class="bg-indigo-500 h-full rounded-full transition-all duration-500"
                                            :style="`width: ${Math.min((student.total_sks / 144) * 100, 100)}%`"
                                        />
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400"
                                        >{{ Math.round((student.total_sks / 144) * 100) }}%</span
                                    >
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all flex items-center gap-1.5 ml-auto"
                                    @click="openEditModal(student)"
                                >
                                    <Edit3Icon class="w-3.5 h-3.5" /> Update Nilai
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Update Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div
                class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                @click="isModalOpen = false"
            />
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden transform transition-all"
            >
                <div
                    class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50"
                >
                    <h3 class="font-bold text-slate-800">
                        Update Nilai: {{ selectedStudent.name }}
                    </h3>
                    <button
                        class="text-slate-400 hover:text-slate-600"
                        @click="isModalOpen = false"
                    >
                        <XIcon class="w-5 h-5" />
                    </button>
                </div>

                <form class="p-6 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1"
                            >IPK Kumulatif</label
                        >
                        <input
                            v-model="form.ipk"
                            type="number"
                            step="0.01"
                            min="0"
                            max="4"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1"
                                >Total SKS Lulus</label
                            >
                            <input
                                v-model="form.total_sks"
                                type="number"
                                min="0"
                                max="144"
                                class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1"
                                >Jumlah MK Lulus</label
                            >
                            <input
                                v-model="form.passed_courses"
                                type="number"
                                min="0"
                                class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                required
                            />
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button
                            type="button"
                            class="flex-1 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-colors"
                            @click="isModalOpen = false"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200"
                        >
                            Update Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
    import StaffLayout from '@/Layouts/StaffLayout.vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import { Edit3Icon, XIcon } from 'lucide-vue-next';

    defineProps({
        students: { type: Array, default: () => [] },
    });

    const isModalOpen = ref(false);
    const selectedStudent = ref(null);

    const form = useForm({
        ipk: 0,
        total_sks: 0,
        passed_courses: 0,
    });

    const openEditModal = (student) => {
        selectedStudent.value = student;
        form.ipk = student.ipk;
        form.total_sks = student.total_sks;
        form.passed_courses = student.passed_courses;
        isModalOpen.value = true;
    };

    const submit = () => {
        form.put(route('staff.progress.update', selectedStudent.value.id), {
            onSuccess: () => (isModalOpen.value = false),
        });
    };
</script>
