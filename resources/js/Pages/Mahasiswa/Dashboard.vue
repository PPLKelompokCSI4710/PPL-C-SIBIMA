<template>
    <StudentLayout>
        <Head title="Dashboard Mahasiswa - SIBIMA" />

        <div class="space-y-8">
            <!-- Notifications/Alerts -->
            <div
                v-if="hasWarnings"
                class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 shadow-sm transition-all"
            >
                <AlertTriangleIcon class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <h4 class="text-sm font-bold text-amber-800">Action Required</h4>
                    <p class="text-sm text-amber-700 mt-1">
                        Your current study plan exceeds the maximum limit of 24 SKS or is
                        incomplete. Please review your KRS.
                    </p>
                </div>
            </div>

            <!-- Top Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- GPA Card -->
                <div
                    class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow relative group"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600"
                        >
                            <AwardIcon class="w-5 h-5" />
                        </div>
                        <button
                            class="p-1.5 bg-slate-50 text-slate-400 hover:text-blue-600 rounded-md transition-colors opacity-0 group-hover:opacity-100"
                            title="Update Progress"
                            @click="openProgressModal"
                        >
                            <Edit2Icon class="w-4 h-4" />
                        </button>
                    </div>
                    <h3 class="text-slate-500 text-sm font-medium">Cumulative GPA (IPK)</h3>
                    <p class="text-3xl font-bold text-slate-800 mt-1">
                        {{ auth.progress?.ipk || auth.mahasiswa?.ipk || '0.00' }}
                    </p>
                </div>

                <!-- Total Credits Card -->
                <div
                    class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600"
                        >
                            <LayersIcon class="w-5 h-5" />
                        </div>
                    </div>
                    <h3 class="text-slate-500 text-sm font-medium">Study Plan Credits</h3>
                    <div class="flex items-baseline gap-2 mt-1">
                        <p
                            class="text-3xl font-bold"
                            :class="totalSks > 24 ? 'text-red-600' : 'text-slate-800'"
                        >
                            {{ totalSks }}
                        </p>
                        <p class="text-slate-500 font-medium">/ 24 SKS</p>
                    </div>
                </div>

                <!-- Progress Card -->
                <div
                    class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow relative group"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600"
                        >
                            <TargetIcon class="w-5 h-5" />
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold text-slate-800"
                                >{{ progressPercentage }}%</span
                            >
                            <button
                                class="p-1.5 bg-slate-50 text-slate-400 hover:text-blue-600 rounded-md transition-colors opacity-0 group-hover:opacity-100"
                                title="Update Progress"
                                @click="openProgressModal"
                            >
                                <Edit2Icon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                    <h3 class="text-slate-500 text-sm font-medium mb-2">
                        Study Progress ({{ passedSks }}/144)
                    </h3>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div
                            class="bg-emerald-500 h-full rounded-full transition-all duration-1000"
                            :style="`width: ${progressPercentage}%`"
                        />
                    </div>
                </div>

                <!-- Status Card -->
                <div
                    class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center items-center text-center"
                >
                    <div
                        class="w-16 h-16 rounded-full flex items-center justify-center mb-3"
                        :class="
                            isWarning
                                ? 'bg-amber-100 text-amber-600'
                                : 'bg-emerald-100 text-emerald-600'
                        "
                    >
                        <AlertCircleIcon v-if="isWarning" class="w-8 h-8" />
                        <CheckCircle2Icon v-else class="w-8 h-8" />
                    </div>
                    <h3 class="text-slate-500 text-sm font-medium">Academic Status</h3>
                    <p
                        class="text-lg font-bold mt-1"
                        :class="isWarning ? 'text-amber-700' : 'text-emerald-700'"
                    >
                        {{ isWarning ? 'Warning' : 'On Track' }}
                    </p>
                </div>
            </div>

            <!-- Main Section Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Study Plan Preview -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Study Plan Preview</h2>
                            <p class="text-sm text-slate-500">
                                Manage your currently selected courses
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <Link
                                :href="route('mahasiswa.study-plans.index')"
                                class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors inline-block"
                            >
                                View Full KRS
                            </Link>
                            <button
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2"
                                @click="openAddModal"
                            >
                                <PlusIcon class="w-4 h-4" /> Tambah Mata Kuliah
                            </button>
                        </div>
                    </div>

                    <div
                        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden"
                    >
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider"
                                >
                                    <th class="px-6 py-4 font-semibold">Course Name</th>
                                    <th class="px-6 py-4 font-semibold">Credits</th>
                                    <th class="px-6 py-4 font-semibold">Semester</th>
                                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr
                                    v-for="course in studyPlans"
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
                                            <div>
                                                <p class="font-medium text-slate-800">
                                                    {{ course.courseName }}
                                                </p>
                                                <p class="text-xs text-slate-500 font-mono">
                                                    {{ course.courseCode }}
                                                </p>
                                            </div>
                                        </div>
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
                                <tr v-if="studyPlans.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        No courses selected yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Quick Info -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">
                            Quick Actions
                        </h3>
                        <div class="space-y-3">
                            <Link
                                :href="route('mahasiswa.calendar')"
                                class="w-full flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-left transition-colors group"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded bg-white border border-slate-200 flex items-center justify-center text-slate-500 group-hover:text-blue-600 transition-colors"
                                    >
                                        <CalendarIcon class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800 text-sm">
                                            Academic Calendar
                                        </p>
                                    </div>
                                </div>
                                <ChevronRightIcon class="w-4 h-4 text-slate-400" />
                            </Link>
                        </div>
                    </div>
                </div>
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

        <!-- Update Progress Modal -->
        <div v-if="isProgressModalOpen" class="relative z-50">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" />
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all w-full max-w-sm"
                    >
                        <div
                            class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50"
                        >
                            <h3 class="text-lg font-semibold text-slate-800">
                                Update Progress Studi
                            </h3>
                            <button
                                class="text-slate-400 hover:text-slate-600 transition-colors"
                                @click="isProgressModalOpen = false"
                            >
                                <XIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <form @submit.prevent="submitProgress">
                            <div class="p-6 space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1"
                                        >IPK Saat Ini <span class="text-red-500">*</span></label
                                    >
                                    <input
                                        v-model="progressForm.ipk"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="4"
                                        required
                                        class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white text-slate-700"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1"
                                        >Total SKS Lulus <span class="text-red-500">*</span></label
                                    >
                                    <input
                                        v-model="progressForm.total_sks"
                                        type="number"
                                        min="0"
                                        max="144"
                                        required
                                        class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white text-slate-700"
                                    />
                                </div>
                            </div>

                            <div
                                class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 rounded-b-xl"
                            >
                                <button
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors"
                                    @click="isProgressModalOpen = false"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    :disabled="progressForm.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center gap-2"
                                >
                                    <SaveIcon class="w-4 h-4" /> Simpan
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
    import { useForm, router, Head, Link } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import {
        AlertTriangleIcon,
        AwardIcon,
        LayersIcon,
        TargetIcon,
        AlertCircleIcon,
        CheckCircle2Icon,
        PlusIcon,
        BookIcon,
        TrashIcon,
        CalendarIcon,
        ChevronRightIcon,
        XIcon,
        SaveIcon,
        Edit2Icon,
    } from 'lucide-vue-next';

    const props = defineProps({
        auth: { type: Object, default: () => ({}) },
        studyPlans: { type: Array, default: () => [] },
        availableCourses: { type: Array, default: () => [] },
    });

    // Compute Student Progress Data
    const passedSks = computed(() => props.auth.progress?.total_sks || 0);
    const progressPercentage = computed(() => {
        const p = Math.round((passedSks.value / 144) * 100);
        return p > 100 ? 100 : p;
    });

    // Compute total SKS
    const totalSks = computed(() => {
        return props.studyPlans.reduce((sum, course) => sum + parseInt(course.credits || 0), 0);
    });

    const isWarning = computed(() => totalSks.value > 24);
    const hasWarnings = computed(() => totalSks.value > 24 || totalSks.value < 12);

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

    // Progress Form State
    const isProgressModalOpen = ref(false);
    const progressForm = useForm({
        ipk: props.auth.progress?.ipk || props.auth.mahasiswa?.ipk || 0,
        total_sks: props.auth.progress?.total_sks || 0,
    });

    const openProgressModal = () => {
        progressForm.ipk = props.auth.progress?.ipk || props.auth.mahasiswa?.ipk || 0;
        progressForm.total_sks = props.auth.progress?.total_sks || 0;
        progressForm.clearErrors();
        isProgressModalOpen.value = true;
    };

    const submitProgress = () => {
        progressForm.put(route('mahasiswa.progress.update'), {
            preserveScroll: true,
            onSuccess: () => {
                isProgressModalOpen.value = false;
            },
        });
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

<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
</style>
