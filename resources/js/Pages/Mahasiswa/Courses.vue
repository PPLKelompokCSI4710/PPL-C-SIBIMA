<template>
    <StudentLayout>
        <Head title="Courses Catalog - SIBIMA" />

        <div class="space-y-6">
            <!-- Top Status Bar -->
            <div
                class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4 shadow-sm"
            >
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Total Courses
                        </p>
                        <div class="flex items-baseline gap-2">
                            <p class="text-2xl font-bold text-slate-800">
                                {{ courses.length }}
                            </p>
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
                            <th class="px-6 py-4 font-semibold text-right">Recommended Semester</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="course in filteredCourses"
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
                                        {{ course.name }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-500 font-mono">
                                    {{ course.code }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-bold"
                                    >{{ course.credits }} SKS</span
                                >
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm text-right">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100"
                                    >Sem {{ course.semester || '-' }}</span
                                >
                            </td>
                        </tr>
                        <tr v-if="filteredCourses.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center text-slate-400"
                                >
                                    <BookOpenIcon class="w-12 h-12 mb-3 opacity-20" />
                                    <p class="text-sm font-medium text-slate-500">
                                        No courses found
                                    </p>
                                    <p class="text-xs mt-1">Try adjusting your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Head } from '@inertiajs/vue3';
    import StudentLayout from '@/Layouts/StudentLayout.vue';
    import { BookIcon, SearchIcon } from 'lucide-vue-next';

    const props = defineProps({
        auth: { type: Object, default: () => ({}) },
        courses: { type: Array, default: () => [] },
    });

    // Search Filter
    const searchQuery = ref('');

    const filteredCourses = computed(() => {
        if (!searchQuery.value) return props.courses;
        const lowerQuery = searchQuery.value.toLowerCase();
        return props.courses.filter(
            (p) =>
                p.name.toLowerCase().includes(lowerQuery) ||
                p.code.toLowerCase().includes(lowerQuery),
        );
    });
</script>
