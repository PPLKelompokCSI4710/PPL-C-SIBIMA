<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
defineProps({
    dosenList: {
        type: Array,
        default: () => [],
    },
    mahasiswa: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    dosen_id: '',
    tanggal: '',
    waktu: '',
    topik_bimbingan: '',
    tipe: 'offline',
});

const submit = () => {
    form.post(route('mahasiswa.jadwal-bimbingan.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Input Jadwal Bimbingan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Input Jadwal Bimbingan
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div
                    v-if="$page.props.flash?.success"
                    class="mb-4 rounded-md bg-green-50 p-4"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5 text-green-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form
                            class="space-y-6"
                            @submit.prevent="submit"
                        >
                            <!-- Dosen Pembimbing -->
                            <div>
                                <label
                                    for="dosen_id"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Dosen Pembimbing
                                </label>
                                <select
                                    id="dosen_id"
                                    v-model="form.dosen_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                >
                                    <option
                                        value=""
                                        disabled
                                    >
                                        -- Pilih Dosen Pembimbing --
                                    </option>
                                    <option
                                        v-for="dosen in dosenList"
                                        :key="dosen.id"
                                        :value="dosen.id"
                                    >
                                        {{ dosen.nama_lengkap }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.dosen_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.dosen_id }}
                                </p>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label
                                    for="tanggal"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Tanggal Bimbingan
                                </label>
                                <input
                                    id="tanggal"
                                    v-model="form.tanggal"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                >
                                <p
                                    v-if="form.errors.tanggal"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.tanggal }}
                                </p>
                            </div>

                            <!-- Waktu -->
                            <div>
                                <label
                                    for="waktu"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Waktu Bimbingan
                                </label>
                                <input
                                    id="waktu"
                                    v-model="form.waktu"
                                    type="time"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                >
                                <p
                                    v-if="form.errors.waktu"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.waktu }}
                                </p>
                            </div>

                            <!-- Topik Bimbingan -->
                            <div>
                                <label
                                    for="topik_bimbingan"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Topik Bimbingan
                                </label>
                                <textarea
                                    id="topik_bimbingan"
                                    v-model="form.topik_bimbingan"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="Tuliskan topik atau materi yang ingin dibahas..."
                                    required
                                />
                                <p
                                    v-if="form.errors.topik_bimbingan"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.topik_bimbingan }}
                                </p>
                            </div>

                            <!-- Tipe Bimbingan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Tipe Bimbingan
                                </label>
                                <div class="mt-2 flex space-x-6">
                                    <label class="inline-flex items-center">
                                        <input
                                            v-model="form.tipe"
                                            type="radio"
                                            value="offline"
                                            class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">Offline (Tatap Muka)</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input
                                            v-model="form.tipe"
                                            type="radio"
                                            value="online"
                                            class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-700">Online (Daring)</span>
                                    </label>
                                </div>
                                <p
                                    v-if="form.errors.tipe"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.tipe }}
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    <svg
                                        v-if="form.processing"
                                        class="mr-2 h-4 w-4 animate-spin"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        />
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        />
                                    </svg>
                                    {{ form.processing ? 'Menyimpan...' : 'Ajukan Jadwal Bimbingan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
