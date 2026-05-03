<template>
    <StudentLayout>
        <Head title="Study Plan (KRS) - SIBIMA" />

        <div class="space-y-6">
            <!-- Top Status Bar -->
            <div
                class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-6 shadow-sm"
            >
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Total Credits
                        </p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <p
                                class="text-2xl font-bold"
                                :class="totalSks > 24 ? 'text-red-600' : 'text-slate-800'"
                            >
                                {{ totalSks }}
                            </p>
                            <p class="text-sm text-slate-500 font-medium">/ 24 SKS</p>
                        </div>
                    </div>
                    <div class="h-10 w-px bg-slate-200" />
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Status
                        </p>
                        <div class="mt-1">
                            <span
                                v-if="totalSks <= 24"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-sm font-medium"
                            >
                                <CheckCircle2Icon class="w-4 h-4" /> Within Limit
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-sm font-medium"
                            >
                                <AlertCircleIcon class="w-4 h-4" /> Exceeds Limit
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <SearchIcon
                            class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search courses..."
                            class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all w-64 text-slate-700"
                        />
                    </div>
                    <button
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2"
                        @click="openAddModal"
                    >
                        <PlusIcon class="w-4 h-4" /> Tambah Mata Kuliah
                    </button>
                </div>
            </div>

            <!-- Main Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider"
                        >
                            <th class="px-6 py-4 font-semibold">Course Name</th>
                            <th class="px-6 py-4 font-semibold">Course Code</th>
                            <th class="px-6 py-4 font-semibold">Credits</th>
                            <th class="px-6 py-4 font-semibold">Semester</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="course in filteredStudyPlans"
                            :key="course.id"
                            class="hover:bg-slate-50 transition-colors group"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs"
                                    >
                                        <BookIcon class="w-4 h-4" />
                                    </div>
                                    <p class="font-medium text-slate-800">
                                        {{ course.courseName }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-500 font-mono">
                                    {{ course.courseCode }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-bold"
                                    >{{ course.credits }} SKS</span
                                >
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                Sem {{ course.semester }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    v-if="course.status === 'approved'"
                                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100"
                                >
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                                    Approved
                                </span>
                                <span
                                    v-else-if="course.status === 'rejected'"
                                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium border border-red-100"
                                >
                                    <div class="w-1.5 h-1.5 rounded-full bg-red-500" />
                                    Rejected
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-amber-50 text-amber-700 text-xs font-medium border border-amber-100"
                                >
                                    <div
                                        class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"
                                    />
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                                        title="Edit"
                                        @click="openEditModal(course)"
                                    >
                                        <Edit2Icon class="w-4 h-4" />
                                    </button>
                                    <button
                                        class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors"
                                        title="Delete"
                                        @click="openDeleteModal(course)"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredStudyPlans.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center text-slate-400"
                                >
                                    <BookOpenIcon class="w-12 h-12 mb-3 opacity-20" />
                                    <p class="text-sm font-medium text-slate-500">
                                        No courses found
                                    </p>
                                    <p class="text-xs mt-1">
                                        Try adjusting your search or add a new course.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Course Modal -->
        <div v-if="isModalOpen" class="relative z-50">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" />
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all w-full max-w-lg"
                    >
                        <div
                            class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50"
                        >
                            <h3 class="text-lg font-semibold text-slate-800">
                                {{ isEditing ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}
                            </h3>
                            <button
                                class="text-slate-400 hover:text-slate-600 transition-colors"
                                @click="isModalOpen = false"
                            >
                                <XIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <form @submit.prevent="submitStudyPlan">
                            <div class="p-6 space-y-5">
                                <!-- Error Message -->
                                <div
                                    v-if="form.errors.course_id || form.errors.message"
                                    class="p-3 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm"
                                >
                                    {{ form.errors.course_id || form.errors.message }}
                                </div>

                                <!-- Course Select -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1"
                                        >Course Name <span class="text-red-500">*</span></label
                                    >
                                    <select
                                        v-model="form.course_id"
                                        class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white text-slate-700"
                                        @change="onCourseSelect"
                                    >
                                        <option value="" disabled>-- Select a course --</option>
                                        <option
                                            v-for="c in availableCourses"
                                            :key="c.id"
                                            :value="c.id"
                                        >
                                            {{ c.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Course Code -->
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1"
                                            >Course Code</label
                                        >
                                        <input
                                            type="text"
                                            :value="selectedCourse?.code"
                                            readonly
                                            class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 cursor-not-allowed border outline-none"
                                        />
                                    </div>
                                    <!-- Credits -->
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1"
                                            >Credits (SKS)</label
                                        >
                                        <input
                                            type="text"
                                            :value="selectedCourse?.credits"
                                            readonly
                                            class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 cursor-not-allowed border outline-none"
                                        />
                                    </div>
                                </div>

                                <!-- Semester -->
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1"
                                        >Semester <span class="text-red-500">*</span></label
                                    >
                                    <select
                                        v-model="form.semester"
                                        class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white text-slate-700"
                                    >
                                        <option v-for="n in 8" :key="n" :value="n">
                                            Semester {{ n }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div
                                class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-xl"
                            >
                                <button
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors"
                                    @click="isModalOpen = false"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.course_id"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center gap-2"
                                >
                                    <SaveIcon class="w-4 h-4" />
                                    {{ isEditing ? 'Update' : 'Tambah' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="isDeleteModalOpen" class="relative z-50">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" />
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all w-full max-w-sm text-center p-6"
                    >
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4"
                        >
                            <AlertTriangleIcon class="h-6 w-6 text-red-600" />
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Hapus Mata Kuliah</h3>
                        <p class="text-sm text-slate-500 mb-6">
                            Apakah Anda yakin ingin menghapus
                            <span class="font-semibold text-slate-700">{{
                                planToDelete?.courseName
                            }}</span>
                            dari KRS Anda? Aksi ini tidak dapat dibatalkan.
                        </p>

                        <div class="flex justify-center gap-3">
                            <button
                                class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors w-full"
                                @click="isDeleteModalOpen = false"
                            >
                                Batal
                            </button>
                            <button
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition-colors shadow-sm w-full"
                                @click="executeDelete"
                            >
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Head, useForm, router } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import {
        BookOpenIcon,
        AlertTriangleIcon,
        CheckCircle2Icon,
        AlertCircleIcon,
        PlusIcon,
        BookIcon,
        TrashIcon,
        XIcon,
        SaveIcon,
        Edit2Icon,
        SearchIcon,
    } from 'lucide-vue-next';

    const props = defineProps({
        auth: { type: Object, default: () => ({}) },
        studyPlans: { type: Array, default: () => [] },
        availableCourses: { type: Array, default: () => [] },
    });

    // Search Filter
    const searchQuery = ref('');

    const filteredStudyPlans = computed(() => {
        if (!searchQuery.value) return props.studyPlans;
        const lowerQuery = searchQuery.value.toLowerCase();
        return props.studyPlans.filter(
            (p) =>
                p.courseName.toLowerCase().includes(lowerQuery) ||
                p.courseCode.toLowerCase().includes(lowerQuery),
        );
    });

    // Compute total SKS
    const totalSks = computed(() => {
        return props.studyPlans.reduce((sum, course) => sum + parseInt(course.credits || 0), 0);
    });

    // Study Plan Form and Modal State
    const isModalOpen = ref(false);
    const isEditing = ref(false);
    const editingPlanId = ref(null);
    const selectedCourse = ref(null);

    const form = useForm({
        course_id: '',
        semester: 5,
    });

    const openAddModal = () => {
        isEditing.value = false;
        editingPlanId.value = null;
        form.reset();
        form.clearErrors();
        selectedCourse.value = null;
        isModalOpen.value = true;
    };

    const openEditModal = (plan) => {
        isEditing.value = true;
        editingPlanId.value = plan.id;
        form.clearErrors();
        form.course_id = plan.courseId;
        form.semester = plan.semester;
        onCourseSelect();
        isModalOpen.value = true;
    };

    const onCourseSelect = () => {
        selectedCourse.value = props.availableCourses.find((c) => c.id === form.course_id);
    };

    const submitStudyPlan = () => {
        if (isEditing.value) {
            form.put(route('mahasiswa.study-plans.update', editingPlanId.value), {
                preserveScroll: true,
                onSuccess: () => {
                    isModalOpen.value = false;
                },
            });
        } else {
            form.post(route('mahasiswa.study-plans.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    isModalOpen.value = false;
                },
            });
        }
    };

    // Delete State
    const isDeleteModalOpen = ref(false);
    const planToDelete = ref(null);

    const openDeleteModal = (plan) => {
        planToDelete.value = plan;
        isDeleteModalOpen.value = true;
    };

    const executeDelete = () => {
        if (planToDelete.value) {
            router.delete(route('mahasiswa.study-plans.destroy', planToDelete.value.id), {
                preserveScroll: true,
                onSuccess: () => {
                    isDeleteModalOpen.value = false;
                    planToDelete.value = null;
                },
            });
        }
    };
</script>
