<template>
    <StaffLayout>
        <Head title="Manajemen Mata Kuliah" />

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Daftar Mata Kuliah</h3>
                    <p class="text-sm text-slate-500">
                        Kelola kurikulum dan mata kuliah yang tersedia di sistem.
                    </p>
                </div>
                <button
                    class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition-colors flex items-center gap-2 shadow-md shadow-indigo-100"
                    @click="openAddModal"
                >
                    <PlusIcon class="w-4 h-4" /> Tambah Mata Kuliah
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider"
                        >
                            <th class="px-6 py-4 font-bold">Kode</th>
                            <th class="px-6 py-4 font-bold">Nama Mata Kuliah</th>
                            <th class="px-6 py-4 font-bold text-center">SKS</th>
                            <th class="px-6 py-4 font-bold text-center">Semester</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="course in courses"
                            :key="course.id"
                            class="hover:bg-slate-50/50 transition-colors group"
                        >
                            <td class="px-6 py-4 font-mono text-sm font-bold text-indigo-600">
                                {{ course.code }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ course.name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-bold"
                                    >{{ course.credits }} SKS</span
                                >
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-600">
                                Semester {{ course.semester }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        @click="openEditModal(course)"
                                    >
                                        <PencilIcon class="w-4 h-4" />
                                    </button>
                                    <button
                                        class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        @click="deleteCourse(course.id)"
                                    >
                                        <Trash2Icon class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
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
                        {{ isEditing ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}
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
                        <label class="block text-sm font-bold text-slate-700 mb-1">Kode MK</label>
                        <input
                            v-model="form.code"
                            type="text"
                            placeholder="E.g. CS101"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required
                        />
                        <p v-if="form.errors.code" class="text-red-500 text-xs mt-1">
                            {{ form.errors.code }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1"
                            >Nama Mata Kuliah</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="E.g. Algoritma"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            required
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">SKS</label>
                            <input
                                v-model="form.credits"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1"
                                >Semester</label
                            >
                            <input
                                v-model="form.semester"
                                type="number"
                                min="1"
                                max="8"
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
                            {{ isEditing ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
    import StaffLayout from '@/Layouts/StaffLayout.vue';
    import { Head, useForm, router } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import { PlusIcon, PencilIcon, Trash2Icon, XIcon } from 'lucide-vue-next';

    defineProps({
        courses: { type: Array, default: () => [] },
    });

    const isModalOpen = ref(false);
    const isEditing = ref(false);
    const editingId = ref(null);

    const form = useForm({
        code: '',
        name: '',
        credits: 3,
        semester: 1,
    });

    const openAddModal = () => {
        isEditing.value = false;
        form.reset();
        isModalOpen.value = true;
    };

    const openEditModal = (course) => {
        isEditing.value = true;
        editingId.value = course.id;
        form.code = course.code;
        form.name = course.name;
        form.credits = course.credits;
        form.semester = course.semester;
        isModalOpen.value = true;
    };

    const submit = () => {
        if (isEditing.value) {
            form.put(route('staff.courses.update', editingId.value), {
                onSuccess: () => (isModalOpen.value = false),
            });
        } else {
            form.post(route('staff.courses.store'), {
                onSuccess: () => (isModalOpen.value = false),
            });
        }
    };

    const deleteCourse = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')) {
            router.delete(route('staff.courses.destroy', id));
        }
    };
</script>
